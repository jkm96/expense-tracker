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
                <div class="relative">
                    <!-- Bell Icon -->
                    <button id="notification-button" class="relative">
                        <svg class="w-6 h-6 text-gray-300 hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405C18.403 14.21 18 13.105 18 12V8c0-3.866-3.134-7-7-7S4 4.134 4 8v4c0 1.105-.403 2.21-1.595 3.595L1 17h5m8 4a3 3 0 01-6 0"></path>
                        </svg>

                        <!-- Badge for unread notifications -->
                        <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5" style="display: none;">
            0
        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notification-menu" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-lg py-2 hidden">
                        <div class="text-gray-800 dark:text-gray-300 px-4 py-2 font-semibold border-b">
                            Notifications
                        </div>

                        <div id="notification-dropdown" class="max-h-48 overflow-y-auto">
                            <p class="px-4 py-2 text-gray-600">No new notifications.</p>
                        </div>

                        <button onclick="window.NotificationStore.markAllAsRead()" class="w-full text-center py-2 text-blue-500 hover:bg-gray-100">Mark All as Read</button>
                    </div>
                </div>


                <!-- User profile -->
                <div x-data="{ open: false }" class="relative">
                    <!-- Profile Button -->
                    <button @click="open = !open" class="flex items-center space-x-2">
                        <img src="{{ Auth::user()->profile_image ?? asset('images/default-avatar.png') }}"
                             alt="Profile" class="w-10 h-10 rounded-full border">
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-lg rounded-lg py-2 z-50"
                         x-cloak>

                        <a href="{{ route('user.dashboard') }}"
                           class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">
                            Dashboard
                        </a>

                        <a href="{{ route('expense.manage') }}"
                           class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">
                            Expenses
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
