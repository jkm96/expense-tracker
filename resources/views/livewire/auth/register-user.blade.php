<div class="flex justify-center py-10">
    <div class="w-96 p-4 rounded-sm bg-gray-700">
        @if($isCreated)
            <!-- Success Message -->
            <div class="p-2">
                <h2 class="text-md font-bold">Registration Successful!</h2>
                <p class="mt-2 text-sm">Thank you for having an account with Expense Tracker! A simple web application to help you track and categorize unplanned daily expenses.</p>
                <p class="mt-2 text-sm">A verification email has been sent to your registered email address. Please check your inbox and
                    verify your email to activate your account.</p>

                <a href="{{ route('login.user') }}">
                    <button class="mt-4 bg-green-400 text-white text-sm py-1 px-2 rounded-md">
                        Proceed to Login
                    </button>
                </a>

{{--                <p class="mt-4 text-sm">--}}
{{--                    Didn’t receive the email?--}}
{{--                    <a href="{{ route('resend.verification') }}" class="text-blue-500 underline">--}}
{{--                        Resend Verification Email--}}
{{--                    </a>--}}
{{--                </p>--}}
            </div>

        @else
            <h2 class="text-md font-semibold mb-2">Create An Account</h2>

            <form wire:submit.prevent="registerUser" class="w-full">

                <!-- Username Input -->
                <div class="mb-2">
                    <label for="username" class="block text-sm font-semibold  text-left">Username</label>
                    <input type="text" id="username" wire:model="username" required
                           class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                    focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50"/>
                    @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

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
                    <button type="submit" class="w-full bg-green-400 text-black text-sm py-1.5 px-2 rounded-md">Create Account
                        <div wire:loading>
                            <svg class="animate-spin h-5 w-5 text-white mx-auto">...</svg>
                        </div>
                    </button>
                </div>

                <div class="mt-2 text-center">
                    <p class="text-sm">
                        Already have an account?
                        <a class="text-green-500 font-semibold hover:underline" href={{ route('login.user') }} >
                            Login here
                        </a>
                    </p>
                </div>
            </form>
        @endif
    </div>
</div>
