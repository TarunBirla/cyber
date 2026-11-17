<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Essentials - Live Database Auto-Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0B0F19; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .glow { box-shadow: 0 0 50px -10px rgba(16, 185, 129, 0.25); }
        .glow-red { box-shadow: 0 0 50px -10px rgba(239, 68, 68, 0.25); }
    </style>
</head>
<body class="min-h-screen text-slate-200 flex items-center justify-center p-6 relative overflow-x-hidden">
    <!-- Background Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-cyan-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl w-full bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 {{ $success ? 'glow border-emerald-500/30' : 'glow-red border-red-500/30' }} relative z-10 shadow-2xl">
        
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $success ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                @if($success)
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @else
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @endif
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $success ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $success ? 'bg-emerald-400 animate-pulse' : 'bg-red-400' }}"></span>
                    {{ $success ? 'Setup Complete' : 'Setup Failed' }}
                </span>
                <h1 class="text-2xl font-bold text-white tracking-tight mt-1">Live Database Deployment Engine</h1>
            </div>
        </div>

        @if($success)
        <div class="mb-6 p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-sm space-y-2">
            <div class="flex justify-between items-center text-xs text-slate-400 pb-2 border-b border-slate-800">
                <span>Execution Time: <strong class="text-emerald-400">{{ $elapsed }}s</strong></span>
                <span>Created Tables: <strong class="text-emerald-400">{{ count($tables) }}</strong></span>
            </div>
            <div class="mono text-xs text-slate-300 space-y-1 pt-1 max-h-48 overflow-y-auto">
                @foreach($logs as $log)
                    <div>{!! $log !!}</div>
                @endforeach
            </div>
        </div>

        <!-- Credentials Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 hover:border-cyan-500/40 transition">
                <div class="text-xs font-semibold text-cyan-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Admin Account
                </div>
                <p class="text-xs text-slate-400">Email: <span class="mono text-white font-medium">{{ $admin_email }}</span></p>
                <p class="text-xs text-slate-400 mt-1">Password: <span class="mono text-emerald-400 font-medium">{{ $admin_pass }}</span></p>
            </div>
            
            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 hover:border-emerald-500/40 transition">
                <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Demo Company Account
                </div>
                <p class="text-xs text-slate-400">Email: <span class="mono text-white font-medium">{{ $demo_company_email }}</span></p>
                <p class="text-xs text-slate-400 mt-1">Password: <span class="mono text-emerald-400 font-medium">{{ $demo_company_pass }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="flex-1 py-3 px-4 rounded-xl font-semibold text-center text-sm bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 shadow-lg shadow-emerald-500/20 transition duration-200">
                Proceed to Login →
            </a>
            <a href="{{ route('home') }}" class="py-3 px-5 rounded-xl text-sm font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 transition">
                Portal Home
            </a>
        </div>
        @else
        <div class="mb-6 p-4 rounded-xl bg-red-950/40 border border-red-800/60 text-sm text-red-200">
            <p class="font-semibold mb-1">Database Error:</p>
            <p class="mono text-xs text-red-300">{{ $error }}</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-400 space-y-1">
            <p class="font-medium text-slate-300">Quick Fix Checklist:</p>
            <ul class="list-disc list-inside space-y-1 mt-2">
                <li>Check your <code class="mono text-cyan-300">.env</code> file for correct <code class="mono text-cyan-300">DB_DATABASE</code>, <code class="mono text-cyan-300">DB_USERNAME</code>, and <code class="mono text-cyan-300">DB_PASSWORD</code>.</li>
                <li>Ensure MySQL service is running on your server.</li>
                <li>Make sure the database exists or create it using phpMyAdmin/MySQL console.</li>
            </ul>
        </div>
        <div class="mt-6">
            <a href="?retry=1" class="inline-block py-2.5 px-5 rounded-xl text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/40 hover:bg-red-500/30 transition">
                Retry Setup ⟳
            </a>
        </div>
        @endif

        <div class="mt-8 text-center text-xs text-slate-500">
            Cyber Essentials Assessment Framework • UK Standard Information Gathering Engine
        </div>
    </div>
</body>
</html>
