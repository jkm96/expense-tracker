<footer id="footer" class="bg-gray-900 text-white p-2 md:px-0">
    <nav class="text-center items-center text-sm">
        <a href="{{ route('home') }}" class="text-gray-300 hover:text-white hover:underline text-sm">Home</a> |
        @auth
            <a href="{{ route('user.dashboard') }}" class="text-gray-300 hover:text-white hover:underline text-sm">Dashboard</a> |
            <a href="{{ route('expense.manage') }}" class="text-gray-300 hover:text-white hover:underline text-sm">Expenses</a> |
            <a href="{{ route('recurring.expense.manage') }}" class="text-gray-300 hover:text-white hover:underline text-sm">Recurring Expenses</a>
        @else
            <a href="{{ route('login.user') }}" class="text-gray-300 hover:text-white hover:underline text-sm">Login</a> |
            <a href="{{ route('register.user') }}" class="text-gray-300 hover:text-white hover:underline text-sm">Sign Up</a>
        @endauth
    </nav>

    <hr class="border-t border-gray-700 mt-1 mb-1 md:w-1/3 mx-auto">

    <!-- Copyright & Quick Links -->
    <div class="text-center text-gray-400 text-sm">
        © <span id="year"></span> Expense Tracker. All rights reserved.
    </div>

    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const footer = document.getElementById('footer');
        const contentHeight = document.body.scrollHeight;
        const viewportHeight = window.innerHeight;

        if (contentHeight < viewportHeight) {
            footer.classList.add('fixed', 'bottom-0', 'w-full');
        } else {
            footer.classList.remove('fixed', 'bottom-0', 'w-full');
            footer.classList.add('relative');
        }
    });
</script>
