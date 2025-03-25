<div class="flex items-center justify-center py-10">
    <div class="w-96 p-4 rounded-sm shadow-lg bg-gray-700">
        <h2 class="text-md font-semibold text-center text-white mb-2">Reset Password</h2>

        <form wire:submit.prevent="resetPassword" class="w-full">
            <!-- Email Input -->
            <div class="mb-2">
                <label for="email" class="block text-sm font-semibold  text-left">Email</label>
                <input type="email" id="email" wire:model="email" required
                       class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                    focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"/>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-2">
                <label for="password" class="block text-sm font-semibold  text-left">Password</label>
                <input type="password" id="password" wire:model="password" required
                       class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                    focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"/>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-2">
                <label for="password_confirmation" class="block text-sm font-semibold  text-left">Confirm
                    Password</label>
                <input type="password" id="password_confirmation" wire:model="password_confirmation" required
                       class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                    focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"/>
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2 text-center">
                <button type="submit"
                        class="w-full bg-green-400 text-black text-sm py-1.5 px-2 rounded-md flex items-center justify-center gap-2">
                    <span wire:loading.remove> Reset Password</span>
                    <span wire:loading>
                       Loading...
                    </span>
                </button>
            </div>
        </form>
    </div>

    @script
    <script>
        Livewire.on('delayed-redirect', (url) => {
            setTimeout(() => {
                window.location.href = url;
            }, 2000);
        });
    </script>
    @endscript
</div>
