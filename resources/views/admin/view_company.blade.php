@extends('layouts.app')

@section('title', 'Auditor Review: ' . ($company->registered_name ?? 'Company'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.dashboard') }}" class="text-xs text-slate-500 hover:text-slate-900 flex items-center gap-1.5 font-bold">
            &larr; Back to Auditor Dashboard
        </a>

        <!-- Status Controller -->
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.company.status', $company->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <span class="text-xs font-semibold text-slate-500">Status:</span>
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none shadow-sm">
                    <option value="draft" {{ $company->status == 'draft' ? 'selected' : '' }}>Draft (In Progress)</option>
                    <option value="submitted" {{ $company->status == 'submitted' ? 'selected' : '' }}>Submitted for Review</option>
                    <option value="approved" {{ $company->status == 'approved' ? 'selected' : '' }}>Approved (Compliant)</option>
                    <option value="flagged" {{ $company->status == 'flagged' ? 'selected' : '' }}>Flagged (Action Needed)</option>
                </select>
            </form>

            <form action="{{ route('admin.company.delete', $company->id) }}" method="POST" onsubmit="return confirm('Permanently delete this company and all its data?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 transition" title="Delete Company">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Company Header Card -->
    <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 mb-8 bg-white shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="ai-badge bg-brand-50 text-brand-700 border border-brand-200">Scope Inspection</span>
                    <span class="text-xs text-slate-500 font-mono font-medium">Reg #: {{ $company->company_number ?? 'N/A' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">{{ $company->registered_name ?? $company->user->name }}</h1>
                <p class="text-xs text-slate-500 mt-1">Trading as: {{ $company->trading_name ?? 'Same as registered' }} | Scope: {{ $company->certification_scope }} ({{ $company->certification_level }})</p>
            </div>

            <!-- Risk Badge -->
            <div class="text-right">
                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Audit Evaluation</div>
                <div class="text-xl font-black {{ $audit['risk_flags']['total_risks'] > 0 ? 'text-red-600' : 'text-emerald-600' }} mt-0.5">
                    {{ $audit['status'] }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">{{ $audit['percent_complete'] }}% Workbook Data Populated</div>
            </div>
        </div>
    </div>

    <!-- 9 Tabs Detailed Readout for Admin -->
    <div class="space-y-8">
        
        <!-- Tab 1: Company Profile -->
        <div class="ai-card rounded-3xl p-6 border border-slate-200 bg-white shadow-sm">
            <h2 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center text-xs font-bold border border-brand-200">1</span>
                Company Details
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Legal Structure</span>
                    <span class="font-bold text-slate-800 mt-1 block">{{ $company->legal_structure ?? '-' }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Registered Address</span>
                    <span class="font-semibold text-slate-800 mt-1 block">{{ $company->address_line1 }}, {{ $company->city }} {{ $company->postcode }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Corporate Contact</span>
                    <span class="font-semibold text-slate-800 mt-1 block">{{ $company->email }} | {{ $company->phone ?? '-' }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Headcount / SIC</span>
                    <span class="font-semibold text-slate-800 mt-1 block">{{ $company->headcount }} employees (SIC: {{ $company->sic_code ?? '-' }})</span>
                </div>
            </div>
        </div>

        <!-- Tab 2: Staff -->
        <div class="ai-card rounded-3xl p-6 border border-slate-200 bg-white shadow-sm">
            <h2 class="text-base font-extrabold text-slate-900 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center text-xs font-bold border border-brand-200">2</span>
                    Staff & Scope Inventory ({{ $company->staffMembers->count() }})
                </span>
                <span class="text-xs font-bold text-amber-600">WFH: {{ $audit['wfh_count'] }} | Personal Devices: {{ $audit['personal_device_staff'] }}</span>
            </h2>
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Role & Type</th>
                            <th class="p-3">Works from home?</th>
                            <th class="p-3">Personal Device?</th>
                            <th class="p-3">Admin Rights?</th>
                            <th class="p-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->staffMembers as $staff)
                        <tr>
                            <td class="p-3 font-bold text-slate-900">{{ $staff->full_name }}</td>
                            <td class="p-3">{{ $staff->job_role }} ({{ $staff->type }})</td>
                            <td class="p-3">{{ $staff->works_from_home }}</td>
                            <td class="p-3">{{ $staff->uses_personal_device }}</td>
                            <td class="p-3">{{ $staff->has_admin_rights }}</td>
                            <td class="p-3 text-slate-500">{{ $staff->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-4 text-center text-slate-400">No staff members recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3 & 4: Endpoints (Computers & Mobiles) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="ai-card rounded-3xl p-6 border border-slate-200 bg-white shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center text-xs font-bold border border-brand-200">3</span>
                    Computers (Total Qty: {{ $audit['computer_qty'] }})
                </h2>
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-2.5">Device</th>
                                <th class="p-2.5">Qty</th>
                                <th class="p-2.5">OS & Version</th>
                                <th class="p-2.5">Supported?</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($company->computers as $comp)
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">{{ $comp->make }} {{ $comp->model }}</td>
                                <td class="p-2.5 font-mono font-bold text-brand-700">{{ $comp->quantity }}</td>
                                <td class="p-2.5">{{ $comp->operating_system }} {{ $comp->os_version }}</td>
                                <td class="p-2.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $comp->os_supported == 'Yes' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $comp->os_supported }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-slate-400">No computers listed.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ai-card rounded-3xl p-6 border border-slate-200 bg-white shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center text-xs font-bold border border-brand-200">4</span>
                    Mobiles & Tablets (Total Qty: {{ $audit['mobile_qty'] }})
                </h2>
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-2.5">Device</th>
                                <th class="p-2.5">Qty</th>
                                <th class="p-2.5">OS & Version</th>
                                <th class="p-2.5">Work Email?</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($company->mobileDevices as $mob)
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">{{ $mob->make }} {{ $mob->model }}</td>
                                <td class="p-2.5 font-mono font-bold text-brand-700">{{ $mob->quantity }}</td>
                                <td class="p-2.5">{{ $mob->operating_system }} {{ $mob->os_version }}</td>
                                <td class="p-2.5">{{ $mob->used_for_work_email }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-4 text-center text-slate-400">No mobiles listed.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 5, 6, 7: Servers, Network, Cloud -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Servers -->
            <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-3">5. Servers ({{ $audit['server_qty'] }})</h2>
                @forelse($company->servers as $srv)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-2">
                        <div class="font-bold text-slate-900">{{ $srv->server_name }} ({{ $srv->type }})</div>
                        <div class="text-[11px] text-slate-500">{{ $srv->operating_system }} {{ $srv->os_version }}</div>
                    </div>
                @empty
                    <div class="text-xs text-slate-400">None declared.</div>
                @endforelse
            </div>

            <!-- Network -->
            <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-3">6. Network ({{ $audit['network_count'] }})</h2>
                @forelse($company->networkDevices as $net)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-2">
                        <div class="font-bold text-slate-900">{{ $net->make }} {{ $net->model }}</div>
                        <div class="text-[11px] font-semibold {{ $net->default_password_changed == 'Yes' ? 'text-emerald-600' : 'text-red-600' }}">
                            Default Password Changed: {{ $net->default_password_changed }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-400">None declared.</div>
                @endforelse
            </div>

            <!-- Cloud -->
            <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-3">7. Cloud Services ({{ $audit['cloud_count'] }})</h2>
                @forelse($company->cloudServices as $cld)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-2">
                        <div class="font-bold text-slate-900">{{ $cld->service_name }} ({{ $cld->users_count }} users)</div>
                        <div class="text-[11px] font-semibold {{ $cld->mfa_enabled == 'Yes' ? 'text-emerald-600' : 'text-red-600' }}">
                            MFA Enabled: {{ $cld->mfa_enabled }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-400">None declared.</div>
                @endforelse
            </div>
        </div>

        <!-- Tab 8 & 9: Software & IT Contact (Admin Editable) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="ai-card rounded-3xl p-6 border border-slate-200 bg-white shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900 mb-4">8. Software Applications ({{ $audit['software_count'] }})</h2>
                <div class="space-y-2 text-xs">
                    @forelse($company->softwareItems as $sw)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-slate-900">{{ $sw->software_name }}</span>
                                <span class="text-slate-500 text-[11px] block">{{ $sw->category }} ({{ $sw->vendor ?? 'Generic' }})</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sw->still_supported == 'Yes' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                Supported: {{ $sw->still_supported }}
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400">No software items declared.</div>
                    @endforelse
                </div>
            </div>

            <!-- Tab 9: IT Contact & Governance (Admin Has Power To Modify) -->
            <div class="ai-card rounded-3xl p-6 border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-extrabold text-slate-900">9. IT Contact & Governance (Admin Edit)</h2>
                    <span class="ai-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Admin Control</span>
                </div>
                
                @php $contact = $company->itContact; @endphp

                <form action="{{ route('admin.company.it-contact.update', $company->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Contact Name</label>
                            <input type="text" name="name" value="{{ old('name', $contact->name ?? 'Mohammed Nasar') }}" required
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:border-brand-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Job Role</label>
                            <input type="text" name="job_role" value="{{ old('job_role', $contact->job_role ?? 'CEO & Enterprise Architect') }}"
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-brand-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Contact Email</label>
                            <input type="email" name="email" value="{{ old('email', $contact->email ?? 'info@nexteck.co.uk') }}" required
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:border-brand-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $contact->phone ?? '07879 175585') }}"
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-brand-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Provider Type</label>
                            <select name="provider_type" class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-brand-500 focus:outline-none">
                                <option value="Managed service provider (MSP)" {{ ($contact->provider_type ?? '') == 'Managed service provider (MSP)' ? 'selected' : '' }}>Managed service provider (MSP)</option>
                                <option value="Internal" {{ ($contact->provider_type ?? '') == 'Internal' ? 'selected' : '' }}>Internal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">MSP Organization</label>
                            <input type="text" name="external_provider_name" value="{{ old('external_provider_name', $contact->external_provider_name ?? 'Nexteck Limited (London, UK)') }}"
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-semibold focus:border-brand-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Admin Approver</label>
                            <input type="text" name="approver_admin_access" value="{{ old('approver_admin_access', $contact->approver_admin_access ?? 'Mohammed Nasar (Nexteck Limited)') }}"
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-brand-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Patching Authority</label>
                            <input type="text" name="person_applying_patches" value="{{ old('person_applying_patches', $contact->person_applying_patches ?? 'Mohammed Nasar (Nexteck Limited)') }}"
                                class="w-full px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:border-brand-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit" class="py-2 px-5 rounded-xl font-bold text-xs bg-slate-900 hover:bg-slate-800 text-white transition shadow-sm">
                            Save IT Governance Updates
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
