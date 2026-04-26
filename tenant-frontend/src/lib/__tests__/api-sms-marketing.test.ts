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
  getSmCampaigns,
  getSmCampaign,
  createSmCampaign,
  updateSmCampaign,
  deleteSmCampaign,
  sendSmCampaign,
  scheduleSmCampaign,
  pauseSmCampaign,
  cancelSmCampaign,
  getSmTemplates,
  createSmTemplate,
  getSmContacts,
  getSmContactLists,
  getSmCredentials,
  getSmAutomationRules,
  getSmWebhooks,
  getSmAbTests,
  getSmImportJobs,
  getSmSendingLogs,
  getSmOptOuts,
} from "@/lib/api-sms-marketing";

const mockApi = api as unknown as {
  get: ReturnType<typeof vi.fn>;
  post: ReturnType<typeof vi.fn>;
  put: ReturnType<typeof vi.fn>;
  delete: ReturnType<typeof vi.fn>;
};

describe("SMS Marketing API Resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Campaigns ──

  it("getSmCampaigns calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmCampaigns();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns", { params: undefined });
  });

  it("getSmCampaign calls correct endpoint with id", async () => {
    mockApi.get.mockResolvedValue({ data: { data: { id: 1 } } });
    await getSmCampaign(1);
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/1");
  });

  it("createSmCampaign posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { data: { id: 1 } } });
    await createSmCampaign({ name: "Flash Sale" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns", { name: "Flash Sale" });
  });

  it("updateSmCampaign puts to correct endpoint", async () => {
    mockApi.put.mockResolvedValue({ data: { data: { id: 1 } } });
    await updateSmCampaign(1, { name: "Updated" });
    expect(mockApi.put).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/1", { name: "Updated" });
  });

  it("deleteSmCampaign deletes correct endpoint", async () => {
    mockApi.delete.mockResolvedValue({ data: null });
    await deleteSmCampaign(1);
    expect(mockApi.delete).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/1");
  });

  it("sendSmCampaign posts to send endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await sendSmCampaign(5);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/5/send");
  });

  it("scheduleSmCampaign posts to schedule endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await scheduleSmCampaign(5, "2026-06-01 09:00:00");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/5/schedule", {
      scheduled_at: "2026-06-01 09:00:00",
    });
  });

  it("pauseSmCampaign posts to pause endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await pauseSmCampaign(3);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/3/pause");
  });

  it("cancelSmCampaign posts to cancel endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: null });
    await cancelSmCampaign(3);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/sms-marketing/campaigns/3/cancel");
  });

  // ── Templates ──

  it("getSmTemplates calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmTemplates();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/templates", { params: undefined });
  });

  it("createSmTemplate posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { data: { id: 1 } } });
    await createSmTemplate({ name: "Welcome SMS" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/sms-marketing/templates", { name: "Welcome SMS" });
  });

  // ── Contacts ──

  it("getSmContacts calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmContacts();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/contacts", { params: undefined });
  });

  // ── Contact Lists ──

  it("getSmContactLists calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmContactLists();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/contact-lists", { params: undefined });
  });

  // ── Credentials ──

  it("getSmCredentials calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmCredentials();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/credentials", { params: undefined });
  });

  // ── Automation Rules ──

  it("getSmAutomationRules calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmAutomationRules();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/automation-rules", { params: undefined });
  });

  // ── Webhooks ──

  it("getSmWebhooks calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmWebhooks();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/webhooks", { params: undefined });
  });

  // ── A/B Tests ──

  it("getSmAbTests calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmAbTests();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/ab-tests", { params: undefined });
  });

  // ── Import Jobs ──

  it("getSmImportJobs calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmImportJobs();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/import-jobs", { params: undefined });
  });

  // ── Sending Logs ──

  it("getSmSendingLogs calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmSendingLogs();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/sending-logs", { params: undefined });
  });

  // ── Opt-Outs ──

  it("getSmOptOuts calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getSmOptOuts();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/sms-marketing/opt-outs", { params: undefined });
  });
});
