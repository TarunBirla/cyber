#!/usr/bin/env node
'use strict';
/**
 * list-installed-software.js  (v2, with security exposure check)
 * Lists installed software on Windows / macOS (Linux bonus) and, with --security,
 * flags software that may be exposed to known, actively exploited vulnerabilities.
 * Needs only Node.js 14+. No npm packages.
 */
const { execFileSync } = require('child_process');
const https = require('https');
const os = require('os');
const fs = require('fs');
const path = require('path');

const args = process.argv.slice(2);
const has = (f) => args.includes(f);
const val = (f) => { const i = args.indexOf(f); return i > -1 ? args[i + 1] : null; };

const KEV_URL = 'https://www.cisa.gov/sites/default/files/feeds/known_exploited_vulnerabilities.json';
const NVD_URL = 'https://services.nvd.nist.gov/rest/json/cves/2.0?cveId=';

const toArray = (x) => (Array.isArray(x) ? x : x ? [x] : []);
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

function run(cmd, cmdArgs) {
  return execFileSync(cmd, cmdArgs, { encoding: 'utf8', maxBuffer: 256 * 1024 * 1024, stdio: ['ignore', 'pipe', 'ignore'] });
}

/* ============================================================ INVENTORY */
function powershell(script) {
  const prefix = '[Console]::OutputEncoding=[System.Text.Encoding]::UTF8;';
  const encoded = Buffer.from(prefix + script, 'utf16le').toString('base64');
  const psArgs = ['-NoProfile', '-NonInteractive', '-ExecutionPolicy', 'Bypass', '-EncodedCommand', encoded];
  let out;
  try { out = run('powershell.exe', psArgs); } catch (e) { out = run('pwsh', psArgs); }
  out = out.trim();
  return out ? toArray(JSON.parse(out)) : [];
}
const fmtDate = (d) => (/^\d{8}$/.test(d || '') ? `${d.slice(0, 4)}-${d.slice(4, 6)}-${d.slice(6, 8)}` : d || '');

function windowsApps() {
  const registry = String.raw`
$paths = 'HKLM:\Software\Microsoft\Windows\CurrentVersion\Uninstall\*',
         'HKLM:\Software\WOW6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*',
         'HKCU:\Software\Microsoft\Windows\CurrentVersion\Uninstall\*'
Get-ItemProperty $paths -ErrorAction SilentlyContinue |
  Where-Object { $_.DisplayName -and -not $_.SystemComponent -and -not $_.ParentKeyName } |
  Select-Object DisplayName, DisplayVersion, Publisher, InstallDate, InstallLocation |
  ConvertTo-Json -Compress`;
  const items = powershell(registry).map((a) => ({
    name: a.DisplayName, version: a.DisplayVersion || '', publisher: a.Publisher || '',
    installDate: fmtDate(a.InstallDate), path: a.InstallLocation || '', source: 'Installed program',
  }));
  if (has('--store')) {
    const store = String.raw`
Get-AppxPackage | Where-Object { -not $_.IsFramework -and -not $_.IsResourcePackage -and $_.SignatureKind -ne 'System' } |
  Select-Object @{n='DisplayName';e={$_.Name}}, @{n='DisplayVersion';e={$_.Version.ToString()}}, @{n='Publisher';e={$_.PublisherId}}, @{n='InstallLocation';e={$_.InstallLocation}} |
  ConvertTo-Json -Compress`;
    try {
      powershell(store).forEach((a) => items.push({ name: a.DisplayName, version: a.DisplayVersion || '', publisher: a.Publisher || '', installDate: '', path: a.InstallLocation || '', source: 'Microsoft Store' }));
    } catch (e) { console.error('Could not read Microsoft Store apps:', e.message); }
  }
  return items;
}

function macApps() {
  const items = [];
  try {
    const data = JSON.parse(run('system_profiler', ['SPApplicationsDataType', '-json']));
    toArray(data.SPApplicationsDataType).forEach((a) => items.push({
      name: a._name, version: a.version || '', publisher: Array.isArray(a.signed_by) ? a.signed_by[0] : '',
      installDate: a.lastModified ? String(a.lastModified).slice(0, 10) : '', path: a.path || '', source: a.obtained_from || 'unknown',
    }));
  } catch (e) {
    ['/Applications', `${os.homedir()}/Applications`].forEach((dir) => {
      try {
        fs.readdirSync(dir).filter((f) => f.endsWith('.app')).forEach((f) => items.push({ name: f.replace(/\.app$/, ''), version: '', publisher: '', installDate: '', path: `${dir}/${f}`, source: 'Applications folder' }));
      } catch (_) { /* folder missing */ }
    });
  }
  if (has('--brew')) {
    [['list', '--versions'], ['list', '--cask', '--versions']].forEach((a, i) => {
      try {
        run('brew', a).split('\n').filter(Boolean).forEach((line) => {
          const [name, ...v] = line.trim().split(/\s+/);
          items.push({ name, version: v.join(', '), publisher: '', installDate: '', path: '', source: i ? 'Homebrew cask' : 'Homebrew' });
        });
      } catch (_) { /* brew not installed */ }
    });
  }
  return items;
}

function linuxApps() {
  const items = [];
  try {
    run('dpkg-query', ['-W', '-f=${binary:Package}\\t${Version}\\n']).split('\n').filter(Boolean).forEach((l) => {
      const [name, version] = l.split('\t');
      items.push({ name, version, publisher: '', installDate: '', path: '', source: 'dpkg' });
    });
  } catch (_) {
    try {
      run('rpm', ['-qa', '--qf', '%{NAME}\\t%{VERSION}-%{RELEASE}\\n']).split('\n').filter(Boolean).forEach((l) => {
        const [name, version] = l.split('\t');
        items.push({ name, version, publisher: '', installDate: '', path: '', source: 'rpm' });
      });
    } catch (e) { /* no supported package manager */ }
  }
  return items;
}

function collect() {
  if (process.platform === 'win32') return windowsApps();
  if (process.platform === 'darwin') return macApps();
  if (process.platform === 'linux') return linuxApps();
  throw new Error(`Unsupported platform: ${process.platform}`);
}

function tidy(items) {
  const seen = new Set();
  return items
    .filter((a) => a.name && String(a.name).trim())
    .filter((a) => { const k = `${a.name}|${a.version}`.toLowerCase(); if (seen.has(k)) return false; seen.add(k); return true; })
    .sort((a, b) => a.name.localeCompare(b.name, undefined, { sensitivity: 'base' }));
}

/* ============================================================ HTTP */
function httpGet(url, headers = {}, redirects = 5) {
  return new Promise((resolve, reject) => {
    const req = https.get(url, { headers: Object.assign({ 'User-Agent': 'installed-software-security-check/2.0' }, headers) }, (res) => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location && redirects > 0) {
        res.resume();
        return resolve(httpGet(new URL(res.headers.location, url).toString(), headers, redirects - 1));
      }
      let body = '';
      res.setEncoding('utf8');
      res.on('data', (c) => { body += c; });
      res.on('end', () => {
        if (res.statusCode < 200 || res.statusCode >= 300) {
          const err = new Error(`HTTP ${res.statusCode} from ${new URL(url).host}`);
          err.statusCode = res.statusCode;
          return reject(err);
        }
        resolve(body);
      });
    });
    req.setTimeout(30000, () => req.destroy(new Error('Request timed out')));
    req.on('error', reject);
  });
}

/* ============================================================ MATCHING */
const NOISE = new Set(['inc', 'corp', 'corporation', 'ltd', 'llc', 'gmbh', 'co', 'company', 'the', 'software', 'systems', 'technologies', 'technology', 'foundation', 'project', 'group', 'limited', 'sa', 'ag', 'plc', 'international', 'solutions']);
const GENERIC = new Set(['windows', 'office', 'server', 'client', 'desktop', 'reader', 'player', 'connect', 'manager', 'edge', 'browser', 'agent', 'mail', 'web', 'security', 'center', 'studio', 'framework', 'sdk', 'runtime', 'tools', 'suite', 'platform', 'cloud', 'services', 'service', 'access', 'virtual', 'remote', 'viewer', 'updater', 'helper']);
const OS_SKIP = new Set(['windows', 'win32k', 'macos', 'mac os x', 'ios', 'ipados', 'tvos', 'watchos', 'android', 'linux kernel', 'kernel']);

const norm = (s) => String(s || '').toLowerCase().replace(/\(.*?\)/g, ' ').replace(/[^a-z0-9]+/g, ' ').trim().split(' ').filter(Boolean);

function containsPhrase(tokens, phrase) {
  if (!phrase.length) return false;
  for (let i = 0; i + phrase.length <= tokens.length; i++) {
    let ok = true;
    for (let j = 0; j < phrase.length; j++) if (tokens[i + j] !== phrase[j]) { ok = false; break; }
    if (ok) return true;
  }
  return false;
}

function buildGroups(kev, days) {
  const cutoff = Date.now() - days * 86400000;
  const groups = new Map();
  toArray(kev.vulnerabilities).forEach((v) => {
    if (!(new Date(v.dateAdded).getTime() >= cutoff)) return;
    const key = `${v.vendorProject}|${v.product}`;
    if (!groups.has(key)) {
      const alts = String(v.product || '').split(/\s+and\s+|\/|,|;|\|/i).map(norm).filter((a) => a.length);
      groups.set(key, {
        vendor: v.vendorProject, product: v.product, alts,
        vendorTokens: norm(v.vendorProject).filter((t) => !NOISE.has(t)),
        productTokens: norm(v.product), cves: [],
        skip: alts.some((a) => OS_SKIP.has(a.join(' '))),
      });
    }
    groups.get(key).cves.push(v);
  });
  return [...groups.values()].filter((g) => !g.skip && g.alts.length);
}

function matchConfidence(sw, g) {
  const nameT = norm(sw.name);
  const pubT = norm(sw.publisher);
  const vendorHit = g.vendorTokens.some((t) => t.length >= 3 && (nameT.includes(t) || pubT.includes(t)));
  for (const alt of g.alts) {
    if (!containsPhrase(nameT, alt)) continue;
    if (vendorHit) return 'high';
    if (alt.length === 1 && GENERIC.has(alt[0])) continue;
    if (alt.length >= 2 || alt[0].length >= 6) return 'medium';
  }
  return null;
}

/* ============================================================ VERSION CHECK (NVD) */
function cmpVersions(a, b) {
  const x = String(a).match(/\d+/g) || ['0'];
  const y = String(b).match(/\d+/g) || ['0'];
  for (let i = 0; i < Math.max(x.length, y.length); i++) {
    const d = (parseInt(x[i] || '0', 10)) - (parseInt(y[i] || '0', 10));
    if (d) return d < 0 ? -1 : 1;
  }
  return 0;
}

function inRange(v, m) {
  const cv = (m.criteria || '').split(':')[5];
  const hasRange = m.versionStartIncluding || m.versionStartExcluding || m.versionEndIncluding || m.versionEndExcluding;
  if (hasRange) {
    if (m.versionStartIncluding && cmpVersions(v, m.versionStartIncluding) < 0) return false;
    if (m.versionStartExcluding && cmpVersions(v, m.versionStartExcluding) <= 0) return false;
    if (m.versionEndIncluding && cmpVersions(v, m.versionEndIncluding) > 0) return false;
    if (m.versionEndExcluding && cmpVersions(v, m.versionEndExcluding) >= 0) return false;
    return true;
  }
  if (cv === '*') return true;
  if (!cv || cv === '-') return false;
  return cmpVersions(v, cv) === 0;
}

function evaluateCve(cve, sw, g) {
  if (!cve) return 'unknown';
  const entries = [];
  toArray(cve.configurations).forEach((c) => toArray(c.nodes).forEach((n) => toArray(n.cpeMatch).forEach((m) => { if (m.vulnerable) entries.push(m); })));
  const tokens = new Set([...norm(sw.name), ...norm(sw.publisher), ...g.productTokens, ...g.vendorTokens]);
  const relevant = entries.filter((m) => {
    const p = (m.criteria || '').split(':');
    const prod = norm((p[4] || '').replace(/_/g, ' '));
    const vend = norm((p[3] || '').replace(/_/g, ' '));
    return prod.some((t) => tokens.has(t)) && vend.some((t) => tokens.has(t));
  });
  const ver = String(sw.version || '').match(/\d+(\.\d+)*/);
  if (!relevant.length || !ver) return 'unknown';
  return relevant.some((m) => inRange(ver[0], m)) ? 'affected' : 'patched';
}

function cvssOf(cve) {
  const m = (cve && cve.metrics) || {};
  const pick = (m.cvssMetricV40 || m.cvssMetricV31 || m.cvssMetricV30 || m.cvssMetricV2 || [])[0];
  return pick && pick.cvssData ? pick.cvssData.baseScore : null;
}

async function fetchNvd(id) {
  const headers = process.env.NVD_API_KEY ? { apiKey: process.env.NVD_API_KEY } : {};
  for (let attempt = 0; attempt < 2; attempt++) {
    try {
      const body = JSON.parse(await httpGet(NVD_URL + encodeURIComponent(id), headers));
      return (body.vulnerabilities && body.vulnerabilities[0] && body.vulnerabilities[0].cve) || null;
    } catch (e) {
      if ((e.statusCode === 403 || e.statusCode === 429) && attempt === 0) { await sleep(31000); continue; }
      throw e;
    }
  }
  return null;
}

/* ============================================================ BUILT-IN RULES */
const EOL_RULES = [
  { test: (a) => /flash player/i.test(a.name), reason: 'Adobe Flash Player reached end of life on 31 Dec 2020 and is no longer patched. Uninstall it.' },
  { test: (a) => /shockwave player/i.test(a.name), reason: 'Adobe Shockwave Player was discontinued in 2019 and is unpatched. Uninstall it.' },
  { test: (a) => /silverlight/i.test(a.name), reason: 'Microsoft Silverlight support ended on 12 Oct 2021.' },
  { test: (a) => process.platform === 'win32' && /quicktime/i.test(a.name), reason: 'Apple stopped updating QuickTime for Windows in 2016; known flaws remain unpatched.' },
  { test: (a) => /^python\s*2\.\d/i.test(a.name) || /^python.*\b2\.7\./i.test(a.name), reason: 'Python 2 reached end of life on 1 Jan 2020.' },
  { test: (a) => /\bjava\b/i.test(a.name) && /^(1\.[67]\.|[67]\.)/.test(a.version || ''), reason: 'Java 6 and 7 no longer receive public security updates.' },
  { test: (a) => /microsoft office/i.test(a.name) && /\b2010\b/.test(a.name), reason: 'Office 2010 support ended on 13 Oct 2020.' },
  { test: (a) => /microsoft office/i.test(a.name) && /\b2013\b/.test(a.name), reason: 'Office 2013 support ended on 11 Apr 2023.' },
  { test: (a) => /microsoft office/i.test(a.name) && /\b(2016|2019)\b/.test(a.name), reason: 'Office 2016 and 2019 support ended on 14 Oct 2025.' },
];

/* ============================================================ SECURITY SCAN */
async function loadKev() {
  const local = val('--kev-file');
  if (local) return JSON.parse(fs.readFileSync(local, 'utf8'));
  const cache = path.join(os.tmpdir(), 'cisa-kev-cache.json');
  let stale = null;
  try {
    const st = fs.statSync(cache);
    stale = fs.readFileSync(cache, 'utf8');
    if (Date.now() - st.mtimeMs < 24 * 3600 * 1000) return JSON.parse(stale);
  } catch (_) { /* no cache yet */ }
  try {
    console.error('Downloading CISA Known Exploited Vulnerabilities catalog...');
    const body = await httpGet(KEV_URL);
    const parsed = JSON.parse(body);
    try { fs.writeFileSync(cache, body); } catch (_) { /* cache is optional */ }
    return parsed;
  } catch (e) {
    if (stale) { console.error(`Download failed (${e.message}); using older cached catalog.`); return JSON.parse(stale); }
    throw new Error(`Could not download the CISA KEV catalog (${e.message}). Check your internet connection, or pass --kev-file.`);
  }
}

async function securityScan(rows) {
  const days = parseInt(val('--days') || '365', 10);
  const maxCves = parseInt(val('--max-cves') || '60', 10);
  const verify = !has('--no-verify');
  const kev = await loadKev();
  const groups = buildGroups(kev, days);

  const candidates = [];
  rows.forEach((sw) => {
    groups.forEach((g) => {
      const conf = matchConfidence(sw, g);
      if (conf) candidates.push({ sw, g, conf });
    });
  });

  const nvd = new Map();
  let verified = false;
  if (verify && candidates.length) {
    const ids = [...new Set(candidates.flatMap((c) => c.g.cves.map((v) => v.cveID)))].slice(0, maxCves);
    const delay = process.env.NVD_API_KEY ? 700 : 6500;
    let failed = false;
    for (let i = 0; i < ids.length && !failed; i++) {
      process.stderr.write(`\rVerifying versions with NVD: ${i + 1}/${ids.length}   `);
      try { nvd.set(ids[i], await fetchNvd(ids[i])); } catch (e) { console.error(`\nNVD lookup failed (${e.message}); remaining items will be marked REVIEW.`); failed = true; }
      if (i < ids.length - 1 && !failed) await sleep(delay);
    }
    process.stderr.write('\n');
    verified = nvd.size > 0;
  }

  const findings = [];
  const bySw = new Map();
  candidates.forEach((c) => {
    const k = `${c.sw.name}|${c.sw.version}`;
    if (!bySw.has(k)) bySw.set(k, { sw: c.sw, affected: [], review: [], conf: c.conf });
    const entry = bySw.get(k);
    c.g.cves.forEach((v) => {
      const cveObj = nvd.get(v.cveID);
      const status = verified && nvd.has(v.cveID) ? evaluateCve(cveObj, c.sw, c.g) : 'unknown';
      if (status === 'patched') return;
      const rec = { id: v.cveID, name: v.vulnerabilityName, dateAdded: v.dateAdded, dueDate: v.dueDate, ransomware: v.knownRansomwareCampaignUse === 'Known', cvss: cvssOf(cveObj), action: v.requiredAction, status };
      (status === 'affected' ? entry.affected : entry.review).push(rec);
    });
  });
  bySw.forEach((e) => {
    const base = { software: e.sw.name, version: e.sw.version, publisher: e.sw.publisher, matchConfidence: e.conf };
    if (e.affected.length) findings.push(Object.assign({ severity: 'CRITICAL', reason: 'Version falls inside the affected range of a vulnerability CISA reports as actively exploited.', cves: e.affected }, base));
    else if (e.review.length) findings.push(Object.assign({ severity: 'REVIEW', reason: verify ? 'Matches actively exploited vulnerabilities but the version could not be confirmed either way. Check you are on the latest version.' : 'Name matches actively exploited vulnerabilities (version NOT checked). Check you are on the latest version.', cves: e.review }, base));
  });

  rows.forEach((sw) => EOL_RULES.forEach((r) => {
    if (r.test(sw)) findings.push({ severity: 'HIGH', software: sw.name, version: sw.version, publisher: sw.publisher, reason: r.reason, cves: [], matchConfidence: 'rule' });
  }));

  const order = { CRITICAL: 0, HIGH: 1, REVIEW: 2 };
  findings.sort((a, b) => order[a.severity] - order[b.severity] || a.software.localeCompare(b.software));
  return {
    summary: { scanned: rows.length, critical: findings.filter((f) => f.severity === 'CRITICAL').length, high: findings.filter((f) => f.severity === 'HIGH').length, review: findings.filter((f) => f.severity === 'REVIEW').length, kevCatalogVersion: kev.catalogVersion || '', kevEntriesConsidered: groups.reduce((n, g) => n + g.cves.length, 0), versionCheck: verified ? 'NVD' : 'not performed', daysWindow: days },
    findings,
  };
}

/* ============================================================ OUTPUT */
const csvCell = (v) => `"${String(v == null ? '' : v).replace(/"/g, '""')}"`;
const inventoryCsv = (rows) => ['Name,Version,Publisher,InstallDate,Source,Path'].concat(rows.map((r) => [r.name, r.version, r.publisher, r.installDate, r.source, r.path].map(csvCell).join(','))).join('\n');
const findingsCsv = (fs_) => ['Severity,Software,Version,Publisher,Reason,CVEs,RansomwareUse'].concat(fs_.map((f) => [f.severity, f.software, f.version, f.publisher, f.reason, f.cves.map((c) => c.id).join(' '), f.cves.some((c) => c.ransomware) ? 'Yes' : ''].map(csvCell).join(','))).join('\n');

function inventoryTable(rows) {
  const cut = (s, n) => { s = String(s || ''); return s.length > n ? s.slice(0, n - 1) + '…' : s; };
  const cols = [['Name', 'name', 50], ['Version', 'version', 22], ['Publisher', 'publisher', 32], ['Source', 'source', 18]];
  const w = cols.map(([h, k, max]) => Math.min(max, Math.max(h.length, ...rows.map((r) => String(r[k] || '').length))));
  const line = (cells) => cells.map((c, i) => cut(c, w[i]).padEnd(w[i])).join('  ');
  return [line(cols.map((c) => c[0])), w.map((x) => '-'.repeat(x)).join('  ')].concat(rows.map((r) => line(cols.map(([, k]) => r[k])))).join('\n');
}

function securityText(res) {
  const s = res.summary;
  const out = [];
  out.push('SECURITY EXPOSURE REPORT');
  out.push(`Scanned ${s.scanned} items | CRITICAL ${s.critical} | HIGH ${s.high} | REVIEW ${s.review}`);
  out.push(`Source: CISA Known Exploited Vulnerabilities (${s.kevEntriesConsidered} entries from the last ${s.daysWindow} days) | version check: ${s.versionCheck}`);
  out.push('');
  if (!res.findings.length) out.push('No exposures found by these checks. This is not proof the machine is secure.');
  res.findings.forEach((f) => {
    out.push(`[${f.severity}] ${f.software} ${f.version || ''}${f.publisher ? ` (${f.publisher})` : ''}`);
    out.push(`    ${f.reason}`);
    f.cves.slice(0, 6).forEach((c) => out.push(`    - ${c.id}  ${c.name} | added ${c.dateAdded}${c.cvss ? ` | CVSS ${c.cvss}` : ''}${c.ransomware ? ' | USED IN RANSOMWARE' : ''}`));
    if (f.cves.length > 6) out.push(`    ... and ${f.cves.length - 6} more (see --json)`);
    if (f.cves[0] && f.cves[0].action) out.push(`    Action: ${f.cves[0].action}`);
    out.push('');
  });
  out.push('Notes: matching is by software name and publisher, so verify each finding. The operating system itself (Windows Update / macOS Software Update) is not covered.');
  return out.join('\n');
}

/* ============================================================ MAIN */
async function main() {
  let raw;
  if (val('--from')) { const j = JSON.parse(fs.readFileSync(val('--from'), 'utf8')); raw = toArray(j.software || j); } else raw = collect();
  const rows = tidy(raw);
  const meta = { machine: os.hostname(), os: `${os.type()} ${os.release()} (${os.arch()})`, generatedAt: new Date().toISOString(), count: rows.length };
  const outFile = val('--out');
  const ext = outFile ? path.extname(outFile).toLowerCase() : '';
  const wantJson = has('--json') || ext === '.json';
  const wantCsv = has('--csv') || ext === '.csv';

  const sec = has('--security') ? await securityScan(rows) : null;

  let text;
  if (wantJson) text = JSON.stringify(Object.assign({}, meta, { software: rows }, sec ? { security: sec } : {}), null, 2);
  else if (wantCsv) text = sec ? findingsCsv(sec.findings) : inventoryCsv(rows);
  else if (sec) text = `${meta.machine} | ${meta.os}\n\n${securityText(sec)}${has('--all') ? `\n\nFULL INVENTORY (${rows.length})\n${inventoryTable(rows)}` : ''}`;
  else text = `${meta.machine} | ${meta.os}\n${meta.count} items found\n\n${inventoryTable(rows)}`;

  if (outFile) { fs.writeFileSync(outFile, text, 'utf8'); console.log(`Saved ${sec ? 'security report' : `${rows.length} items`} to ${outFile}`); if (sec) console.log(`CRITICAL ${sec.summary.critical} | HIGH ${sec.summary.high} | REVIEW ${sec.summary.review}`); } else console.log(text);
  if (sec && sec.summary.critical > 0) process.exitCode = 2;
}

if (require.main === module) {
  main().catch((err) => { console.error('Failed:', err.message); process.exit(1); });
} else {
  module.exports = { norm, cmpVersions, inRange, evaluateCve, buildGroups, matchConfidence, EOL_RULES };
}
