<footer id="footer" class="bg-black text-white p-4 md:px-0 px-2">
    <div class="container mx-auto px-4">
       Footer
    </div>
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
