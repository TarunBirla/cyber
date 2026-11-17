@extends('layouts.app')

@section('title', 'Register Company - Cyber Essentials Scope')

@section('content')
<div class="max-w-lg mx-auto px-4 py-12">
    <div class="ai-glass rounded-3xl p-8 border border-slate-800 ai-glow-emerald">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Register UK Company</h1>
            <p class="text-xs text-slate-400 mt-1">Initialize your Cyber Essentials Scope and Asset Inventory Workbook</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Registered Company Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="e.g. Acme Technologies Ltd"
                    class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Official Company Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="e.g. contact@acmetech.co.uk"
                    class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Company Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    placeholder="e.g. +44 20 7946 0991"
                    class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" required
                        placeholder="Min 8 characters"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Re-enter password"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-emerald-500/5 border border-emerald-500/20 text-[11px] text-emerald-300 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Once registered, your step-by-step 9-tab Cyber Essentials scope workbook will be instantly initialized.</span>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-bold text-sm bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 hover:opacity-95 shadow-lg shadow-emerald-500/20 transition duration-200 mt-2">
                Create Account & Start Workbook →
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-800 text-center text-xs text-slate-400">
            <span>Already registered?</span>
            <a href="{{ route('login') }}" class="text-emerald-400 hover:underline font-semibold ml-1">Sign in here</a>
        </div>

    </div>
</div>
@endsection
