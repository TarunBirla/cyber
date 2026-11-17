@extends('layouts.app')

@section('title', 'Sign In - Cyber Essentials Portal')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="ai-glass rounded-3xl p-8 border border-slate-800 ai-glow-cyan">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Portal Access</h1>
            <p class="text-xs text-slate-400 mt-1">Authenticate as a registered UK Company or Lead Auditor</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Corporate Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="e.g. director@company.co.uk"
                    class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition">
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Access Password</label>
                </div>
                <input type="password" name="password" required
                    placeholder="••••••••••••"
                    class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-300">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-0">
                    <span>Keep signed in</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-bold text-sm bg-gradient-to-r from-cyan-500 to-emerald-400 text-slate-950 hover:opacity-95 shadow-lg shadow-cyan-500/20 transition duration-200 mt-2">
                Sign In to Cyber Essentials →
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-800 text-center text-xs text-slate-400">
            <span>New UK organization?</span>
            <a href="{{ route('register') }}" class="text-cyan-400 hover:underline font-semibold ml-1">Register Company</a>
        </div>

        <div class="mt-6 p-3.5 rounded-xl bg-slate-900/60 border border-slate-800 text-[11px] text-slate-400">
            <span class="text-cyan-300 font-semibold block mb-1">💡 Live System Demo Accounts:</span>
            <div class="flex justify-between items-center py-0.5">
                <span>Admin Auditor:</span>
                <span class="font-mono text-slate-200">admin@cyberessentials.uk</span>
            </div>
            <div class="flex justify-between items-center py-0.5">
                <span>Demo Company:</span>
                <span class="font-mono text-slate-200">contact@acmeltd.co.uk</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Default Password: <span class="font-mono text-emerald-400">Admin@12345</span> / <span class="font-mono text-emerald-400">Password@123</span></div>
        </div>

    </div>
</div>
@endsection
