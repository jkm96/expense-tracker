<div class="max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl mx-auto p-2 rounded-lg">
    <h2 class="text-2xl font-semibold mb-6">Sign In Account</h2>

    <form wire:submit.prevent="loginUser">

        <!-- Username Input -->
        <div class="mb-4">
            <label for="identifier" class="block text-sm font-semibold text-left">Username/Email</label>
            <input type="text" id="identifier" wire:model="identifier"
                   class="mt-1 block w-full p-2 bg-gray-800 border border-gray-300 rounded-md focus:bg-gray-800 focus:outline-none" required/>
            @error('identifier') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Password Input -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-left">Password</label>
            <input type="password" id="password" wire:model="password"
                   class="mt-1 block w-full p-2 bg-gray-800 border border-gray-300 rounded-md" required/>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-6 text-center">
            <button type="submit" class="bg-green-400 text-black px-2 py-1.5 rounded-md">
                Login
                <div wire:loading>
                    <svg class="animate-spin h-5 w-5 text-white mx-auto">...</svg>
                </div>
            </button>
        </div>
    </form>

    <!-- Redirect to Register Section -->
    <div class="mt-4 text-center">
        <p class="text-sm">
            Don't have an account?
            <a class="text-green-500 font-semibold hover:underline" href={{ route('register.user') }} >
                Register here
            </a>
        </p>
    </div>
</div>
