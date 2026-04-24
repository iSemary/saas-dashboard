"use client";

import dynamic from "next/dynamic";
import { useChartPalette } from "@/context/chart-palette-context";
import { useMemo } from "react";

// Dynamically import ApexCharts to avoid SSR issues
const ReactApexChart = dynamic(() => import("react-apexcharts"), { ssr: false });

type ChartType = "line" | "area" | "bar" | "pie" | "donut" | "radialBar" | "scatter" | "bubble" | "heatmap" | "candlestick" | "boxPlot" | "radar" | "polarArea" | "rangeBar" | "rangeArea" | "treemap";

export interface ApexChartProps {
  type: ChartType;
  series: Array<Record<string, unknown>>;
  options?: Record<string, unknown>;
  height?: number | string;
  width?: number | string;
}

export function ApexChart({
  type,
  series,
  options = {},
  height = 350,
  width = "100%",
}: ApexChartProps) {
  const { palette } = useChartPalette();

  const chartOptions = useMemo(() => {
    return {
      colors: palette,
      chart: {
        toolbar: {
          show: true,
          tools: {
            download: true,
            selection: false,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: false,
            reset: true,
          },
        },
        fontFamily: "inherit",
      },
      stroke: {
        curve: "smooth" as const,
        width: 2,
      },
      dataLabels: {
        enabled: false,
      },
      legend: {
        position: "top" as const,
        horizontalAlign: "right" as const,
      },
      grid: {
        borderColor: "hsl(var(--border))",
        strokeDashArray: 4,
      },
      xaxis: {
        axisBorder: {
          show: false,
        },
        axisTicks: {
          show: false,
        },
      },
      yaxis: {
        labels: {
          formatter: (value: number) => {
            return value?.toLocaleString() ?? "0";
          },
        },
      },
      ...options,
    };
  }, [options, palette]);

  return (
    <ReactApexChart
      type={type}
      series={series as never}
      options={chartOptions as never}
      height={height}
      width={width}
    />
  );
}
