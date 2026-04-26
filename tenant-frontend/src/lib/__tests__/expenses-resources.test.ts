import { describe, it, expect, vi, beforeEach } from "vitest";

// Mock the api module before importing the resource files
vi.mock("@/lib/api", () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}));

import api from "@/lib/api";
import {
  listExpenseCategories,
  createExpenseCategory,
  updateExpenseCategory,
  deleteExpenseCategory,
  listExpenses,
  createExpense,
  updateExpense,
  deleteExpense,
  listExpenseReports,
  createExpenseReport,
  updateExpenseReport,
  deleteExpenseReport,
  listExpensePolicies,
  createExpensePolicy,
  updateExpensePolicy,
  deleteExpensePolicy,
  listExpenseTags,
  createExpenseTag,
  updateExpenseTag,
  deleteExpenseTag,
  listReimbursements,
  createReimbursement,
  updateReimbursement,
  deleteReimbursement,
  getExpensesDashboard,
} from "@/lib/expenses-resources";

describe("expenses-resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Categories ──────────────────────────────────────────────────

  describe("listExpenseCategories", () => {
    it("calls GET /tenant/expenses/categories with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listExpenseCategories({ page: 1, per_page: 10 });
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/categories?page=1&per_page=10");
      expect(result.data).toEqual([]);
      expect(result.meta.total).toBe(0);
    });

    it("calls GET without query when no params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [{ id: 1, name: "Travel" }], meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 } },
      });

      const result = await listExpenseCategories();
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/categories");
      expect(result.data).toHaveLength(1);
    });
  });

  describe("createExpenseCategory", () => {
    it("calls POST /tenant/expenses/categories with payload", async () => {
      const payload = { name: "Travel", description: "Travel expenses" };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, ...payload } },
      });

      const result = await createExpenseCategory(payload);
      expect(api.post).toHaveBeenCalledWith("/tenant/expenses/categories", payload);
      expect(result.name).toBe("Travel");
    });
  });

  describe("updateExpenseCategory", () => {
    it("calls PUT /tenant/expenses/categories/:id", async () => {
      const payload = { name: "Updated" };
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, name: "Updated" } },
      });

      const result = await updateExpenseCategory(1, payload);
      expect(api.put).toHaveBeenCalledWith("/tenant/expenses/categories/1", payload);
      expect(result.name).toBe("Updated");
    });
  });

  describe("deleteExpenseCategory", () => {
    it("calls DELETE /tenant/expenses/categories/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: {} });
      await deleteExpenseCategory(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/expenses/categories/1");
    });
  });

  // ── Expenses ────────────────────────────────────────────────────

  describe("listExpenses", () => {
    it("calls GET /tenant/expenses/expenses", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listExpenses();
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/expenses");
      expect(result.meta.total).toBe(0);
    });
  });

  describe("createExpense", () => {
    it("calls POST with correct endpoint", async () => {
      const payload = { title: "Flight", amount: 500, category_id: 1, date: "2024-01-15" };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, ...payload } },
      });

      const result = await createExpense(payload);
      expect(api.post).toHaveBeenCalledWith("/tenant/expenses/expenses", payload);
      expect(result.title).toBe("Flight");
    });
  });

  // ── Reports ─────────────────────────────────────────────────────

  describe("listExpenseReports", () => {
    it("calls GET /tenant/expenses/reports", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listExpenseReports();
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/reports");
      expect(result.data).toEqual([]);
    });
  });

  // ── Policies ────────────────────────────────────────────────────

  describe("createExpensePolicy", () => {
    it("calls POST with correct endpoint", async () => {
      const payload = { name: "Max $500", type: "max_amount", rules: { amount: 500 } };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, ...payload } },
      });

      const result = await createExpensePolicy(payload);
      expect(api.post).toHaveBeenCalledWith("/tenant/expenses/policies", payload);
    });
  });

  // ── Tags ────────────────────────────────────────────────────────

  describe("listExpenseTags", () => {
    it("calls GET /tenant/expenses/tags", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [{ id: 1, name: "Urgent", color: "#ff0000" }], meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 } },
      });

      const result = await listExpenseTags();
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/tags");
      expect(result.data).toHaveLength(1);
    });
  });

  // ── Reimbursements ──────────────────────────────────────────────

  describe("listReimbursements", () => {
    it("calls GET /tenant/expenses/reimbursements", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listReimbursements();
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/reimbursements");
    });
  });

  // ── Dashboard ───────────────────────────────────────────────────

  describe("getExpensesDashboard", () => {
    it("calls GET /tenant/expenses/dashboard/stats", async () => {
      const stats = { total_expenses: 100, pending_count: 5 };
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: stats },
      });

      const result = await getExpensesDashboard();
      expect(api.get).toHaveBeenCalledWith("/tenant/expenses/dashboard/stats");
      expect(result.total_expenses).toBe(100);
    });
  });
});
