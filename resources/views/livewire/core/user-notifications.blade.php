<div class="relative flex" x-data="{ open: false }">
    <!-- Notification Bell -->
    <button @click="open = true" class="relative text-xs">
        <svg class="w-6 h-6 text-gray-300 hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405C18.403 14.21 18 13.105 18 12V8c0-3.866-3.134-7-7-7S4 4.134 4
            8v4c0 1.105-.403 2.21-1.595 3.595L1 17h5m8 4a3 3 0 01-6 0"></path>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">
                <small>{{ $unreadCount }}</small>
            </span>
        @endif
    </button>

    <!-- Side Modal -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-80 bg-white dark:bg-gray-800 shadow-lg z-50 flex flex-col">

        <div class="flex justify-between items-center p-2 border-b dark:border-gray-700">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-300 flex items-center">
                Notifications
                @if($unreadCount > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-xs font-semibold text-white bg-green-500 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </h2>

            <button @click="open = false" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-300">
                &times;
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            @if($notifications->isNotEmpty())
                @forelse($notifications as $notification)
                    <div wire:key="notification-{{ $notification->id }}"
                         wire:click="markAsRead('{{ $notification->id }}')"
                         class="px-2 py-1 border-b border-gray-600 cursor-pointer"
                         :class="{ 'bg-gray-100 dark:bg-gray-700': {{ $notification->unread() ? 'true' : 'false' }} }"
                    >

                        <div class="flex items-center space-x-1">
                            <span
                                class="text-sm {{ $notification->unread() ? 'font-bold' : '' }}">
                                <i class="fas fa-thumbtack text-sm {{ $notification->type->badgeColor() }}"></i>
                                <span class="text-xs">{{ $notification->data['message'] }}</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between mt-1 text-xs">
                            <div>
                                <small class="font-semibold {{ $notification->type->badgeColor() }}">
                                    {{ ucfirst($notification->type->value) }}
                                </small>
                                <small>
                                    <button wire:click="deleteNotification('{{ $notification->id }}')"
                                            class="text-red-500 hover:underline ml-1">
                                        Delete
                                    </button>
                                </small>
                            </div>
                            <small class="text-gray-400">{{ $notification->formattedTimestamp }}</small>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-gray-600 text-center">No new notifications.</p>
                @endforelse
            @else
                <p class="px-4 py-6 text-sm text-gray-600 text-center">No new notifications.</p>
            @endif

            @if(count($notifications) > 0)
                <div class="flex items-center justify-between px-4 py-2 border-t dark:border-gray-700">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} of {{ $notifications->total() }}
                    </span>

                    <div class="flex items-center space-x-4">
                        @if($notifications->onFirstPage())
                            <span class="text-gray-400 text-sm cursor-not-allowed">Prev</span>
                        @else
                            <button @click="$wire.goToPreviousPage()" x-on:click.stop
                                    class="text-green-500 text-sm hover:underline">
                                Prev
                            </button>
                        @endif

                        @if($notifications->hasMorePages())
                            <button @click="$wire.goToNextPage()" x-on:click.stop
                                    class="text-green-500 text-sm hover:underline">
                                Next
                            </button>
                        @else
                            <span class="text-gray-400 text-sm cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        @if(count($notifications) > 0)
            <div class="border-t dark:border-gray-700 p-2 flex justify-between">
                <button wire:click="markAllAsRead" class="text-sm text-green-500 hover:underline">
                    Mark All as Read
                </button>
                <button wire:click="deleteAllNotifications" class="text-sm text-red-500 hover:underline">
                    Delete All
                </button>
            </div>
        @endif
    </div>

    <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 bg-black bg-opacity-80 z-40"></div>
</div>
