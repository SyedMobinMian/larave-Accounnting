<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Enterprise Accounting & Business ERP') }}</title>
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e2e8f0;
            overflow-x: hidden;
        }
        .bg-grid {
            background-image: linear-gradient(rgba(148, 163, 184, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(148, 163, 184, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
        }
        .bg-glow {
            position: fixed;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            z-index: 0;
        }
        .glow-1 { top: -200px; right: -200px; background: #f59e0b; }
        .glow-2 { bottom: -200px; left: -200px; background: #3b82f6; }
        .glow-3 { top: 50%; left: 50%; transform: translate(-50%, -50%); background: #8b5cf6; opacity: 0.08; }
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        .nav-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .nav-logo svg { width: 2rem; height: 2rem; color: #f59e0b; }
        .nav-logo span {
            font-size: 1.25rem; font-weight: 700;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-links { display: flex; align-items: center; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-links a:not(.btn-primary) { color: #94a3b8; }
        .nav-links a:not(.btn-primary):hover { color: #f1f5f9; background: rgba(148, 163, 184, 0.1); }
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: #fff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); }
        .btn-outline { border: 1px solid rgba(148, 163, 184, 0.3); color: #e2e8f0 !important; }
        .btn-outline:hover { border-color: #f59e0b; background: rgba(245, 158, 11, 0.1) !important; }
        .hero {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 6rem 2rem 4rem;
            text-align: center;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            font-size: 0.875rem; font-weight: 500; color: #fbbf24;
            margin-bottom: 2rem;
        }
        .hero-badge svg { width: 1rem; height: 1rem; }
        h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800; line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #f1f5f9 0%, #94a3b8 50%, #f1f5f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        h1 span {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-subtitle {
            max-width: 600px;
            font-size: 1.125rem; line-height: 1.75;
            color: #94a3b8; margin-bottom: 2.5rem;
        }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; }
        .hero-actions a {
            padding: 0.875rem 2rem; border-radius: 0.75rem;
            font-size: 1rem; font-weight: 600;
            text-decoration: none; transition: all 0.3s;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .hero-actions .btn-primary { font-size: 1rem; padding: 0.875rem 2rem; }
        .hero-actions .btn-outline { border: 1px solid rgba(148, 163, 184, 0.3); color: #e2e8f0; }
        .hero-actions .btn-outline:hover { border-color: #f59e0b; background: rgba(245, 158, 11, 0.1); }
        .stats {
            position: relative; z-index: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        .stat-card:hover { border-color: rgba(245, 158, 11, 0.3); transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); }
        .stat-number {
            font-size: 2rem; font-weight: 800;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }
        .features { position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; padding: 4rem 2rem; }
        .features-title { text-align: center; font-size: 2rem; font-weight: 700; margin-bottom: 3rem; color: #f1f5f9; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .feature-card { background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(148, 163, 184, 0.08); border-radius: 1rem; padding: 1.75rem; transition: all 0.3s; }
        .feature-card:hover { border-color: rgba(245, 158, 11, 0.2); background: rgba(30, 41, 59, 0.6); transform: translateY(-2px); }
        .feature-icon { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; font-size: 1.5rem; }
        .feature-card h3 { font-size: 1.125rem; font-weight: 600; color: #f1f5f9; margin-bottom: 0.5rem; }
        .feature-card p { font-size: 0.875rem; color: #64748b; line-height: 1.6; }
        footer { position: relative; z-index: 1; text-align: center; padding: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1); color: #475569; font-size: 0.875rem; }
        @media (max-width: 768px) { nav { padding: 0.75rem 1rem; } .nav-links a { padding: 0.375rem 0.75rem; font-size: 0.8125rem; } .hero { padding: 5rem 1rem 3rem; } .stat-number { font-size: 1.5rem; } }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="bg-glow glow-1"></div>
    <div class="bg-glow glow-2"></div>
    <div class="bg-glow glow-3"></div>

    <nav>
        <a href="{{ url('/') }}" class="nav-logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm1.5 0a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
            </svg>
            <span>LedgerPro</span>
        </a>
        <div class="nav-links">
            @auth
                <a href="{{ url('/admin') }}">Dashboard</a>
            @else
                <a href="{{ url('/admin/login') }}">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ url('/admin/register') }}">Register</a>
                @endif
                <a href="{{ url('/admin/login') }}" class="btn-primary">Get Started</a>
            @endauth
        </div>
    </nav>

    <section class="hero">
        <div class="hero-badge">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Enterprise-Grade Security
        </div>
        <h1>Enterprise<br><span>Accounting & Business</span><br>ERP System</h1>
        <p class="hero-subtitle">
            A modern, scalable, and domain-driven enterprise resource planning & financial management system built for growing businesses.
        </p>
        <div class="hero-actions">
            @auth
                <a href="{{ url('/admin') }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                    Go to Dashboard
                </a>
            @else
                <a href="{{ url('/admin/login') }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Login to Dashboard
                </a>
                <a href="#features" class="btn-outline">
                    Explore Features
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 13l5 5 5-5"/><path d="M7 6l5 5 5-5"/></svg>
                </a>
            @endauth
        </div>
    </section>

    <section class="stats">
        <div class="stat-card">
            <div class="stat-number">10+</div>
            <div class="stat-label">Core Accounting Modules</div>
        <div class="stat-card">
            <div class="stat-number">99.9%</div>
            <div class="stat-label">Uptime Reliability</div>
        <div class="stat-card">
            <div class="stat-number">256-bit</div>
            <div class="stat-label">Encryption Standard</div>
        <div class="stat-card">
            <div class="stat-number">50+</div>
            <div class="stat-label">Pre-built Reports</div>
    </section>

    <section class="features" id="features">
        <h2 class="features-title">Everything You Need to Run Your Business</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                </div>
                <h3>Chart of Accounts</h3>
                <p>Comprehensive double-entry accounting with customizable chart of accounts, journal entries, and financial reporting.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <h3>Invoicing & Billing</h3>
                <p>Create, send, and manage professional invoices with automated payment tracking and recurring billing.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Expense Management</h3>
                <p>Track and categorize expenses with receipt scanning, approval workflows, and real-time budget monitoring.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                </div>
                <h3>Purchase Orders</h3>
                <p>Streamline procurement with automated purchase orders, vendor management, and goods receipt tracking.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10h-10V2Z"/><path d="M14 2a10 10 0 0 1 8 8h-8V2Z"/><path d="M14 12h8"/></svg>
                </div>
                <h3>Financial Reports</h3>
                <p>Generate balance sheets, profit & loss statements, cash flow reports, and customizable financial analytics.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(249, 115, 22, 0.15); color: #f97316;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3>Client & CRM</h3>
                <p>Manage clients, contacts, and sales pipeline with integrated communication history and lead tracking.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3>Role-Based Access</h3>
                <p>Granular permissions and role management with Filament Shield. Control who sees what across the entire system.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(250, 204, 21, 0.15); color: #facc15;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Banking & Reconciliation</h3>
                <p>Connect bank accounts, import statements, and automate reconciliation with your accounting records.</p>
            </div>
    </section>

    <footer>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'LedgerPro') }}. All rights reserved. Built with Laravel & Filament.</p>
    </footer>
</body>
</html>
