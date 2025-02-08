<div class="max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl mx-auto p-2 rounded-lg">
    @if($isCreated)
        <!-- Success Message -->
        <div class="p-6 bg-green-100 text-green-800 rounded shadow">
            <h2 class="text-lg font-bold">Registration Successful!</h2>
            <p class="mt-2">Thank you for joining our directory! We’re excited to have you as part of our community promoting
                eco-friendly and sustainable living.</p>
            <p class="mt-2">A verification email has been sent to your registered email address. Please check your inbox and
                verify your email to activate your account.</p>

            <a href="{{ route('login.user') }}" class="text-black">
                <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
                    Proceed to Login
                </button>
            </a>

            <p class="mt-4 text-sm text-gray-700">
                Didn’t receive the email?
                <a href="{{ route('resend.verification') }}" class="text-blue-500 underline">
                    Resend Verification Email
                </a>
            </p>
        </div>

    @else
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Create An Account</h2>

        <form wire:submit.prevent="registerUser">

            <!-- Username Input -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-semibold text-gray-700 text-left">Username</label>
                <input type="text" id="username" wire:model="username"
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required/>
                @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 text-left">Email</label>
                <input type="email" id="email" wire:model="email"
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required/>
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold text-gray-700 text-left">Password</label>
                <input type="password" id="password" wire:model="password"
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required/>
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 text-left">Confirm
                    Password</label>
                <input type="password" id="password_confirmation" wire:model="password_confirmation"
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required/>
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="bg-green-400 text-black px-6 py-3 rounded-md">Create Account
                    <div wire:loading>
                        <svg class="animate-spin h-5 w-5 text-white mx-auto">...</svg>
                    </div>
                </button>
            </div>
        </form>

        <!-- Redirect to Login Section -->
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a class="text-green-500 font-semibold hover:underline" href={{ route('login.user') }} >
                    Login here
                </a>
            </p>
        </div>
    @endif
</div>
