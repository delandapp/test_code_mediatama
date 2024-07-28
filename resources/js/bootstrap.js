import axios from "axios";
import "flowbite";
import $ from "jquery";
import jQuery from "jquery";
import select2 from "select2";
import DataTable from "datatables.net-dt";
import Echo from "laravel-echo";
import socketio from "socket.io-client";
import Swal from "sweetalert2";
import "datatables.net-dt/js/dataTables.fixedColumns.js";
import "@fortawesome/fontawesome-free/js/all.js";
const csrfToken = document.head.querySelector(
    'meta[name="csrf-token"]'
).content;
window.DataTable = DataTable;
window.Swal = Swal;
window.axios = axios;
window.$ = $;
window.jQuery = jQuery;
window.axios = axios;
window.Echo = new Echo({
    client: socketio,
    broadcaster: "socket.io",
    host: window.location.hostname + ":6001",
});

window.axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
window.axios.defaults.headers.common["Accept"] = "application/json";
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
select2();
const ToastNotifikasi = Swal.mixin({
    toast: true,
    position: "top-end",
    iconColor: "white",
    customClass: {
        popup: "colored-toast",
    },
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
});
window.Echo.channel("laravel_database_approve-channel").listen(
    ".notifikasi-user-event",
    (e) => {
        const notifData = e.message;

        axios
            .get("/check-permission/approve-video")
            .then((response) => {
                if (response.data.allowed) {
                    ToastNotifikasi.fire({
                        icon: "success",
                        title: notifData,
                    });
                }
            })
            .catch((error) => {
                console.error("Terjadi kesalahan saat memeriksa izin:", error);
            });
    }
);
