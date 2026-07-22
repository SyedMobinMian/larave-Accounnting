<div>
    <x-filament-panels::page>
        {{-- 1. Top Summary Metric Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Revenue Card --}}
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</div>
                <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                    ₹{{ number_format($totalRevenue, 2) }}
                </div>
            </div>

            {{-- Total Expense Card --}}
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</div>
                <div class="text-3xl font-bold text-rose-600 dark:text-rose-400 mt-2">
                    ₹{{ number_format($totalExpense, 2) }}
                </div>
            </div>

            {{-- Net Profit Card --}}
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Profit / (Loss)</div>
                <div class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} mt-2">
                    ₹{{ number_format($netProfit, 2) }}
                </div>
            </div>
        </div>

        {{-- 2. Trial Balance Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-bold text-lg text-gray-800 dark:text-gray-200">
                Trial Balance Overview
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Code</th>
                            <th class="px-6 py-3">Account Name</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Debit (₹)</th>
                            <th class="px-6 py-3 text-right">Credit (₹)</th>
                            <th class="px-6 py-3 text-right">Net Balance (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($trialBalance as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4 font-mono font-medium">{{ $row['code'] }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $row['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono">{{ number_format($row['debit'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-mono">{{ number_format($row['credit'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-mono font-bold {{ $row['balance'] >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-500' }}">
                                    {{ number_format($row['balance'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No transaction entries found in General Ledger.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament-panels::page>
</div>

