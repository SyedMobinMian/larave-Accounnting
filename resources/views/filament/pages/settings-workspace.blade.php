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
                    <x-filament::icon name="heroicon-o-cog-6-tooth" class="h-5 w-5" />
                    <span>Settings</span>
                </div>

                @php
                    $expandedGroup = null;
                    $activeCatKey = null;
                    foreach ($sortedGroups as $groupName => $groupItems) {
                        foreach ($groupItems as $catKey => $cat) {
                            if ($catKey === $activeCategory) {
                                $expandedGroup = $groupName;
                                $activeCatKey = $catKey;
                                break 2;
                            }
                        }
                    }
                    if ($expandedGroup === null) {
                        $firstGroup = array_key_first($sortedGroups);
                        $expandedGroup = $firstGroup;
                    }
                @endphp

                <nav
                    class="settings-workspace__nav-list"
                    x-data="{ openGroups: @js([$expandedGroup]) }"
                >
                    @foreach($sortedGroups as $groupName => $groupItems)
                        <div class="settings-workspace__nav-group">
                            <button
                                type="button"
                                class="settings-workspace__nav-group-header"
                                @click="openGroups = openGroups.includes(@js($groupName)) ? [] : [@js($groupName)]"
                                :class="openGroups.includes(@js($groupName)) ? 'is-open' : ''"
                            >
                                <span class="settings-workspace__nav-group-dot"></span>
                                <span class="settings-workspace__nav-group-label">{{ $groupName }}</span>
                                <span
                                    class="settings-workspace__nav-group-caret"
                                    :class="openGroups.includes(@js($groupName)) ? 'is-rotated' : ''"
                                >
                                    <x-filament::icon name="heroicon-m-chevron-down" class="h-3 w-3" />
                                </span>
                            </button>

                            <div
                                class="settings-workspace__nav-group-body"
                                x-show="openGroups.includes(@js($groupName))"
                                x-transition.opacity.duration.200ms
                                x-data="{ openCategory: @js($activeCatKey) }"
                            >
                                @foreach($groupItems as $categoryKey => $category)
                                    @php
                                        $hasNestedTabs = count($category['tabs'] ?? []) > 0;
                                    @endphp
                                    <div class="settings-workspace__nav-category">
                                        <button
                                            type="button"
                                            class="settings-workspace__nav-item {{ $activeCategory === $categoryKey ? 'settings-workspace__nav-item--active' : '' }}"
                                            wire:click="$set('activeCategory', '{{ $categoryKey }}')"
                                            @if($hasNestedTabs)
                                                @click.stop="openCategory = (openCategory === @js($categoryKey)) ? null : @js($categoryKey)"
                                            @endif
                                        >
                                            @if(isset($category['icon']))
                                                <x-filament::icon name="{{ $category['icon'] }}" class="h-4 w-4" />
                                            @endif
                                            <span class="settings-workspace__nav-item-label">{{ $category['label'] }}</span>
                                            @if($hasNestedTabs)
                                                <span
                                                    class="settings-workspace__nav-item-caret"
                                                    :class="openCategory === @js($categoryKey) ? 'is-rotated' : ''"
                                                >
                                                    <x-filament::icon name="heroicon-m-chevron-right" class="h-3 w-3" />
                                                </span>
                                            @endif
                                        </button>

                                        @if($hasNestedTabs)
                                            <div
                                                class="settings-workspace__nav-submenu"
                                                x-show="openCategory === @js($categoryKey)"
                                                x-transition.opacity.duration.200ms
                                            >
                                                @foreach($category['tabs'] as $tabKey => $tab)
                                                    <button
                                                        type="button"
                                                        class="settings-workspace__nav-subitem {{ $activeCategory === $categoryKey && $activeTab === $tabKey ? 'settings-workspace__nav-subitem--active' : '' }}"
                                                        wire:click="$set('activeTab', '{{ $tabKey }}')"
                                                    >
                                                        @if(isset($tab['icon']))
                                                            <x-filament::icon name="{{ $tab['icon'] }}" class="h-3.5 w-3.5" />
                                                        @endif
                                                        <span>{{ $tab['label'] }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
                                        <x-filament::icon name="{{ $tab['icon'] }}" class="h-3.5 w-3.5" />
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
            background: linear-gradient(135deg, #faf7ff 0%, #eef5ff 50%, #f0fdf4 100%);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .settings-workspace__nav {
            width: 264px;
            min-width: 264px;
            border-right: 1px solid rgba(120, 120, 200, 0.12);
            background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            scrollbar-width: thin;
            scrollbar-color: rgba(120, 120, 200, 0.35) transparent;
        }

        .settings-workspace__nav-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 1rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(120, 120, 200, 0.12);
            color: #4f46e5;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.08), rgba(168, 85, 247, 0.06));
        }

        .settings-workspace__nav-list {
            padding: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .settings-workspace__nav-group {
            display: flex;
            flex-direction: column;
        }

        .settings-workspace__nav-group-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.6rem;
            border-radius: 0.375rem;
            border: none;
            background: transparent;
            cursor: pointer;
            width: 100%;
            text-align: left;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            transition: background 0.15s ease;
        }

        .settings-workspace__nav-group-header:hover {
            background: rgba(99, 102, 241, 0.06);
        }

        .settings-workspace__nav-group-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            flex-shrink: 0;
        }

        .settings-workspace__nav-group-label {
            flex: 1;
            min-width: 0;
        }

        .settings-workspace__nav-group-caret {
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            color: #9ca3af;
        }

        .settings-workspace__nav-group-caret.is-rotated {
            transform: rotate(180deg);
        }

        .settings-workspace__nav-group-body {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            padding-left: 0.375rem;
        }

        .settings-workspace__nav-category {
            display: flex;
            flex-direction: column;
        }

        .settings-workspace__nav-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.625rem;
            border-radius: 0.375rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            color: #4b5563;
            transition: all 0.15s ease;
            text-align: left;
            width: 100%;
        }

        .settings-workspace__nav-item:hover {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.05));
            color: #4f46e5;
        }

        .settings-workspace__nav-item--active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.14), rgba(168, 85, 247, 0.08));
            color: #4f46e5;
            font-weight: 600;
            box-shadow: inset 3px 0 0 #6366f1;
        }

        .settings-workspace__nav-item-label {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .settings-workspace__nav-item-caret {
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            color: #9ca3af;
        }

        .settings-workspace__nav-item-caret.is-rotated {
            transform: rotate(90deg);
        }

        .settings-workspace__nav-submenu {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            padding-left: 1rem;
            border-left: 1px solid rgba(99, 102, 241, 0.15);
            margin-left: 1.1rem;
        }

        .settings-workspace__nav-subitem {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3125rem 0.625rem;
            border-radius: 0.375rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 0.6875rem;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.15s ease;
            text-align: left;
            width: 100%;
        }

        .settings-workspace__nav-subitem:hover {
            background: rgba(168, 85, 247, 0.08);
            color: #7c3aed;
        }

        .settings-workspace__nav-subitem--active {
            background: linear-gradient(90deg, rgba(168, 85, 247, 0.12), rgba(236, 72, 153, 0.06));
            color: #7c3aed;
            font-weight: 600;
            box-shadow: inset 3px 0 0 #a855f7;
        }

        .settings-workspace__content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: transparent;
        }

        .settings-workspace__tabs {
            border-bottom: 1px solid rgba(120, 120, 200, 0.12);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            padding: 0 1.25rem;
        }

        .settings-workspace__tabs-list {
            display: flex;
            gap: 0.25rem;
            overflow-x: auto;
            padding: 0.5rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(120, 120, 200, 0.35) transparent;
        }

        .settings-workspace__tab {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.375rem 0.875rem;
            border-radius: 0.5rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 0.6875rem;
            font-weight: 500;
            color: #6b7280;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .settings-workspace__tab:hover {
            background: rgba(99, 102, 241, 0.08);
            color: #4f46e5;
        }

        .settings-workspace__tab--active {
            background: linear-gradient(90deg, #6366f1, #a855f7);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
        }

        .settings-workspace__tab-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(120, 120, 200, 0.35) transparent;
        }

        /* Slim scrollbars */
        .settings-workspace__nav::-webkit-scrollbar,
        .settings-workspace__tabs-list::-webkit-scrollbar,
        .settings-workspace__tab-content::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .settings-workspace__nav::-webkit-scrollbar-track,
        .settings-workspace__tabs-list::-webkit-scrollbar-track,
        .settings-workspace__tab-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .settings-workspace__nav::-webkit-scrollbar-thumb,
        .settings-workspace__tabs-list::-webkit-scrollbar-thumb,
        .settings-workspace__tab-content::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #6366f1, #a855f7);
            border-radius: 9999px;
        }

        .settings-workspace__nav::-webkit-scrollbar-thumb:hover,
        .settings-workspace__tabs-list::-webkit-scrollbar-thumb:hover,
        .settings-workspace__tab-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4f46e5, #7c3aed);
        }
    </style>
</x-filament-panels::page>

