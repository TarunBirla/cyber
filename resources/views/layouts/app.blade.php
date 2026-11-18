<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Cyber Essentials Information Gathering') - Nexteck Limited</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    screens: {
                        'xs': '420px',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; color: #0f172a; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        /* Modern Clean AI Cards */
        .ai-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }
        .ai-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }
        
        /* Subtle AI Gradient Background Accent */
        .ai-hero-mesh {
            background-image: 
                radial-gradient(at 10% 10%, rgba(14, 165, 233, 0.08) 0px, transparent 50%),
                radial-gradient(at 90% 10%, rgba(16, 185, 129, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 90%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
        }

        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        /* Smooth touch scrolling & hidden scrollbars */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .touch-scroll {
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="min-h-full bg-white text-slate-800 antialiased flex flex-col selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <nav class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 px-3 sm:px-6 py-2.5 sm:py-3.5 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-4">
            
            <!-- Logo & Brand -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 group min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <span class="font-extrabold text-slate-900 text-sm sm:text-base tracking-tight truncate">Cyber Essentials</span>
                            <span class="ai-badge bg-brand-50 text-brand-700 border border-brand-200 hidden xs:inline-flex text-[10px] py-0.5 px-2">AI Verified</span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium truncate">Nexteck Limited • UK Scope Engine</p>
                    </div>
                </a>
            </div>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-1.5 sm:gap-3 flex-shrink-0">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm whitespace-nowrap">
                            <span class="hidden sm:inline">Admin Command Center</span>
                            <span class="sm:hidden flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Admin
                            </span>
                        </a>
                    @else
                        <a href="{{ route('company.wizard', 1) }}" class="text-xs font-semibold px-2 sm:px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 border border-brand-200 hover:bg-brand-100 transition whitespace-nowrap">
                            <span class="hidden sm:inline">Workbook (1–9)</span>
                            <span class="sm:hidden">Workbook</span>
                        </a>
                        <a href="{{ route('company.summary') }}" class="text-xs font-semibold px-2 sm:px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition whitespace-nowrap">
                            <span class="hidden sm:inline">Summary & Checks</span>
                            <span class="sm:hidden">Summary</span>
                        </a>
                    @endif

                    <!-- User Profile & Logout -->
                    <div class="flex items-center gap-1.5 sm:gap-3 pl-1.5 sm:pl-3 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-slate-900 truncate max-w-[120px]">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-slate-500 font-medium capitalize">{{ auth()->user()->role }}</div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="p-1.5 sm:p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition flex items-center justify-center" title="Sign Out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-2 sm:px-3 py-1.5 transition">Sign In</a>
                    <a href="{{ route('register') }}" class="text-xs font-bold px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700 transition shadow-sm shadow-brand-600/30 whitespace-nowrap">
                        <span class="hidden sm:inline">Register Company</span>
                        <span class="sm:hidden">Register</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 w-full pt-4">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm">
                <div class="font-bold mb-1 flex items-center gap-2 text-red-700">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Attention Required:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-red-600 mt-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 pb-16 ai-hero-mesh">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 sm:py-8 px-4 sm:px-6 text-[11px] sm:text-xs text-slate-500">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center justify-center md:justify-start gap-2 font-medium text-slate-600 text-center md:text-left">
                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                <span>UK Cyber Essentials Scope Gathering System &bull; In accordance with NCSC Standards</span>
            </div>
            <div class="text-center md:text-right text-slate-600 font-medium leading-relaxed">
                Managed by <strong class="text-slate-900">Nexteck Limited</strong> | Mohammed Nasar (CEO & Enterprise Architect) | Tel: <a href="tel:07879175585" class="hover:text-brand-600 hover:underline">07879 175585</a>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
