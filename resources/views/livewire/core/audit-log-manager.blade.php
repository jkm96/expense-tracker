<div class="p-2">
    <h2 class="text-md font-bold flex items-center mb-2">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
             xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 6h14M5 12h14M5 18h14"></path>
        </svg>
        Audit Logs
    </h2>

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">

        <input type="text" wire:model.live.debounce.500ms="search"
               autocomplete="off"
               autocapitalize="off"
               class="px-1.5 py-0.5 bg-gray-800 text-sm border rounded  border-gray-500 focus:outline-none
                              focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"
               placeholder="🔍Search logs...">

        <div class="flex items-center space-x-2 mt-1 md:mt-0">
            <select wire:model.live="actionFilter"
                    class="w-full px-1.5 py-0.5 bg-gray-800 text-sm border rounded  border-gray-500 focus:outline-none
                              focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                <option value="all">All Actions</option>
                @foreach($auditActions as $action)
                    <option value="{{ $action->value }}">{{ ucfirst($action->value) }}</option>
                @endforeach
            </select>

            <select wire:model.live="perPage"
                    class="w-full px-1.5 py-0.5 bg-gray-800 text-sm border rounded  border-gray-500 focus:outline-none
                              focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                <option value="5">5 per page</option>
                <option value="10">10 per page</option>
                <option value="20">20 per page</option>
            </select>
        </div>
    </div>

    <!-- Log Cards -->
    <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1">
        @forelse ($auditLogs as $log)
            <div class="bg-gray-700 w-full shadow-md overflow-hidden rounded-sm">
                <div class="p-2">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-2">
                            <div class="mt-0.5">
                                <div class="flex items-center justify-center">
                                    <i class="{{ $log->action_traits['icon'] }} {{ $log->action_traits['color'] }}"></i>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                <span
                                    class="border {{ $log->action_traits['border'] }} px-1 text-xs font-semibold rounded-full">
                                    {{ $log->action }}
                                </span>
                                    <span class="text-sm">
                                    {{ $log->formatted_model }} #{{ $log->model_id }}
                                </span>
                                </div>

                                <div class="flex items-center mt-1 text-xs  text-gray-500">
                                    <i class="fas fa-user mr-1 text-xs "></i>
                                    <span>{{ $log->user->username ?? 'Unknown' }}</span>
                                    <span class="mx-1">•</span>
                                    <i class="fas fa-clock mr-1 text-xs "></i>
                                    <span class="font-mono">{{ $log->formatted_created_at }}</span>
                                </div>

                                <div class="mt-1 text-sm">
                                    {{ $log->activity }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-network-wired mr-1"></i>
                                    IP: {{ $log->ip_address }}
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Toggle -->
                        <button type="button" class="h-8 w-8 p-0 text-gray-500 hover:text-gray-300 transition"
                                wire:click="toggleLog({{ $log->id }})">
                            <i class="{{ isset($expandedLogs[$log->id]) && $expandedLogs[$log->id] ? 'fas fa-chevron-up' : 'fas fa-chevron-down' }}"></i>
                            <span class="sr-only">Toggle details</span>
                        </button>
                    </div>

                    <!-- Collapsible Content -->
                    @if(isset($expandedLogs[$log->id]) && $expandedLogs[$log->id])
                        <div class="mt-3">
                            <hr class="my-3">
                            <div class="text-sm font-medium mb-2">Changes:</div>
                            <div class="pl-11">
                                <pre class="text-xs p-3 rounded-md overflow-auto max-h-48 whitespace-pre-wrap">
                                    {{ json_encode($filteredChanges[$log->id] ?? [], JSON_PRETTY_PRINT) }}
                                </pre>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        @empty
            <p class="text-center col-span-full text-gray-400">🚫 No audit logs found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between text-sm text-gray-300">
        <div>
            Showing
            <span class="font-medium">{{ $auditLogs->firstItem() }}</span>
            to
            <span class="font-medium">{{ $auditLogs->lastItem() }}</span>
            of
            <span class="font-medium">{{ $auditLogs->total() }}</span>
            results
        </div>

        <div class="space-x-2">
            @if ($auditLogs->onFirstPage())
                <span class="text-gray-500">Prev</span>
            @else
                <button wire:click="previousPage" class="text-blue-500 hover:underline">Prev</button>
            @endif

            @if ($auditLogs->hasMorePages())
                <button wire:click="nextPage" class="text-blue-500 hover:underline">Next</button>
            @else
                <span class="text-gray-500">Next</span>
            @endif
        </div>
    </div>

</div>
