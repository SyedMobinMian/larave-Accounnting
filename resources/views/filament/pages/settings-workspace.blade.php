@php
    $categories = $this->getCategories();

    // Organize categories by group
    $groupedCategories = [];
    $groupOrder = ['Core', 'Business', 'Appearance', 'Administration', 'Platform'];
    foreach ($categories as $key => $category) {
        $group = $category['group'] ?? 'Other';
        $groupedCategories[$group][$key] = $category;
    }
    // Sort groups by defined order
    $sortedGroups = [];
    foreach ($groupOrder as $g) {
        if (isset($groupedCategories[$g])) {
            $sortedGroups[$g] = $groupedCategories[$g];
        }
    }
    // Add any remaining groups at the end
    foreach ($groupedCategories as $g => $items) {
        if (!isset($sortedGroups[$g])) {
            $sortedGroups[$g] = $items;
        }
    }
@endphp

<x-filament-panels::page>
    <div class="settings-workspace">
        <div class="settings-workspace__layout">
            <!-- Left Navigation Panel -->
            <div class="settings-workspace__nav">
                <div class="settings-workspace__nav-header">
                    <x-filament::icon name="heroicon-o-cog-6-tooth" class="h-6 w-6" />
                    <span>Settings</span>
                </div>
                <nav class="settings-workspace__nav-list">
                    @foreach($sortedGroups as $groupName => $groupItems)
                        <div class="settings-workspace__nav-group-label">{{ $groupName }}</div>
                        @foreach($groupItems as $categoryKey => $category)
                            <button
                                wire:click="$set('activeCategory', '{{ $categoryKey }}')"
                                class="settings-workspace__nav-item {{ $activeCategory === $categoryKey ? 'settings-workspace__nav-item--active' : '' }}"
                                type="button"
                            >
                                @if(isset($category['icon']))
                                    <x-filament::icon name="{{ $category['icon'] }}" class="h-5 w-5" />
                                @endif
                                <span>{{ $category['label'] }}</span>
                            </button>
                        @endforeach
                    @endforeach
                </nav>
            </div>

            <!-- Main Content Area -->
            <div class="settings-workspace__content">
                <!-- Top Tab Navigation -->
@php $tabs = $this->tabs; @endphp
                @if(count($tabs) > 0)
                    <div class="settings-workspace__tabs">
                        <div class="settings-workspace__tabs-list">
                            @foreach($tabs as $tabKey => $tab)
                                <button
                                    wire:click="$set('activeTab', '{{ $tabKey }}')"
                                    class="settings-workspace__tab {{ $activeTab === $tabKey ? 'settings-workspace__tab--active' : '' }}"
                                    type="button"
                                >
                                    @if(isset($tab['icon']))
                                        <x-filament::icon name="{{ $tab['icon'] }}" class="h-4 w-4" />
                                    @endif
                                    <span>{{ $tab['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Tab Content -->
                <div class="settings-workspace__tab-content">
                    <x-filament-panels::form wire:submit="save">
                        {{ $this->form }}

                        <div class="flex justify-end mt-6">
                            <x-filament::button type="submit">
                                Save Settings
                            </x-filament::button>
                        </div>
                    </x-filament-panels::form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .settings-workspace {
            height: calc(100vh - 8rem);
            margin: -1.5rem;
        }

        .settings-workspace__layout {
            display: flex;
            height: 100%;
            background: var(--filament-forms-bg);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .settings-workspace__nav {
            width: 260px;
            min-width: 260px;
            border-right: 1px solid var(--filament-forms-border-color);
            background: var(--filament-sidebar-bg, #fff);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .settings-workspace__nav-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem 1.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            border-bottom: 1px solid var(--filament-forms-border-color);
            color: var(--filament-forms-label-color);
        }

        .settings-workspace__nav-list {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .settings-workspace__nav-group-label {
            padding: 0.75rem 1rem 0.375rem;
            font-size: 0.675rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--filament-forms-label-color, #6b7280);
            opacity: 0.6;
        }

        .settings-workspace__nav-group-label:not(:first-child) {
            margin-top: 0.5rem;
            border-top: 1px solid var(--filament-forms-border-color, rgba(0,0,0,0.06));
            padding-top: 0.75rem;
        }

        .settings-workspace__nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--filament-forms-label-color);
            transition: all 0.15s ease;
            text-align: left;
            width: 100%;
        }

        .settings-workspace__nav-item:hover {
            background: var(--filament-sidebar-item-hover-bg, rgba(0,0,0,0.03));
        }

        .settings-workspace__nav-item--active {
            background: var(--filament-sidebar-item-active-bg, rgba(251, 191, 36, 0.1));
            color: var(--filament-sidebar-item-active-color, #d97706);
            font-weight: 600;
        }

        .settings-workspace__content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--filament-forms-bg);
        }

        .settings-workspace__tabs {
            border-bottom: 1px solid var(--filament-forms-border-color);
            background: var(--filament-sidebar-bg, #fff);
            padding: 0 1.5rem;
        }

        .settings-workspace__tabs-list {
            display: flex;
            gap: 0.25rem;
            overflow-x: auto;
            padding: 0.75rem 0;
        }

        .settings-workspace__tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--filament-forms-label-color);
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .settings-workspace__tab:hover {
            background: var(--filament-sidebar-item-hover-bg, rgba(0,0,0,0.03));
        }

        .settings-workspace__tab--active {
            background: var(--filament-sidebar-item-active-bg, rgba(251, 191, 36, 0.1));
            color: var(--filament-sidebar-item-active-color, #d97706);
            font-weight: 600;
        }

        .settings-workspace__tab-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
    </style>
</x-filament-panels::page>

