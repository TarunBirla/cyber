@extends('layouts.app')

@section('title', 'Cyber Essentials Workbook - Step ' . $step)

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 py-4 sm:py-8">
    
    <!-- Top Header & Completion Header -->
    <div class="ai-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-slate-200 mb-6 sm:mb-8 bg-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 sm:gap-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2 sm:gap-2.5">
                <span class="ai-badge bg-brand-50 text-brand-700 border border-brand-200">Step {{ $step }} of 9</span>
                <span class="text-xs text-slate-500 font-medium">UK Cyber Essentials Workbook</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1.5">{{ $company->registered_name ?? 'Company Inventory' }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Scoping & Asset Inventory Data Gathering</p>
        </div>

        <!-- Realtime Compliance Snapshot -->
        <div class="w-full md:w-auto flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-start gap-3 sm:gap-4 bg-slate-50 border border-slate-200 p-3 sm:p-3.5 rounded-2xl">
            <div>
                <div class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Completion Score</div>
                <div class="text-xl sm:text-2xl font-black text-brand-600">{{ $audit['percent_complete'] }}%</div>
            </div>
            <div class="flex-1 sm:w-32 bg-slate-200 rounded-full h-2 overflow-hidden min-w-[70px]">
                <div class="bg-gradient-to-r from-brand-600 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: {{ $audit['percent_complete'] }}%"></div>
            </div>
            <a href="{{ route('company.summary') }}" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-white text-slate-800 border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                <span>Summary</span>
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Stepper Navigation Bar (Tabs 1 to 9) -->
    <div class="flex overflow-x-auto no-scrollbar touch-scroll gap-2 pb-2 mb-6 sm:mb-8 md:grid md:grid-cols-9">
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
               class="flex-shrink-0 min-w-[105px] md:min-w-0 p-2.5 sm:p-3 rounded-2xl border text-center transition flex flex-col justify-center {{ $step == $sNum ? 'bg-brand-50 border-brand-500 text-brand-900 shadow-sm font-bold ring-2 ring-brand-500/20' : 'bg-white border-slate-200 hover:bg-slate-50 text-slate-600' }}">
                <span class="text-xs block truncate">{{ $sData['label'] }}</span>
                <span class="text-[10px] font-normal truncate mt-0.5 {{ $step == $sNum ? 'text-brand-700 font-semibold' : 'text-slate-400' }}">{{ $sData['sub'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Step Content Container -->
    <div class="ai-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 border border-slate-200 bg-white shadow-sm">
        
        {{-- ========================================================== --}}
        {{-- TAB 1: COMPANY DETAILS --}}
        {{-- ========================================================== --}}
        @if($step == 1)
        <div>
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">1</span>
                        <span>Company Details</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Fill in registered company details. Use Companies House for exact registered details.</p>
                </div>
                <span class="ai-badge bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">Official Scope</span>
            </div>

            <form action="{{ route('company.step1.save') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
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
                    <button type="submit" class="w-full sm:w-auto py-3 px-6 sm:px-8 rounded-xl font-bold text-xs sm:text-sm bg-brand-600 hover:bg-brand-700 text-white shadow-md shadow-brand-600/20 transition duration-200 text-center">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">2</span>
                        <span>Staff Inventory & Scoping</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">List every employee, contractor and volunteer who uses your systems. Mark who works from home – their home router and devices are in scope.</p>
                </div>
            </div>

            <!-- Add Staff Form -->
            <form action="{{ route('company.staff.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Staff Member / Contractor
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
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
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notes</label>
                        <input type="text" name="notes" placeholder="e.g. Accesses Microsoft 365 on home laptop"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add to Staff Scope
                        </button>
                    </div>
                </div>
            </form>

            <!-- Staff Table List -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[650px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Full Name</th>
                            <th class="p-3 whitespace-nowrap">Role</th>
                            <th class="p-3 whitespace-nowrap">Type</th>
                            <th class="p-3 whitespace-nowrap">WFH?</th>
                            <th class="p-3 whitespace-nowrap">Personal Device?</th>
                            <th class="p-3 whitespace-nowrap">Admin Rights?</th>
                            <th class="p-3">Notes</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($staffMembers ?? $company->staffMembers) as $staff)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $staff->full_name }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $staff->job_role ?? '-' }}</td>
                            <td class="p-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $staff->type }}</span></td>
                            <td class="p-3 whitespace-nowrap">
                                @if($staff->works_from_home == 'Yes' || $staff->works_from_home == 'Hybrid')
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-semibold">{{ $staff->works_from_home }}</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($staff->uses_personal_device == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-semibold">Yes (In Scope)</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($staff->has_admin_rights == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">Admin</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                            <td class="p-3 text-slate-500">{{ $staff->notes ?? '-' }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
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

            @if(isset($staffMembers) && method_exists($staffMembers, 'links'))
                <div class="mt-4">
                    {{ $staffMembers->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 1) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Company Details</a>
                <a href="{{ route('company.wizard', 3) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">3</span>
                        <span>Computers (Laptops & Desktops)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">One row per model/OS group, or one per device. Operating systems must still receive security updates.</p>
                </div>
                <div class="flex items-center gap-2 self-start md:self-auto">
                    <span class="ai-badge bg-brand-50 text-brand-700 border border-brand-200">Automated Audit Available</span>
                </div>
            </div>

            <!-- SMART SCAN HELPER BANNER FOR NON-TECHNICAL USERS -->
            <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-brand-50 via-teal-50/40 to-slate-50 border border-brand-200 mb-6 sm:mb-8 shadow-sm">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 sm:gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="ai-badge bg-white text-brand-700 border border-brand-200">🤖 Non-Technical 1-Click Scan</span>
                            <span class="text-xs font-bold text-slate-700">Don't know your exact OS Build or Anti-malware details?</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Run the Automatic Cyber Essentials Scanner on this Mac/PC</h3>
                        <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
                            Run our lightweight, read-only script. It inspects your <strong>Make, Model, OS Version, Support Lifespan, Firewall, and Antivirus</strong> in 5 seconds and generates a <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200 text-brand-700 font-bold">my-device.json</code> file.
                        </p>
                    </div>

                    <!-- Scan Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full lg:w-auto">
                        <button type="button" onclick="document.getElementById('scanScriptGuide').classList.toggle('hidden')" 
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-xs bg-white text-brand-700 border border-brand-300 hover:bg-brand-50 transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Get Computer Scanner</span>
                        </button>

                        <!-- Upload JSON Modal Trigger -->
                        <button type="button" onclick="document.getElementById('deviceUploadModal').classList.remove('hidden')" 
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <span>Upload JSON (Auto-Fill)</span>
                        </button>
                    </div>
                </div>

                <!-- Device Upload Modal -->
                <div id="deviceUploadModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 hidden">
                    <div class="ai-card rounded-2xl sm:rounded-3xl p-5 sm:p-7 border border-slate-200 max-w-md w-full max-h-[92vh] overflow-y-auto relative bg-white shadow-2xl">
                        <button type="button" onclick="document.getElementById('deviceUploadModal').classList.add('hidden')" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center border border-brand-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">Upload Device Scan JSON</h3>
                                <p class="text-xs text-slate-500">Auto-fill computer inventory and assign to user</p>
                            </div>
                        </div>

                        <form action="{{ route('company.computer.upload_scan') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Assign Name *
                                </label>
                                <input type="text" name="assigned_to" required placeholder="e.g. John Doe / Mohammed Nasar"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-semibold focus:border-brand-500 focus:bg-white focus:outline-none transition">
                                <p class="text-[11px] text-slate-400 mt-1">Person or employee name this computer is assigned to.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Scan JSON File *
                                </label>
                                <input type="file" name="device_file" accept=".json,.txt" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition focus:outline-none">
                                <p class="text-[11px] text-slate-400 mt-1">Select the generated <code class="font-mono text-slate-600">my-device.json</code> file.</p>
                            </div>

                            <div class="pt-2 flex flex-col-reverse sm:flex-row justify-end gap-2">
                                <button type="button" onclick="document.getElementById('deviceUploadModal').classList.add('hidden')"
                                    class="w-full sm:w-auto py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition text-center">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm text-center">
                                    Save & Import Asset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Step-by-Step Instructions Toggle Panel -->
                <div id="scanScriptGuide" class="mt-4 sm:mt-5 pt-4 sm:pt-5 border-t border-brand-200/80 hidden text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200">
                            <div class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[11px] font-bold">W</span>
                                Windows (1-Click Run)
                            </div>
                            <p class="text-[11px] text-slate-500 mb-2">Option A: Download and double click our Windows batch file:</p>
                            <a href="/scripts/scan-windows.bat" download class="inline-flex items-center gap-1.5 text-brand-600 font-bold hover:underline mb-2">
                                <span>Download scan-windows.bat</span> &darr;
                            </a>
                            <p class="text-[11px] text-slate-500 mt-1">Option B: In terminal / command prompt run:</p>
                            <code class="block font-mono bg-slate-50 p-2 rounded-lg border border-slate-200 text-slate-800 text-[11px] select-all mt-1 overflow-x-auto whitespace-pre-wrap break-all sm:break-normal">
                                node device-register.js --json --out my-device.json
                            </code>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-slate-200">
                            <div class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[11px] font-bold">M</span>
                                Apple Mac (macOS Terminal)
                            </div>
                            <p class="text-[11px] text-slate-500 mb-2">Open Terminal app on your Mac and run this one-line command:</p>
                            <code class="block font-mono bg-slate-50 p-2 rounded-lg border border-slate-200 text-slate-800 text-[11px] select-all mt-1 overflow-x-auto whitespace-pre-wrap break-all sm:break-normal">
                                curl -s https://cyber.nexteck.uk/scripts/device-register.js | node - --json --out my-device.json
                            </code>
                            <p class="text-[10px] text-slate-400 mt-2">Then upload the generated <code class="font-mono text-slate-600 font-bold">my-device.json</code> using the button above!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Add Computer Form -->
            <form id="manualComputerForm" action="{{ route('company.computer.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-4 mb-4">
                    <div class="text-xs font-bold text-brand-700 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Manual Computer Registration Form</span>
                    </div>
                    <span class="text-[11px] text-slate-400">Or paste scanner JSON into upload button above</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Device Type *</label>
                        <select name="device_type" id="input_device_type" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Laptop">Laptop</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Workstation">Workstation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Make *</label>
                        <input type="text" name="make" id="input_make" required placeholder="e.g. Dell, Apple, Lenovo, HP"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Model (Optional)</label>
                        <input type="text" name="model" id="input_model" placeholder="e.g. Latitude 5440 / MacBook Pro"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Quantity *</label>
                        <input type="number" name="quantity" id="input_quantity" value="1" min="1" required
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Operating System *</label>
                        <input type="text" name="operating_system" id="input_operating_system" required placeholder="e.g. Windows 11 Pro, macOS Sonoma"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Version</label>
                        <input type="text" name="os_version" id="input_os_version" placeholder="e.g. 24H2, 14.5"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" id="input_location" placeholder="e.g. Office / Home"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assigned To / User(s)</label>
                        <input type="text" name="assigned_to" id="input_assigned_to" placeholder="e.g. All staff / Jane Smith"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">OS Supported? *</label>
                        <select name="os_supported" id="input_os_supported" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes (Receiving security updates)</option>
                            <option value="No">No (OUT OF SUPPORT - Risk Flag!)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Auto-updates On? *</label>
                        <select name="auto_updates_on" id="input_auto_updates_on" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Firewall & Anti-malware On? *</label>
                        <select name="security_software_on" id="input_security_software_on" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Computer Group
                        </button>
                    </div>
                </div>
            </form>

            <!-- Computers Table List -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[700px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Device</th>
                            <th class="p-3 whitespace-nowrap">Make & Model</th>
                            <th class="p-3 whitespace-nowrap">Qty</th>
                            <th class="p-3 whitespace-nowrap">OS & Version</th>
                            <th class="p-3 whitespace-nowrap">Assigned Name</th>
                            <th class="p-3 whitespace-nowrap">Location</th>
                            <th class="p-3 whitespace-nowrap">OS Supported?</th>
                            <th class="p-3 whitespace-nowrap">Updates & Security</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($computers ?? $company->computers) as $comp)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $comp->device_type }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $comp->make }} {{ $comp->model }}</td>
                            <td class="p-3 font-mono font-bold text-brand-700 whitespace-nowrap">{{ $comp->quantity }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $comp->operating_system }} <span class="text-slate-400 font-mono">({{ $comp->os_version ?? '-' }})</span></td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">
                                    {{ $comp->assigned_to ?: 'Unassigned' }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-500 whitespace-nowrap">{{ $comp->location ?? '-' }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @if($comp->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-[11px] whitespace-nowrap">
                                <span>Updates: {{ $comp->auto_updates_on }}</span> | <span>AV: {{ $comp->security_software_on }}</span>
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <form action="{{ route('company.computer.delete', $comp->id) }}" method="POST" onsubmit="return confirm('Remove this computer group?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                No computer inventory recorded.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($computers) && method_exists($computers, 'links'))
                <div class="mt-4">
                    {{ $computers->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 2) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Staff</a>
                <a href="{{ route('company.wizard', 4) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">4</span>
                        <span>Mobile Phones & Tablets</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Include personal phones used for work email. Any device that can access company data is in scope.</p>
                </div>
            </div>

            <!-- Add Mobile Form -->
            <form action="{{ route('company.mobile.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Register Mobile / Tablet Group
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Mobile Device
                        </button>
                    </div>
                </div>
            </form>

            <!-- Mobiles Table -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[720px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Type</th>
                            <th class="p-3 whitespace-nowrap">Make & Model</th>
                            <th class="p-3 whitespace-nowrap">Qty</th>
                            <th class="p-3 whitespace-nowrap">OS / Version</th>
                            <th class="p-3 whitespace-nowrap">Ownership</th>
                            <th class="p-3 whitespace-nowrap">Work Email?</th>
                            <th class="p-3 whitespace-nowrap">Screen Lock</th>
                            <th class="p-3 whitespace-nowrap">OS Supported?</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($mobileDevices ?? $company->mobileDevices) as $mob)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $mob->device_type }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $mob->make }} {{ $mob->model }}</td>
                            <td class="p-3 font-mono font-bold text-brand-700 whitespace-nowrap">{{ $mob->quantity }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $mob->operating_system }} <span class="text-slate-400 font-mono">({{ $mob->os_version ?? '-' }})</span></td>
                            <td class="p-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $mob->ownership }}</span></td>
                            <td class="p-3 whitespace-nowrap">{{ $mob->used_for_work_email }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $mob->screen_lock }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @if($mob->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
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

            @if(isset($mobileDevices) && method_exists($mobileDevices, 'links'))
                <div class="mt-4">
                    {{ $mobileDevices->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 3) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Computers</a>
                <a href="{{ route('company.wizard', 5) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">5</span>
                        <span>Servers (Physical, Virtual or Cloud VMs)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Leave blank if you have no servers. Zero is fine if none exist.</p>
                </div>
            </div>

            <!-- Add Server Form -->
            <form action="{{ route('company.server.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    Add Server Asset
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Server
                        </button>
                    </div>
                </div>
            </form>

            <!-- Servers Table -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[650px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Server Name / Role</th>
                            <th class="p-3 whitespace-nowrap">Qty</th>
                            <th class="p-3 whitespace-nowrap">OS & Version</th>
                            <th class="p-3 whitespace-nowrap">Type</th>
                            <th class="p-3 whitespace-nowrap">Location</th>
                            <th class="p-3 whitespace-nowrap">Supported?</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($servers ?? $company->servers) as $srv)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $srv->server_name }}</td>
                            <td class="p-3 font-mono font-bold text-brand-700 whitespace-nowrap">{{ $srv->quantity }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $srv->operating_system }} <span class="text-slate-400 font-mono">({{ $srv->os_version ?? '-' }})</span></td>
                            <td class="p-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $srv->type }}</span></td>
                            <td class="p-3 whitespace-nowrap">{{ $srv->location ?? '-' }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @if($srv->os_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
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

            @if(isset($servers) && method_exists($servers, 'links'))
                <div class="mt-4">
                    {{ $servers->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 4) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Mobiles</a>
                <a href="{{ route('company.wizard', 6) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">6</span>
                        <span>Network (Office Routers & Firewalls)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">List each office router/firewall. Default admin passwords MUST be changed and remote admin access blocked.</p>
                </div>
            </div>

            <!-- Add Network Form -->
            <form action="{{ route('company.network.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Register Network Device
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notes</label>
                        <input type="text" name="notes" placeholder="Notes on ISP or network config"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Network Device
                        </button>
                    </div>
                </div>
            </form>

            <!-- Network Table -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[680px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Location</th>
                            <th class="p-3 whitespace-nowrap">Make & Model</th>
                            <th class="p-3 whitespace-nowrap">Firmware</th>
                            <th class="p-3 whitespace-nowrap">Default Pass Changed?</th>
                            <th class="p-3 whitespace-nowrap">Firewall On?</th>
                            <th class="p-3 whitespace-nowrap">WAN Admin Blocked?</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($networkDevices ?? $company->networkDevices) as $net)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $net->location }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $net->make }} {{ $net->model }}</td>
                            <td class="p-3 font-mono text-slate-500 whitespace-nowrap">{{ $net->firmware_version ?? '-' }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @if($net->default_password_changed == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (FAIL RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">{{ $net->firewall_enabled }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $net->internet_admin_blocked }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
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

            @if(isset($networkDevices) && method_exists($networkDevices, 'links'))
                <div class="mt-4">
                    {{ $networkDevices->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 5) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Servers</a>
                <a href="{{ route('company.wizard', 7) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">7</span>
                        <span>Cloud Services & SaaS</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Everything you use online: email, storage, accounting, CRM. Cyber Essentials mandates MFA on cloud admin accounts.</p>
                </div>
            </div>

            <!-- Add Cloud Form -->
            <form action="{{ route('company.cloud.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                    Register Cloud Service
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Cloud Service
                        </button>
                    </div>
                </div>
            </form>

            <!-- Cloud Table -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[700px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Service Name</th>
                            <th class="p-3 whitespace-nowrap">Purpose & Provider</th>
                            <th class="p-3 whitespace-nowrap">Users</th>
                            <th class="p-3 whitespace-nowrap">Admin Owner</th>
                            <th class="p-3 whitespace-nowrap">MFA Enabled?</th>
                            <th class="p-3 whitespace-nowrap">Sensitive Data?</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($cloudServices ?? $company->cloudServices) as $cloud)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $cloud->service_name }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $cloud->purpose }} <span class="text-slate-400 font-medium">({{ $cloud->provider ?? '-' }})</span></td>
                            <td class="p-3 font-mono font-bold text-brand-700 whitespace-nowrap">{{ $cloud->users_count }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $cloud->admin_owner ?? '-' }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @if($cloud->mfa_enabled == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes (Protected)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (MFA REQUIRED)</span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">{{ $cloud->holds_sensitive_data }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
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

            @if(isset($cloudServices) && method_exists($cloudServices, 'links'))
                <div class="mt-4">
                    {{ $cloudServices->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 6) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Network</a>
                <a href="{{ route('company.wizard', 8) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">8</span>
                        <span>Software & Installed Applications</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Web browsers, antivirus/anti-malware, email clients and office applications. Must still be vendor-supported.</p>
                </div>
            </div>

            <!-- 1-Click Software Scanner Banner for Non-Technical Users -->
            <div class="mb-6 sm:mb-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-brand-50/70 via-blue-50/40 to-slate-50 border border-brand-200">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 sm:gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="ai-badge bg-white text-brand-700 border border-brand-200">🤖 Non-Technical 1-Click Software Scan</span>
                            <span class="text-xs font-bold text-slate-700">Don't know all installed software versions or EOL status?</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Run the Automatic Installed Software & Security Scanner</h3>
                        <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
                            Run our lightweight, read-only script. It automatically audits your <strong>Web Browsers, Antivirus / EDR, Email Clients, and Office Applications</strong>, flags out-of-support apps, and checks against CISA KEV vulnerabilities in seconds.
                        </p>
                    </div>

                    <!-- Scan Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full lg:w-auto">
                        <button type="button" onclick="document.getElementById('softwareScanScriptGuide').classList.toggle('hidden')" 
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-xs bg-white text-brand-700 border border-brand-300 hover:bg-brand-50 transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Get Software Scanner</span>
                        </button>

                        <!-- Upload JSON Modal Trigger -->
                        <button type="button" onclick="document.getElementById('softwareUploadModal').classList.remove('hidden')" 
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <span>Upload JSON (Auto-Fill)</span>
                        </button>
                    </div>
                </div>

                <!-- Software Upload Modal -->
                <div id="softwareUploadModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 hidden">
                    <div class="ai-card rounded-2xl sm:rounded-3xl p-5 sm:p-7 border border-slate-200 max-w-md w-full max-h-[92vh] overflow-y-auto relative bg-white shadow-2xl">
                        <button type="button" onclick="document.getElementById('softwareUploadModal').classList.add('hidden')" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center border border-brand-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">Upload Software Scan JSON</h3>
                                <p class="text-xs text-slate-500">Auto-fill applications inventory and assign to user</p>
                            </div>
                        </div>

                        <form action="{{ route('company.software.upload_scan') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Assign Name *
                                </label>
                                <input type="text" name="assigned_to" required placeholder="e.g. John Doe / Mohammed Nasar / Marketing PC"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-semibold focus:border-brand-500 focus:bg-white focus:outline-none transition">
                                <p class="text-[11px] text-slate-400 mt-1">Person or workstation name these applications are installed on.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Scan JSON File *
                                </label>
                                <input type="file" name="software_file" accept=".json,.txt" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition focus:outline-none">
                                <p class="text-[11px] text-slate-400 mt-1">Select the generated <code class="font-mono text-slate-600">my-software.json</code> file.</p>
                            </div>

                            <div class="pt-2 flex flex-col-reverse sm:flex-row justify-end gap-2">
                                <button type="button" onclick="document.getElementById('softwareUploadModal').classList.add('hidden')"
                                    class="w-full sm:w-auto py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition text-center">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm text-center">
                                    Save & Import Software
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Step-by-Step Instructions Toggle Panel -->
                <div id="softwareScanScriptGuide" class="mt-4 sm:mt-5 pt-4 sm:pt-5 border-t border-brand-200/80 hidden text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200">
                            <div class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[11px] font-bold">W</span>
                                Windows (1-Click Run)
                            </div>
                            <p class="text-[11px] text-slate-500 mb-2">Option A: Download and double click our Windows batch file:</p>
                            <a href="/scripts/scan-software-windows.bat" download class="inline-flex items-center gap-1.5 text-brand-600 font-bold hover:underline mb-2">
                                <span>Download scan-software-windows.bat</span> &darr;
                            </a>
                            <p class="text-[11px] text-slate-500 mt-1">Option B: In terminal / command prompt run:</p>
                            <code class="block font-mono bg-slate-50 p-2 rounded-lg border border-slate-200 text-slate-800 text-[11px] select-all mt-1 overflow-x-auto whitespace-pre-wrap break-all sm:break-normal">
                                node list-installed-software.js --json --out my-software.json
                            </code>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-slate-200">
                            <div class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[11px] font-bold">M</span>
                                Apple Mac (macOS Terminal)
                            </div>
                            <p class="text-[11px] text-slate-500 mb-2">Open Terminal app on your Mac and run this one-line command:</p>
                            <code class="block font-mono bg-slate-50 p-2 rounded-lg border border-slate-200 text-slate-800 text-[11px] select-all mt-1 overflow-x-auto whitespace-pre-wrap break-all sm:break-normal">
                                curl -s https://cyber.nexteck.uk/scripts/list-installed-software.js | node - --json --out my-software.json
                            </code>
                            <p class="text-[10px] text-slate-400 mt-2">Then upload the generated <code class="font-mono text-slate-600 font-bold">my-software.json</code> using the button above!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Software Form -->
            <form action="{{ route('company.software.add') }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6 sm:mb-8">
                @csrf
                <div class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    Register Software Application
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Assigned Name</label>
                        <input type="text" name="assigned_to" placeholder="e.g. John Doe / Mohammed Nasar"
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
                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="w-full sm:w-auto py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
                            + Add Software
                        </button>
                    </div>
                </div>
            </form>

            <!-- Software Table -->
            <div class="sm:hidden text-[11px] text-slate-400 mb-1.5 flex items-center gap-1">
                <span>&larr; Swipe table to view all columns &rarr;</span>
            </div>
            <div class="overflow-x-auto touch-scroll rounded-2xl border border-slate-200">
                <table class="w-full min-w-[680px] text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Category</th>
                            <th class="p-3 whitespace-nowrap">Software & Vendor</th>
                            <th class="p-3 whitespace-nowrap">Version</th>
                            <th class="p-3 whitespace-nowrap">Assigned Name</th>
                            <th class="p-3 whitespace-nowrap">Installed On</th>
                            <th class="p-3 whitespace-nowrap">Auto Update?</th>
                            <th class="p-3 whitespace-nowrap">Supported?</th>
                            <th class="p-3 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($softwareItems ?? $company->softwareItems) as $sw)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $sw->category }}</span></td>
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ $sw->software_name }} <span class="text-slate-400 font-normal">({{ $sw->vendor ?? '-' }})</span></td>
                            <td class="p-3 font-mono text-slate-600 whitespace-nowrap">{{ $sw->version ?? 'Latest' }}</td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">
                                    {{ $sw->assigned_to ?: ($sw->installed_on ? str_replace('Installed on ', '', $sw->installed_on) : 'All devices') }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-500 whitespace-nowrap">{{ $sw->installed_on ?? '-' }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $sw->auto_update_on }}</td>
                            <td class="p-3 whitespace-nowrap">
                                @if($sw->still_supported == 'Yes')
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">NO (RISK)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <form action="{{ route('company.software.delete', $sw->id) }}" method="POST" onsubmit="return confirm('Remove software item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                No software items declared.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($softwareItems) && method_exists($softwareItems, 'links'))
                <div class="mt-4">
                    {{ $softwareItems->links() }}
                </div>
            @endif

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                <a href="{{ route('company.wizard', 7) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Cloud Services</a>
                <a href="{{ route('company.wizard', 9) }}" class="w-full sm:w-auto text-center py-2.5 px-6 rounded-xl font-bold text-xs bg-brand-600 hover:bg-brand-700 text-white transition shadow-sm">
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
            <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 flex items-center gap-2 sm:gap-2.5">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center text-xs sm:text-sm font-bold border border-brand-200 flex-shrink-0">9</span>
                        <span>IT Contact & Security Governance</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Designated Managed Service Provider (MSP) and IT Governance for Cyber Essentials Certification.</p>
                </div>
                <span class="ai-badge bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">Pre-Verified & Locked</span>
            </div>

            @php $contact = $company->itContact; @endphp

            <!-- Governance Verified Banner -->
            <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-brand-50 via-teal-50 to-emerald-50 border border-brand-200 mb-6 sm:mb-8 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-white shadow-sm border border-brand-200 text-brand-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <span class="ai-badge bg-white text-brand-700 border border-brand-200 mb-2">Designated IT Lead & MSP</span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Mohammed Nasar</h3>
                        <p class="text-xs text-brand-700 font-semibold">CEO & Enterprise Architect &bull; Nexteck Limited</p>
                        <p class="text-xs text-slate-600 mt-1">London, UK &bull; Direct: <strong><a href="tel:07879175585" class="hover:underline">07879 175585</a></strong> &bull; Email: <strong>info@nexteck.co.uk</strong></p>
                        <div class="text-[11px] text-slate-500 mt-2">
                            * Locked for company modifications. Administering governance and patch approval are certified under Nexteck Limited. (Lead Auditor can update from Admin Command Center).
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('company.step9.save') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
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

                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3 sm:gap-0 justify-between items-stretch sm:items-center">
                    <a href="{{ route('company.wizard', 8) }}" class="text-xs text-slate-500 hover:text-slate-900 font-medium text-center sm:text-left py-2 sm:py-0">&larr; Back to Software</a>
                    <button type="submit" class="w-full sm:w-auto text-center py-3 px-8 rounded-xl font-bold text-xs sm:text-sm bg-gradient-to-r from-brand-600 to-teal-600 text-white shadow-md shadow-brand-600/20 transition duration-200">
                        Confirm Governance & Review Full Summary &rarr;
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>

</div>
@endsection
