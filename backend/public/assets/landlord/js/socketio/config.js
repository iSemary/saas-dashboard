var socket = io("http://192.168.10.10:3000");
socket.on("test-channel:App\\Events\\TestNotification", function (message) {
    $("#power").text(
        parseInt($("#power").text()) + parseInt(message.data.power)
    );
});
