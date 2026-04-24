import React from "react";
import { Grid } from "react-loader-spinner";

export default function Loader() {
    return (
        <div className="spinner-container">
            <Grid
                visible={true}
                height="80"
                width="80"
                color="#1560BD"
                ariaLabel="grid-loading"
                radius="12.5"
                wrapperStyle={{
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    height: "50vh",
                }}
                wrapperClass="grid-wrapper"
            />
        </div>
    );
}
