const socketServer = document.currentScript.getAttribute("data-socket-server");
console.log(socketServer);

document.addEventListener("DOMContentLoaded", function () {
    const socket = io(socketServer, {
        withCredentials: true,
        transports: ["websocket", "polling"],
    });

    socket.on("connect", () => {
        console.log("Connected to WebSocket server:", socket.id);
        if (typeof CURRENT_USER_ID !== "undefined") {
            const channel = `private-user.notification.${CURRENT_USER_ID}`;

            socket.emit("join", channel);

            console.log(`Joining channel: ${channel}`);
        } else {
            console.error("CURRENT_USER_ID is not defined.");
        }
    });

    // Listen for the specific event name
    socket.on(`user.notification`, function (message) {
        Swal.fire({
            title: message?.data?.title,
            html: message?.data?.message,
            position: "bottom-end",
            toast: true,
            showConfirmButton: true,
            timer: 150000,
            confirmButtonText: '<i class="fas fa-times"></i>',
            customClass: {
                popup: "notification-toast-container info-notification",
                confirmButton: "swal-confirm-button",
            },
        });
    });

    // Add debugging
    socket.onAny((eventName, ...args) => {
        console.log("Received event:", eventName, "with data:", args);
    });
});
