import React from "react";
import ReactDOM from "react-dom/client";
import AppRoutes from "./routes/AppRoutes";

// Wait for the DOM to be fully loaded before rendering
document.addEventListener("DOMContentLoaded", () => {
    const root = ReactDOM.createRoot(document.getElementById("app"));
    root.render(
        <React.StrictMode>
            <AppRoutes />
        </React.StrictMode>
    );
});
