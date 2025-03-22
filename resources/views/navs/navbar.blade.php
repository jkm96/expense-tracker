<nav class="bg-gray-900 text-white p-4 md:px-0 px-2">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <!-- Left side -->
        @auth
            <a href="{{ route('user.dashboard') }}" class="font-bold text-xl hover:underline flex items-center">
                Expense Tracker
            </a>
        @else
            <a href="{{ route('home') }}" class="font-bold text-xl hover:underline flex items-center">
                Expense Tracker
            </a>
        @endauth

        <!-- Right side -->
        <div class="flex items-center gap-3">
            @auth
                <!-- Notification Dropdown -->
                <livewire:core.user-notifications />

                <!-- User profile -->
                <div x-data="{ open: false }" class="relative">
                    <!-- Profile Button -->
                    <button @click="open = !open" class="flex items-center space-x-2">
                        <img src="{{ Auth::user()->profile_image ?? asset('images/default-avatar.png') }}"
                             alt="Profile" class="w-7 h-7 rounded-full border">
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-lg rounded-lg py-1 z-50"
                         x-cloak>

                        <a href="{{ route('user.dashboard') }}"
                           class="block px-4 py-1 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">
                            Dashboard
                        </a>

                        <a href="{{ route('expense.manage') }}"
                           class="block px-4 py-1 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">
                            Expenses
                        </a>

                        <a href="{{ route('recurring.expense.manage') }}"
                           class="block px-4 py-1 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">
                            Recurring Expenses
                        </a>

                        <a href="{{ route('settings.manage') }}"
                           class="block px-4 py-1 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">
                            Settings
                        </a>

                        <livewire:auth.logout-user />
                    </div>
                </div>
            @else
                <a href="{{ route('login.user')  }}" class="font-bold hover:underline transition-colors">
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>
