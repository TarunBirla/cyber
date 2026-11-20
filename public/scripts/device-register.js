#!/usr/bin/env node
'use strict';
/**
 * device-register.js
 * Collects the details of THIS Mac or Windows computer for a Cyber Security
 * asset register. Needs only Node.js 14+. No npm packages. Read-only: it changes nothing.
 *
 * Columns produced: Device type, Make, Model, Quantity, Operating system, OS version,
 * Location, Assigned to / user(s), OS still supported?, Auto-updates on?,
 * Firewall & anti-malware on?  (plus hostname, serial, support end date, last patch, issues)
 *
 * USAGE
 *   node device-register.js                          show this device
 *   node device-register.js --ask                    prompt for Location and Assigned to
 *   node device-register.js --location "Home office" --assigned-to "Jane Smith"
 *   node device-register.js --csv                    CSV to the terminal
 *   node device-register.js --json                   JSON to the terminal
 *   node device-register.js --out mylaptop.json      save (.json or .csv)
 *   node device-register.js --append register.csv    add this device as a row to a shared CSV
 *
 * COMBINE SEVERAL COMPUTERS (run the script on each, saving .json, then):
 *   node device-register.js --merge a.json b.json c.json --out register.csv
 *   (identical devices are grouped and Quantity is added up; add --no-group for one row each)
 */
const { execFileSync } = require('child_process');
const os = require('os');
const fs = require('fs');
const path = require('path');
const readline = require('readline');

const args = process.argv.slice(2);
const has = (f) => args.includes(f);
const val = (f) => { const i = args.indexOf(f); return i > -1 && args[i + 1] && !args[i + 1].startsWith('--') ? args[i + 1] : null; };
const toArray = (x) => (Array.isArray(x) ? x : x == null ? [] : [x]);

/* Support tables were verified on this date. Re-check them at least every 6 months. */
const LIFECYCLE_CHECKED = '2026-11-17';
const COLUMNS = ['Device type', 'Make', 'Model', 'Quantity', 'Operating system', 'OS version', 'Location', 'Assigned to / user(s)', 'OS still supported?', 'Auto-updates on?', 'Firewall & anti-malware on?', 'Hostname', 'Serial number', 'OS support ends', 'Last patch installed', 'Issues / notes', 'Scanned on'];

function run(cmd, a) {
  return execFileSync(cmd, a, { encoding: 'utf8', maxBuffer: 64 * 1024 * 1024, stdio: ['ignore', 'pipe', 'ignore'] });
}
const tryRun = (cmd, a) => { try { return run(cmd, a); } catch (e) { return ''; } };

/* ================================================================ SUPPORT TABLES */
// Windows 11: [build, name, end of updates Home/Pro, end of updates Enterprise/Education]
const WIN11 = [
  { build: 26200, name: 'Windows 11 25H2', homePro: '2027-10-12', ent: '2028-10-10' },
  { build: 26100, name: 'Windows 11 24H2', homePro: '2026-10-13', ent: '2027-10-12' },
  { build: 22631, name: 'Windows 11 23H2', homePro: '2025-11-11', ent: '2026-11-10' },
  { build: 22621, name: 'Windows 11 22H2', homePro: '2024-10-08', ent: '2025-10-14' },
  { build: 22000, name: 'Windows 11 21H2', homePro: '2023-10-10', ent: '2024-10-08' },
];
// macOS: Apple publishes no end dates; it patches the current release and the two before it.
const MAC_SUPPORTED = [27, 26, 15];
const MAC_NAMES = { 10: 'Legacy macOS', 11: 'Big Sur', 12: 'Monterey', 13: 'Ventura', 14: 'Sonoma', 15: 'Sequoia', 26: 'Tahoe', 27: 'Golden Gate' };

function fromDate(date, now, label) {
  const ok = now.getTime() <= Date.parse(`${date}T23:59:59Z`);
  return { supported: ok ? 'Yes' : 'No', ends: date, note: ok ? '' : `${label} stopped receiving security updates on ${date}.` };
}

function winSupport(build, editionId, caption, now) {
  const b = parseInt(build, 10) || 0;
  const ed = String(editionId || '');
  if (/server/i.test(caption || '')) return { supported: 'Unknown', ends: '', note: 'Server edition: check the Microsoft Lifecycle page.' };
  if (/^(EnterpriseS|IoTEnterpriseS)$/i.test(ed)) {
    const iot = /^IoT/i.test(ed);
    const ltsc = { 14393: '2026-10-13', 17763: '2029-01-09', 19044: iot ? '2032-01-13' : '2027-01-12', 26100: iot ? '2034-10-10' : '2029-10-09' };
    if (ltsc[b]) return fromDate(ltsc[b], now, 'This LTSC edition');
  }
  if (b >= 22000) {
    const isEnt = /^(Enterprise|Education|IoTEnterprise)/i.test(ed);
    const exact = WIN11.find((r) => r.build === b);
    if (exact) return fromDate(isEnt ? exact.ent : exact.homePro, now, exact.name);
    if (b > 26200) return { supported: 'Yes', ends: '', note: 'Windows build is newer than this script knows. Confirm on the Microsoft Lifecycle page.' };
    const near = WIN11.find((r) => b >= r.build);
    if (near) return Object.assign(fromDate(isEnt ? near.ent : near.homePro, now, near.name), { note: `Approximate: build ${b} is treated like ${near.name}.` });
  }
  if (b >= 10240) return { supported: 'No', ends: '2025-10-14', note: 'Windows 10 reached end of support on 14 Oct 2025. It counts as supported only if enrolled in Extended Security Updates (ESU). Upgrade to Windows 11 or confirm ESU enrolment.' };
  return { supported: 'No', ends: '', note: 'This Windows version is no longer supported.' };
}

function macSupport(version) {
  const parts = String(version || '').split('.').map((n) => parseInt(n, 10));
  const major = parts[0] === 10 ? 10 : parts[0];
  const latest = Math.max.apply(null, MAC_SUPPORTED);
  if (major > latest) return { supported: 'Yes', ends: '', note: 'macOS is newer than this script knows. Confirm on Apple’s security releases page.', major };
  if (MAC_SUPPORTED.includes(major)) return { supported: 'Yes', ends: 'Not published by Apple', note: major === MAC_SUPPORTED[MAC_SUPPORTED.length - 1] ? 'Oldest macOS still receiving fixes. Plan an upgrade.' : '', major };
  return { supported: 'No', ends: 'Not published by Apple', note: `macOS ${major} no longer receives security updates.`, major };
}

/* ================================================================ WINDOWS */
function powershell(script) {
  const prefix = '[Console]::OutputEncoding=[System.Text.Encoding]::UTF8;';
  const enc = Buffer.from(prefix + script, 'utf16le').toString('base64');
  const a = ['-NoProfile', '-NonInteractive', '-ExecutionPolicy', 'Bypass', '-EncodedCommand', enc];
  let out;
  try { out = run('powershell.exe', a); } catch (e) { out = run('pwsh', a); }
  return JSON.parse(out.trim());
}

function collectWindows() {
  return powershell(String.raw`
$ErrorActionPreference='SilentlyContinue'
$o=[ordered]@{}
$cs=Get-CimInstance Win32_ComputerSystem
$csp=Get-CimInstance Win32_ComputerSystemProduct
$bios=Get-CimInstance Win32_BIOS
$enc=Get-CimInstance Win32_SystemEnclosure
$osr=Get-CimInstance Win32_OperatingSystem
$cv=Get-ItemProperty 'HKLM:\SOFTWARE\Microsoft\Windows NT\CurrentVersion'
$o.hostname=$env:COMPUTERNAME
$o.make=$cs.Manufacturer
$o.model=$cs.Model
$o.modelFriendly=$csp.Version
$o.serial=$bios.SerialNumber
$o.chassis=@($enc.ChassisTypes)
$o.pcSystemType=$cs.PCSystemType
$o.hasBattery=[bool](Get-CimInstance Win32_Battery)
$o.user=$cs.UserName
$o.osCaption=$osr.Caption
$o.build=[string]$cv.CurrentBuild
$o.ubr=$cv.UBR
$o.displayVersion=$cv.DisplayVersion
$o.editionId=$cv.EditionID
$au=Get-ItemProperty 'HKLM:\SOFTWARE\Policies\Microsoft\Windows\WindowsUpdate\AU'
$o.noAutoUpdate=$au.NoAutoUpdate
$o.auOptions=$au.AUOptions
$o.wuStartMode=(Get-CimInstance Win32_Service -Filter "Name='wuauserv'").StartMode
$o.pauseExpiry=(Get-ItemProperty 'HKLM:\SOFTWARE\Microsoft\WindowsUpdate\UX\Settings').PauseUpdatesExpiryTime
$h=Get-HotFix | Where-Object { $_.InstalledOn } | Sort-Object InstalledOn -Descending | Select-Object -First 1
if($h){ $o.lastPatch=($h.HotFixID + ' on ' + $h.InstalledOn.ToString('yyyy-MM-dd')) }
$o.firewall=@(Get-NetFirewallProfile | ForEach-Object { @{ name=[string]$_.Name; enabled=([string]$_.Enabled -eq 'True') } })
$o.av=@(Get-CimInstance -Namespace root/SecurityCenter2 -ClassName AntiVirusProduct | ForEach-Object { @{ name=[string]$_.displayName; state=[int]$_.productState } })
$mp=Get-MpComputerStatus
if($mp){ $o.defender=@{ enabled=[bool]$mp.AntivirusEnabled; realtime=[bool]$mp.RealTimeProtectionEnabled; sigAge=[int]$mp.AntivirusSignatureAge } }
$o | ConvertTo-Json -Depth 5 -Compress`);
}

const avState = (ps) => { const hex = ('000000' + Number(ps).toString(16)).slice(-6); const rt = hex.substr(2, 2); const sig = hex.substr(4, 2); return { enabled: rt === '10' || rt === '11', upToDate: sig === '00' }; };

function buildWindowsRecord(r, now) {
  const issues = [];
  const build = parseInt(r.build, 10) || 0;
  const chassis = toArray(r.chassis).map(Number);
  const laptop = [8, 9, 10, 11, 12, 14, 18, 21, 30, 31, 32];
  const desktop = [3, 4, 5, 6, 7, 13, 15, 16, 24, 35, 36];
  let type = 'Other';
  if (/virtual|vmware|kvm|qemu|virtualbox/i.test(`${r.model} ${r.make}`)) type = 'Virtual machine';
  else if (chassis.some((c) => laptop.includes(c)) || r.pcSystemType === 2 || r.hasBattery) type = 'Laptop';
  else if (chassis.some((c) => desktop.includes(c))) type = 'Desktop';

  const model = /lenovo/i.test(r.make || '') && r.modelFriendly ? r.modelFriendly : r.model;
  const osName = (build >= 22000 ? 'Windows 11' : build >= 10240 ? 'Windows 10' : String(r.osCaption || 'Windows').replace(/^Microsoft\s+/, ''))
    + (r.osCaption ? ` (${String(r.osCaption).replace(/^Microsoft\s+Windows\s+(10|11)\s*/i, '').trim() || r.editionId})` : '');
  const osVersion = `${r.displayVersion ? `${r.displayVersion} ` : ''}(build ${r.build}${r.ubr != null ? `.${r.ubr}` : ''})`;
  const sup = winSupport(build, r.editionId, r.osCaption, now);
  if (sup.supported !== 'Yes' && sup.note) issues.push(sup.note);

  // auto updates
  let auto = 'Yes';
  const notes = [];
  if (r.noAutoUpdate === 1) { auto = 'No'; notes.push('Group Policy disables automatic updates'); }
  else if (r.auOptions === 1 || r.auOptions === 2) { auto = 'No'; notes.push('Updates are set to notify only, not install automatically'); }
  else if (/disabled/i.test(r.wuStartMode || '')) { auto = 'No'; notes.push('Windows Update service is disabled'); }
  else if (r.pauseExpiry && Date.parse(r.pauseExpiry) > now.getTime()) { auto = 'Partial'; notes.push(`Updates are paused until ${String(r.pauseExpiry).slice(0, 10)}`); }
  notes.forEach((n) => issues.push(n));

  // firewall
  const fw = toArray(r.firewall);
  const fwOn = fw.length ? fw.every((p) => p.enabled) : null;
  if (fw.length && !fwOn) issues.push(`Windows Firewall is off for: ${fw.filter((p) => !p.enabled).map((p) => p.name).join(', ')}`);
  // anti-malware
  const av = toArray(r.av).map((p) => Object.assign({ name: p.name }, avState(p.state)));
  let amOn = av.some((p) => p.enabled);
  let amNote = av.filter((p) => p.enabled).map((p) => p.name).join(', ');
  if (!av.length && r.defender) { amOn = !!(r.defender.enabled && r.defender.realtime); amNote = 'Microsoft Defender'; }
  const stale = av.filter((p) => p.enabled && !p.upToDate).map((p) => p.name);
  if (stale.length) issues.push(`Anti-malware signatures out of date: ${stale.join(', ')}`);
  if (!av.length && !r.defender) amOn = null;
  else if (!amOn) issues.push('No active anti-malware product detected');
  const fwam = fwOn === null || amOn === null ? 'Unknown' : fwOn && amOn && !stale.length ? 'Yes' : !fwOn && !amOn ? 'No' : 'Partial';

  return {
    'Device type': type, Make: r.make || '', Model: model || '', Quantity: 1,
    'Operating system': osName, 'OS version': osVersion,
    'Assigned to / user(s)': r.user || '', 'OS still supported?': sup.supported, 'Auto-updates on?': auto,
    'Firewall & anti-malware on?': fwam, Hostname: r.hostname || '', 'Serial number': r.serial || '',
    'OS support ends': sup.ends, 'Last patch installed': r.lastPatch || 'Not detected', 'Issues / notes': issues,
    _detail: { firewall: fw, antimalware: amNote },
  };
}

/* ================================================================ MACOS */
function defaultsRead(file, key) {
  const v = tryRun('defaults', ['read', file, key]).trim();
  return v === '' ? null : v;
}

function collectMac() {
  const hwAll = JSON.parse(run('system_profiler', ['SPHardwareDataType', '-json']));
  const hw = (hwAll.SPHardwareDataType || [{}])[0];
  const swu = {};
  ['AutomaticCheckEnabled', 'AutomaticDownload', 'AutomaticallyInstallMacOSUpdates', 'CriticalUpdateInstall', 'ConfigDataInstall'].forEach((k) => {
    swu[k] = defaultsRead('/Library/Managed Preferences/com.apple.SoftwareUpdate', k) || defaultsRead('/Library/Preferences/com.apple.SoftwareUpdate', k);
  });
  const fwText = tryRun('/usr/libexec/ApplicationFirewall/socketfilterfw', ['--getglobalstate']);
  const fwMatch = fwText.match(/State\s*=\s*(\d)/);
  const avNames = ['Sophos', 'CrowdStrike', 'Falcon', 'SentinelOne', 'Malwarebytes', 'Bitdefender', 'ESET', 'Microsoft Defender', 'Norton', 'Avast', 'AVG', 'Kaspersky', 'McAfee', 'Jamf Protect', 'Intego', 'Trend Micro', 'ClamXav', 'Webroot', 'Cylance', 'Carbon Black'];
  let apps = [];
  try { apps = fs.readdirSync('/Applications'); } catch (e) { /* ignore */ }
  const userName = os.userInfo().username;
  return {
    version: run('sw_vers', ['-productVersion']).trim(), build: run('sw_vers', ['-buildVersion']).trim(),
    hw, swu, hostname: tryRun('scutil', ['--get', 'ComputerName']).trim() || os.hostname(),
    fullName: tryRun('id', ['-F']).trim(), userName,
    firewallState: fwMatch ? parseInt(fwMatch[1], 10) : null,
    gatekeeper: /enabled/i.test(tryRun('spctl', ['--status'])) ? true : /disabled/i.test(tryRun('spctl', ['--status'])) ? false : null,
    sip: /enabled/i.test(tryRun('csrutil', ['status'])),
    thirdPartyAv: apps.filter((a) => a.endsWith('.app') && avNames.some((n) => a.toLowerCase().includes(n.toLowerCase()))).map((a) => a.replace(/\.app$/, '')),
  };
}

function buildMacRecord(r, now) {
  const issues = [];
  const hw = r.hw || {};
  const name = hw.machine_name || '';
  let type = 'Other';
  if (/macbook/i.test(name)) type = 'Laptop';
  else if (/imac|mac mini|mac studio|mac pro/i.test(name)) type = 'Desktop';
  const sup = macSupport(r.version);
  if (sup.supported !== 'Yes' && sup.note) issues.push(sup.note);
  else if (sup.note) issues.push(sup.note);
  const chip = hw.chip_type || hw.cpu_type || '';
  if (/intel/i.test(chip) && sup.major <= 26) issues.push('Intel Mac: cannot run macOS 27, so macOS 26 and 15 are its final versions. Plan hardware replacement.');

  const s = r.swu || {};
  const on = (v) => v === '1' || v === 'true'; const off = (v) => v === '0' || v === 'false';
  let auto;
  if (off(s.AutomaticCheckEnabled)) { auto = 'No'; issues.push('Automatic update checking is off'); }
  else if (on(s.AutomaticallyInstallMacOSUpdates)) auto = 'Yes';
  else if (off(s.AutomaticallyInstallMacOSUpdates)) {
    if (on(s.CriticalUpdateInstall) || on(s.ConfigDataInstall)) { auto = 'Partial'; issues.push('Only security responses and system files install automatically; macOS updates do not'); } else { auto = 'No'; issues.push('Automatic macOS updates are off'); }
  } else { auto = 'Unknown'; issues.push('Auto-update setting not set explicitly. Check System Settings > General > Software Update > Automatic updates (i).'); }

  const fwOn = r.firewallState == null ? null : r.firewallState !== 0;
  if (fwOn === false) issues.push('macOS firewall is off');
  const thirdParty = toArray(r.thirdPartyAv);
  let amOn = r.gatekeeper === null ? null : !!r.gatekeeper || thirdParty.length > 0;
  if (r.gatekeeper === false && !thirdParty.length) issues.push('Gatekeeper is off and no third-party anti-malware found');
  if (off(s.ConfigDataInstall)) issues.push('XProtect/system data updates are turned off');
  const fwam = fwOn === null || amOn === null ? 'Unknown' : fwOn && amOn && !off(s.ConfigDataInstall) ? 'Yes' : !fwOn && !amOn ? 'No' : 'Partial';

  const osName = `macOS ${sup.major} ${MAC_NAMES[sup.major] || ''}`.trim();
  const user = r.fullName && r.fullName !== r.userName ? `${r.fullName} (${r.userName})` : r.userName;
  return {
    'Device type': type, Make: 'Apple', Model: `${name}${hw.machine_model ? ` (${hw.machine_model})` : ''}${chip ? `, ${chip}` : ''}`, Quantity: 1,
    'Operating system': osName, 'OS version': `${r.version} (build ${r.build})`,
    'Assigned to / user(s)': user, 'OS still supported?': sup.supported, 'Auto-updates on?': auto,
    'Firewall & anti-malware on?': fwam, Hostname: r.hostname, 'Serial number': hw.serial_number || '',
    'OS support ends': sup.ends, 'Last patch installed': 'See About This Mac / Software Update history', 'Issues / notes': issues,
    _detail: { firewall: fwOn, antimalware: thirdParty.length ? thirdParty.join(', ') : 'XProtect + Gatekeeper (built in)' },
  };
}

/* ================================================================ OUTPUT */
function finalise(rec, now) {
  rec.Location = val('--location') || rec.Location || '';
  rec['Assigned to / user(s)'] = val('--assigned-to') || rec['Assigned to / user(s)'];
  rec['Scanned on'] = now.toISOString().slice(0, 10);
  const stale = (now - new Date(LIFECYCLE_CHECKED)) / 86400000 > 183;
  if (stale) rec['Issues / notes'].push(`Support tables were last verified on ${LIFECYCLE_CHECKED}; check the Microsoft and Apple lifecycle pages.`);
  return rec;
}

const csvCell = (v) => `"${String(Array.isArray(v) ? v.join('; ') : v == null ? '' : v).replace(/"/g, '""')}"`;
const csvRow = (rec) => COLUMNS.map((c) => csvCell(rec[c])).join(',');
const csvHeader = () => COLUMNS.map(csvCell).join(',');
const strip = (rec) => { const o = {}; COLUMNS.forEach((c) => { o[c] = rec[c]; }); return o; };

function printDevice(rec) {
  console.log('\nCyber Security DEVICE REGISTER ENTRY\n');
  COLUMNS.filter((c) => c !== 'Issues / notes').forEach((c) => console.log(`${c.padEnd(30)} ${Array.isArray(rec[c]) ? rec[c].join('; ') : rec[c]}`));
  const d = rec._detail || {};
  if (d.antimalware) console.log(`${'Anti-malware detail'.padEnd(30)} ${d.antimalware}`);
  if (Array.isArray(d.firewall) && d.firewall.length) console.log(`${'Firewall detail'.padEnd(30)} ${d.firewall.map((p) => `${p.name}: ${p.enabled ? 'on' : 'OFF'}`).join(', ')}`);
  console.log('');
  if (rec['Issues / notes'].length) { console.log('ISSUES TO FIX / CHECK'); rec['Issues / notes'].forEach((i) => console.log(`  - ${i}`)); } else console.log('No issues detected by these checks.');
  console.log('\nNote: this is a snapshot of settings, not proof of compliance. Keep screenshots as evidence.');
}

function ask(q) {
  const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
  return new Promise((res) => rl.question(q, (a) => { rl.close(); res(a.trim()); }));
}

/* ================================================================ MERGE */
function mergeFiles(files, group) {
  const rows = [];
  files.forEach((f) => {
    const j = JSON.parse(fs.readFileSync(f, 'utf8'));
    toArray(j.devices || j.device || j).forEach((d) => rows.push(d));
  });
  if (!group) return rows;
  const keyCols = ['Device type', 'Make', 'Model', 'Operating system', 'OS version', 'OS still supported?', 'Auto-updates on?', 'Firewall & anti-malware on?'];
  const map = new Map();
  rows.forEach((d) => {
    const k = keyCols.map((c) => d[c]).join('|');
    if (!map.has(k)) map.set(k, Object.assign({}, d, { Quantity: 0, _loc: new Set(), _who: new Set(), _host: new Set(), _issues: new Set() }));
    const g = map.get(k);
    g.Quantity += Number(d.Quantity) || 1;
    if (d.Location) g._loc.add(d.Location);
    if (d['Assigned to / user(s)']) g._who.add(d['Assigned to / user(s)']);
    if (d.Hostname) g._host.add(d.Hostname);
    toArray(d['Issues / notes']).forEach((i) => g._issues.add(i));
  });
  return [...map.values()].map((g) => Object.assign({}, g, {
    Location: [...g._loc].join('; '), 'Assigned to / user(s)': [...g._who].join('; '), Hostname: [...g._host].join('; '),
    'Serial number': '', 'Issues / notes': [...g._issues],
  }));
}

/* ================================================================ MAIN */
async function main() {
  if (has('--help') || has('-h')) {
    const src = fs.readFileSync(__filename, 'utf8');
    console.log(src.slice(src.indexOf('/**') + 3, src.indexOf('*/')).split('\n').map((l) => l.replace(/^ \* ?/, '')).join('\n'));
    return;
  }
  const now = process.env.FAKE_NOW ? new Date(process.env.FAKE_NOW) : new Date();
  const outFile = val('--out');

  if (has('--merge')) {
    const i = args.indexOf('--merge');
    const files = [];
    for (k = i + 1; k < args.length && !args[k].startsWith('--'); k++) files.push(args[k]);
    if (!files.length) throw new Error('Give at least one .json file after --merge');
    const rows = mergeFiles(files, !has('--no-group'));
    const total = rows.reduce((n, r) => n + (Number(r.Quantity) || 1), 0);
    const text = outFile && outFile.toLowerCase().endsWith('.json') ? JSON.stringify({ devices: rows.map(strip) }, null, 2) : [csvHeader()].concat(rows.map(csvRow)).join('\n');
    if (outFile) { fs.writeFileSync(outFile, (outFile.toLowerCase().endsWith('.csv') ? '\uFEFF' : '') + text, 'utf8'); console.log(`Saved ${rows.length} line(s) covering ${total} device(s) to ${outFile}`); } else console.log(text);
    return;
  }

  if (has('--ask')) {
    if (!val('--location')) rec.Location = await ask('Location (e.g. Home office, Harrow office): ');
    if (!val('--assigned-to')) { const who = await ask(`Assigned to / user(s) [${rec['Assigned to / user(s)']}]: `); if (who) rec['Assigned to / user(s)'] = who; }
  }

  let rec;
  if (process.platform === 'win32') rec = buildWindowsRecord(collectWindows(), now);
  else if (process.platform === 'darwin') rec = buildMacRecord(collectMac(), now);
  else throw new Error(`This script supports Windows and macOS only (found ${process.platform}).`);

  finalise(rec, now);

  const wantJson = has('--json') || (outFile && outFile.toLowerCase().endsWith('.json'));
  const wantCsv = has('--csv') || (outFile && outFile.toLowerCase().endsWith('.csv'));
  if (val('--append')) {
    const f = val('--append');
    const exists = fs.existsSync(f);
    fs.appendFileSync(f, (exists ? '' : `\uFEFF${csvHeader()}\n`) + csvRow(rec) + '\n', 'utf8');
    console.log(`Added ${rec.Hostname} to ${f}`);
    return;
  }
  if (wantJson || wantCsv) {
    const text = wantJson ? JSON.stringify({ generatedAt: now.toISOString(), device: strip(rec) }, null, 2) : `${csvHeader()}\n${csvRow(rec)}`;
    if (outFile) { fs.writeFileSync(outFile, (wantCsv ? '\uFEFF' : '') + text, 'utf8'); console.log(`Saved to ${outFile}`); } else console.log(text);
  } else printDevice(rec);
}

if (require.main === module) {
  main().catch((e) => { console.error('Failed:', e.message); process.exit(1); });
} else {
  module.exports = { winSupport, macSupport, buildWindowsRecord, buildMacRecord, avState, mergeFiles, finalise, COLUMNS };
}
