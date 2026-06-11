<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>{{ config('app.name', 'Communa') }}</title>
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased text-gray-900">
    @auth
        <nav class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900">
                        {{ config('app.name', 'Communa') }}
                    </a>
                    <div class="flex flex-wrap gap-2 text-sm font-medium">
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Dashboard</a>
                        <a href="{{ route('tickets.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('tickets.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Tickets</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Users</a>
                            <a href="{{ route('units.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('units.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Units</a>
                        @endif
                        @if(auth()->user()->isResident() || auth()->user()->isBillingStaff() || auth()->user()->isAdmin())
                            <a href="{{ route('bills.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('bills.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Bills</a>
                            <a href="{{ route('payments.index') }}" class="rounded-md px-3 py-2 {{ request()->routeIs('payments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Payments</a>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="font-medium text-gray-600 hover:text-gray-900">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-medium text-red-600 hover:text-red-800">Log out</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    <main>
        @if(session('success') || session('error') || session('info'))
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
