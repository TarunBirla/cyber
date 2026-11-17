@extends('layouts.app')

@section('title', 'Workbook Summary & Completeness Check')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="ai-glass rounded-3xl p-6 sm:p-8 border border-slate-800 mb-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 ai-glow-cyan">
        <div>
            <div class="flex items-center gap-3">
                <span class="ai-badge bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Audit Matrix</span>
                <span class="text-xs text-slate-400">Cyber Essentials Readiness Check</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">{{ $company->registered_name ?? 'Company Inventory' }}</h1>
            <p class="text-xs text-slate-400 mt-1">Automatic verification formulas across all 9 tabs. Compliant with UK Information Gathering Workbook format.</p>
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('company.wizard', 1) }}" class="py-2.5 px-5 rounded-xl text-xs font-semibold bg-slate-900 border border-slate-700 text-slate-300 hover:text-white transition">
                &larr; Return to Step 1
            </a>
            
            <form action="{{ route('company.submit') }}" method="POST">
                @csrf
                <button type="submit" class="py-3 px-6 rounded-xl font-bold text-xs bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 hover:opacity-95 shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Submit Workbook to Auditor
                </button>
            </form>
        </div>
    </div>

    <!-- Top Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="ai-glass rounded-2xl p-5 border border-slate-800">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Completeness</span>
            <div class="text-3xl font-extrabold text-white mt-2">{{ $audit['percent_complete'] }}%</div>
            <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-cyan-400 h-1.5 rounded-full" style="width: {{ $audit['percent_complete'] }}%"></div>
            </div>
        </div>

        <div class="ai-glass rounded-2xl p-5 border border-slate-800">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Endpoints</span>
            <div class="text-3xl font-extrabold text-cyan-400 mt-2">{{ $audit['computer_qty'] + $audit['mobile_qty'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $audit['computer_qty'] }} Laptops + {{ $audit['mobile_qty'] }} Mobiles</div>
        </div>

        <div class="ai-glass rounded-2xl p-5 border border-slate-800">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Home-Working Staff</span>
            <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $audit['wfh_count'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Home routers in scope</div>
        </div>

        <div class="ai-glass rounded-2xl p-5 border border-slate-800 {{ $audit['risk_flags']['total_risks'] > 0 ? 'border-red-500/40' : 'border-emerald-500/40' }}">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Critical Risk Flags</span>
            <div class="text-3xl font-extrabold {{ $audit['risk_flags']['total_risks'] > 0 ? 'text-red-400' : 'text-emerald-400' }} mt-2">
                {{ $audit['risk_flags']['total_risks'] }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $audit['risk_flags']['total_risks'] > 0 ? 'Action Required to Pass' : 'Ready for Assessment' }}</div>
        </div>
    </div>

    <!-- Risk Flags Warning Box (Excel: Risk flags should be 0) -->
    <div class="ai-glass rounded-2xl p-6 border border-slate-800 mb-8 {{ $audit['risk_flags']['total_risks'] > 0 ? 'ai-glow-red border-red-500/30' : 'border-emerald-500/30' }}">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $audit['risk_flags']['total_risks'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">UK Cyber Essentials Risk Checks (Target: 0)</h2>
                    <p class="text-xs text-slate-400">These 6 core failure conditions are automatically flagged from your asset inventory.</p>
                </div>
            </div>
            <span class="ai-badge {{ $audit['risk_flags']['total_risks'] > 0 ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' }}">
                {{ $audit['risk_flags']['total_risks'] }} Discrepancies
            </span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-3">Risk Flag Item</th>
                        <th class="p-3 text-center">Count</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">What To Do / Remediation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <tr>
                        <td class="p-3 font-medium text-slate-200">Computers with OS not supported (e.g. Win 10 without ESU)</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['computers_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['computers_unsupported'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $audit['risk_flags']['computers_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-400">Upgrade operating system or remove out-of-scope devices.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium text-slate-200">Mobiles / tablets with OS not supported</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['mobiles_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['mobiles_unsupported'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $audit['risk_flags']['mobiles_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-400">Update software or disallow personal phones accessing work email.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium text-slate-200">Servers with OS not supported</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['servers_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['servers_unsupported'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $audit['risk_flags']['servers_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-400">Upgrade server or isolate from internet.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium text-slate-200">Cloud services without MFA (Multi-Factor Authentication)</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['cloud_no_mfa'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['cloud_no_mfa'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $audit['risk_flags']['cloud_no_mfa'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-400">Enable multi-factor authentication (MFA) immediately across all admin accounts.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium text-slate-200">Routers with default password unchanged</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['router_default_password'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['router_default_password'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $audit['risk_flags']['router_default_password'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-400">Change factory default router administrator passwords.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-medium text-slate-200">Installed software not supported</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['software_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['software_unsupported'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $audit['risk_flags']['software_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-400">Update application to supported version or uninstall legacy software.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inventory Counts & Completeness Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Inventory Breakdown -->
        <div class="ai-glass rounded-2xl p-6 border border-slate-800">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Asset Inventory Matrix</span>
                <span class="text-xs text-slate-400 font-normal">Excel Formula Equivalent</span>
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Staff listed (people)</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['staff_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $audit['staff_count'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                            {{ $audit['staff_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Computers (Laptops & Desktops)</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['computer_qty'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $audit['computer_qty'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                            {{ $audit['computer_qty'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Mobiles & Tablets</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['mobile_qty'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $audit['mobile_qty'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                            {{ $audit['mobile_qty'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Servers declared</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['server_qty'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-400">
                            {{ $audit['server_qty'] > 0 ? 'Declared' : 'None Declared' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Network Devices (Firewalls/Routers)</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['network_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $audit['network_count'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                            {{ $audit['network_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Cloud Services & SaaS</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['cloud_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $audit['cloud_count'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                            {{ $audit['cloud_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-300">Software Applications</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-white">{{ $audit['software_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $audit['software_count'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                            {{ $audit['software_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Single Entry Sections & Governance -->
        <div class="ai-glass rounded-2xl p-6 border border-slate-800 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Governance & Key Fields Check</h3>
                
                <div class="space-y-4 text-xs">
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-white">Tab 1: Company Details Key Fields</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $audit['company_details_ready'] ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ $audit['company_details_ready'] ? 'COMPLETED' : 'INCOMPLETE' }}
                            </span>
                        </div>
                        <p class="text-slate-400 text-[11px]">Requires Registered Name, Companies House Reg No, and Physical Address.</p>
                        <a href="{{ route('company.wizard', 1) }}" class="text-cyan-400 hover:underline text-[11px] font-semibold mt-2 inline-block">Edit Details &rarr;</a>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-white">Tab 9: IT Contact & Governance Lead</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $audit['it_contact_ready'] ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ $audit['it_contact_ready'] ? 'COMPLETED' : 'INCOMPLETE' }}
                            </span>
                        </div>
                        <p class="text-slate-400 text-[11px]">Requires Designated IT Manager / MSP, Email, and Patching Authority.</p>
                        <a href="{{ route('company.wizard', 9) }}" class="text-cyan-400 hover:underline text-[11px] font-semibold mt-2 inline-block">Edit IT Contact &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 mt-6">
                <div class="text-[11px] text-slate-400">
                    <strong class="text-slate-300">Submission Note:</strong> This workbook gathers information to scope and support your official Cyber Essentials UK certification.
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
