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
  listPmWorkspaces,
  createPmWorkspace,
  updatePmWorkspace,
  deletePmWorkspace,
  listPmProjects,
  createPmProject,
  updatePmProject,
  deletePmProject,
  archivePmProject,
  pausePmProject,
  completePmProject,
  listPmMilestones,
  createPmMilestone,
  updatePmMilestone,
  deletePmMilestone,
  listPmTasks,
  createPmTask,
  updatePmTask,
  deletePmTask,
  movePmTask,
  listPmRisks,
  createPmRisk,
  updatePmRisk,
  deletePmRisk,
  listPmIssues,
  createPmIssue,
  updatePmIssue,
  deletePmIssue,
  promotePmIssueToTask,
  listPmTemplates,
  createPmTemplate,
  updatePmTemplate,
  deletePmTemplate,
  listPmWebhooks,
  createPmWebhook,
  updatePmWebhook,
  deletePmWebhook,
  getPmDashboard,
  getPmReportThroughput,
  getPmReportOverdue,
  getPmReportWorkload,
  getPmReportHealth,
} from "@/lib/pm-resources";

const paginatedMock = (items: unknown[] = []) => ({
  data: { data: items, meta: { current_page: 1, last_page: 1, per_page: 15, total: items.length } },
});

const B = "/tenant/project-management";

describe("pm-resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Workspaces ──────────────────────────────────────────────────

  describe("listPmWorkspaces", () => {
    it("calls GET /workspaces with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmWorkspaces({ page: 1, per_page: 10 });
      expect(api.get).toHaveBeenCalledWith(`${B}/workspaces?page=1&per_page=10`);
    });

    it("calls GET without query when no params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock([{ id: 1 }]));
      const result = await listPmWorkspaces();
      expect(api.get).toHaveBeenCalledWith(`${B}/workspaces`);
      expect(result.data).toHaveLength(1);
    });
  });

  describe("createPmWorkspace", () => {
    it("calls POST with payload", async () => {
      const payload = { name: "WS 1" };
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, ...payload } } });
      const result = await createPmWorkspace(payload);
      expect(api.post).toHaveBeenCalledWith(`${B}/workspaces`, payload);
      expect(result.name).toBe("WS 1");
    });
  });

  describe("updatePmWorkspace", () => {
    it("calls PUT with id and payload", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, name: "Updated" } } });
      const result = await updatePmWorkspace(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/workspaces/1`, { name: "Updated" });
      expect(result.name).toBe("Updated");
    });
  });

  describe("deletePmWorkspace", () => {
    it("calls DELETE with id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmWorkspace(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/workspaces/1`);
    });
  });

  // ── Projects ─────────────────────────────────────────────────────

  describe("listPmProjects", () => {
    it("calls GET /projects", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmProjects();
      expect(api.get).toHaveBeenCalledWith(`${B}/projects`);
    });
  });

  describe("createPmProject", () => {
    it("calls POST /projects", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmProject({ name: "P1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/projects`, { name: "P1" });
    });
  });

  describe("updatePmProject", () => {
    it("calls PUT /projects/:id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1, name: "Updated" } } });
      const result = await updatePmProject(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/projects/1`, { name: "Updated" });
      expect(result.name).toBe("Updated");
    });
  });

  describe("deletePmProject", () => {
    it("calls DELETE /projects/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmProject(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/projects/1`);
    });
  });

  describe("project actions", () => {
    it("archivePmProject calls POST /projects/:id/archive", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await archivePmProject(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/1/archive`);
    });

    it("pausePmProject calls POST /projects/:id/pause", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await pausePmProject(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/1/pause`);
    });

    it("completePmProject calls POST /projects/:id/complete", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await completePmProject(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/1/complete`);
    });
  });

  // ── Milestones (nested routes) ────────────────────────────────────

  describe("listPmMilestones", () => {
    it("uses nested route when project_id provided", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmMilestones({ project_id: 5 });
      expect(api.get).toHaveBeenCalledWith(`${B}/projects/5/milestones`);
    });

    it("uses top-level route when no project_id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmMilestones();
      expect(api.get).toHaveBeenCalledWith(`${B}/milestones`);
    });
  });

  describe("createPmMilestone", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmMilestone({ project_id: 5, name: "M1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/5/milestones`, { project_id: 5, name: "M1" });
    });
  });

  describe("updatePmMilestone", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmMilestone(1, { project_id: 5, name: "M1" });
      expect(api.put).toHaveBeenCalledWith(`${B}/projects/5/milestones/1`, { project_id: 5, name: "M1" });
    });

    it("uses top-level route when no project_id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmMilestone(1, { name: "M1" });
      expect(api.put).toHaveBeenCalledWith(`${B}/milestones/1`, { name: "M1" });
    });
  });

  describe("deletePmMilestone", () => {
    it("uses nested route when projectId provided", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmMilestone(1, 5);
      expect(api.delete).toHaveBeenCalledWith(`${B}/projects/5/milestones/1`);
    });

    it("uses top-level route when no projectId", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmMilestone(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/milestones/1`);
    });
  });

  // ── Tasks (nested routes) ─────────────────────────────────────────

  describe("listPmTasks", () => {
    it("uses nested route when project_id provided", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmTasks({ project_id: 3 });
      expect(api.get).toHaveBeenCalledWith(`${B}/projects/3/tasks`);
    });

    it("uses top-level route when no project_id", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmTasks();
      expect(api.get).toHaveBeenCalledWith(`${B}/tasks`);
    });
  });

  describe("createPmTask", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmTask({ project_id: 3, title: "T1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/3/tasks`, { project_id: 3, title: "T1" });
    });

    it("uses top-level route when no project_id", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmTask({ title: "T1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/tasks`, { title: "T1" });
    });
  });

  describe("updatePmTask", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmTask(1, { project_id: 3, title: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/projects/3/tasks/1`, { project_id: 3, title: "Updated" });
    });

    it("uses top-level route when no project_id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmTask(1, { title: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/tasks/1`, { title: "Updated" });
    });
  });

  describe("deletePmTask", () => {
    it("uses nested route when projectId provided", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmTask(1, 3);
      expect(api.delete).toHaveBeenCalledWith(`${B}/projects/3/tasks/1`);
    });

    it("uses top-level route when no projectId", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmTask(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/tasks/1`);
    });
  });

  describe("movePmTask", () => {
    it("calls POST /tasks/:id/move", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await movePmTask(1, { column_id: "todo" });
      expect(api.post).toHaveBeenCalledWith(`${B}/tasks/1/move`, { column_id: "todo" });
    });
  });

  // ── Risks ─────────────────────────────────────────────────────────

  describe("listPmRisks", () => {
    it("uses nested route when project_id provided", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmRisks({ project_id: 2 });
      expect(api.get).toHaveBeenCalledWith(`${B}/projects/2/risks`);
    });
  });

  describe("createPmRisk", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmRisk({ project_id: 2, title: "R1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/2/risks`, { project_id: 2, title: "R1" });
    });
  });

  describe("updatePmRisk", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmRisk(1, { project_id: 2, title: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/projects/2/risks/1`, { project_id: 2, title: "Updated" });
    });
  });

  describe("deletePmRisk", () => {
    it("uses nested route when projectId provided", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmRisk(1, 2);
      expect(api.delete).toHaveBeenCalledWith(`${B}/projects/2/risks/1`);
    });
  });

  // ── Issues ────────────────────────────────────────────────────────

  describe("listPmIssues", () => {
    it("uses nested route when project_id provided", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmIssues({ project_id: 2 });
      expect(api.get).toHaveBeenCalledWith(`${B}/projects/2/issues`);
    });
  });

  describe("createPmIssue", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmIssue({ project_id: 2, title: "I1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/projects/2/issues`, { project_id: 2, title: "I1" });
    });
  });

  describe("updatePmIssue", () => {
    it("uses nested route when project_id in payload", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmIssue(1, { project_id: 2, title: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/projects/2/issues/1`, { project_id: 2, title: "Updated" });
    });
  });

  describe("deletePmIssue", () => {
    it("uses nested route when projectId provided", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmIssue(1, 2);
      expect(api.delete).toHaveBeenCalledWith(`${B}/projects/2/issues/1`);
    });
  });

  describe("promotePmIssueToTask", () => {
    it("calls POST /issues/:id/promote-to-task", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 5 } } });
      await promotePmIssueToTask(3);
      expect(api.post).toHaveBeenCalledWith(`${B}/issues/3/promote-to-task`);
    });
  });

  // ── Templates ─────────────────────────────────────────────────────

  describe("listPmTemplates", () => {
    it("calls GET /templates", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmTemplates();
      expect(api.get).toHaveBeenCalledWith(`${B}/templates`);
    });
  });

  describe("createPmTemplate", () => {
    it("calls POST /templates", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmTemplate({ name: "T1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/templates`, { name: "T1" });
    });
  });

  describe("updatePmTemplate", () => {
    it("calls PUT /templates/:id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmTemplate(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/templates/1`, { name: "Updated" });
    });
  });

  describe("deletePmTemplate", () => {
    it("calls DELETE /templates/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmTemplate(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/templates/1`);
    });
  });

  // ── Webhooks ──────────────────────────────────────────────────────

  describe("listPmWebhooks", () => {
    it("calls GET /webhooks", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listPmWebhooks();
      expect(api.get).toHaveBeenCalledWith(`${B}/webhooks`);
    });
  });

  describe("createPmWebhook", () => {
    it("calls POST /webhooks", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createPmWebhook({ url: "https://example.com" });
      expect(api.post).toHaveBeenCalledWith(`${B}/webhooks`, { url: "https://example.com" });
    });
  });

  describe("updatePmWebhook", () => {
    it("calls PUT /webhooks/:id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updatePmWebhook(1, { url: "https://updated.com" });
      expect(api.put).toHaveBeenCalledWith(`${B}/webhooks/1`, { url: "https://updated.com" });
    });
  });

  describe("deletePmWebhook", () => {
    it("calls DELETE /webhooks/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deletePmWebhook(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/webhooks/1`);
    });
  });

  // ── Dashboard ─────────────────────────────────────────────────────

  describe("getPmDashboard", () => {
    it("calls GET /dashboard and returns data.data", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { total_projects: 10 } } });
      const result = await getPmDashboard();
      expect(api.get).toHaveBeenCalledWith(`${B}/dashboard`);
      expect(result.total_projects).toBe(10);
    });
  });

  // ── Reports ───────────────────────────────────────────────────────

  describe("reports", () => {
    it("getPmReportThroughput calls GET with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getPmReportThroughput({ from: "2025-01-01" });
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/throughput`, { params: { from: "2025-01-01" } });
    });

    it("getPmReportOverdue calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getPmReportOverdue();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/overdue`, { params: undefined });
    });

    it("getPmReportWorkload calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getPmReportWorkload();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/workload`);
    });

    it("getPmReportHealth calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getPmReportHealth();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/health`);
    });
  });
});
