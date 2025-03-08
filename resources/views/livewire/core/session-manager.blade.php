<div class="p-2">
    <div class="space-y-1 py-2">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Active Sessions
            </h2>
            <span
                class="inline-flex items-center gap-1 py-1 px-2 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
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
        @if(session()->has('success'))
            <div class="bg-gray-700 border border-green-500 text-green-500 p-2 shadow-md rounded mb-2">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('info'))
            <div class="bg-gray-700 border border-blue-500 text-blue-500 p-2 shadow-md rounded mb-2">
                {{ session('info') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-gray-700 border border-red-500 text-red-500 p-2 shadow-md rounded mb-2">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden bg-gray-700">
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
                                        <div class="font-medium">{{ $session->deviceName }}</div>
                                        <div class="text-xs text-gray-400">{{ $session->browserName }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
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
                            <td class="px-4 py-3 text-gray-300">
                                {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3">
                                @if($session->id === session()->getId())
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                Current Session
                                            </span>
                                @else
                                    <!-- Logout Confirmation Modal -->
                                    @if($showLogoutModal)
                                        <div x-data="{ open: @entangle('showLogoutModal') }" x-show="open"
                                             class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                                            <!-- Modal Content -->
                                            <div class="bg-gray-700 rounded-lg shadow-lg w-96 p-6">
                                                <h2 class="text-lg font-bold">Confirm Session Logout</h2>
                                                <p class="mt-4">
                                                    Are you sure you want to log out this session? The user will need to
                                                    login again to access their account.
                                                </p>
                                                <div class="mt-6 flex justify-between space-x-4">
                                                    <button @click="open = false"
                                                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded">
                                                        Cancel
                                                    </button>
                                                    <button wire:click="logoutSession"
                                                            class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                                                        Logout
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

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
                <h3 class="text-lg font-semibold">Logout from Other Devices</h3>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 items-stretch max-w-xl">
                <input
                    type="password"
                    wire:model.defer="password"
                    placeholder="Enter your password to confirm ..."
                    class="sm:w-1/2 w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300"
                />

                <button
                    wire:click="confirmLogoutOtherDevices"
                    class="inline-flex items-center justify-center gap-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors"
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
                <div class="bg-gray-700 rounded-lg shadow-lg w-96 p-6">
                    <h2 class="text-lg font-bold">Confirm Logout Other Devices</h2>
                    <p class="mb-4">Are you sure you want to log out from all other devices?</p>
                    <div class="mt-6 flex justify-between space-x-4">
                        <button @click="open = false"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button wire:click="logoutOtherDevices"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
