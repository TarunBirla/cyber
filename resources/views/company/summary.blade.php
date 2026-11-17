@extends('layouts.app')

@section('title', 'Workbook Summary & Completeness Check')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 mb-8 bg-white flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="ai-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Audit Matrix</span>
                <span class="text-xs text-slate-500 font-medium">Cyber Essentials Readiness Verification</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">{{ $company->registered_name ?? 'Company Inventory' }}</h1>
            <p class="text-xs text-slate-500 mt-1">Automatic verification formulas across all 9 tabs. Compliant with UK Information Gathering Workbook format.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('company.wizard', 1) }}" class="py-2.5 px-5 rounded-xl text-xs font-bold bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 transition shadow-sm">
                &larr; Return to Step 1
            </a>
            
            <form action="{{ route('company.submit') }}" method="POST">
                @csrf
                <button type="submit" class="py-3 px-6 rounded-xl font-bold text-xs bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/25 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Submit Workbook to Lead Auditor
                </button>
            </form>
        </div>
    </div>

    <!-- Top Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Completeness</span>
            <div class="text-3xl font-black text-slate-900 mt-2">{{ $audit['percent_complete'] }}%</div>
            <div class="w-full bg-slate-100 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-brand-600 h-2 rounded-full" style="width: {{ $audit['percent_complete'] }}%"></div>
            </div>
        </div>

        <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Endpoints</span>
            <div class="text-3xl font-black text-brand-600 mt-2">{{ $audit['computer_qty'] + $audit['mobile_qty'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $audit['computer_qty'] }} Laptops + {{ $audit['mobile_qty'] }} Mobiles</div>
        </div>

        <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Home-Working Staff</span>
            <div class="text-3xl font-black text-amber-600 mt-2">{{ $audit['wfh_count'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Home routers in scope</div>
        </div>

        <div class="ai-card rounded-3xl p-5 border {{ $audit['risk_flags']['total_risks'] > 0 ? 'border-red-200 bg-red-50/30' : 'border-emerald-200 bg-emerald-50/30' }} shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Critical Risk Flags</span>
            <div class="text-3xl font-black {{ $audit['risk_flags']['total_risks'] > 0 ? 'text-red-600' : 'text-emerald-600' }} mt-2">
                {{ $audit['risk_flags']['total_risks'] }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $audit['risk_flags']['total_risks'] > 0 ? 'Remediation Required to Pass' : 'Ready for Assessment' }}</div>
        </div>
    </div>

    <!-- Risk Flags Warning Box (Excel: Risk flags should be 0) -->
    <div class="ai-card rounded-3xl p-6 sm:p-8 border {{ $audit['risk_flags']['total_risks'] > 0 ? 'border-red-200 bg-white' : 'border-emerald-200 bg-white' }} mb-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center {{ $audit['risk_flags']['total_risks'] > 0 ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">UK Cyber Essentials Risk Checks (Target: 0)</h2>
                    <p class="text-xs text-slate-500">6 core failure conditions are automatically flagged from your asset inventory.</p>
                </div>
            </div>
            <span class="ai-badge {{ $audit['risk_flags']['total_risks'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                {{ $audit['risk_flags']['total_risks'] }} Discrepancies
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                    <tr>
                        <th class="p-3">Risk Flag Item</th>
                        <th class="p-3 text-center">Count</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">What To Do / Remediation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="p-3 font-semibold text-slate-900">Computers with OS not supported (e.g. Windows 10 without ESU)</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['computers_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['computers_unsupported'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $audit['risk_flags']['computers_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">Upgrade operating system or remove out-of-scope devices.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-900">Mobiles / tablets with OS not supported</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['mobiles_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['mobiles_unsupported'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $audit['risk_flags']['mobiles_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">Update software or disallow personal phones accessing work email.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-900">Servers with OS not supported</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['servers_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['servers_unsupported'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $audit['risk_flags']['servers_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">Upgrade server or isolate from internet.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-900">Cloud services without MFA (Multi-Factor Authentication)</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['cloud_no_mfa'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['cloud_no_mfa'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $audit['risk_flags']['cloud_no_mfa'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">Enable multi-factor authentication (MFA) immediately across all admin accounts.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-900">Routers with default password unchanged</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['router_default_password'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['router_default_password'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $audit['risk_flags']['router_default_password'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">Change factory default router administrator passwords.</td>
                    </tr>
                    <tr>
                        <td class="p-3 font-semibold text-slate-900">Installed software not supported</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $audit['risk_flags']['software_unsupported'] }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $audit['risk_flags']['software_unsupported'] > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $audit['risk_flags']['software_unsupported'] > 0 ? 'FAIL RISK' : 'OK' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">Update application to supported version or uninstall legacy software.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inventory Counts & Governance Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Inventory Breakdown -->
        <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 bg-white shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Asset Inventory Matrix</span>
                <span class="text-xs text-slate-400 font-normal">Excel Formula Equivalent</span>
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Staff listed (people)</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['staff_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $audit['staff_count'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $audit['staff_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Computers (Laptops & Desktops)</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['computer_qty'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $audit['computer_qty'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $audit['computer_qty'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Mobiles & Tablets</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['mobile_qty'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $audit['mobile_qty'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $audit['mobile_qty'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Servers declared</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['server_qty'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600 font-medium">
                            {{ $audit['server_qty'] > 0 ? 'Declared' : 'None Declared' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Network Devices (Firewalls/Routers)</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['network_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $audit['network_count'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $audit['network_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Cloud Services & SaaS</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['cloud_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $audit['cloud_count'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $audit['cloud_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-700 font-medium">Software Applications</span>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-slate-900">{{ $audit['software_count'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $audit['software_count'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $audit['software_count'] > 0 ? 'Listed' : 'MISSING' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Governance Lead (Nexteck Limited) Card -->
        <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 bg-white shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Governance & Verification Check</h3>
                
                <div class="space-y-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900">Tab 1: Company Profile Ready</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $audit['company_details_ready'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ $audit['company_details_ready'] ? 'COMPLETED' : 'INCOMPLETE' }}
                            </span>
                        </div>
                        <p class="text-slate-500 text-[11px]">Registered Name, Companies House Reg No, and Registered Address.</p>
                        <a href="{{ route('company.wizard', 1) }}" class="text-brand-600 hover:underline text-[11px] font-bold mt-2 inline-block">Edit Details &rarr;</a>
                    </div>

                    <div class="p-4 rounded-2xl bg-gradient-to-r from-brand-50 to-emerald-50 border border-brand-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900">Tab 9: IT Contact & Governance Lead</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                PRE-VERIFIED (MSP)
                            </span>
                        </div>
                        <div class="text-slate-700 font-semibold mt-1">Mohammed Nasar &bull; Nexteck Limited</div>
                        <p class="text-slate-500 text-[11px] mt-0.5">CEO & Enterprise Architect &bull; London, UK &bull; 07879 175585 &bull; info@nexteck.co.uk</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 mt-6">
                <div class="text-[11px] text-slate-500 font-medium">
                    <strong class="text-slate-700">Audit Assurance:</strong> Certified information gathering workbook designed for UK Cyber Essentials and Cyber Essentials Plus assessments.
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
