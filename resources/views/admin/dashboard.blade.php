@extends('layouts.app')

@section('title', 'Lead Auditor Command Center - Nexteck Limited')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 mb-8 bg-white flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="ai-badge bg-slate-100 text-slate-800 border border-slate-300">Lead Auditor</span>
                <span class="text-xs text-slate-500 font-medium">Nexteck Limited &bull; UK Assurance Center</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Audit Command Center</h1>
            <p class="text-xs text-slate-500 mt-1">
                Auditor: <strong class="text-slate-800">Mohammed Nasar</strong> (CEO & Enterprise Architect &bull; 07879 175585 &bull; info@nexteck.co.uk)
            </p>
        </div>

        <div>
            <button onclick="document.getElementById('newCompanyModal').classList.remove('hidden')" 
                    class="py-3 px-6 rounded-2xl font-bold text-xs bg-slate-900 text-white hover:bg-slate-800 shadow-md transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                + Register New Company
            </button>
        </div>
    </div>

    <!-- Stat Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Registered</span>
            <div class="text-3xl font-black text-slate-900 mt-2">{{ $totalCompanies }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">UK Organizations</div>
        </div>

        <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Submitted Workbooks</span>
            <div class="text-3xl font-black text-emerald-600 mt-2">{{ $submittedCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Ready for Review</div>
        </div>

        <div class="ai-card rounded-3xl p-5 border border-slate-200 bg-white shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">In-Progress Drafts</span>
            <div class="text-3xl font-black text-amber-600 mt-2">{{ $draftCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Completing Tabs 1–9</div>
        </div>

        <div class="ai-card rounded-3xl p-5 border {{ $totalRisksIdentified > 0 ? 'border-red-200 bg-red-50/20' : 'border-emerald-200 bg-emerald-50/20' }} shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Risk Flags</span>
            <div class="text-3xl font-black {{ $totalRisksIdentified > 0 ? 'text-red-600' : 'text-emerald-600' }} mt-2">
                {{ $totalRisksIdentified }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Identified in Inventories</div>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="ai-card rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-slate-900">Registered Companies & Submission Status</h2>
            <span class="text-xs text-slate-500 font-medium">{{ $companies->count() }} organizations tracked</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-200 font-bold">
                    <tr>
                        <th class="p-4">Company Name</th>
                        <th class="p-4">Reg Number</th>
                        <th class="p-4">Level & Scope</th>
                        <th class="p-4">Endpoints</th>
                        <th class="p-4">Progress</th>
                        <th class="p-4">Risk Flags</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($companies as $comp)
                    @php $sum = $comp->audit_summary; @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-4">
                            <a href="{{ route('admin.company.show', $comp->id) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block text-sm">
                                {{ $comp->registered_name ?? $comp->user->name }}
                            </a>
                            <span class="text-[11px] text-slate-500">{{ $comp->email ?? $comp->user->email }}</span>
                        </td>
                        <td class="p-4 font-mono font-medium text-slate-600">{{ $comp->company_number ?? 'Not declared' }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-semibold">{{ $comp->certification_level }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $comp->certification_scope }}</span>
                        </td>
                        <td class="p-4 font-mono">
                            <span class="font-bold text-slate-900">{{ $sum['computer_qty'] + $sum['mobile_qty'] }}</span>
                            <span class="text-[10px] text-slate-400 block font-sans">({{ $sum['computer_qty'] }} Comp, {{ $sum['mobile_qty'] }} Mob)</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">{{ $sum['percent_complete'] }}%</span>
                                <div class="w-16 bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-brand-600 h-1.5 rounded-full" style="width: {{ $sum['percent_complete'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @if($sum['risk_flags']['total_risks'] > 0)
                                <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold">
                                    {{ $sum['risk_flags']['total_risks'] }} RISKS
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">
                                    0 Risks (Pass)
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                @if($comp->status == 'submitted') bg-brand-50 text-brand-700 border border-brand-200
                                @elseif($comp->status == 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($comp->status == 'flagged') bg-red-50 text-red-700 border border-red-200
                                @else bg-slate-100 text-slate-600
                                @endif">
                                {{ $comp->status }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <a href="{{ route('admin.company.show', $comp->id) }}" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-50 hover:text-brand-700 text-slate-700 border border-slate-200 transition">
                                Inspect Scope &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">
                            No companies registered yet. Create one using the button above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Company Modal -->
<div id="newCompanyModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="ai-card rounded-3xl p-6 sm:p-8 border border-slate-200 max-w-lg w-full relative bg-white shadow-2xl">
        <button onclick="document.getElementById('newCompanyModal').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl font-extrabold text-slate-900 mb-1">Create New UK Company Profile</h3>
        <p class="text-xs text-slate-500 mb-6">Initialize their Cyber Essentials workbook and email access credentials directly.</p>

        <form action="{{ route('admin.company.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Company Name *</label>
                <input type="text" name="company_name" required placeholder="e.g. Apex Cyber Solutions Ltd"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Official Company Email *</label>
                <input type="email" name="email" required placeholder="director@apexcyber.co.uk"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Company Number</label>
                    <input type="text" name="company_number" placeholder="e.g. 11223344"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone</label>
                    <input type="text" name="phone" placeholder="020 ..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Planned Certification</label>
                <select name="certification_level" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    <option value="Cyber Essentials">Cyber Essentials</option>
                    <option value="Cyber Essentials Plus">Cyber Essentials Plus</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Initial Password *</label>
                <div class="relative">
                    <input type="password" id="adminCompanyPass" name="password" required placeholder="Min 8 characters"
                        class="w-full px-4 py-2.5 pr-11 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:border-brand-500 focus:outline-none">
                    <button type="button" onclick="togglePasswordVisibility('adminCompanyPass', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                        <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg class="w-4 h-4 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('newCompanyModal').classList.add('hidden')" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition">
                    Cancel
                </button>
                <button type="submit" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-slate-900 hover:bg-slate-800 text-white transition shadow-sm">
                    Create Company & Send Email
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const eye = btn.querySelector('.eye-icon');
        const eyeOff = btn.querySelector('.eye-off-icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.add('hidden');
            eyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            eye.classList.remove('hidden');
            eyeOff.classList.add('hidden');
        }
    }
</script>
@endsection
