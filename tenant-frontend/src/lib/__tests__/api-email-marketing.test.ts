import { describe, it, expect, vi, beforeEach } from "vitest";

// Mock the api module before importing the resource modules
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
  getEmCampaigns,
  getEmCampaign,
  createEmCampaign,
  updateEmCampaign,
  deleteEmCampaign,
  sendEmCampaign,
  scheduleEmCampaign,
  pauseEmCampaign,
  cancelEmCampaign,
  getEmTemplates,
  createEmTemplate,
  getEmContacts,
  getEmContactLists,
  getEmCredentials,
  getEmAutomationRules,
  getEmWebhooks,
  getEmAbTests,
  getEmImportJobs,
  getEmSendingLogs,
  getEmUnsubscribes,
  getEmDashboardStats,
} from "@/lib/api-email-marketing";

const mockApi = api as unknown as {
  get: ReturnType<typeof vi.fn>;
  post: ReturnType<typeof vi.fn>;
  put: ReturnType<typeof vi.fn>;
  delete: ReturnType<typeof vi.fn>;
};

describe("Email Marketing API Resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Campaigns ──

  it("getEmCampaigns calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmCampaigns();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/campaigns", { params: undefined });
  });

  it("getEmCampaigns passes params", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmCampaigns({ page: 2, per_page: 10 });
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/campaigns", {
      params: { page: 2, per_page: 10 },
    });
  });

  it("getEmCampaign calls correct endpoint with id", async () => {
    mockApi.get.mockResolvedValue({ data: { data: { id: 1 } } });
    await getEmCampaign(1);
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/1");
  });

  it("createEmCampaign posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { data: { id: 1, name: "Test" } } });
    await createEmCampaign({ name: "Test", subject: "Sub" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/email-marketing/campaigns", {
      name: "Test",
      subject: "Sub",
    });
  });

  it("updateEmCampaign puts to correct endpoint", async () => {
    mockApi.put.mockResolvedValue({ data: { data: { id: 1 } } });
    await updateEmCampaign(1, { name: "Updated" });
    expect(mockApi.put).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/1", { name: "Updated" });
  });

  it("deleteEmCampaign deletes correct endpoint", async () => {
    mockApi.delete.mockResolvedValue({ data: null });
    await deleteEmCampaign(1);
    expect(mockApi.delete).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/1");
  });

  it("sendEmCampaign posts to send endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await sendEmCampaign(5);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/5/send");
  });

  it("scheduleEmCampaign posts to schedule endpoint with date", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await scheduleEmCampaign(5, "2026-06-01 09:00:00");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/5/schedule", {
      scheduled_at: "2026-06-01 09:00:00",
    });
  });

  it("pauseEmCampaign posts to pause endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await pauseEmCampaign(3);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/3/pause");
  });

  it("cancelEmCampaign posts to cancel endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await cancelEmCampaign(3);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/email-marketing/campaigns/3/cancel");
  });

  // ── Templates ──

  it("getEmTemplates calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmTemplates();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/templates", { params: undefined });
  });

  it("createEmTemplate posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { data: { id: 1 } } });
    await createEmTemplate({ name: "Welcome" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/email-marketing/templates", { name: "Welcome" });
  });

  // ── Contacts ──

  it("getEmContacts calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmContacts();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/contacts", { params: undefined });
  });

  // ── Contact Lists ──

  it("getEmContactLists calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmContactLists();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/contact-lists", { params: undefined });
  });

  // ── Credentials ──

  it("getEmCredentials calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmCredentials();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/credentials", { params: undefined });
  });

  // ── Automation Rules ──

  it("getEmAutomationRules calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmAutomationRules();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/automation-rules", { params: undefined });
  });

  // ── Webhooks ──

  it("getEmWebhooks calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmWebhooks();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/webhooks", { params: undefined });
  });

  // ── A/B Tests ──

  it("getEmAbTests calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmAbTests();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/ab-tests", { params: undefined });
  });

  // ── Import Jobs ──

  it("getEmImportJobs calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmImportJobs();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/import-jobs", { params: undefined });
  });

  // ── Sending Logs ──

  it("getEmSendingLogs calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmSendingLogs();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/sending-logs", { params: undefined });
  });

  // ── Unsubscribes ──

  it("getEmUnsubscribes calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getEmUnsubscribes();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/unsubscribes", { params: undefined });
  });

  // ── Dashboard ──

  it("getEmDashboardStats calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: { total_campaigns: 5 } } });
    await getEmDashboardStats();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/email-marketing/dashboard/stats");
  });
});
