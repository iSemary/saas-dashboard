import { describe, it, expect, vi, beforeEach } from "vitest";

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
  listChartOfAccounts,
  createChartOfAccount,
  updateChartOfAccount,
  deleteChartOfAccount,
  listJournalEntries,
  createJournalEntry,
  listFiscalYears,
  listBudgets,
  listTaxRates,
  listBankAccounts,
  listBankTransactions,
  createBankTransaction,
  listReconciliations,
  completeReconciliation,
  getAccountingDashboard,
} from "@/lib/accounting-resources";

describe("accounting-resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Chart of Accounts ────────────────────────────────────────────

  describe("listChartOfAccounts", () => {
    it("calls GET /tenant/accounting/chart-of-accounts with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listChartOfAccounts({ page: 2, per_page: 5 });
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/chart-of-accounts?page=2&per_page=5");
      expect(result.meta.total).toBe(0);
    });

    it("calls GET without query when no params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [{ id: 1, code: "1000" }], meta: { current_page: 1, last_page: 1, per_page: 15, total: 1 } },
      });

      const result = await listChartOfAccounts();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/chart-of-accounts");
      expect(result.data).toHaveLength(1);
    });
  });

  describe("createChartOfAccount", () => {
    it("calls POST with correct endpoint and payload", async () => {
      const payload = { code: "1000", name: "Cash", type: "asset" };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, ...payload } },
      });

      const result = await createChartOfAccount(payload);
      expect(api.post).toHaveBeenCalledWith("/tenant/accounting/chart-of-accounts", payload);
      expect(result.code).toBe("1000");
    });
  });

  describe("updateChartOfAccount", () => {
    it("calls PUT /tenant/accounting/chart-of-accounts/:id", async () => {
      const payload = { name: "Cash & Equivalents" };
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, name: "Cash & Equivalents" } },
      });

      const result = await updateChartOfAccount(1, payload);
      expect(api.put).toHaveBeenCalledWith("/tenant/accounting/chart-of-accounts/1", payload);
      expect(result.name).toBe("Cash & Equivalents");
    });
  });

  describe("deleteChartOfAccount", () => {
    it("calls DELETE /tenant/accounting/chart-of-accounts/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: {} });
      await deleteChartOfAccount(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/accounting/chart-of-accounts/1");
    });
  });

  // ── Journal Entries ─────────────────────────────────────────────

  describe("listJournalEntries", () => {
    it("calls GET /tenant/accounting/journal-entries", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listJournalEntries();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/journal-entries");
      expect(result.meta.total).toBe(0);
    });
  });

  describe("createJournalEntry", () => {
    it("calls POST with items array", async () => {
      const payload = { entry_date: "2024-01-15", fiscal_year_id: 1, items: [{ account_id: 1, debit: 100 }] };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, state: "draft" } },
      });

      const result = await createJournalEntry(payload);
      expect(api.post).toHaveBeenCalledWith("/tenant/accounting/journal-entries", payload);
    });
  });

  // ── Fiscal Years ────────────────────────────────────────────────

  describe("listFiscalYears", () => {
    it("calls GET /tenant/accounting/fiscal-years", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      const result = await listFiscalYears();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/fiscal-years");
    });
  });

  // ── Budgets ─────────────────────────────────────────────────────

  describe("listBudgets", () => {
    it("calls GET /tenant/accounting/budgets", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      await listBudgets();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/budgets");
    });
  });

  // ── Tax Rates ───────────────────────────────────────────────────

  describe("listTaxRates", () => {
    it("calls GET /tenant/accounting/tax-rates", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      await listTaxRates();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/tax-rates");
    });
  });

  // ── Bank Accounts ───────────────────────────────────────────────

  describe("listBankAccounts", () => {
    it("calls GET /tenant/accounting/bank-accounts", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      await listBankAccounts();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/bank-accounts");
    });
  });

  // ── Bank Transactions ───────────────────────────────────────────

  describe("listBankTransactions", () => {
    it("calls GET /tenant/accounting/bank-transactions", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      await listBankTransactions();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/bank-transactions");
    });
  });

  describe("createBankTransaction", () => {
    it("calls POST with correct endpoint", async () => {
      const payload = { bank_account_id: 1, type: "debit", amount: 100 };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1 } },
      });

      await createBankTransaction(payload);
      expect(api.post).toHaveBeenCalledWith("/tenant/accounting/bank-transactions", payload);
    });
  });

  // ── Reconciliation ─────────────────────────────────────────────

  describe("listReconciliations", () => {
    it("calls GET /tenant/accounting/reconciliations", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } },
      });

      await listReconciliations();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/reconciliations");
    });
  });

  describe("completeReconciliation", () => {
    it("calls POST /tenant/accounting/reconciliations/:id/complete", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: { id: 1, status: "completed" } },
      });

      const result = await completeReconciliation(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/accounting/reconciliations/1/complete");
      expect(result.status).toBe("completed");
    });
  });

  // ── Dashboard ───────────────────────────────────────────────────

  describe("getAccountingDashboard", () => {
    it("calls GET /tenant/accounting/dashboard/stats", async () => {
      const stats = { active_accounts: 25, draft_entries: 3 };
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({
        data: { data: stats },
      });

      const result = await getAccountingDashboard();
      expect(api.get).toHaveBeenCalledWith("/tenant/accounting/dashboard/stats");
      expect(result.active_accounts).toBe(25);
    });
  });
});
