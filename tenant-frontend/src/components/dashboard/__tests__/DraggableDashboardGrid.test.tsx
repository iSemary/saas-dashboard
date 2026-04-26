import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { render, screen, fireEvent, waitFor, act } from "@testing-library/react";
import React from "react";

// ---------------------------------------------------------------------------
// Mocks — must be declared before the component import
// ---------------------------------------------------------------------------

// Mock react-grid-layout/legacy so we don't need a real browser layout engine
vi.mock("react-grid-layout/legacy", () => {
  const WidthProvider = (Component: React.ComponentType<Record<string, unknown>>) =>
    function WidthProvided(props: Record<string, unknown>) {
      return <Component {...props} width={1200} />;
    };

  const Responsive = ({
    children,
    onLayoutChange,
  }: {
    children: React.ReactNode;
    onLayoutChange?: (layout: unknown[], layouts: Record<string, unknown[]>) => void;
    [key: string]: unknown;
  }) => {
    return (
      <div data-testid="responsive-grid">
        {children}
        <button
          data-testid="trigger-layout-change"
          onClick={() =>
            onLayoutChange?.([], { lg: [], md: [], sm: [], xs: [] })
          }
        />
      </div>
    );
  };

  return { Responsive, WidthProvider };
});

// Mock the api module
vi.mock("@/lib/api", () => ({
  default: {
    get: vi.fn().mockResolvedValue({ data: null }),
    post: vi.fn().mockResolvedValue({ data: {} }),
    delete: vi.fn().mockResolvedValue({ data: {} }),
  },
}));

// ---------------------------------------------------------------------------
// Import after mocks
// ---------------------------------------------------------------------------
import DraggableDashboardGrid from "../DraggableDashboardGrid";
import api from "@/lib/api";
import type { ResponsiveLayouts } from "react-grid-layout";

const defaultLayouts: ResponsiveLayouts = {
  lg: [
    { i: "card-a", x: 0, y: 0, w: 3, h: 2 },
    { i: "card-b", x: 3, y: 0, w: 3, h: 2 },
  ],
  md: [
    { i: "card-a", x: 0, y: 0, w: 3, h: 2 },
    { i: "card-b", x: 3, y: 0, w: 3, h: 2 },
  ],
  sm: [
    { i: "card-a", x: 0, y: 0, w: 3, h: 3 },
    { i: "card-b", x: 3, y: 0, w: 3, h: 3 },
  ],
  xs: [
    { i: "card-a", x: 0, y: 0, w: 4, h: 3 },
    { i: "card-b", x: 0, y: 3, w: 4, h: 3 },
  ],
};

const renderGrid = (overrides?: Partial<React.ComponentProps<typeof DraggableDashboardGrid>>) =>
  render(
    <DraggableDashboardGrid
      storageKey="test_layout_key"
      defaultLayouts={defaultLayouts}
      {...overrides}
    >
      {[
        <div key="card-a">Card A</div>,
        <div key="card-b">Card B</div>,
      ]}
    </DraggableDashboardGrid>
  );

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

describe("DraggableDashboardGrid", () => {
  beforeEach(() => {
    vi.clearAllMocks();
    localStorage.clear();
    vi.mocked(api.get).mockResolvedValue({ data: null } as never);
  });

  afterEach(() => {
    localStorage.clear();
  });

  it("renders children in SSR fallback (before mount)", () => {
    // Before effects fire the component shows a simple grid
    renderGrid();
    expect(screen.getByText("Card A")).toBeInTheDocument();
    expect(screen.getByText("Card B")).toBeInTheDocument();
  });

  it("shows Edit Layout and Reset buttons after mounting", async () => {
    renderGrid();
    await waitFor(() => {
      expect(screen.getByText("Edit Layout")).toBeInTheDocument();
      expect(screen.getByText("Reset")).toBeInTheDocument();
    });
  });

  it("renders the responsive grid after mounting", async () => {
    renderGrid();
    await waitFor(() => {
      expect(screen.getByTestId("responsive-grid")).toBeInTheDocument();
    });
  });

  it("toggles edit mode on button click", async () => {
    renderGrid();
    const editBtn = await screen.findByText("Edit Layout");
    fireEvent.click(editBtn);
    expect(screen.getByText("Done")).toBeInTheDocument();
    fireEvent.click(screen.getByText("Done"));
    expect(screen.getByText("Edit Layout")).toBeInTheDocument();
  });

  it("fetches saved layout from API on mount", async () => {
    renderGrid();
    await waitFor(() => {
      expect(vi.mocked(api.get)).toHaveBeenCalledWith("/user-meta/test_layout_key");
    });
  });

  it("applies saved layout from localStorage on mount", async () => {
    const savedLayouts: ResponsiveLayouts = {
      lg: [
        { i: "card-a", x: 6, y: 6, w: 6, h: 4 },
        { i: "card-b", x: 0, y: 0, w: 6, h: 4 },
      ],
    };
    localStorage.setItem("test_layout_key", JSON.stringify(savedLayouts));
    renderGrid();
    await waitFor(() => {
      // grid is rendered — layout was applied from localStorage
      expect(screen.getByTestId("responsive-grid")).toBeInTheDocument();
    });
  });

  it("applies saved layout from API response when available", async () => {
    const apiLayouts: ResponsiveLayouts = {
      lg: [{ i: "card-a", x: 3, y: 3, w: 4, h: 3 }],
    };
    vi.mocked(api.get).mockResolvedValue({ data: JSON.stringify(apiLayouts) } as never);
    renderGrid();
    await waitFor(() => {
      expect(vi.mocked(api.get)).toHaveBeenCalledWith("/user-meta/test_layout_key");
    });
  });

  it("reset clears localStorage and calls DELETE api", async () => {
    localStorage.setItem("test_layout_key", JSON.stringify({ lg: [] }));
    renderGrid();
    const resetBtn = await screen.findByText("Reset");
    fireEvent.click(resetBtn);
    expect(localStorage.getItem("test_layout_key")).toBeNull();
    expect(vi.mocked(api.delete)).toHaveBeenCalledWith("/user-meta/test_layout_key");
  });

  it("saves layout to localStorage when layout changes", async () => {
    renderGrid();
    await screen.findByTestId("trigger-layout-change");
    fireEvent.click(screen.getByTestId("trigger-layout-change"));
    // localStorage should be set immediately (synchronous part of saveLayouts)
    expect(localStorage.getItem("test_layout_key")).not.toBeNull();
  });

  it("debounces API save on layout change", async () => {
    vi.useFakeTimers();
    renderGrid();
    await screen.findByTestId("trigger-layout-change");

    act(() => {
      fireEvent.click(screen.getByTestId("trigger-layout-change"));
      fireEvent.click(screen.getByTestId("trigger-layout-change"));
      fireEvent.click(screen.getByTestId("trigger-layout-change"));
    });

    // API should NOT have been called yet
    expect(vi.mocked(api.post)).not.toHaveBeenCalled();

    await act(async () => {
      vi.advanceTimersByTime(700);
    });

    // After debounce delay, API should have been called exactly once
    expect(vi.mocked(api.post)).toHaveBeenCalledTimes(1);
    expect(vi.mocked(api.post)).toHaveBeenCalledWith(
      "/user-meta",
      expect.objectContaining({ key: "test_layout_key" })
    );

    vi.useRealTimers();
  });

  it("dispatches resize event on layout change", async () => {
    const dispatchSpy = vi.spyOn(window, "dispatchEvent");
    renderGrid();
    await screen.findByTestId("trigger-layout-change");
    fireEvent.click(screen.getByTestId("trigger-layout-change"));
    expect(dispatchSpy).toHaveBeenCalledWith(expect.any(Event));
    const calls = dispatchSpy.mock.calls.map((c) => (c[0] as Event).type);
    expect(calls).toContain("resize");
  });

  it("ignores corrupted localStorage JSON", async () => {
    localStorage.setItem("test_layout_key", "not-valid-json{{{");
    // Should not throw
    expect(() => renderGrid()).not.toThrow();
    await waitFor(() => {
      expect(screen.getByText("Card A")).toBeInTheDocument();
    });
  });

  it("gracefully handles API error without crashing", async () => {
    vi.mocked(api.get).mockRejectedValue(new Error("Network error"));
    expect(() => renderGrid()).not.toThrow();
    await waitFor(() => {
      expect(screen.getByText("Card A")).toBeInTheDocument();
    });
  });
});
