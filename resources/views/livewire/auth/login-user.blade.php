<div class="flex justify-center py-10">
    <div class="w-96 p-4 rounded-sm bg-gray-700">
        <h2 class="text-md font-semibold mb-2 text-center">Sign In Account</h2>

        <form wire:submit.prevent="loginUser" class="w-full">

            <!-- Username Input -->
            <div class="mb-2">
                <label for="identifier" class="block text-sm font-semibold text-left">Username/Email</label>
                <input type="text" id="identifier" wire:model="identifier" required
                       class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                    focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"/>
                @error('identifier') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Password Input -->
            <div class="mb-2">
                <label for="password" class="block text-sm font-semibold text-left">Password</label>
                <input type="password" id="password" wire:model="password" required
                       class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                    focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"/>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2 text-center">
                <button type="submit"
                        class="w-full bg-green-400 text-black text-sm py-1.5 px-2 rounded-md flex items-center justify-center gap-2">
                    <span wire:loading.remove>Login</span>
                    <span wire:loading>
                       Loading...
                    </span>
                </button>
            </div>

            <div class="mt-2 text-center">
                <p class="text-xs">
                    No account?
                    <a class="text-green-500 font-semibold hover:underline" href={{ route('register.user') }} >
                        Register here
                    </a>
                    Forgot password?
                    <a class="text-green-500 font-semibold hover:underline" href={{ route('forgot.password') }} >
                        Reset here
                    </a>
                </p>
            </div>
        </form>

    </div>
</div>
