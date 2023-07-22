
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

try {
    window.$ = window.jQuery = require('jquery');
    window.Popper = require('@popperjs/core');
    window.bootstrap = require('bootstrap');

} catch (e) {}

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['CSRF-TOKEN'] = document.getElementById('token').value;

window.url = document.getElementById('base_url').value;
window.app_path = document.getElementById('app_path').value;

// window.axios.defaults.headers.common['pusher_app_key'] = document.getElementById('pusher_app_key').value;
// window.axios.defaults.headers.common['pusher_app_cluster'] = document.getElementById('pusher_app_cluster').value;


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */


import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

/*window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});*/
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: document.getElementById("f_pusher_app_key") ? document.getElementById("f_pusher_app_key").value : "",
    cluster: document.getElementById("f_pusher_app_cluster") ? document.getElementById("f_pusher_app_cluster").value : "",
    forceTLS: true
});
