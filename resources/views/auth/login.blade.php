@extends('layouts.app')

@section('title', 'Sign In - Cyber Essentials Portal')

@section('content')
<div class="max-w-md mx-auto px-3 sm:px-4 py-8 sm:py-16">
    <div class="ai-card rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-slate-200 shadow-xl bg-white">
        
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-brand-50 border border-brand-200 text-brand-600 mb-3 sm:mb-4 shadow-sm">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Portal Access</h1>
            <p class="text-xs text-slate-500 mt-1">Authenticate as a registered UK Company or Lead Auditor</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Corporate Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="e.g. director@company.co.uk"
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Access Password</label>
                </div>
                <div class="relative">
                    <input type="password" id="loginPassword" name="password" required
                        placeholder="••••••••••••"
                        class="w-full px-4 py-3 pr-11 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                    <button type="button" onclick="togglePasswordVisibility('loginPassword', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                        <!-- Eye open icon (show) -->
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <!-- Eye closed icon (hide) -->
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900">
                    <input type="checkbox" name="remember" class="rounded bg-white border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>Keep signed in</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-bold text-sm bg-gradient-to-r from-brand-600 to-teal-600 text-white hover:from-brand-700 hover:to-teal-700 shadow-md shadow-brand-600/25 transition duration-200 mt-2">
                Sign In to Cyber Essentials &rarr;
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>New UK organization?</span>
            <a href="{{ route('register') }}" class="text-brand-600 hover:underline font-bold ml-1">Register Company</a>
        </div>

        

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
