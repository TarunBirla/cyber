@extends('layouts.app')

@section('title', 'Cyber Essentials Workbook - Step ' . $step)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    
    <!-- Top Header & Completion Header -->
    <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 mb-8 bg-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="ai-badge bg-brand-50 text-brand-700 border border-brand-200">Step {{ $step }} of 9</span>
                <span class="text-xs text-slate-500 font-medium">UK Cyber Essentials Workbook</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1.5">{{ $company->registered_name ?? 'Company Inventory' }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Scoping & Asset Inventory Data Gathering</p>
        </div>

        <!-- Realtime Compliance Snapshot -->
        <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 p-3.5 rounded-2xl">
            <div>
                <div class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Completion Score</div>
                <div class="text-2xl font-black text-brand-600">{{ $audit['percent_complete'] }}%</div>
            </div>
            <div class="w-32 bg-slate-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-600 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: {{ $audit['percent_complete'] }}%"></div>
            </div>
            <a href="{{ route('company.summary') }}" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-white text-slate-800 border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition shadow-sm flex items-center gap-1.5">
                <span>Summary</span>
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Stepper Navigation Bar (Tabs 1 to 9) -->
    <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-9 gap-2 mb-8">
        @php
            $steps = [
                1 => ['label' => '1. Company', 'sub' => 'Details'],
                2 => ['label' => '2. Staff', 'sub' => 'Users (' . $audit['staff_count'] . ')'],
                3 => ['label' => '3. Computers', 'sub' => 'Laptops (' . $audit['computer_qty'] . ')'],
                4 => ['label' => '4. Mobiles', 'sub' => 'Phones (' . $audit['mobile_qty'] . ')'],
                5 => ['label' => '5. Servers', 'sub' => 'Infra (' . $audit['server_qty'] . ')'],
                6 => ['label' => '6. Network', 'sub' => 'Routers (' . $audit['network_count'] . ')'],
                7 => ['label' => '7. Cloud', 'sub' => 'SaaS (' . $audit['cloud_count'] . ')'],
                8 => ['label' => '8. Software', 'sub' => 'Apps (' . $audit['software_count'] . ')'],
                9 => ['label' => '9. IT Contact', 'sub' => 'Nexteck (MSP)'],
            ];
        @endphp

        @foreach($steps as $sNum => $sData)
            <a href="{{ route('company.wizard', $sNum) }}" 
               class="p-3 rounded-2xl border text-center transition flex flex-col justify-center {{ $step == $sNum ? 'bg-brand-50 border-brand-500 text-brand-900 shadow-sm font-bold' : 'bg-white border-slate-200 hover:bg-slate-50 text-slate-600' }}">
                <span class="text-xs block truncate">{{ $sData['label'] }}</span>
                <span class="text-[10px] font-normal truncate mt-0.5 {{ $step == $sNum ? 'text-brand-700 font-semibold' : 'text-slate-400' }}">{{ $sData['sub'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Step Content Container -->
    <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 bg-white shadow-sm">
        
        {{-- ========================================================== --}}
        {{-- TAB 1: COMPANY DETAILS --}}
        {{-- ========================================================== --}}
        @if($step == 1)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">1</span>
                        Company Details
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Fill in registered company details. Use Companies House for exact registered details.</p>
                </div>
                <span class="ai-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Official Scope</span>
            </div>

            <form action="{{ route('company.step1.save') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Registered Company Name *</label>
                        <input type="text" name="registered_name" value="{{ old('registered_name', $company->registered_name) }}" required
                            placeholder="e.g. Acme Ltd (Companies House Name)"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Trading Name (if different)</label>
                        <input type="text" name="trading_name" value="{{ old('trading_name', $company->trading_name) }}"
                            placeholder="e.g. Acme Tech Solutions"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company Registration Number *</label>
                        <input type="text" name="company_number" value="{{ old('company_number', $company->company_number) }}" required
                            placeholder="8 digits, e.g. 12345678"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Legal Structure</label>
                        <select name="legal_structure" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                            <option value="Ltd" {{ old('legal_structure', $company->legal_structure) == 'Ltd' ? 'selected' : '' }}>Private Limited Company (Ltd)</option>
                            <option value="LLP" {{ old('legal_structure', $company->legal_structure) == 'LLP' ? 'selected' : '' }}>Limited Liability Partnership (LLP)</option>
                            <option value="Sole trader" {{ old('legal_structure', $company->legal_structure) == 'Sole trader' ? 'selected' : '' }}>Sole trader</option>
                            <option value="Partnership" {{ old('legal_structure', $company->legal_structure) == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                            <option value="Charity" {{ old('legal_structure', $company->legal_structure) == 'Charity' ? 'selected' : '' }}>Charity</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Certification Level Planned *</label>
                        <select name="certification_level" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                            <option value="Cyber Essentials" {{ old('certification_level', $company->certification_level) == 'Cyber Essentials' ? 'selected' : '' }}>Cyber Essentials (Self-Assessment)</option>
                            <option value="Cyber Essentials Plus" {{ old('certification_level', $company->certification_level) == 'Cyber Essentials Plus' ? 'selected' : '' }}>Cyber Essentials Plus (Audited Hands-on)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Scope of Certification *</label>
                        <select name="certification_scope" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                            <option value="Whole company" {{ old('certification_scope', $company->certification_scope) == 'Whole company' ? 'selected' : '' }}>Whole company (Recommended)</option>
                            <option value="Specific sub-set / Department" {{ old('certification_scope', $company->certification_scope) == 'Specific sub-set / Department' ? 'selected' : '' }}>Specific sub-set / Network segment</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Registered Address – Line 1 *</label>
                        <input type="text" name="address_line1" value="{{ old('address_line1', $company->address_line1) }}" required
                            placeholder="Building name / Street"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Address Line 2</label>
                        <input type="text" name="address_line2" value="{{ old('address_line2', $company->address_line2) }}"
                            placeholder="Suite / Floor"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Town / City *</label>
                        <input type="text" name="city" value="{{ old('city', $company->city) }}" required
                            placeholder="e.g. London"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Postcode *</label>
                        <input type="text" name="postcode" value="{{ old('postcode', $company->postcode) }}" required
                            placeholder="e.g. EC1V 1AB"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website', $company->website) }}"
                            placeholder="https://company.co.uk"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Main Company Email *</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                            placeholder="info@company.co.uk"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Main Company Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                            placeholder="+44 20 7000 0000"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Headcount (Employees)</label>
                        <input type="number" name="headcount" value="{{ old('headcount', $company->headcount) }}"
                            placeholder="e.g. 15"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Industry / SIC Code</label>
                        <input type="text" name="sic_code" value="{{ old('sic_code', $company->sic_code) }}"
                            placeholder="e.g. 62020 (IT Consultancy)"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-brand-500">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="py-3 px-8 rounded-xl font-bold text-sm bg-brand-600 hover:bg-brand-700 text-white shadow-md shadow-brand-600/20 transition duration-200">
                        Save & Continue to Staff (Tab 2) &rarr;
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 2: STAFF --}}
        {{-- ========================================================== --}}
        @if($step == 2)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">2</span>
                        Staff Inventory & Scoping
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">List every employee, contractor and volunteer who uses your systems. Mark who works from home – their home router and devices are in scope.</p>
                </div>
            </div>

            <!-- Add Staff Form -->
            <form action="{{ route('company.staff.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Staff Member / Contractor
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name *</label>
                        <input type="text" name="full_name" required placeholder="e.g. Jane Smith"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Job Role</label>
                        <input type="text" name="job_role" placeholder="e.g. Operations Manager"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Employment Type</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Employee">Employee</option>
                            <option value="Contractor">Contractor</option>
                            <option value="Volunteer">Volunteer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Works From Home?</label>
                        <select name="works_from_home" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="No">No (Office Only)</option>
                            <option value="Hybrid">Hybrid</option>
                            <option value="Yes">Yes (Remote)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Uses Personal Device for Work?</label>
                        <select name="uses_personal_device" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="No">No</option>
                            <option value="Yes">Yes (BYOD in scope)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Has Admin Rights?</label>
                        <select name="has_admin_rights" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notes</label>
                        <input type="text" name="notes" placeholder="e.g. Accesses Microsoft 365 on home laptop"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add to Staff Scope
                        </button>
                    </div>
                </div>
            </form>

            <!-- Staff Table List -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Full Name</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">WFH?</th>
                            <th class="p-3">Personal Device?</th>
                            <th class="p-3">Admin Rights?</th>
                            <th class="p-3">Notes</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->staffMembers as $staff)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900">{{ $staff->full_name }}</td>
                            <td class="p-3">{{ $staff->job_role ?? '-' }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $staff->type }}</span></td>
                            <td class="p-3">
                                @if($staff->works_from_home == 'Yes' || $staff->works_from_home == 'Hybrid')
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-semibold">{{ $staff->works_from_home }}</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($staff->uses_personal_device == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-semibold">Yes (In Scope)</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($staff->has_admin_rights == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">Admin</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                            <td class="p-3 text-slate-500">{{ $staff->notes ?? '-' }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.staff.delete', $staff->id) }}" method="POST" onsubmit="return confirm('Remove this staff record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                No staff members listed yet. Add your employees and contractors above.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 1) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Company Details</a>
                <a href="{{ route('company.wizard', 3) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: Computers & Laptops (Tab 3) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 3: COMPUTERS --}}
        {{-- ========================================================== --}}
        @if($step == 3)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">3</span>
                        Computers (Laptops & Desktops)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">One row per model/OS group, or one per device. Operating systems must still receive security updates.</p>
                </div>
            </div>

            <!-- Add Computer Form -->
            <form action="{{ route('company.computer.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Register Computer Asset Group
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <select name="device_type" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Laptop">Laptop</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Workstation">Workstation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Make</label>
                        <input type="text" name="make" placeholder="e.g. Dell, Apple, Lenovo"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Model (Optional)</label>
                        <input type="text" name="model" placeholder="e.g. Latitude 5440"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Operating System *</label>
                        <input type="text" name="operating_system" required placeholder="e.g. Windows 11 Pro, macOS"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" placeholder="e.g. 24H2, Sonoma"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" placeholder="e.g. Office / Home"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assigned To / User(s)</label>
                        <input type="text" name="assigned_to" placeholder="e.g. All staff"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Supported? *</label>
                        <select name="os_supported" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes (Receiving security updates)</option>
                            <option value="No">No (OUT OF SUPPORT - Risk Flag!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Auto-updates On? *</label>
                        <select name="auto_updates_on" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Firewall & Anti-malware On? *</label>
                        <select name="security_software_on" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Computer Group
                        </button>
                    </div>
                </div>
            </form>

            <!-- Computers Table List -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Device</th>
                            <th class="p-3">Make & Model</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">OS & Version</th>
                            <th class="p-3">Location / User</th>
                            <th class="p-3">OS Supported?</th>
                            <th class="p-3">Updates & Security</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->computers as $comp)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900">{{ $comp->device_type }}</td>
                            <td class="p-3">{{ $comp->make }} {{ $comp->model }}</td>
                            <td class="p-3 font-mono font-bold text-brand-700">{{ $comp->quantity }}</td>
                            <td class="p-3">{{ $comp->operating_system }} <span class="text-slate-400 font-mono">({{ $comp->os_version ?? '-' }})</span></td>
                            <td class="p-3">{{ $comp->location }} / <span class="text-slate-500">{{ $comp->assigned_to }}</span></td>
                            <td class="p-3">
                                @if($comp->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-[11px]">
                                <span>Updates: {{ $comp->auto_updates_on }}</span> | <span>AV: {{ $comp->security_software_on }}</span>
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.computer.delete', $comp->id) }}" method="POST" onsubmit="return confirm('Remove this computer group?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                No computer inventory recorded.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 2) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Staff</a>
                <a href="{{ route('company.wizard', 4) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: Mobiles & Tablets (Tab 4) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 4: MOBILES & TABLETS --}}
        {{-- ========================================================== --}}
        @if($step == 4)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">4</span>
                        Mobile Phones & Tablets
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Include personal phones used for work email. Any device that can access company data is in scope.</p>
                </div>
            </div>

            <!-- Add Mobile Form -->
            <form action="{{ route('company.mobile.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Register Mobile / Tablet Group
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <select name="device_type" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Phone">Phone</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Make</label>
                        <input type="text" name="make" placeholder="e.g. Apple, Samsung, Google"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Model (Optional)</label>
                        <input type="text" name="model" placeholder="e.g. iPhone 14"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Operating System *</label>
                        <input type="text" name="operating_system" required placeholder="e.g. iOS, Android"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" placeholder="e.g. 18, 14"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ownership *</label>
                        <select name="ownership" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Personal">Personal (BYOD)</option>
                            <option value="Company">Company Owned</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Used for Work Email?</label>
                        <select name="used_for_work_email" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Screen Lock (PIN/Biometric)? *</label>
                        <select name="screen_lock" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes (Enforced)</option>
                            <option value="No">No (Risk)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Supported / Auto-update? *</label>
                        <select name="os_supported" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No (Risk Flag)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Mobile Device
                        </button>
                    </div>
                </div>
            </form>

            <!-- Mobiles Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Type</th>
                            <th class="p-3">Make & Model</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">OS / Version</th>
                            <th class="p-3">Ownership</th>
                            <th class="p-3">Work Email?</th>
                            <th class="p-3">Screen Lock</th>
                            <th class="p-3">OS Supported?</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->mobileDevices as $mob)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900">{{ $mob->device_type }}</td>
                            <td class="p-3">{{ $mob->make }} {{ $mob->model }}</td>
                            <td class="p-3 font-mono font-bold text-brand-700">{{ $mob->quantity }}</td>
                            <td class="p-3">{{ $mob->operating_system }} <span class="text-slate-400 font-mono">({{ $mob->os_version ?? '-' }})</span></td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $mob->ownership }}</span></td>
                            <td class="p-3">{{ $mob->used_for_work_email }}</td>
                            <td class="p-3">{{ $mob->screen_lock }}</td>
                            <td class="p-3">
                                @if($mob->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.mobile.delete', $mob->id) }}" method="POST" onsubmit="return confirm('Remove this mobile device?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                No mobile phones or tablets registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 3) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Computers</a>
                <a href="{{ route('company.wizard', 5) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: Servers (Tab 5) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 5: SERVERS --}}
        {{-- ========================================================== --}}
        @if($step == 5)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">5</span>
                        Servers (Physical, Virtual or Cloud VMs)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Leave blank if you have no servers. Zero is fine if none exist.</p>
                </div>
            </div>

            <!-- Add Server Form -->
            <form action="{{ route('company.server.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    Add Server Asset
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Server Name / Role *</label>
                        <input type="text" name="server_name" required placeholder="e.g. File Server, Domain Controller"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Operating System</label>
                        <input type="text" name="operating_system" placeholder="e.g. Windows Server, Ubuntu"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" placeholder="e.g. 2022, 22.04"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Type *</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Physical">Physical</option>
                            <option value="Virtual">Virtual (Hyper-V / VMware)</option>
                            <option value="Cloud VM">Cloud VM (AWS / Azure)</option>
                            <option value="NAS">NAS Storage</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" placeholder="e.g. Comms room, AWS UK"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Still Supported? *</label>
                        <select name="os_supported" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No (Risk Flag)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Server
                        </button>
                    </div>
                </div>
            </form>

            <!-- Servers Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Server Name / Role</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">OS & Version</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Location</th>
                            <th class="p-3">Supported?</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->servers as $srv)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900">{{ $srv->server_name }}</td>
                            <td class="p-3 font-mono font-bold text-brand-700">{{ $srv->quantity }}</td>
                            <td class="p-3">{{ $srv->operating_system }} <span class="text-slate-400 font-mono">({{ $srv->os_version ?? '-' }})</span></td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $srv->type }}</span></td>
                            <td class="p-3">{{ $srv->location ?? '-' }}</td>
                            <td class="p-3">
                                @if($srv->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.server.delete', $srv->id) }}" method="POST" onsubmit="return confirm('Remove server record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Zero servers declared. (Zero is fine if your organization is purely cloud-based).
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 4) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Mobiles</a>
                <a href="{{ route('company.wizard', 6) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: Network & Firewalls (Tab 6) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 6: NETWORK (ROUTERS / FIREWALLS) --}}
        {{-- ========================================================== --}}
        @if($step == 6)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">6</span>
                        Network (Office Routers & Firewalls)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">List each office router/firewall. Default admin passwords MUST be changed and remote admin access blocked.</p>
                </div>
            </div>

            <!-- Add Network Form -->
            <form action="{{ route('company.network.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Register Network Device
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location *</label>
                        <input type="text" name="location" required placeholder="e.g. Head Office / Branch"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <input type="text" name="device_type" value="Router/Firewall" required
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Make</label>
                        <input type="text" name="make" placeholder="e.g. Ubiquiti, Cisco, DrayTek"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Model</label>
                        <input type="text" name="model" placeholder="e.g. UniFi Dream Machine"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Firmware Version</label>
                        <input type="text" name="firmware_version" placeholder="e.g. 3.2.x"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Default Admin Password Changed? *</label>
                        <select name="default_password_changed" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes (Changed from factory default)</option>
                            <option value="No">No (CRITICAL RISK FLAG!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Firewall Enabled? *</label>
                        <select name="firewall_enabled" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Internet Admin Access Blocked? *</label>
                        <select name="internet_admin_blocked" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes (Blocked from WAN)</option>
                            <option value="No">No (Risk)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notes</label>
                        <input type="text" name="notes" placeholder="Notes on ISP or network config"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Network Device
                        </button>
                    </div>
                </div>
            </form>

            <!-- Network Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Location</th>
                            <th class="p-3">Make & Model</th>
                            <th class="p-3">Firmware</th>
                            <th class="p-3">Default Pass Changed?</th>
                            <th class="p-3">Firewall On?</th>
                            <th class="p-3">WAN Admin Blocked?</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->networkDevices as $net)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900">{{ $net->location }}</td>
                            <td class="p-3">{{ $net->make }} {{ $net->model }}</td>
                            <td class="p-3 font-mono text-slate-500">{{ $net->firmware_version ?? '-' }}</td>
                            <td class="p-3">
                                @if($net->default_password_changed == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $net->firewall_enabled }}</td>
                            <td class="p-3">{{ $net->internet_admin_blocked }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.network.delete', $net->id) }}" method="POST" onsubmit="return confirm('Remove network device?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                No network devices or firewalls registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 5) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Servers</a>
                <a href="{{ route('company.wizard', 7) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: Cloud Services (Tab 7) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 7: CLOUD SERVICES --}}
        {{-- ========================================================== --}}
        @if($step == 7)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">7</span>
                        Cloud Services & SaaS
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Everything you use online: email, storage, accounting, CRM. Cyber Essentials mandates MFA on cloud admin accounts.</p>
                </div>
            </div>

            <!-- Add Cloud Form -->
            <form action="{{ route('company.cloud.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                    Register Cloud Service
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Service Name *</label>
                        <input type="text" name="service_name" required placeholder="e.g. Microsoft 365, Google Workspace"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Purpose</label>
                        <input type="text" name="purpose" placeholder="e.g. Email & Office apps, Accounting"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Provider</label>
                        <input type="text" name="provider" placeholder="e.g. Microsoft, Google, AWS"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Number of Users *</label>
                        <input type="number" name="users_count" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Admin Account Owner</label>
                        <input type="text" name="admin_owner" placeholder="e.g. Jane Smith"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">MFA (2FA) Enabled? *</label>
                        <select name="mfa_enabled" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes (Multi-Factor Enabled)</option>
                            <option value="No">No (CRITICAL AUDIT FAILURE RISK!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Holds Personal / Sensitive Data?</label>
                        <select name="holds_sensitive_data" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Cloud Service
                        </button>
                    </div>
                </div>
            </form>

            <!-- Cloud Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Service Name</th>
                            <th class="p-3">Purpose & Provider</th>
                            <th class="p-3">Users</th>
                            <th class="p-3">Admin Owner</th>
                            <th class="p-3">MFA Enabled?</th>
                            <th class="p-3">Sensitive Data?</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->cloudServices as $cloud)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900">{{ $cloud->service_name }}</td>
                            <td class="p-3">{{ $cloud->purpose }} <span class="text-slate-400 font-medium">({{ $cloud->provider ?? '-' }})</span></td>
                            <td class="p-3 font-mono font-bold text-brand-700">{{ $cloud->users_count }}</td>
                            <td class="p-3">{{ $cloud->admin_owner ?? '-' }}</td>
                            <td class="p-3">
                                @if($cloud->mfa_enabled == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes (Protected)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (MFA REQUIRED)</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $cloud->holds_sensitive_data }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.cloud.delete', $cloud->id) }}" method="POST" onsubmit="return confirm('Remove cloud service?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                No cloud services listed yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 6) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Network</a>
                <a href="{{ route('company.wizard', 8) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: Software & Apps (Tab 8) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 8: SOFTWARE --}}
        {{-- ========================================================== --}}
        @if($step == 8)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">8</span>
                        Software & Installed Applications
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Web browsers, antivirus/anti-malware, email clients and office applications. Must still be vendor-supported.</p>
                </div>
            </div>

            <!-- Add Software Form -->
            <form action="{{ route('company.software.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    Register Software Application
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Web browser">Web browser</option>
                            <option value="Antivirus / EDR">Antivirus / Anti-malware</option>
                            <option value="Email client">Email client</option>
                            <option value="Office productivity">Office productivity suite</option>
                            <option value="Other">Other business application</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Software Name *</label>
                        <input type="text" name="software_name" required placeholder="e.g. Google Chrome, Defender"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Version</label>
                        <input type="text" name="version" placeholder="e.g. Latest, 128.0"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Vendor</label>
                        <input type="text" name="vendor" placeholder="e.g. Google, Microsoft"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Installed On</label>
                        <input type="text" name="installed_on" placeholder="e.g. All laptops & phones"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Auto-update On? *</label>
                        <select name="auto_update_on" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Still Supported? *</label>
                        <select name="still_supported" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No (Risk Flag)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Software
                        </button>
                    </div>
                </div>
            </form>

            <!-- Software Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3">Category</th>
                            <th class="p-3">Software & Vendor</th>
                            <th class="p-3">Version</th>
                            <th class="p-3">Installed On</th>
                            <th class="p-3">Auto Update?</th>
                            <th class="p-3">Supported?</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($company->softwareItems as $sw)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $sw->category }}</span></td>
                            <td class="p-3 font-bold text-slate-900">{{ $sw->software_name }} <span class="text-slate-400 font-normal">({{ $sw->vendor ?? '-' }})</span></td>
                            <td class="p-3 font-mono text-slate-600">{{ $sw->version ?? 'Latest' }}</td>
                            <td class="p-3 text-slate-500">{{ $sw->installed_on ?? '-' }}</td>
                            <td class="p-3">{{ $sw->auto_update_on }}</td>
                            <td class="p-3">
                                @if($sw->still_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.software.delete', $sw->id) }}" method="POST" onsubmit="return confirm('Remove software item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                No software items declared.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('company.wizard', 7) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Cloud Services</a>
                <a href="{{ route('company.wizard', 9) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                    Next: IT Contact & Governance (Tab 9) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 9: IT CONTACT & GOVERNANCE --}}
        {{-- LOCKED BY DEFAULT TO NEXTECK LIMITED / MOHAMMED NASAR --}}
        {{-- ========================================================== --}}
        @if($step == 9)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-sm font-bold border border-brand-200">9</span>
                        IT Contact & Security Governance
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Designated Managed Service Provider (MSP) and IT Governance for Cyber Essentials Certification.</p>
                </div>
                <span class="ai-badge bg-emerald-50 text-emerald-700 border border-emerald-200">Pre-Verified & Locked</span>
            </div>

            @php $contact = $company->itContact; @endphp

            <!-- Governance Verified Banner -->
            <div class="p-6 rounded-3xl bg-gradient-to-r from-brand-50 via-teal-50 to-emerald-50 border border-brand-200 mb-8 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-brand-200 text-brand-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <span class="ai-badge bg-white text-brand-700 border border-brand-200 mb-2">Designated IT Lead & MSP</span>
                        <h3 class="text-lg font-black text-slate-900">Mohammed Nasar</h3>
                        <p class="text-xs text-brand-700 font-semibold">CEO & Enterprise Architect &bull; Nexteck Limited</p>
                        <p class="text-xs text-slate-600 mt-1">London, UK &bull; Direct: <strong>07879 175585</strong> &bull; Email: <strong>info@nexteck.co.uk</strong></p>
                        <div class="text-[11px] text-slate-500 mt-2">
                            * Locked for company modifications. Administering governance and patch approval are certified under Nexteck Limited. (Lead Auditor can update from Admin Command Center).
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('company.step9.save') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Designated IT Lead Name</label>
                        <input type="text" value="{{ $contact->name ?? 'Mohammed Nasar' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Job Role</label>
                        <input type="text" value="{{ $contact->job_role ?? 'CEO & Enterprise Architect' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">IT Email Address</label>
                        <input type="email" value="{{ $contact->email ?? 'info@nexteck.co.uk' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm font-mono cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">IT Phone</label>
                        <input type="text" value="{{ $contact->phone ?? '07879 175585' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Provider Model</label>
                        <input type="text" value="{{ $contact->provider_type ?? 'Managed service provider (MSP)' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">External MSP Provider</label>
                        <input type="text" value="{{ $contact->external_provider_name ?? 'Nexteck Limited (London, UK)' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Admin Access Approver</label>
                        <input type="text" value="{{ $contact->approver_admin_access ?? 'Mohammed Nasar (Nexteck Limited)' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Patching & Updates Authority</label>
                        <input type="text" value="{{ $contact->person_applying_patches ?? 'Mohammed Nasar (Nexteck Limited)' }}" readonly disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-sm cursor-not-allowed">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                    <a href="{{ route('company.wizard', 8) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium">&larr; Back to Software</a>
                    <button type="submit" class="py-3 px-8 rounded-xl font-bold text-sm bg-gradient-to-r from-brand-600 to-teal-600 text-white shadow-md shadow-brand-600/20 transition duration-200">
                        Confirm Governance & Review Full Summary &rarr;
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>

</div>
@endsection
