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
  getSurveys,
  getSurvey,
  createSurvey,
  updateSurvey,
  deleteSurvey,
  duplicateSurvey,
  publishSurvey,
  closeSurvey,
  pauseSurvey,
  resumeSurvey,
  getSurveyPages,
  createSurveyPage,
  updateSurveyPage,
  deleteSurveyPage,
  reorderSurveyPages,
  getSurveyQuestions,
  getSurveyQuestion,
  createSurveyQuestion,
  updateSurveyQuestion,
  deleteSurveyQuestion,
  reorderSurveyQuestions,
  getSurveyResponses,
  getSurveyResponse,
  deleteSurveyResponse,
  getSurveyAnalytics,
  getSurveyTemplates,
  getSurveyTemplate,
  createSurveyFromTemplate,
  getSurveyThemes,
  getSurveyTheme,
  createSurveyTheme,
  updateSurveyTheme,
  deleteSurveyTheme,
  getSurveyShares,
  createSurveyShare,
  deleteSurveyShare,
  getSurveyDashboard,
  getSurveyAutomationRules,
  createSurveyAutomationRule,
  updateSurveyAutomationRule,
  deleteSurveyAutomationRule,
  toggleSurveyAutomationRule,
  getSurveyWebhooks,
  createSurveyWebhook,
  updateSurveyWebhook,
  deleteSurveyWebhook,
  toggleSurveyWebhook,
  regenerateSurveyWebhookSecret,
  getPublicSurvey,
  startSurveyResponse,
  submitAnswer,
  completeSurveyResponse,
  resumeSurveyResponse,
} from "@/lib/api-survey";

describe("api-survey", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Surveys CRUD ─────────────────────────────────────────────────

  describe("surveys", () => {
    it("getSurveys calls GET with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [], meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 } } });
      await getSurveys({ page: 1 });
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys", { params: { page: 1 } });
    });

    it("getSurvey calls GET /surveys/:id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, title: "Q1" } } });
      const result = await getSurvey(1);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/1");
      expect(result.data.title).toBe("Q1");
    });

    it("createSurvey calls POST", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, title: "New" } } });
      const result = await createSurvey({ title: "New" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys", { title: "New" });
      expect(result.data.id).toBe(1);
    });

    it("updateSurvey calls PUT", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateSurvey(1, { title: "Updated" });
      expect(api.put).toHaveBeenCalledWith("/tenant/survey/surveys/1", { title: "Updated" });
    });

    it("deleteSurvey calls DELETE", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurvey(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/surveys/1");
    });

    it("duplicateSurvey calls POST /duplicate", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 2 } } });
      await duplicateSurvey(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/1/duplicate");
    });

    it("publishSurvey calls POST /publish", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, status: "active" } } });
      await publishSurvey(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/1/publish");
    });

    it("closeSurvey calls POST /close", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await closeSurvey(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/1/close");
    });

    it("pauseSurvey calls POST /pause", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await pauseSurvey(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/1/pause");
    });

    it("resumeSurvey calls POST /resume", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await resumeSurvey(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/1/resume");
    });
  });

  // ── Pages ────────────────────────────────────────────────────────

  describe("pages", () => {
    it("getSurveyPages calls GET nested route", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyPages(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/pages");
    });

    it("createSurveyPage calls POST nested route", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createSurveyPage(5, { title: "Page 1" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/pages", { title: "Page 1" });
    });

    it("updateSurveyPage calls PUT /pages/:id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateSurveyPage(1, { title: "Updated" });
      expect(api.put).toHaveBeenCalledWith("/tenant/survey/pages/1", { title: "Updated" });
    });

    it("deleteSurveyPage calls DELETE /pages/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyPage(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/pages/1");
    });

    it("reorderSurveyPages calls POST /reorder", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await reorderSurveyPages(5, [1, 2, 3]);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/pages/reorder", { ordered_ids: [1, 2, 3] });
    });
  });

  // ── Questions ─────────────────────────────────────────────────────

  describe("questions", () => {
    it("getSurveyQuestions calls GET nested route", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyQuestions(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/questions");
    });

    it("getSurveyQuestion calls GET /questions/:id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await getSurveyQuestion(1);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/questions/1");
    });

    it("createSurveyQuestion calls POST nested route", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createSurveyQuestion(5, { title: "Q1", type: "text" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/questions", { title: "Q1", type: "text" });
    });

    it("updateSurveyQuestion calls PUT /questions/:id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateSurveyQuestion(1, { title: "Updated" });
      expect(api.put).toHaveBeenCalledWith("/tenant/survey/questions/1", { title: "Updated" });
    });

    it("deleteSurveyQuestion calls DELETE", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyQuestion(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/questions/1");
    });

    it("reorderSurveyQuestions calls POST /reorder", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await reorderSurveyQuestions(5, [3, 1, 2]);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/questions/reorder", { ordered_ids: [3, 1, 2] });
    });
  });

  // ── Responses ────────────────────────────────────────────────────

  describe("responses", () => {
    it("getSurveyResponses calls GET nested route", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [], meta: {} } });
      await getSurveyResponses(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/responses", { params: undefined });
    });

    it("getSurveyResponse calls GET /responses/:id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await getSurveyResponse(1);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/responses/1");
    });

    it("deleteSurveyResponse calls DELETE", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyResponse(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/responses/1");
    });

    it("getSurveyAnalytics calls GET /analytics", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { total: 50 } } });
      const result = await getSurveyAnalytics(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/analytics");
      expect(result.data.total).toBe(50);
    });
  });

  // ── Templates ────────────────────────────────────────────────────

  describe("templates", () => {
    it("getSurveyTemplates calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyTemplates();
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/templates");
    });

    it("getSurveyTemplate calls GET /:id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await getSurveyTemplate(1);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/templates/1");
    });

    it("createSurveyFromTemplate calls POST", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 10 } } });
      await createSurveyFromTemplate(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/templates/1/create-survey");
    });
  });

  // ── Themes ───────────────────────────────────────────────────────

  describe("themes", () => {
    it("getSurveyThemes calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyThemes();
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/themes");
    });

    it("getSurveyTheme calls GET /:id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, name: "Blue" } } });
      await getSurveyTheme(1);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/themes/1");
    });

    it("createSurveyTheme calls POST", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, name: "Blue" } } });
      await createSurveyTheme({ name: "Blue" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/themes", { name: "Blue" });
    });

    it("updateSurveyTheme calls PUT", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateSurveyTheme(1, { name: "Red" });
      expect(api.put).toHaveBeenCalledWith("/tenant/survey/themes/1", { name: "Red" });
    });

    it("deleteSurveyTheme calls DELETE", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyTheme(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/themes/1");
    });
  });

  // ── Shares ───────────────────────────────────────────────────────

  describe("shares", () => {
    it("getSurveyShares calls GET nested route", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyShares(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/shares");
    });

    it("createSurveyShare calls POST nested route", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createSurveyShare(5, { channel: "email" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/shares", { channel: "email" });
    });

    it("deleteSurveyShare calls DELETE /shares/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyShare(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/shares/1");
    });
  });

  // ── Dashboard ────────────────────────────────────────────────────

  describe("dashboard", () => {
    it("getSurveyDashboard calls GET and returns data.data", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { total_surveys: 10 } } });
      const result = await getSurveyDashboard();
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/dashboard");
      expect(result.total_surveys).toBe(10);
    });
  });

  // ── Automation Rules ─────────────────────────────────────────────

  describe("automation rules", () => {
    it("getSurveyAutomationRules calls GET nested route", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyAutomationRules(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/automation-rules");
    });

    it("createSurveyAutomationRule calls POST", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createSurveyAutomationRule(5, { name: "Rule 1" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/automation-rules", { name: "Rule 1" });
    });

    it("updateSurveyAutomationRule calls PUT", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateSurveyAutomationRule(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith("/tenant/survey/automation-rules/1", { name: "Updated" });
    });

    it("deleteSurveyAutomationRule calls DELETE", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyAutomationRule(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/automation-rules/1");
    });

    it("toggleSurveyAutomationRule calls POST /toggle", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, is_active: true } } });
      await toggleSurveyAutomationRule(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/automation-rules/1/toggle");
    });
  });

  // ── Webhooks ──────────────────────────────────────────────────────

  describe("webhooks", () => {
    it("getSurveyWebhooks calls GET nested route", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getSurveyWebhooks(5);
      expect(api.get).toHaveBeenCalledWith("/tenant/survey/surveys/5/webhooks");
    });

    it("createSurveyWebhook calls POST", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, secret: "abc" } } });
      await createSurveyWebhook(5, { url: "https://example.com" });
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/surveys/5/webhooks", { url: "https://example.com" });
    });

    it("updateSurveyWebhook calls PUT", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateSurveyWebhook(1, { url: "https://updated.com" });
      expect(api.put).toHaveBeenCalledWith("/tenant/survey/webhooks/1", { url: "https://updated.com" });
    });

    it("deleteSurveyWebhook calls DELETE", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({ data: undefined });
      await deleteSurveyWebhook(1);
      expect(api.delete).toHaveBeenCalledWith("/tenant/survey/webhooks/1");
    });

    it("toggleSurveyWebhook calls POST /toggle", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await toggleSurveyWebhook(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/webhooks/1/toggle");
    });

    it("regenerateSurveyWebhookSecret calls POST /regenerate-secret", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, secret: "new" } } });
      await regenerateSurveyWebhookSecret(1);
      expect(api.post).toHaveBeenCalledWith("/tenant/survey/webhooks/1/regenerate-secret");
    });
  });

  // ── Public Survey ─────────────────────────────────────────────────

  describe("public survey", () => {
    it("getPublicSurvey calls GET /public/survey/:token", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { survey: { id: 1 }, share: { id: 2 } } });
      const result = await getPublicSurvey("abc123");
      expect(api.get).toHaveBeenCalledWith("/public/survey/abc123");
      expect(result.survey.id).toBe(1);
    });

    it("startSurveyResponse calls POST /start", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { response: { id: 1 }, resume_token: "tok" } });
      const result = await startSurveyResponse("abc123", { locale: "en" });
      expect(api.post).toHaveBeenCalledWith("/public/survey/abc123/start", { locale: "en" });
      expect(result.resume_token).toBe("tok");
    });

    it("submitAnswer calls POST /answer", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: {} } });
      await submitAnswer("abc123", { question_id: 1, value: "Yes" });
      expect(api.post).toHaveBeenCalledWith("/public/survey/abc123/answer", { question_id: 1, value: "Yes" });
    });

    it("completeSurveyResponse calls POST /complete", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, status: "completed" } } });
      await completeSurveyResponse("abc123", { response_id: 1 });
      expect(api.post).toHaveBeenCalledWith("/public/survey/abc123/complete", { response_id: 1 });
    });

    it("resumeSurveyResponse calls GET /resume/:token", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { response: { id: 1 }, answers: [] } });
      await resumeSurveyResponse("abc123", "resume-tok");
      expect(api.get).toHaveBeenCalledWith("/public/survey/abc123/resume/resume-tok");
    });
  });
});
