import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
if (import.meta.env.VITE_PUSHER_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
        wsHost: import.meta.env.VITE_PUSHER_HOST ?? "127.0.0.1",
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
        forceTLS: false,
        enabledTransports: ["ws", "wss"]
    });
}
