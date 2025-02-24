require("dotenv").config();
const express = require("express");
const http = require("http");
const socketIO = require("socket.io");
const cors = require("cors");

const app = express();
const server = http.createServer(app);

const allowedOrigins = process.env.ALLOWED_ORIGINS
    ? process.env.ALLOWED_ORIGINS.split(",")
    : [];

// Middleware should come before routes
app.use((req, res, next) => {
    console.log(`${new Date().toISOString()} - ${req.method} ${req.url}`);
    next();
});

app.use(express.json());

app.use(
    cors({
        origin: allowedOrigins,
        methods: ["GET", "POST"],
        credentials: true,
    })
);

const io = socketIO(server, {
    cors: {
        origin: allowedOrigins,
        methods: ["GET", "POST"],
        credentials: true,
    },
});

app.get("/health", (req, res) => {
    res.status(200).json({ success: true, message: "Websocket server is running" });
});

// Handle Laravel broadcasts
app.post("/broadcast", (req, res) => {
    try {
        const { channel, event, data } = req.body;

        if (!channel.name || !event) {
            console.log("Invalid broadcast request - missing channel or event");
            return res
                .status(400)
                .json({ success: false, error: "Missing channel or event" });
        }

        io.to(channel.name).emit(event, data);

        res.status(200).json({ success: true });
    } catch (error) {
        console.error("Error in broadcast endpoint:", error);
        res.status(500).json({ success: false, error: error.message });
    }
});

// Socket.IO connection handling
io.on("connection", (socket) => {
    console.log("Client connected, socket ID:", socket.id);

    // Handle channel joining
    socket.on("join", (channel) => {
        socket.join(channel);
    });

    // Debug all events
    socket.onAny((eventName, ...args) => {
        console.log("Received event:", eventName, args);
    });

    socket.on("disconnect", () => {
        console.log("Client disconnected:", socket.id);
    });

    socket.on("getRooms", (callback) => {
        const rooms = Array.from(socket.rooms);
        callback(rooms);
    });
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error("Global error handler:", err);
    res.status(500).json({ success: false, error: "Internal server error" });
});

const NODE_PORT = process.env.NODE_PORT || 4000;
server.listen(NODE_PORT, () => {
    console.log(`Socket.io server running on port ${NODE_PORT}`);
});
