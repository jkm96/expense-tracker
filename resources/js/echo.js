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

document.getElementById('notification-button').addEventListener('click', () => {
    const menu = document.getElementById('notification-menu');
    menu.classList.toggle('hidden');
});


// Listen to the user's private channel
window.Echo.private(`user.${userId}`)
    .listen('.expense.notification', (data) => {
        console.log('New Expense Notification:', data);
        window.NotificationStore.addNotification(data.message);
    });


// Store notifications in a global object
window.NotificationStore = {
    notifications: [],
    unreadCount: 0,
    addNotification(notification) {
        this.notifications.unshift(notification);  // Add to the beginning
        this.unreadCount++;
        this.updateUI();
    },
    markAllAsRead() {
        this.unreadCount = 0;
        this.updateUI();
    },
    updateUI() {
        // Update the badge count
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.textContent = this.unreadCount > 0 ? this.unreadCount : '';
            badge.style.display = this.unreadCount > 0 ? 'inline' : 'none';
        }

        // Update the dropdown list
        const dropdown = document.getElementById('notification-dropdown');
        if (dropdown) {
            dropdown.innerHTML = this.notifications.length
                ? this.notifications.map(notification => `<a href="#" class="block px-4 py-2 text-xs">${notification}</a>`).join('')
                : '<p class="px-4 py-2 text-gray-600">No new notifications.</p>';
        }
    }
};
