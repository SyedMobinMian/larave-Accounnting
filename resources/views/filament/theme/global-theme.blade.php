{{-- Global Admin Panel Theme - applied to the entire panel via PanelsRenderHook::STYLES_AFTER --}}
@php
    // Theme colors are driven by Settings (see AdminPanelProvider::resolveTheme).
    $primary = $primary ?? '#6366f1';
    $accent = $accent ?? '#a855f7';
    $sidebarBg = $sidebarBg ?? '#ffffff';
    $primaryRgb = implode(',', array_map('hexdec', str_split(ltrim($primary, '#'), 2)));
    $accentRgb = implode(',', array_map('hexdec', str_split(ltrim($accent, '#'), 2)));
@endphp
<style>
    :root {
        --theme-primary: {{ $primary }};
        --theme-accent: {{ $accent }};
        --theme-sidebar-bg: {{ $sidebarBg }};
    }

    /* =========================================================
       Slim Sidebar
       ========================================================= */
    .fi-sidebar {
        width: 15.5rem !important;
        background: linear-gradient(180deg, var(--theme-sidebar-bg) 0%, {{ $sidebarBg === '#10112a' ? '#141433' : '#f7f8ff' }} 100%) !important;
        border-right: 1px solid rgba(99, 102, 241, 0.12);
    }

    .fi-sidebar .fi-sidebar-header {
        padding-inline: 0.75rem;
    }

    .fi-sidebar .fi-sidebar-item {
        margin-inline: 0.375rem;
        border-radius: 0.5rem;
    }

    .fi-sidebar .fi-sidebar-item .fi-icon {
        width: 1.05rem !important;
        height: 1.05rem !important;
    }

    .fi-sidebar .fi-sidebar-item .fi-sidebar-item-label {
        font-size: 0.8125rem;
    }

    .fi-sidebar .fi-sidebar-group-label {
        padding-inline: 0.875rem;
        font-size: 0.625rem;
        letter-spacing: 0.08em;
        color: #8b5cf6;
        font-weight: 700;
    }

    .fi-sidebar .fi-sidebar-item.fi-active,
    .fi-sidebar .fi-sidebar-item.fi-sidebar-item-active {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.16), rgba(168, 85, 247, 0.08)) !important;
        box-shadow: inset 3px 0 0 #6366f1;
    }

    .fi-sidebar .fi-sidebar-item:hover {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.05)) !important;
    }

    /* Slim scrollbar on sidebar */
    .fi-sidebar::-webkit-scrollbar,
    .fi-sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }
    .fi-sidebar::-webkit-scrollbar-track,
    .fi-sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }
    .fi-sidebar::-webkit-scrollbar-thumb,
    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #6366f1, #a855f7);
        border-radius: 9999px;
    }

    /* =========================================================
       Colorful App Background & Topbar
       ========================================================= */
    .fi-layout {
        background:
            radial-gradient(1200px 600px at 10% -10%, rgba(99, 102, 241, 0.10), transparent 60%),
            radial-gradient(1000px 500px at 100% 0%, rgba(168, 85, 247, 0.10), transparent 55%),
            radial-gradient(900px 500px at 50% 110%, rgba(236, 72, 153, 0.08), transparent 55%),
            linear-gradient(160deg, #f7f8ff 0%, #eef4ff 45%, #f4f0ff 100%);
        background-attachment: fixed;
    }

    .fi-topbar {
        background: rgba(255, 255, 255, 0.72) !important;
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(99, 102, 241, 0.10);
    }

    /* =========================================================
       Colorful Sidebar Brand / Logo
       ========================================================= */
    .fi-sidebar-header .fi-logo {
        background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 800;
    }

    /* =========================================================
       Slim Scrollbars - whole app
       ========================================================= */
    *::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    *::-webkit-scrollbar-track {
        background: transparent;
    }
    *::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, rgba(99, 102, 241, 0.5), rgba(168, 85, 247, 0.5));
        border-radius: 9999px;
    }
    *::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #6366f1, #a855f7);
    }
    * {
        scrollbar-width: thin;
        scrollbar-color: rgba(99, 102, 241, 0.45) transparent;
    }

    /* =========================================================
       Colorful Cards & Buttons
       ========================================================= */
    .fi-section,
    .fi-wi-stats-overview-stat {
        border-radius: 0.75rem;
        border: 1px solid rgba(99, 102, 241, 0.08);
        box-shadow: 0 2px 12px rgba(99, 102, 241, 0.06);
    }

    .fi-section .fi-section-header .fi-section-header-heading {
        font-weight: 700;
        color: #4338ca;
    }

    .fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-label {
        color: #6d28d9;
        font-weight: 600;
    }

    .fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-value {
        background: linear-gradient(90deg, #4f46e5, #a855f7, #ec4899);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 800;
    }

    /* Primary button gradient */
    .fi-btn-color-primary.fi-btn {
        background: linear-gradient(90deg, #6366f1, #a855f7) !important;
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.35);
        border: none;
        font-weight: 600;
    }
    .fi-btn-color-primary.fi-btn:hover {
        background: linear-gradient(90deg, #4f46e5, #9333ea) !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.45);
    }

    /* =========================================================
       Colorful Tables
       ========================================================= */
    .fi-ta-content thead th {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.06), rgba(168, 85, 247, 0.04));
        color: #4338ca;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .fi-ta-content tbody tr:hover {
        background: rgba(99, 102, 241, 0.04) !important;
    }

    /* =========================================================
       Tabs & Filters
       ========================================================= */
    .fi-tabs .fi-tabs-item.fi-tabs-item-active {
        background: linear-gradient(90deg, #6366f1, #a855f7);
        color: #fff;
        border-radius: 0.5rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
    }

    .fi-filters .fi-input-wrp,
    .fi-filters .fi-select-input {
        border-radius: 0.5rem !important;
    }

    /* =========================================================
       Breadcrumbs / Page Headings
       ========================================================= */
    .fi-header-heading {
        background: linear-gradient(90deg, #4338ca, #7c3aed, #db2777);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 800;
    }

    .fi-breadcrumbs li a {
        color: #6d28d9;
        font-weight: 500;
    }

    /* =========================================================
       Notifications Toast
       ========================================================= */
    .fi-notification {
        border-radius: 0.75rem;
        border-left: 4px solid #6366f1;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
    }

    /* =========================================================
       Dark-mode safe adjustments (optional graceful fallback)
       ========================================================= */
    .dark .fi-layout {
        background:
            radial-gradient(1200px 600px at 10% -10%, rgba(99, 102, 241, 0.18), transparent 60%),
            radial-gradient(1000px 500px at 100% 0%, rgba(168, 85, 247, 0.16), transparent 55%),
            radial-gradient(900px 500px at 50% 110%, rgba(236, 72, 153, 0.12), transparent 55%),
            linear-gradient(160deg, #0f1020 0%, #141530 45%, #1b1230 100%);
    }
    .dark .fi-topbar {
        background: rgba(15, 16, 32, 0.72) !important;
        border-bottom-color: rgba(99, 102, 241, 0.15);
    }
    .dark .fi-sidebar {
        background: linear-gradient(180deg, #10112a 0%, #141433 100%) !important;
        border-right-color: rgba(99, 102, 241, 0.15);
    }
    .dark .fi-sidebar .fi-sidebar-group-label {
        color: #a78bfa;
    }
    .dark .fi-sidebar .fi-sidebar-item.fi-sidebar-item-active {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.3), rgba(168, 85, 247, 0.15)) !important;
        box-shadow: inset 3px 0 0 #818cf8;
    }
    .dark .fi-section,
    .dark .fi-wi-stats-overview-stat {
        border-color: rgba(99, 102, 241, 0.15);
    }
.dark .fi-ta-content thead th {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.08));
        color: #c7d2fe;
    }
</style>

<script>
    // =========================================================
    // Sidebar Accordion: auto-collapse other groups when one expands
    // =========================================================
    document.addEventListener('DOMContentLoaded', function () {
        // Collapse all other expanded nav groups when one is expanded.
        const collapseOthers = (clickedButton) => {
            const allButtons = document.querySelectorAll('.fi-sidebar-group-button');
            allButtons.forEach((other) => {
                if (other === clickedButton) return;
                if (other.getAttribute('aria-expanded') === 'true') {
                    other.click();
                }
            });
        };

        // Event delegation handles Livewire re-renders automatically.
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.fi-sidebar-group-button');
            if (!btn) return;
            // Wait for Filament's own toggle handler to run, then collapse others.
            setTimeout(() => collapseOthers(btn), 30);
        });
    });
</script>

