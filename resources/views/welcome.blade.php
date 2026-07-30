<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enterprise Accounting ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
</head>
<body class="antialiased font-sans">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-red-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <h1 class="text-3xl font-bold dark:text-white">Accounting ERP</h1>
                    </div>
                    <nav class="flex flex-1 justify-end">
                        @if (Route::has('filament.admin.auth.login'))
                            <a
                                href="{{ route('filament.admin.auth.login') }}"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </a>
                        @endif
                        @if (Route::has('filament.admin.auth.register'))
                            <a
                                href="{{ route('filament.admin.auth.register') }}"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </a>
                        @endif
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="text-center">
                        <p class="text-lg text-gray-600 dark:text-gray-400">
                            A Modern, Scalable, and Domain-Driven Enterprise Resource Planning & Financial Management System.
                        </p>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>