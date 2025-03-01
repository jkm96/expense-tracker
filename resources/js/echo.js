import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 9080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Listen for notifications on the authenticated user channel
const userId = document.head.querySelector('meta[name="user-id"]').content;

// Listen to the user's private channel
window.Echo.private(`user.${userId}`)
    .listen('.expense.reminder', (data) => {
        console.log('New Expense Notification:', data);
        Livewire.dispatch('notificationReceived');
    });

