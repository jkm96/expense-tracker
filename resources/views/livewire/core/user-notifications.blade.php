<div class="relative" x-data="{ open: false }">
    <!-- Notification Bell -->
    <button @click="open = !open" class="relative">
        <svg class="w-6 h-6 text-gray-300 hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405C18.403 14.21 18 13.105 18 12V8c0-3.866-3.134-7-7-7S4 4.134 4 8v4c0 1.105-.403 2.21-1.595 3.595L1 17h5m8 4a3 3 0 01-6 0"></path>
        </svg>

        <!-- Notification Badge -->
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" @click.away="open = false"
         class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-lg py-2 z-50">

        <div class="text-gray-800 dark:text-gray-300 px-4 py-2 font-semibold border-b">
            Notifications
        </div>

        <div class="max-h-48 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-2 border-b dark:border-gray-700">
                    <!-- First Row: Pin Icon and Message -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm"><i class="fas fa-thumbtack text-sm {{ $notification->type->badgeColor() }}"></i> {{ $notification->data['message'] }}</span>
                    </div>

                    <!-- Second Row: Type Badge and Timestamp -->
                    <div class="flex items-center justify-between mt-1 text-xs">
                        <small class="font-semibold {{ $notification->type->badgeColor() }}">
                            {{ ucfirst($notification->type->value) }}
                        </small>
                        <small class="text-gray-200">{{ $notification->formattedTimestamp }}</small>
                    </div>
                </div>
            @empty
                <p class="px-4 py-2 text-gray-600 text-center">No new notifications.</p>
            @endforelse
        </div>

        @if(count($notifications) > 0)
            <button wire:click="markAllAsRead"
                    class="w-full text-center py-2 text-blue-500 hover:underline">
                Mark All as Read
            </button>
        @endif
    </div>
</div>
