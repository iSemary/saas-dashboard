import axios from "axios";

const axiosConfig = axios.create({
    baseURL: `${import.meta.env.VITE_API_PROTOCOL}${
        import.meta.env.VITE_API_TENANT
    }.${import.meta.env.VITE_API_URL}`,
    headers: {
        // Authorization: "Bearer " + Token.get(),
        "Content-Type": "application/json",
    },
});

export default axiosConfig;
