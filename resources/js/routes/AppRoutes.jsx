import React, { useState, useEffect } from "react";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import FlowModules from "../components/landlord/developments/flows/FlowModules";
import FlowDatabase from "../components/landlord/developments/flows/FlowDatabase";
import axiosConfig from "../configs/AxiosConfig";
import { Grid } from "react-loader-spinner";

const AppRoutes = () => {
    const [routes, setRoutes] = useState({});
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axiosConfig
            .get("/development/routes")
            .then((response) => {
                if (response.data.success) {
                    const routeMap = response.data.data.routes.reduce(
                        (acc, route) => {
                            acc[route.name] = new URL(route.route).pathname.substring(1);
                            return acc;
                        },
                        {}
                    );
                    setRoutes(routeMap);
                }
            })
            .catch((error) => {
                console.error("Error fetching routes:", error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <div className="spinner-container">
                <Grid
                    visible={true}
                    height="80"
                    width="80"
                    color="#1560BD"
                    ariaLabel="grid-loading"
                    radius="12.5"
                    wrapperStyle={{ display: "flex", justifyContent: "center", alignItems: "center", height: "50vh" }}
                    wrapperClass="grid-wrapper"
                />
            </div>
        );
    }

    return (
        <Router>
            <Routes>
                <Route
                    path={routes["flows.database"]}
                    element={<FlowDatabase />}
                />
                <Route
                    path={routes["flows.modules"]}
                    element={<FlowModules />}
                />
            </Routes>
        </Router>
    );
};

export default AppRoutes;
