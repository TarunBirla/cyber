@extends('layouts.app')

@section('title', 'Auditor Review: ' . ($company->registered_name ?? 'Company'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 font-semibold">
            &larr; Back to Auditor Dashboard
        </a>

        <!-- Status Controller -->
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.company.status', $company->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <span class="text-xs text-slate-400">Current Status:</span>
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs font-semibold text-white focus:outline-none">
                    <option value="draft" {{ $company->status == 'draft' ? 'selected' : '' }}>Draft (In Progress)</option>
                    <option value="submitted" {{ $company->status == 'submitted' ? 'selected' : '' }}>Submitted for Audit</option>
                    <option value="approved" {{ $company->status == 'approved' ? 'selected' : '' }}>Approved (Pass)</option>
                    <option value="flagged" {{ $company->status == 'flagged' ? 'selected' : '' }}>Flagged (Action Needed)</option>
                </select>
            </form>

            <form action="{{ route('admin.company.delete', $company->id) }}" method="POST" onsubmit="return confirm('Permanently delete this company and all its data?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition" title="Delete Company">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Company Header Card -->
    <div class="ai-glass rounded-3xl p-6 sm:p-8 border border-slate-800 mb-8 ai-glow-cyan">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <span class="ai-badge bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Scope Verification</span>
                    <span class="text-xs text-slate-400 font-mono">Reg #: {{ $company->company_number ?? 'N/A' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">{{ $company->registered_name ?? $company->user->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Trading as: {{ $company->trading_name ?? 'Same as registered' }} | Scope: {{ $company->certification_scope }} ({{ $company->certification_level }})</p>
            </div>

            <!-- Risk Badge -->
            <div class="text-right">
                <div class="text-xs text-slate-400 uppercase tracking-wider">Audit Evaluation</div>
                <div class="text-xl font-bold {{ $audit['risk_flags']['total_risks'] > 0 ? 'text-red-400' : 'text-emerald-400' }} mt-0.5">
                    {{ $audit['status'] }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">{{ $audit['percent_complete'] }}% Workbook Data Populated</div>
            </div>
        </div>
    </div>

    <!-- 9 Tabs Detailed Readout for Admin -->
    <div class="space-y-8">
        
        <!-- Tab 1: Company Profile -->
        <div class="ai-glass rounded-2xl p-6 border border-slate-800">
            <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/20">1</span>
                Company Details
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-xs">
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-400 block text-[10px] uppercase">Legal Structure</span>
                    <span class="font-semibold text-white mt-1 block">{{ $company->legal_structure ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-400 block text-[10px] uppercase">Registered Address</span>
                    <span class="font-semibold text-white mt-1 block">{{ $company->address_line1 }}, {{ $company->city }} {{ $company->postcode }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-400 block text-[10px] uppercase">Corporate Contact</span>
                    <span class="font-semibold text-white mt-1 block">{{ $company->email }} | {{ $company->phone ?? '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <span class="text-slate-400 block text-[10px] uppercase">Headcount / SIC</span>
                    <span class="font-semibold text-white mt-1 block">{{ $company->headcount }} employees (SIC: {{ $company->sic_code ?? '-' }})</span>
                </div>
            </div>
        </div>

        <!-- Tab 2: Staff -->
        <div class="ai-glass rounded-2xl p-6 border border-slate-800">
            <h2 class="text-base font-bold text-white mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/20">2</span>
                    Staff & Scope Inventory ({{ $company->staffMembers->count() }})
                </span>
                <span class="text-xs text-amber-400">WFH: {{ $audit['wfh_count'] }} | Personal Devices: {{ $audit['personal_device_staff'] }}</span>
            </h2>
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900 text-slate-400 uppercase text-[10px]">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Role & Type</th>
                            <th class="p-3">Works from home?</th>
                            <th class="p-3">Personal Device?</th>
                            <th class="p-3">Admin Rights?</th>
                            <th class="p-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->staffMembers as $staff)
                        <tr>
                            <td class="p-3 font-semibold text-white">{{ $staff->full_name }}</td>
                            <td class="p-3">{{ $staff->job_role }} ({{ $staff->type }})</td>
                            <td class="p-3">{{ $staff->works_from_home }}</td>
                            <td class="p-3">{{ $staff->uses_personal_device }}</td>
                            <td class="p-3">{{ $staff->has_admin_rights }}</td>
                            <td class="p-3 text-slate-400">{{ $staff->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-4 text-center text-slate-500">No staff members recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3 & 4: Endpoints (Computers & Mobiles) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="ai-glass rounded-2xl p-6 border border-slate-800">
                <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/20">3</span>
                    Computers (Total Qty: {{ $audit['computer_qty'] }})
                </h2>
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900 text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5">Device</th>
                                <th class="p-2.5">Qty</th>
                                <th class="p-2.5">OS & Version</th>
                                <th class="p-2.5">Supported?</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($company->computers as $comp)
                            <tr>
                                <td class="p-2.5 font-semibold text-white">{{ $comp->make }} {{ $comp->model }}</td>
                                <td class="p-2.5 font-mono text-cyan-400 font-bold">{{ $comp->quantity }}</td>
                                <td class="p-2.5">{{ $comp->operating_system }} {{ $comp->os_version }}</td>
                                <td class="p-2.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ $comp->os_supported == 'Yes' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400 font-bold' }}">
                                        {{ $comp->os_supported }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-slate-500">No computers listed.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ai-glass rounded-2xl p-6 border border-slate-800">
                <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/20">4</span>
                    Mobiles & Tablets (Total Qty: {{ $audit['mobile_qty'] }})
                </h2>
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900 text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5">Device</th>
                                <th class="p-2.5">Qty</th>
                                <th class="p-2.5">OS & Version</th>
                                <th class="p-2.5">Work Email?</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($company->mobileDevices as $mob)
                            <tr>
                                <td class="p-2.5 font-semibold text-white">{{ $mob->make }} {{ $mob->model }}</td>
                                <td class="p-2.5 font-mono text-cyan-400 font-bold">{{ $mob->quantity }}</td>
                                <td class="p-2.5">{{ $mob->operating_system }} {{ $mob->os_version }}</td>
                                <td class="p-2.5">{{ $mob->used_for_work_email }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-slate-500">No mobiles listed.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 5, 6, 7: Servers, Network, Cloud -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Servers -->
            <div class="ai-glass rounded-2xl p-5 border border-slate-800">
                <h2 class="text-sm font-bold text-white mb-3">5. Servers ({{ $audit['server_qty'] }})</h2>
                @forelse($company->servers as $srv)
                    <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-800 text-xs mb-2">
                        <div class="font-bold text-white">{{ $srv->server_name }} ({{ $srv->type }})</div>
                        <div class="text-[11px] text-slate-400">{{ $srv->operating_system }} {{ $srv->os_version }}</div>
                    </div>
                @empty
                    <div class="text-xs text-slate-500">None declared.</div>
                @endforelse
            </div>

            <!-- Network -->
            <div class="ai-glass rounded-2xl p-5 border border-slate-800">
                <h2 class="text-sm font-bold text-white mb-3">6. Network ({{ $audit['network_count'] }})</h2>
                @forelse($company->networkDevices as $net)
                    <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-800 text-xs mb-2">
                        <div class="font-bold text-white">{{ $net->make }} {{ $net->model }}</div>
                        <div class="text-[11px] {{ $net->default_password_changed == 'Yes' ? 'text-emerald-400' : 'text-red-400 font-bold' }}">
                            Default Password Changed: {{ $net->default_password_changed }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-500">None declared.</div>
                @endforelse
            </div>

            <!-- Cloud -->
            <div class="ai-glass rounded-2xl p-5 border border-slate-800">
                <h2 class="text-sm font-bold text-white mb-3">7. Cloud Services ({{ $audit['cloud_count'] }})</h2>
                @forelse($company->cloudServices as $cld)
                    <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-800 text-xs mb-2">
                        <div class="font-bold text-white">{{ $cld->service_name }} ({{ $cld->users_count }} users)</div>
                        <div class="text-[11px] {{ $cld->mfa_enabled == 'Yes' ? 'text-emerald-400' : 'text-red-400 font-bold' }}">
                            MFA Enabled: {{ $cld->mfa_enabled }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-500">None declared.</div>
                @endforelse
            </div>
        </div>

        <!-- Tab 8 & 9: Software & IT Contact -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="ai-glass rounded-2xl p-6 border border-slate-800">
                <h2 class="text-base font-bold text-white mb-4">8. Software Applications ({{ $audit['software_count'] }})</h2>
                <div class="space-y-2 text-xs">
                    @forelse($company->softwareItems as $sw)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-white">{{ $sw->software_name }}</span>
                                <span class="text-slate-400 text-[11px] block">{{ $sw->category }} ({{ $sw->vendor ?? 'Generic' }})</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] {{ $sw->still_supported == 'Yes' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400 font-bold' }}">
                                Supported: {{ $sw->still_supported }}
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500">No software items declared.</div>
                    @endforelse
                </div>
            </div>

            <div class="ai-glass rounded-2xl p-6 border border-slate-800">
                <h2 class="text-base font-bold text-white mb-4">9. IT Contact & Governance</h2>
                @php $contact = $company->itContact; @endphp
                @if($contact)
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase">Contact Name & Role</span>
                            <span class="font-bold text-white block mt-1">{{ $contact->name }} ({{ $contact->job_role ?? '-' }})</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase">Email & Phone</span>
                            <span class="font-bold text-white block mt-1">{{ $contact->email }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase">Provider Model</span>
                            <span class="font-bold text-white block mt-1">{{ $contact->provider_type }} {{ $contact->external_provider_name ? '('.$contact->external_provider_name.')' : '' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase">Patching Authority</span>
                            <span class="font-bold text-white block mt-1">{{ $contact->person_applying_patches ?? '-' }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-500">IT Contact details not yet submitted.</div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
