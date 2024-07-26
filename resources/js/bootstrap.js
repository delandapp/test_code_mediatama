import axios from "axios";
import "flowbite";
import $ from "jquery";
import jQuery from "jquery";
import select2 from "select2";
import DataTable from "datatables.net-dt";
import Echo from "laravel-echo";
import socketio from "socket.io-client";
const csrfToken = document.head.querySelector(
    'meta[name="csrf-token"]'
).content;
window.axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
window.axios.defaults.headers.common["Accept"] = "application/json";
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.axios = axios;
window.$ = $;
window.jQuery = jQuery;
window.axios = axios;
select2();
