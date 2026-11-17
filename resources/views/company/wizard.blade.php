@extends('layouts.app')

@section('title', 'Cyber Essentials Workbook - Step ' . $step)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    
    <!-- Top Header & Completion Header -->
    <div class="ai-glass rounded-2xl p-6 border border-slate-800 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <span class="ai-badge bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Step {{ $step }} of 9</span>
                <span class="text-xs text-slate-400">UK Cyber Essentials Workbook</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight mt-1">{{ $company->registered_name ?? 'Company Inventory' }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Asset Inventory & Scoping Questionnaire</p>
        </div>

        <!-- Realtime Compliance Snapshot -->
        <div class="flex items-center gap-4 bg-slate-900/90 border border-slate-800 p-3.5 rounded-xl">
            <div>
                <div class="text-[10px] uppercase tracking-wider text-slate-400">Overall Progress</div>
                <div class="text-xl font-bold text-cyan-400">{{ $audit['percent_complete'] }}%</div>
            </div>
            <div class="w-32 bg-slate-800 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-500 to-emerald-400 h-2 rounded-full transition-all duration-500" style="width: {{ $audit['percent_complete'] }}%"></div>
            </div>
            <a href="{{ route('company.summary') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                <span>View Summary</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
                6 => ['label' => '6. Network', 'sub' => 'Firewalls (' . $audit['network_count'] . ')'],
                7 => ['label' => '7. Cloud', 'sub' => 'Services (' . $audit['cloud_count'] . ')'],
                8 => ['label' => '8. Software', 'sub' => 'Apps (' . $audit['software_count'] . ')'],
                9 => ['label' => '9. IT Contact', 'sub' => 'Governance'],
            ];
        @endphp

        @foreach($steps as $sNum => $sData)
            <a href="{{ route('company.wizard', $sNum) }}" 
               class="p-3 rounded-xl border text-center transition flex flex-col justify-center {{ $step == $sNum ? 'bg-cyan-500/10 border-cyan-500/50 text-cyan-300 shadow-lg shadow-cyan-500/10' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 text-slate-400' }}">
                <span class="text-xs font-bold block truncate">{{ $sData['label'] }}</span>
                <span class="text-[10px] text-slate-500 truncate mt-0.5">{{ $sData['sub'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Step Content Container -->
    <div class="ai-glass rounded-2xl p-6 md:p-8 border border-slate-800">
        
        {{-- ========================================================== --}}
        {{-- TAB 1: COMPANY DETAILS --}}
        {{-- ========================================================== --}}
        @if($step == 1)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">1</span>
                        Company Details
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Fill in registered company details. Use Companies House for exact registered details.</p>
                </div>
                <span class="ai-badge bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Essential Scope</span>
            </div>

            <form action="{{ route('company.step1.save') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Registered Company Name *</label>
                        <input type="text" name="registered_name" value="{{ old('registered_name', $company->registered_name) }}" required
                            placeholder="e.g. Acme Ltd (exactly as on Companies House)"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Trading Name (if different)</label>
                        <input type="text" name="trading_name" value="{{ old('trading_name', $company->trading_name) }}"
                            placeholder="e.g. Acme Tech Solutions"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Company Registration Number *</label>
                        <input type="text" name="company_number" value="{{ old('company_number', $company->company_number) }}" required
                            placeholder="8 digits, e.g. 12345678"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Legal Structure</label>
                        <select name="legal_structure" class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                            <option value="Ltd" {{ old('legal_structure', $company->legal_structure) == 'Ltd' ? 'selected' : '' }}>Private Limited Company (Ltd)</option>
                            <option value="LLP" {{ old('legal_structure', $company->legal_structure) == 'LLP' ? 'selected' : '' }}>Limited Liability Partnership (LLP)</option>
                            <option value="Sole trader" {{ old('legal_structure', $company->legal_structure) == 'Sole trader' ? 'selected' : '' }}>Sole trader</option>
                            <option value="Partnership" {{ old('legal_structure', $company->legal_structure) == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                            <option value="Charity" {{ old('legal_structure', $company->legal_structure) == 'Charity' ? 'selected' : '' }}>Charity</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Certification Level Planned *</label>
                        <select name="certification_level" class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                            <option value="Cyber Essentials" {{ old('certification_level', $company->certification_level) == 'Cyber Essentials' ? 'selected' : '' }}>Cyber Essentials (Self-Assessment)</option>
                            <option value="Cyber Essentials Plus" {{ old('certification_level', $company->certification_level) == 'Cyber Essentials Plus' ? 'selected' : '' }}>Cyber Essentials Plus (Audited Hands-on)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Scope of Certification *</label>
                        <select name="certification_scope" class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                            <option value="Whole company" {{ old('certification_scope', $company->certification_scope) == 'Whole company' ? 'selected' : '' }}>Whole company (Recommended)</option>
                            <option value="Specific sub-set / Department" {{ old('certification_scope', $company->certification_scope) == 'Specific sub-set / Department' ? 'selected' : '' }}>Specific sub-set / Network segment</option>
                        </select>
                    </div>

                    <!-- Addresses -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Registered Address – Line 1 *</label>
                        <input type="text" name="address_line1" value="{{ old('address_line1', $company->address_line1) }}" required
                            placeholder="Building name / Street"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Address Line 2</label>
                        <input type="text" name="address_line2" value="{{ old('address_line2', $company->address_line2) }}"
                            placeholder="Suite / Floor"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Town / City *</label>
                        <input type="text" name="city" value="{{ old('city', $company->city) }}" required
                            placeholder="e.g. London"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Postcode *</label>
                        <input type="text" name="postcode" value="{{ old('postcode', $company->postcode) }}" required
                            placeholder="e.g. EC1V 1AB"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website', $company->website) }}"
                            placeholder="https://company.co.uk"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Main Company Email *</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                            placeholder="info@company.co.uk"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Main Company Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                            placeholder="+44 20 7000 0000"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Number of Employees (Headcount)</label>
                        <input type="number" name="headcount" value="{{ old('headcount', $company->headcount) }}"
                            placeholder="e.g. 15"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Industry / SIC Code</label>
                        <input type="text" name="sic_code" value="{{ old('sic_code', $company->sic_code) }}"
                            placeholder="e.g. 62020 (IT Consultancy)"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Information Completed By</label>
                        <input type="text" name="completed_by" value="{{ old('completed_by', $company->completed_by ?? auth()->user()->name) }}"
                            placeholder="Full Name"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end">
                    <button type="submit" class="py-3 px-8 rounded-xl font-bold text-sm bg-gradient-to-r from-cyan-500 to-teal-500 text-slate-950 hover:opacity-95 shadow-lg shadow-cyan-500/20 transition duration-200">
                        Save & Continue to Staff (Tab 2) →
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">2</span>
                        Staff Inventory & Scoping
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">List every employee, contractor and volunteer who uses your systems. Mark who works from home – their home router and devices are in scope.</p>
                </div>
            </div>

            <!-- Add Staff Form -->
            <form action="{{ route('company.staff.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Staff Member / Contractor
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Full Name *</label>
                        <input type="text" name="full_name" required placeholder="e.g. Jane Smith"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Job Role</label>
                        <input type="text" name="job_role" placeholder="e.g. Operations Manager"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Employment Type</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Employee">Employee</option>
                            <option value="Contractor">Contractor</option>
                            <option value="Volunteer">Volunteer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Works From Home?</label>
                        <select name="works_from_home" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="No">No (Office Only)</option>
                            <option value="Hybrid">Hybrid</option>
                            <option value="Yes">Yes (Remote)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Uses Personal Device for Work?</label>
                        <select name="uses_personal_device" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="No">No</option>
                            <option value="Yes">Yes (BYOD)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Has Admin Rights?</label>
                        <select name="has_admin_rights" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Notes</label>
                        <input type="text" name="notes" placeholder="e.g. Accesses Microsoft 365 on personal mobile"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add to Staff Scope
                        </button>
                    </div>
                </div>
            </form>

            <!-- Staff Table List -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->staffMembers as $staff)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3 font-semibold text-white">{{ $staff->full_name }}</td>
                            <td class="p-3 text-slate-300">{{ $staff->job_role ?? '-' }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">{{ $staff->type }}</span></td>
                            <td class="p-3">
                                @if($staff->works_from_home == 'Yes' || $staff->works_from_home == 'Hybrid')
                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ $staff->works_from_home }}</span>
                                @else
                                    <span class="text-slate-500">No</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($staff->uses_personal_device == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">Yes (In Scope)</span>
                                @else
                                    <span class="text-slate-500">No</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($staff->has_admin_rights == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20">Admin</span>
                                @else
                                    <span class="text-slate-500">No</span>
                                @endif
                            </td>
                            <td class="p-3 text-slate-400">{{ $staff->notes ?? '-' }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.staff.delete', $staff->id) }}" method="POST" onsubmit="return confirm('Remove this staff record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                No staff members listed yet. Add your employees and contractors above.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 1) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Company Details</a>
                <a href="{{ route('company.wizard', 3) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">3</span>
                        Computers (Laptops & Desktops)
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">One row per model/OS group, or one per device. Quantity can be >1 (e.g. 10 x Dell, Windows 11 Pro 24H2). Operating systems must still receive security updates.</p>
                </div>
            </div>

            <!-- Add Computer Form -->
            <form action="{{ route('company.computer.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Register Computer Asset Group
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <select name="device_type" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Laptop">Laptop</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Workstation">Workstation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Make</label>
                        <input type="text" name="make" placeholder="e.g. Dell, Apple, Lenovo"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Model (Optional)</label>
                        <input type="text" name="model" placeholder="e.g. Latitude 5440 / MacBook Pro"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Operating System *</label>
                        <input type="text" name="operating_system" required placeholder="e.g. Windows 11 Pro, macOS Sonoma"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" placeholder="e.g. 24H2, 14.5"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" placeholder="e.g. Office / Home"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Assigned To / User(s)</label>
                        <input type="text" name="assigned_to" placeholder="e.g. All staff / Dev team"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">OS Still Supported? *</label>
                        <select name="os_supported" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes (Receiving security patches)</option>
                            <option value="No">No (OUT OF SUPPORT - Risk Flag!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Auto-updates On? *</label>
                        <select name="auto_updates_on" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Firewall & Anti-malware On? *</label>
                        <select name="security_software_on" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add Computer Group
                        </button>
                    </div>
                </div>
            </form>

            <!-- Computers Table List -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->computers as $comp)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3 font-semibold text-white">{{ $comp->device_type }}</td>
                            <td class="p-3">{{ $comp->make }} {{ $comp->model }}</td>
                            <td class="p-3 font-mono text-cyan-300 font-bold">{{ $comp->quantity }}</td>
                            <td class="p-3">{{ $comp->operating_system }} <span class="text-slate-400 font-mono">({{ $comp->os_version ?? '-' }})</span></td>
                            <td class="p-3">{{ $comp->location }} / <span class="text-slate-400">{{ $comp->assigned_to }}</span></td>
                            <td class="p-3">
                                @if($comp->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-[11px]">
                                <span>Updates: {{ $comp->auto_updates_on }}</span> | <span>AV: {{ $comp->security_software_on }}</span>
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.computer.delete', $comp->id) }}" method="POST" onsubmit="return confirm('Remove this computer group?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                No computer inventory recorded.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 2) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Staff</a>
                <a href="{{ route('company.wizard', 4) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">4</span>
                        Mobile Phones & Tablets
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Include personal phones used for work email. Any device that can access company data is in scope.</p>
                </div>
            </div>

            <!-- Add Mobile Form -->
            <form action="{{ route('company.mobile.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Register Mobile / Tablet Group
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <select name="device_type" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Phone">Phone</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Make</label>
                        <input type="text" name="make" placeholder="e.g. Apple, Samsung, Google"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Model (Optional)</label>
                        <input type="text" name="model" placeholder="e.g. iPhone 14, Galaxy S23"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Operating System *</label>
                        <input type="text" name="operating_system" required placeholder="e.g. iOS, Android"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" placeholder="e.g. 18, 14"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Ownership *</label>
                        <select name="ownership" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Personal">Personal (BYOD)</option>
                            <option value="Company">Company Owned</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Used for Work Email?</label>
                        <select name="used_for_work_email" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Assigned To / User(s)</label>
                        <input type="text" name="assigned_to" placeholder="e.g. Sales team, Directors"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Screen Lock (PIN/Biometric)? *</label>
                        <select name="screen_lock" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes (Enforced)</option>
                            <option value="No">No (Risk)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">OS Supported / Auto-update? *</label>
                        <select name="os_supported" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No (Risk Flag)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add Mobile Device
                        </button>
                    </div>
                </div>
            </form>

            <!-- Mobiles Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->mobileDevices as $mob)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3 font-semibold text-white">{{ $mob->device_type }}</td>
                            <td class="p-3">{{ $mob->make }} {{ $mob->model }}</td>
                            <td class="p-3 font-mono text-cyan-300 font-bold">{{ $mob->quantity }}</td>
                            <td class="p-3">{{ $mob->operating_system }} <span class="text-slate-400 font-mono">({{ $mob->os_version ?? '-' }})</span></td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">{{ $mob->ownership }}</span></td>
                            <td class="p-3">{{ $mob->used_for_work_email }}</td>
                            <td class="p-3">{{ $mob->screen_lock }}</td>
                            <td class="p-3">
                                @if($mob->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.mobile.delete', $mob->id) }}" method="POST" onsubmit="return confirm('Remove this mobile device?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500">
                                No mobile phones or tablets registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 3) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Computers</a>
                <a href="{{ route('company.wizard', 5) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">5</span>
                        Servers (Physical, Virtual or Cloud VMs)
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Leave blank if you have no servers. Include physical, virtual and cloud-hosted servers (e.g. Azure/AWS VMs, NAS used as file server).</p>
                </div>
            </div>

            <!-- Add Server Form -->
            <form action="{{ route('company.server.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    Add Server Asset
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Server Name / Role *</label>
                        <input type="text" name="server_name" required placeholder="e.g. File Server, Domain Controller"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Operating System</label>
                        <input type="text" name="operating_system" placeholder="e.g. Windows Server, Ubuntu LTS"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" placeholder="e.g. 2022, 22.04"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Type *</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Physical">Physical</option>
                            <option value="Virtual">Virtual (Hyper-V / VMware)</option>
                            <option value="Cloud VM">Cloud VM (AWS / Azure)</option>
                            <option value="NAS">NAS / Storage Box</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" placeholder="e.g. Office comms room, AWS eu-west-2"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">OS Still Supported? *</label>
                        <select name="os_supported" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No (Risk Flag)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add Server
                        </button>
                    </div>
                </div>
            </form>

            <!-- Servers Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->servers as $srv)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3 font-semibold text-white">{{ $srv->server_name }}</td>
                            <td class="p-3 font-mono text-cyan-300 font-bold">{{ $srv->quantity }}</td>
                            <td class="p-3">{{ $srv->operating_system }} <span class="text-slate-400 font-mono">({{ $srv->os_version ?? '-' }})</span></td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">{{ $srv->type }}</span></td>
                            <td class="p-3">{{ $srv->location ?? '-' }}</td>
                            <td class="p-3">
                                @if($srv->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">NO (RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.server.delete', $srv->id) }}" method="POST" onsubmit="return confirm('Remove server record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                Zero servers declared. (Zero is completely fine if your organization is purely cloud-based).
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 4) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Mobiles</a>
                <a href="{{ route('company.wizard', 6) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">6</span>
                        Network (Office Routers & Firewalls)
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">List each office router/firewall. Important Cyber Essentials rule: Default admin passwords MUST be changed and remote admin access blocked.</p>
                </div>
            </div>

            <!-- Add Network Form -->
            <form action="{{ route('company.network.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Register Network Device
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Location *</label>
                        <input type="text" name="location" required placeholder="e.g. Head Office / Branch"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <input type="text" name="device_type" value="Router/Firewall" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Make</label>
                        <input type="text" name="make" placeholder="e.g. Ubiquiti, Cisco, DrayTek, BT Hub"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Model</label>
                        <input type="text" name="model" placeholder="e.g. UniFi Dream Machine, 2865"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Firmware Version</label>
                        <input type="text" name="firmware_version" placeholder="e.g. 3.2.x"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Default Admin Password Changed? *</label>
                        <select name="default_password_changed" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes (Changed from factory default)</option>
                            <option value="No">No (CRITICAL RISK FLAG!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Firewall Enabled? *</label>
                        <select name="firewall_enabled" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Internet Admin Access Blocked? *</label>
                        <select name="internet_admin_blocked" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes (Blocked from WAN)</option>
                            <option value="No">No (Risk)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Notes</label>
                        <input type="text" name="notes" placeholder="Notes on ISP or failover"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add Network Device
                        </button>
                    </div>
                </div>
            </form>

            <!-- Network Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->networkDevices as $net)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3 font-semibold text-white">{{ $net->location }}</td>
                            <td class="p-3">{{ $net->make }} {{ $net->model }}</td>
                            <td class="p-3 font-mono text-slate-400">{{ $net->firmware_version ?? '-' }}</td>
                            <td class="p-3">
                                @if($net->default_password_changed == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $net->firewall_enabled }}</td>
                            <td class="p-3">{{ $net->internet_admin_blocked }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.network.delete', $net->id) }}" method="POST" onsubmit="return confirm('Remove network device?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                No network devices or firewalls registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 5) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Servers</a>
                <a href="{{ route('company.wizard', 7) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">7</span>
                        Cloud Services & SaaS
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Everything you use online: email, storage, accounting, CRM, website hosting. Cyber Essentials mandates MFA on cloud admin accounts.</p>
                </div>
            </div>

            <!-- Add Cloud Form -->
            <form action="{{ route('company.cloud.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                    Register Cloud Service
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Service Name *</label>
                        <input type="text" name="service_name" required placeholder="e.g. Microsoft 365, Google Workspace, Xero"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Purpose</label>
                        <input type="text" name="purpose" placeholder="e.g. Email & Office apps, Accounting"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Provider</label>
                        <input type="text" name="provider" placeholder="e.g. Microsoft, Google, AWS"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Number of Users *</label>
                        <input type="number" name="users_count" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Admin Account Owner</label>
                        <input type="text" name="admin_owner" placeholder="e.g. Jane Smith (IT Admin)"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">MFA (2FA) Enabled? *</label>
                        <select name="mfa_enabled" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes (Multi-Factor Enabled)</option>
                            <option value="No">No (CRITICAL AUDIT FAILURE RISK!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Holds Personal / Sensitive Data?</label>
                        <select name="holds_sensitive_data" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add Cloud Service
                        </button>
                    </div>
                </div>
            </form>

            <!-- Cloud Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->cloudServices as $cloud)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3 font-semibold text-white">{{ $cloud->service_name }}</td>
                            <td class="p-3">{{ $cloud->purpose }} <span class="text-slate-400">({{ $cloud->provider ?? '-' }})</span></td>
                            <td class="p-3 font-mono text-cyan-300 font-bold">{{ $cloud->users_count }}</td>
                            <td class="p-3 text-slate-300">{{ $cloud->admin_owner ?? '-' }}</td>
                            <td class="p-3">
                                @if($cloud->mfa_enabled == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Yes (Protected)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">NO (MFA REQUIRED)</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $cloud->holds_sensitive_data }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.cloud.delete', $cloud->id) }}" method="POST" onsubmit="return confirm('Remove cloud service?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                No cloud services listed yet. Add your SaaS, email, and storage tools.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 6) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Network</a>
                <a href="{{ route('company.wizard', 8) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
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
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">8</span>
                        Software & Installed Applications
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Web browsers, antivirus/anti-malware, email clients and office applications. Must still be vendor-supported.</p>
                </div>
            </div>

            <!-- Add Software Form -->
            <form action="{{ route('company.software.add') }}" method="POST" class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 mb-8">
                @csrf
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    Register Software Application
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Web browser">Web browser</option>
                            <option value="Antivirus / EDR">Antivirus / Anti-malware</option>
                            <option value="Email client">Email client</option>
                            <option value="Office productivity">Office productivity suite</option>
                            <option value="Other">Other business application</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Software Name *</label>
                        <input type="text" name="software_name" required placeholder="e.g. Google Chrome, Defender, Outlook"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Version</label>
                        <input type="text" name="version" placeholder="e.g. Latest, 128.0"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Vendor</label>
                        <input type="text" name="vendor" placeholder="e.g. Google, Microsoft"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Installed On</label>
                        <input type="text" name="installed_on" placeholder="e.g. All laptops & phones"
                            class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Auto-update On? *</label>
                        <select name="auto_update_on" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Still Supported? *</label>
                        <select name="still_supported" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No (Risk Flag)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                            + Add Software
                        </button>
                    </div>
                </div>
            </form>

            <!-- Software Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
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
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($company->softwareItems as $sw)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">{{ $sw->category }}</span></td>
                            <td class="p-3 font-semibold text-white">{{ $sw->software_name }} <span class="text-slate-400 font-normal">({{ $sw->vendor ?? '-' }})</span></td>
                            <td class="p-3 font-mono text-slate-300">{{ $sw->version ?? 'Latest' }}</td>
                            <td class="p-3 text-slate-400">{{ $sw->installed_on ?? '-' }}</td>
                            <td class="p-3">{{ $sw->auto_update_on }}</td>
                            <td class="p-3">
                                @if($sw->still_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">NO (RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form action="{{ route('company.software.delete', $sw->id) }}" method="POST" onsubmit="return confirm('Remove software item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                No software items declared.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 flex justify-between items-center">
                <a href="{{ route('company.wizard', 7) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Cloud Services</a>
                <a href="{{ route('company.wizard', 9) }}" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                    Next: IT Contact & Governance (Tab 9) &rarr;
                </a>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAB 9: IT CONTACT & GOVERNANCE --}}
        {{-- ========================================================== --}}
        @if($step == 9)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/20">9</span>
                        IT Contact & Security Governance
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">The person who manages your IT internally or your outsourced Managed Service Provider (MSP).</p>
                </div>
            </div>

            @php $contact = $company->itContact; @endphp

            <form action="{{ route('company.step9.save') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">IT Lead Name *</label>
                        <input type="text" name="name" value="{{ old('name', $contact->name ?? '') }}" required
                            placeholder="e.g. David Miller"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Job Role</label>
                        <input type="text" name="job_role" value="{{ old('job_role', $contact->job_role ?? '') }}"
                            placeholder="e.g. IT Manager, Head of Tech"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $contact->email ?? '') }}" required
                            placeholder="e.g. it@company.co.uk"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Direct Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $contact->phone ?? '') }}"
                            placeholder="+44 20 7123 4599"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Internal or External Provider? *</label>
                        <select name="provider_type" class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                            <option value="Internal" {{ old('provider_type', $contact->provider_type ?? '') == 'Internal' ? 'selected' : '' }}>Internal Staff</option>
                            <option value="Managed service provider (MSP)" {{ old('provider_type', $contact->provider_type ?? '') == 'Managed service provider (MSP)' ? 'selected' : '' }}>Managed Service Provider (MSP)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">External Provider Name (if any)</label>
                        <input type="text" name="external_provider_name" value="{{ old('external_provider_name', $contact->external_provider_name ?? '') }}"
                            placeholder="e.g. CyberCloud Solutions UK"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Person Responsible for Approving Admin Access</label>
                        <input type="text" name="approver_admin_access" value="{{ old('approver_admin_access', $contact->approver_admin_access ?? '') }}"
                            placeholder="Name & Role"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Person Responsible for Applying Updates / Patches</label>
                        <input type="text" name="person_applying_patches" value="{{ old('person_applying_patches', $contact->person_applying_patches ?? '') }}"
                            placeholder="Name & Role"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:border-cyan-400 focus:outline-none">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-between items-center">
                    <a href="{{ route('company.wizard', 8) }}" class="text-xs text-slate-400 hover:text-white">&larr; Back to Software</a>
                    <button type="submit" class="py-3 px-8 rounded-xl font-bold text-sm bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 hover:opacity-95 shadow-lg shadow-emerald-500/20 transition duration-200">
                        Save IT Governance & View Full Summary →
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>

</div>
@endsection
