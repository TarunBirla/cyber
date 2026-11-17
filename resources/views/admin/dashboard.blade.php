@extends('layouts.app')

@section('title', 'Lead Auditor Command Center')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="ai-glass rounded-3xl p-6 sm:p-8 border border-slate-800 mb-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 ai-glow-cyan">
        <div>
            <div class="flex items-center gap-3">
                <span class="ai-badge bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Administrator</span>
                <span class="text-xs text-slate-400">Cyber Essentials UK Lead Auditor Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">Audit Command Center</h1>
            <p class="text-xs text-slate-400 mt-1">Manage all participating UK companies, review submitted workbooks, and add corporate entities.</p>
        </div>

        <div>
            <button onclick="document.getElementById('newCompanyModal').classList.remove('hidden')" 
                    class="py-3 px-6 rounded-xl font-bold text-xs bg-gradient-to-r from-cyan-500 to-emerald-400 text-slate-950 hover:opacity-95 shadow-lg shadow-cyan-500/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                + Create New Company
            </button>
        </div>
    </div>

    <!-- Stat Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="ai-glass rounded-2xl p-5 border border-slate-800">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Registered</span>
            <div class="text-3xl font-extrabold text-white mt-2">{{ $totalCompanies }}</div>
            <div class="text-[11px] text-slate-400 mt-1">UK Organizations</div>
        </div>

        <div class="ai-glass rounded-2xl p-5 border border-slate-800">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Submitted Workbooks</span>
            <div class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $submittedCount }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Ready for Scope Verification</div>
        </div>

        <div class="ai-glass rounded-2xl p-5 border border-slate-800">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">In-Progress Drafts</span>
            <div class="text-3xl font-extrabold text-amber-400 mt-2">{{ $draftCount }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Completing Tabs 1–9</div>
        </div>

        <div class="ai-glass rounded-2xl p-5 border border-slate-800 {{ $totalRisksIdentified > 0 ? 'border-red-500/40' : 'border-emerald-500/40' }}">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Risk Flags Flagged</span>
            <div class="text-3xl font-extrabold {{ $totalRisksIdentified > 0 ? 'text-red-400' : 'text-emerald-400' }} mt-2">
                {{ $totalRisksIdentified }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Across all inventories</div>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="ai-glass rounded-2xl border border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Registered Companies & Submission Status</h2>
            <span class="text-xs text-slate-400">{{ $companies->count() }} entities tracked</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="p-4">Company Name</th>
                        <th class="p-4">Companies House Reg #</th>
                        <th class="p-4">Level & Scope</th>
                        <th class="p-4">Endpoints</th>
                        <th class="p-4">Compliance Progress</th>
                        <th class="p-4">Risk Flags</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($companies as $comp)
                    @php $sum = $comp->audit_summary; @endphp
                    <tr class="hover:bg-slate-900/40 transition">
                        <td class="p-4">
                            <a href="{{ route('admin.company.show', $comp->id) }}" class="font-bold text-white hover:text-cyan-400 transition block text-sm">
                                {{ $comp->registered_name ?? $comp->user->name }}
                            </a>
                            <span class="text-[11px] text-slate-400">{{ $comp->email ?? $comp->user->email }}</span>
                        </td>
                        <td class="p-4 font-mono text-slate-300">{{ $comp->company_number ?? 'Not declared' }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-semibold">{{ $comp->certification_level }}</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">{{ $comp->certification_scope }}</span>
                        </td>
                        <td class="p-4 font-mono">
                            <span class="text-cyan-300 font-bold">{{ $sum['computer_qty'] + $sum['mobile_qty'] }}</span>
                            <span class="text-[10px] text-slate-500 block">({{ $sum['computer_qty'] }} Comp, {{ $sum['mobile_qty'] }} Mob)</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white">{{ $sum['percent_complete'] }}%</span>
                                <div class="w-20 bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-cyan-400 h-1.5 rounded-full" style="width: {{ $sum['percent_complete'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @if($sum['risk_flags']['total_risks'] > 0)
                                <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20 font-bold">
                                    {{ $sum['risk_flags']['total_risks'] }} RISKS
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    0 Risks (Clean)
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                @if($comp->status == 'submitted') bg-cyan-500/10 text-cyan-400 border border-cyan-500/20
                                @elseif($comp->status == 'approved') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                @elseif($comp->status == 'flagged') bg-red-500/10 text-red-400 border border-red-500/20
                                @else bg-slate-800 text-slate-400
                                @endif">
                                {{ $comp->status }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <a href="{{ route('admin.company.show', $comp->id) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500/20 transition">
                                Inspect Scope &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-500">
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
<div id="newCompanyModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="ai-glass rounded-3xl p-6 sm:p-8 border border-slate-800 max-w-lg w-full relative ai-glow-cyan">
        <button onclick="document.getElementById('newCompanyModal').classList.add('hidden')" class="absolute top-6 right-6 text-slate-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl font-bold text-white mb-2">Create New UK Company Profile</h3>
        <p class="text-xs text-slate-400 mb-6">Create the company record and initialize their Cyber Essentials workbook credentials.</p>

        <form action="{{ route('admin.company.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Company Name *</label>
                <input type="text" name="company_name" required placeholder="e.g. Apex Cyber Solutions Ltd"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Official Email *</label>
                <input type="email" name="email" required placeholder="director@apexcyber.co.uk"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Company Number</label>
                    <input type="text" name="company_number" placeholder="e.g. 11223344"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Phone</label>
                    <input type="text" name="phone" placeholder="+44 20 ..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Planned Certification</label>
                <select name="certification_level" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
                    <option value="Cyber Essentials">Cyber Essentials</option>
                    <option value="Cyber Essentials Plus">Cyber Essentials Plus</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Initial Password *</label>
                <input type="password" name="password" required placeholder="Min 8 characters"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:border-cyan-400 focus:outline-none">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('newCompanyModal').classList.add('hidden')" class="py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-900 border border-slate-800">
                    Cancel
                </button>
                <button type="submit" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition">
                    Create Company & Authorize
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
