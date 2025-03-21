<div class="p-2">
    <div class="space-y-1 py-2">
        <div class="flex items-center justify-between">
            <h2 class="text-md font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Active Sessions
            </h2>
            <span
                class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-xs font-medium bg-green-400 text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Updated just now
                </span>
        </div>
    </div>

    <div class="">
        <div class="overflow-hidden rounded-sm bg-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="">
                        <th class="px-4 py-3 text-left font-medium">Device</th>
                        <th class="px-4 py-3 text-left font-medium">IP Address</th>
                        <th class="px-4 py-3 text-left font-medium">Last Active</th>
                        <th class="px-4 py-3 text-left font-medium">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sessions as $session)
                        <tr class="border-t border-gray-600 hover:bg-gray-500">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($session->deviceType === 'mobile')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                                            <line x1="12" y1="18" x2="12" y2="18"/>
                                        </svg>
                                    @elseif($session->deviceType === 'tablet')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                                            <line x1="12" y1="18" x2="12" y2="18"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                            <line x1="8" y1="21" x2="16" y2="21"/>
                                            <line x1="12" y1="17" x2="12" y2="21"/>
                                        </svg>
                                    @endif

                                    <div>
                                        <div class="text-sm">{{ $session->deviceName }}</div>
                                        <div class="text-xs text-gray-400">{{ $session->browserName }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="2" y1="12" x2="22" y2="12"/>
                                        <path
                                            d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    </svg>
                                    {{ $session->ip_address }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">
                                {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3">
                                @if($session->id === session()->getId())
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                    </span>
                                @else
                                    <button
                                        wire:click="confirmLogout('{{ $session->id }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <polyline points="16 17 21 12 16 7"/>
                                            <line x1="21" y1="12" x2="9" y2="12"/>
                                        </svg>
                                        Logout
                                    </button>

                                    <!-- Logout Confirmation Modal -->
                                    @if($showLogoutModal)
                                        <div x-data="{ open: @entangle('showLogoutModal') }" x-show="open"
                                             class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                                            <!-- Modal Content -->
                                            <div class="bg-gray-700 rounded-lg shadow-lg w-96 p-4">
                                                <h2 class="text-md font-bold mb-4">Confirm Session Logout</h2>
                                                <p class="text-sm">
                                                    Are you sure you want to log out this session? The user will need to
                                                    login again to access their account.
                                                </p>
                                                <div class="mt-4 flex justify-between space-x-4">
                                                    <button @click="open = false"
                                                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-1 px-2 rounded">
                                                        Cancel
                                                    </button>
                                                    <button wire:click="logoutSession"
                                                            class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-2 rounded">
                                                        Logout
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                No active sessions found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 space-y-4">
            <div class="flex items-center">
                <h3 class="text-md font-semibold">Logout from Other Devices</h3>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 items-stretch max-w-xl">
                <input
                    type="password"
                    wire:model.defer="password"
                    placeholder="Enter your password to confirm ..."
                    class="sm:w-1/2 w-full text-sm p-1 bg-gray-700 border rounded focus:ring focus:ring-blue-300"
                />

                <button
                    wire:click="confirmLogoutOtherDevices"
                    class="inline-flex items-center justify-center gap-1 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout Other Devices
                </button>
            </div>

            <p class="text-sm text-red-500">
                For security reasons, please enter your password to perform this action.
            </p>
        </div>

        <!-- Logout Other Devices Confirmation Modal -->
        @if($showLogoutOtherDevicesModal)
            <div x-data="{ open: @entangle('showLogoutOtherDevicesModal') }" x-show="open"
                 class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                <!-- Modal Content -->
                <div class="bg-gray-700 rounded-md shadow-lg w-96 p-4">
                    <h2 class="text-md font-bold mb-2">Confirm Logout Other Devices</h2>

                    <p class="text-sm">Are you sure you want to log out from all other devices?</p>

                    <div class="mt-4 flex justify-between space-x-4">
                        <button @click="open = false"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-1 px-2 rounded">
                            Cancel
                        </button>
                        <button wire:click="logoutOtherDevices"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-2 rounded">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
