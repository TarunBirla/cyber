<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Essentials - Live Database Auto-Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .ai-card { background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-3 sm:p-6 relative">
    <div class="max-w-2xl w-full ai-card rounded-2xl sm:rounded-3xl p-5 sm:p-8 relative z-10 border {{ $success ? 'border-emerald-200' : 'border-red-200' }}">
        
        <div class="flex flex-col sm:flex-row sm:items-center items-start gap-4 mb-6">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center shrink-0 {{ $success ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                @if($success)
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @else
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @endif
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-wider {{ $success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                    {{ $success ? 'Deployment Complete' : 'Deployment Failed' }}
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">Live Database Deployment Engine</h1>
                <p class="text-xs text-slate-500">Nexteck Limited &bull; UK Cyber Essentials Framework</p>
            </div>
        </div>

        @if($success)
        <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-sm space-y-2">
            <div class="flex flex-wrap justify-between items-center gap-2 text-xs text-slate-500 pb-2 border-b border-slate-200">
                <span>Execution Time: <strong class="text-slate-900">{{ $elapsed }}s</strong></span>
                <span>Created Tables: <strong class="text-emerald-600 font-bold">{{ count($tables) }}</strong></span>
            </div>
            <div class="mono text-xs text-slate-600 space-y-1 pt-1 max-h-48 overflow-y-auto">
                @foreach($logs as $log)
                    <div>{!! $log !!}</div>
                @endforeach
            </div>
        </div>

        <!-- Credentials Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 sm:mb-8">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Lead Auditor Account
                </div>
                <p class="text-xs text-slate-700 font-bold">{{ $admin_name }}</p>
                <p class="text-[11px] text-slate-500">{{ $admin_role }}</p>
                <p class="text-xs text-slate-600 mt-2">Email: <span class="mono text-slate-900 font-semibold break-all">{{ $admin_email }}</span></p>
                <p class="text-xs text-slate-600 mt-0.5">Password: <span class="mono text-emerald-600 font-bold">{{ $admin_pass }}</span></p>
            </div>
            
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Demo Company Account
                </div>
                <p class="text-xs text-slate-700 font-bold">Acme Technologies Ltd</p>
                <p class="text-[11px] text-slate-500">Sample pre-populated workbook</p>
                <p class="text-xs text-slate-600 mt-2">Email: <span class="mono text-slate-900 font-semibold break-all">{{ $demo_company_email }}</span></p>
                <p class="text-xs text-slate-600 mt-0.5">Password: <span class="mono text-emerald-600 font-bold">{{ $demo_company_pass }}</span></p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
            <a href="{{ route('login') }}" class="flex-1 py-3 px-4 rounded-xl font-bold text-center text-sm bg-gradient-to-r from-brand-600 to-teal-600 text-white hover:from-brand-700 hover:to-teal-700 shadow-md shadow-brand-600/20 transition duration-200">
                Proceed to Sign In &rarr;
            </a>
            <a href="{{ route('home') }}" class="py-3 px-5 rounded-xl text-center text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Portal Home
            </a>
        </div>
        @else
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-sm text-red-800">
            <p class="font-bold mb-1">Database Error:</p>
            <p class="mono text-xs text-red-700">{{ $error }}</p>
        </div>
        <div class="mt-6">
            <a href="?retry=1" class="inline-block py-2.5 px-5 rounded-xl text-xs font-bold bg-red-100 text-red-800 hover:bg-red-200 transition">
                Retry Setup ⟳
            </a>
        </div>
        @endif

        <div class="mt-8 text-center text-xs text-slate-400">
            Nexteck Limited &bull; Cyber Essentials UK Automated Framework
        </div>
    </div>
</body>
</html>
