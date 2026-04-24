import React, { useState, useCallback, useEffect } from "react";
import {
    ReactFlow,
    MiniMap,
    Controls,
    Background,
    useNodesState,
    useEdgesState,
    addEdge,
} from "@xyflow/react";
import "@xyflow/react/dist/style.css";
import axiosConfig from "../../../../configs/AxiosConfig";
import Loader from "../../../utilities/Loader";

const FlowsDatabase = () => {
    const [nodes, setNodes, onNodesChange] = useNodesState([]);
    const [edges, setEdges, onEdgesChange] = useEdgesState([]);
    const [databases, setDatabases] = useState({});
    const [loading, setLoading] = useState(true);
    const [syncLoading, setSyncLoading] = useState(false);

    const onConnect = useCallback(
        (params) => setEdges((eds) => addEdge(params, eds)),
        [setEdges]
    );

    // Transform database structure into nodes and edges
    const processDbStructure = (dbData) => {
        const newNodes = [];
        const newEdges = [];
        let yOffset = 0;

        Object.entries(dbData).forEach(([dbName, tables]) => {
            tables.forEach((table, tableIndex) => {
                const nodeId = `${dbName}-${table.name}`;
                const label = (
                    <div>
                        <strong>{table.name}</strong>
                        <div style={{ fontSize: "0.8em" }}>
                            {table.columns.map((col) => (
                                <div key={col.title}>
                                    {col.title} ({col.data_type})
                                </div>
                            ))}
                        </div>
                    </div>
                );

                newNodes.push({
                    id: nodeId,
                    data: { label },
                    position: table.design?.position
                        ? {
                              x: parseInt(table.design.position.x, 10),
                              y: parseInt(table.design.position.y, 10),
                          }
                        : {
                              x: 250 + (tableIndex % 2) * 300,
                              y: 100 + yOffset,
                          },
                    style: {
                        width: "auto",
                        padding: 10,
                        backgroundColor: table.design?.color || "#fff",
                    },
                });

                if (table.relations) {
                    table.relations.forEach((relation, index) => {
                        const targetId = `${dbName}-${relation.references.table}`;
                        newEdges.push({
                            id: `${nodeId}-${targetId}-${index}`,
                            source: nodeId,
                            target: targetId,
                            label: `${relation.column} → ${relation.references.column}`,
                            type: "smoothstep",
                            animated: true,
                            style: { stroke: "#888" },
                        });
                    });
                }

                yOffset += 250;
            });
        });

        setNodes(newNodes);
        setEdges(newEdges);
    };

    useEffect(() => {
        axiosConfig
            .get("/development/databases/flow")
            .then((response) => {
                if (response.data.success) {
                    setDatabases(response.data.data.databases);
                    processDbStructure(response.data.data.databases);
                }
            })
            .catch((error) => {
                console.error("Error fetching databases:", error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    const handleSaveFlow = () => {
        setSyncLoading(true);
        const formattedData = nodes.map((node) => {
            const [connection, table] = node.id.split("-");
            return {
                table,
                connection,
                position: node.position,
                color: node.style?.backgroundColor || "#fff",
            };
        });

        axiosConfig
            .post("/development/databases/flow", { nodes: formattedData })
            .then((response) => {
                if (response.data.success) {
                    alert("Flow saved successfully!");
                }
            })
            .catch((error) => {
                console.error("Error syncing databases:", error);
            })
            .finally(() => {
                setSyncLoading(false);
            });
    };

    if (loading) {
        return <Loader />;
    }

    return (
        <>
            <div className="text-revert mb-2">
                <button
                    className="btn btn-success"
                    type="button"
                    disabled={syncLoading}
                    onClick={handleSaveFlow}
                >
                    Save
                </button>
            </div>
            <div style={{ width: "100%", height: "70vh" }}>
                <ReactFlow
                    nodes={nodes}
                    edges={edges}
                    onNodesChange={onNodesChange}
                    onEdgesChange={onEdgesChange}
                    onConnect={onConnect}
                    fitView
                    attributionPosition="bottom-right"
                >
                    <Controls />
                    <MiniMap zoomable pannable />
                    <Background variant="dots" gap={12} size={1} />
                </ReactFlow>
            </div>
        </>
    );
};

export default FlowsDatabase;
