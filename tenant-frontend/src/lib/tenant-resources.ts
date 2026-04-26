import api from "@/lib/api";

const T = "/tenant";

/**
 * Table listing parameters for backend search, sort, and pagination
 */
export interface TableParams {
  page?: number;
  per_page?: number | 'all';
  search?: string;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
  filters?: Record<string, unknown>;
}

/**
 * Paginated response from backend
 */
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

/**
 * Build query string from table params
 */
function buildTableQuery(params?: TableParams): string {
  if (!params) return '';
  const query = new URLSearchParams();
  if (params.page) query.append('page', String(params.page));
  if (params.per_page) query.append('per_page', String(params.per_page));
  if (params.search) query.append('search', params.search);
  if (params.sort_by) query.append('sort_by', params.sort_by);
  if (params.sort_direction) query.append('sort_direction', params.sort_direction);
  if (params.filters) {
    Object.entries(params.filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        query.append(`filters[${key}]`, String(value));
      }
    });
  }
  const queryString = query.toString();
  return queryString ? `?${queryString}` : '';
}

export const listRoles = (params?: TableParams) => api.get(`${T}/roles${buildTableQuery(params)}`).then((r) => (r.data?.data ?? r.data) as unknown[]);
export const createRole = (p: Record<string, unknown>) => api.post(`${T}/roles`, p);
export const updateRole = (id: number, p: Record<string, unknown>) => api.put(`${T}/roles/${id}`, p);
export const deleteRole = (id: number) => api.delete(`${T}/roles/${id}`);

export const listPermissions = (params?: TableParams) => api.get(`${T}/permissions${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPermission = (p: Record<string, unknown>) => api.post(`${T}/permissions`, p);
export const updatePermission = (id: number, p: Record<string, unknown>) => api.put(`${T}/permissions/${id}`, p);
export const deletePermission = (id: number) => api.delete(`${T}/permissions/${id}`);

export const listUsers = (params?: TableParams) => api.get(`${T}/users${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createUser = (p: Record<string, unknown>) => api.post(`${T}/users`, p);
export const updateUser = (id: number, p: Record<string, unknown>) => api.put(`${T}/users/${id}`, p);
export const deleteUser = (id: number) => api.delete(`${T}/users/${id}`);

export const listActivityLogs = (params?: TableParams) => api.get(`${T}/activity-logs${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const listLoginAttempts = (params?: TableParams) => api.get(`${T}/login-attempts${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);

export const getSettings = () => api.get(`${T}/settings`).then((r) => r.data?.data ?? r.data);
export const updateSettings = (p: Record<string, unknown>) => api.put(`${T}/settings`, p);

export const getProfile = () => api.get(`${T}/profile`).then((r) => r.data?.data ?? r.data);
export const updateProfile = (p: Record<string, unknown>) => api.put(`${T}/profile`, p);
export const uploadAvatar = (f: File) => { const fd = new FormData(); fd.append("avatar", f); return api.post(`${T}/profile/avatar`, fd); };
export const changePassword = (p: Record<string, unknown>) => api.post(`${T}/profile/password`, p);

export const listBrands = (params?: TableParams) => api.get(`${T}/brands${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createBrand = (p: Record<string, unknown>) => api.post(`${T}/brands`, p);
export const updateBrand = (id: number, p: Record<string, unknown>) => api.put(`${T}/brands/${id}`, p);
export const deleteBrand = (id: number) => api.delete(`${T}/brands/${id}`);
export const getAvailableModules = () => api.get(`${T}/available-modules`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getBrandWithModules = (id: number) => api.get(`${T}/brands/${id}`).then((r) => r.data?.data ?? r.data);
export const uploadBrandingFile = (f: File, key: string) => { const fd = new FormData(); fd.append(key, f); return api.post(`${T}/settings/branding`, fd); };

export const listBranches = (params?: TableParams) => api.get(`${T}/branches${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createBranch = (p: Record<string, unknown>) => api.post(`${T}/branches`, p);
export const updateBranch = (id: number, p: Record<string, unknown>) => api.put(`${T}/branches/${id}`, p);
export const deleteBranch = (id: number) => api.delete(`${T}/branches/${id}`);

export const listTickets = (params?: TableParams) => api.get(`${T}/tickets${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTicket = (p: Record<string, unknown>) => api.post(`${T}/tickets`, p);
export const updateTicket = (id: number, p: Record<string, unknown>) => api.put(`${T}/tickets/${id}`, p);
export const deleteTicket = (id: number) => api.delete(`${T}/tickets/${id}`);
export const getTicketKanban = () => api.get(`${T}/tickets/kanban-data`).then((r) => r.data?.data ?? r.data);
export const getTicketStats = () => api.get(`${T}/tickets/stats`).then((r) => r.data?.data ?? r.data);

export const getCrmData = () => api.get(`${T}/modules/crm`).then((r) => r.data?.data ?? r.data);

// ─── CRM Module ───────────────────────────────────────────────────────────────
const CRM = `${T}/crm`;

export const getCrmDashboard = () => api.get(`${CRM}/dashboard`).then((r) => r.data?.data ?? r.data);

export const listCrmLeads = <T = unknown>(params?: TableParams): Promise<PaginatedResponse<T>> => api.get(`${CRM}/leads${buildTableQuery(params)}`).then((r) => r.data as PaginatedResponse<T>);
export const getCrmLead = (id: number) => api.get(`${CRM}/leads/${id}`).then((r) => r.data?.data ?? r.data);
export const createCrmLead = (p: Record<string, unknown>) => api.post(`${CRM}/leads`, p);
export const updateCrmLead = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/leads/${id}`, p);
export const deleteCrmLead = (id: number): Promise<void> => api.delete(`${CRM}/leads/${id}`).then(() => undefined);
export const convertCrmLead = (id: number, p?: Record<string, unknown>) => api.post(`${CRM}/leads/${id}/convert`, p ?? {});

export const listCrmOpportunities = <T = unknown>(params?: TableParams): Promise<PaginatedResponse<T>> => api.get(`${CRM}/opportunities${buildTableQuery(params)}`).then((r) => r.data as PaginatedResponse<T>);
export const getCrmOpportunity = (id: number) => api.get(`${CRM}/opportunities/${id}`).then((r) => r.data?.data ?? r.data);
export const createCrmOpportunity = (p: Record<string, unknown>) => api.post(`${CRM}/opportunities`, p);
export const updateCrmOpportunity = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/opportunities/${id}`, p);
export const deleteCrmOpportunity = (id: number): Promise<void> => api.delete(`${CRM}/opportunities/${id}`).then(() => undefined);
export const moveCrmOpportunityStage = (id: number, stage: string) => api.post(`${CRM}/opportunities/${id}/move-stage`, { stage });
export const closeCrmOpportunityWon = (id: number) => api.post(`${CRM}/opportunities/${id}/close-won`);
export const getCrmPipeline = () => api.get(`${CRM}/opportunities/pipeline`).then((r) => r.data?.data ?? r.data);

export const listCrmActivities = <T = unknown>(params?: TableParams): Promise<PaginatedResponse<T>> => api.get(`${CRM}/activities${buildTableQuery(params)}`).then((r) => r.data as PaginatedResponse<T>);
export const getCrmActivity = (id: number) => api.get(`${CRM}/activities/${id}`).then((r) => r.data?.data ?? r.data);
export const createCrmActivity = (p: Record<string, unknown>) => api.post(`${CRM}/activities`, p);
export const updateCrmActivity = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/activities/${id}`, p);
export const deleteCrmActivity = (id: number): Promise<void> => api.delete(`${CRM}/activities/${id}`).then(() => undefined);
export const completeCrmActivity = (id: number, outcome?: string) => api.post(`${CRM}/activities/${id}/complete`, { outcome });

export const listCrmContacts = <T = unknown>(params?: TableParams): Promise<PaginatedResponse<T>> => api.get(`${CRM}/contacts${buildTableQuery(params)}`).then((r) => r.data as PaginatedResponse<T>);
export const createCrmContact = (p: Record<string, unknown>) => api.post(`${CRM}/contacts`, p);
export const updateCrmContact = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/contacts/${id}`, p);
export const deleteCrmContact = (id: number): Promise<void> => api.delete(`${CRM}/contacts/${id}`).then(() => undefined);

export const listCrmCompanies = <T = unknown>(params?: TableParams): Promise<PaginatedResponse<T>> => api.get(`${CRM}/companies${buildTableQuery(params)}`).then((r) => r.data as PaginatedResponse<T>);
export const createCrmCompany = (p: Record<string, unknown>) => api.post(`${CRM}/companies`, p);
export const updateCrmCompany = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/companies/${id}`, p);
export const deleteCrmCompany = (id: number): Promise<void> => api.delete(`${CRM}/companies/${id}`).then(() => undefined);

export const listCrmNotes = (params?: TableParams) => api.get(`${CRM}/notes${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmNote = (p: Record<string, unknown>) => api.post(`${CRM}/notes`, p);
export const updateCrmNote = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/notes/${id}`, p);
export const deleteCrmNote = (id: number) => api.delete(`${CRM}/notes/${id}`);
export const getCrmNotesForRelated = (type: string, id: number) => api.get(`${CRM}/notes/related/${type}/${id}`).then((r) => r.data?.data ?? r.data);

export const listCrmFiles = (params?: TableParams) => api.get(`${CRM}/files${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmFile = (p: Record<string, unknown>) => api.post(`${CRM}/files`, p);
export const deleteCrmFile = (id: number) => api.delete(`${CRM}/files/${id}`);
export const downloadCrmFile = (id: number) => api.get(`${CRM}/files/${id}/download`);
export const getCrmFilesForRelated = (type: string, id: number) => api.get(`${CRM}/files/related/${type}/${id}`).then((r) => r.data?.data ?? r.data);

export const listCrmPipelineStages = () => api.get(`${CRM}/pipeline-stages`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmPipelineStage = (p: Record<string, unknown>) => api.post(`${CRM}/pipeline-stages`, p);
export const updateCrmPipelineStage = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/pipeline-stages/${id}`, p);
export const deleteCrmPipelineStage = (id: number) => api.delete(`${CRM}/pipeline-stages/${id}`);
export const reorderCrmPipelineStages = (stages: { id: number; order: number }[]) => api.post(`${CRM}/pipeline-stages/reorder`, { stages });

export const listCrmAutomationRules = (params?: TableParams) => api.get(`${CRM}/automation-rules${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmAutomationRule = (p: Record<string, unknown>) => api.post(`${CRM}/automation-rules`, p);
export const updateCrmAutomationRule = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/automation-rules/${id}`, p);
export const deleteCrmAutomationRule = (id: number) => api.delete(`${CRM}/automation-rules/${id}`);
export const toggleCrmAutomationRule = (id: number) => api.post(`${CRM}/automation-rules/${id}/toggle`);

export const listCrmWebhooks = (params?: TableParams) => api.get(`${CRM}/webhooks${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmWebhook = (p: Record<string, unknown>) => api.post(`${CRM}/webhooks`, p);
export const updateCrmWebhook = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/webhooks/${id}`, p);
export const deleteCrmWebhook = (id: number) => api.delete(`${CRM}/webhooks/${id}`);
export const toggleCrmWebhook = (id: number) => api.post(`${CRM}/webhooks/${id}/toggle`);
export const regenerateCrmWebhookSecret = (id: number) => api.post(`${CRM}/webhooks/${id}/regenerate-secret`).then((r) => r.data);

export const listCrmImportJobs = (params?: TableParams) => api.get(`${CRM}/import-jobs${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmImportJob = (p: Record<string, unknown>) => api.post(`${CRM}/import-jobs`, p);
export const deleteCrmImportJob = (id: number) => api.delete(`${CRM}/import-jobs/${id}`);
export const downloadCrmImportTemplate = (entityType: string) => api.get(`${CRM}/import-jobs/template/${entityType}`).then((r) => r.data);

export const getCrmReportOverview = () => api.get(`${CRM}/reports/overview`).then((r) => r.data?.data ?? r.data);
export const getCrmReportPipeline = () => api.get(`${CRM}/reports/pipeline`).then((r) => r.data?.data ?? r.data);
export const getCrmReportConversion = () => api.get(`${CRM}/reports/conversion`).then((r) => r.data?.data ?? r.data);
export const getCrmReportMonthlyTrends = (months?: number) => api.get(`${CRM}/reports/monthly-trends`, { params: { months } }).then((r) => r.data?.data ?? r.data);

export const crmSearch = (q: string, limit?: number) => api.get(`${CRM}/search`, { params: { q, limit } }).then((r) => r.data?.data ?? r.data);
export const getHrData = () => api.get(`${T}/modules/hr`).then((r) => r.data?.data ?? r.data);
export const getPosData = () => api.get(`${T}/modules/pos`).then((r) => r.data?.data ?? r.data);

// ─── HR Module Helpers ───────────────────────────────────────────────────────
export const getHrMe = () => api.get(`${T}/hr/me`).then((r) => r.data?.data ?? r.data);
export const getHrReportsHeadcount = () => api.get(`${T}/hr/reports/headcount`).then((r) => r.data?.data ?? r.data);
export const listHrRecruitmentJobs = (params?: TableParams) => api.get(`${T}/hr/recruitment/jobs${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);

export const getSubscribedModules = () => api.get(`${T}/modules`).then((r) => r.data?.data ?? r.data);
export const getModule = (moduleKey: string) => api.get(`${T}/modules/${moduleKey}`).then((r) => r.data?.data ?? r.data);

// ─── POS Module ──────────────────────────────────────────────────────────────
const POS = `${T}/pos`;

export const getPosDashboard = () => api.get(`${POS}/dashboard`).then((r) => r.data?.data ?? r.data);

export const listPosProducts = (params?: TableParams) => api.get(`${POS}/products${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getPosProduct = (id: number) => api.get(`${POS}/products/${id}`).then((r) => r.data?.data ?? r.data);
export const createPosProduct = (p: Record<string, unknown>) => api.post(`${POS}/products`, p);
export const updatePosProduct = (id: number, p: Record<string, unknown>) => api.put(`${POS}/products/${id}`, p);
export const deletePosProduct = (id: number) => api.delete(`${POS}/products/${id}`);
export const bulkDeletePosProducts = (ids: number[]) => api.post(`${POS}/products/bulk-delete`, { ids });
export const changePosProductStock = (id: number, p: { amount: number; direction: 'increment' | 'decrement'; branch_id?: number }) => api.patch(`${POS}/products/${id}/stock`, p);
export const searchPosByBarcode = (barcode: string) => api.get(`${POS}/products/barcode/${barcode}`).then((r) => r.data?.data ?? r.data);

export const listPosCategories = (params?: TableParams) => api.get(`${POS}/categories${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPosCategory = (p: Record<string, unknown>) => api.post(`${POS}/categories`, p);
export const updatePosCategory = (id: number, p: Record<string, unknown>) => api.put(`${POS}/categories/${id}`, p);
export const deletePosCategory = (id: number) => api.delete(`${POS}/categories/${id}`);

export const listPosSubCategories = (params?: TableParams) => api.get(`${POS}/sub-categories${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPosSubCategory = (p: Record<string, unknown>) => api.post(`${POS}/sub-categories`, p);
export const updatePosSubCategory = (id: number, p: Record<string, unknown>) => api.put(`${POS}/sub-categories/${id}`, p);
export const deletePosSubCategory = (id: number) => api.delete(`${POS}/sub-categories/${id}`);

export const listPosBarcodes = (params?: TableParams) => api.get(`${POS}/barcodes${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPosBarcode = (p: Record<string, unknown>) => api.post(`${POS}/barcodes`, p);
export const deletePosBarcode = (id: number) => api.delete(`${POS}/barcodes/${id}`);
export const searchPosBarcode = (barcode: string) => api.get(`${POS}/barcodes/search/${barcode}`).then((r) => r.data?.data ?? r.data);

export const listPosTags = (params?: TableParams) => api.get(`${POS}/tags${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPosTag = (p: Record<string, unknown>) => api.post(`${POS}/tags`, p);
export const deletePosTag = (id: number) => api.delete(`${POS}/tags/${id}`);

export const listPosOfferPrices = (params?: TableParams) => api.get(`${POS}/offer-prices${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPosOfferPrice = (p: Record<string, unknown>) => api.post(`${POS}/offer-prices`, p);
export const updatePosOfferPrice = (id: number, p: Record<string, unknown>) => api.put(`${POS}/offer-prices/${id}`, p);
export const deletePosOfferPrice = (id: number) => api.delete(`${POS}/offer-prices/${id}`);

export const listPosDamaged = (params?: TableParams) => api.get(`${POS}/damaged${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPosDamaged = (p: Record<string, unknown>) => api.post(`${POS}/damaged`, p);
export const deletePosDamaged = (id: number) => api.delete(`${POS}/damaged/${id}`);

// ─── Sales Module ─────────────────────────────────────────────────────────────
const SALES = `${T}/sales`;

export const getSalesSummary = (params?: { date?: string; branch_id?: number }) => api.get(`${SALES}/summary`, { params }).then((r) => r.data?.data ?? r.data);
export const listSalesOrders = (params?: TableParams) => api.get(`${SALES}/orders${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getSalesOrder = (id: number) => api.get(`${SALES}/orders/${id}`).then((r) => r.data?.data ?? r.data);
export const createSalesOrder = (p: Record<string, unknown>) => api.post(`${SALES}/orders`, p);
export const cancelSalesOrder = (id: number) => api.patch(`${SALES}/orders/${id}/cancel`);
export const deleteSalesOrder = (id: number) => api.delete(`${SALES}/orders/${id}`);
export const bulkDeleteSalesOrders = (ids: number[]) => api.post(`${SALES}/orders/bulk-delete`, { ids });

// ─── Inventory Module ─────────────────────────────────────────────────────────
const INV = `${T}/inventory`;

export const listWarehouses = (params?: TableParams) => api.get(`${INV}/warehouses${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getWarehouse = (id: number) => api.get(`${INV}/warehouses/${id}`).then((r) => r.data?.data ?? r.data);
export const createWarehouse = (p: Record<string, unknown>) => api.post(`${INV}/warehouses`, p);
export const updateWarehouse = (id: number, p: Record<string, unknown>) => api.put(`${INV}/warehouses/${id}`, p);
export const deleteWarehouse = (id: number) => api.delete(`${INV}/warehouses/${id}`);
export const getWarehouseStockSummary = (id: number, productId?: number) => api.get(`${INV}/warehouses/${id}/stock-summary`, { params: productId ? { product_id: productId } : {} }).then((r) => r.data?.data ?? r.data);

export const listStockMoves = (params?: TableParams) => api.get(`${INV}/stock-moves${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createStockMove = (p: Record<string, unknown>) => api.post(`${INV}/stock-moves`, p);
export const confirmStockMove = (id: number) => api.patch(`${INV}/stock-moves/${id}/confirm`);
export const completeStockMove = (id: number) => api.patch(`${INV}/stock-moves/${id}/complete`);
export const cancelStockMove = (id: number) => api.patch(`${INV}/stock-moves/${id}/cancel`);
export const deleteStockMove = (id: number) => api.delete(`${INV}/stock-moves/${id}`);

export const getDashboardStats = () => api.get(`${T}/dashboard/stats`).then((r) => r.data?.data ?? r.data);

export const setup2fa = () => api.post(`${T}/2fa/setup`).then((r) => r.data?.data ?? r.data);
export const confirm2fa = (code: string, secret: string) => api.post(`${T}/2fa/confirm`, { code, secret });
export const disable2fa = () => api.post(`${T}/2fa/disable`);

// ─── Project Management Module ──────────────────────────────────────────────
const PM = `${T}/project-management`;

export const getProjectManagementData = () => api.get(`${T}/modules/project-management`).then((r) => r.data?.data ?? r.data);
export const getPmDashboard = () => api.get(`${PM}/dashboard`).then((r) => r.data?.data ?? r.data);

export const listPmWorkspaces = (params?: TableParams) => api.get(`${PM}/workspaces${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmWorkspace = (p: Record<string, unknown>) => api.post(`${PM}/workspaces`, p);
export const getPmWorkspace = (id: string) => api.get(`${PM}/workspaces/${id}`).then((r) => r.data?.data ?? r.data);
export const updatePmWorkspace = (id: string, p: Record<string, unknown>) => api.put(`${PM}/workspaces/${id}`, p);
export const deletePmWorkspace = (id: string) => api.delete(`${PM}/workspaces/${id}`);

export const listPmProjects = (params?: TableParams) => api.get(`${PM}/projects${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getPmProject = (id: string) => api.get(`${PM}/projects/${id}`).then((r) => r.data?.data ?? r.data);
export const createPmProject = (p: Record<string, unknown>) => api.post(`${PM}/projects`, p);
export const updatePmProject = (id: string, p: Record<string, unknown>) => api.put(`${PM}/projects/${id}`, p);
export const deletePmProject = (id: string) => api.delete(`${PM}/projects/${id}`);
export const archivePmProject = (id: string) => api.post(`${PM}/projects/${id}/archive`);
export const pausePmProject = (id: string) => api.post(`${PM}/projects/${id}/pause`);
export const completePmProject = (id: string) => api.post(`${PM}/projects/${id}/complete`);
export const recalculatePmProjectHealth = (id: string) => api.post(`${PM}/projects/${id}/health`);
export const createPmProjectFromTemplate = (id: string, p?: Record<string, unknown>) => api.post(`${PM}/projects/${id}/create-from-template`, p ?? {});

export const listPmMilestones = (projectId: string, params?: TableParams) => api.get(`${PM}/projects/${projectId}/milestones${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmMilestone = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/milestones`, p);
export const getPmMilestone = (id: string) => api.get(`${PM}/milestones/${id}`).then((r) => r.data?.data ?? r.data);
export const updatePmMilestone = (id: string, p: Record<string, unknown>) => api.put(`${PM}/milestones/${id}`, p);
export const deletePmMilestone = (id: string) => api.delete(`${PM}/milestones/${id}`);

export const listPmTasks = (projectId: string, params?: TableParams) => api.get(`${PM}/projects/${projectId}/tasks${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getPmTask = (id: string) => api.get(`${PM}/tasks/${id}`).then((r) => r.data?.data ?? r.data);
export const createPmTask = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/tasks`, p);
export const updatePmTask = (id: string, p: Record<string, unknown>) => api.put(`${PM}/tasks/${id}`, p);
export const deletePmTask = (id: string) => api.delete(`${PM}/tasks/${id}`);
export const movePmTask = (id: string, p: { board_column_id: string; position?: number }) => api.post(`${PM}/tasks/${id}/move`, p);
export const reorderPmTask = (id: string, p: { position: number }) => api.post(`${PM}/tasks/${id}/reorder`, p);
export const attachPmTaskLabels = (id: string, labelIds: string[]) => api.post(`${PM}/tasks/${id}/labels`, { label_ids: labelIds });
export const detachPmTaskLabels = (id: string, labelIds: string[]) => api.delete(`${PM}/tasks/${id}/labels`, { data: { label_ids: labelIds } });

export const getPmBoard = (projectId: string) => api.get(`${PM}/projects/${projectId}/board`).then((r) => r.data?.data ?? r.data);
export const configurePmBoard = (projectId: string, p: Record<string, unknown>) => api.put(`${PM}/projects/${projectId}/board/configure`, p);

export const listPmBoardColumns = (projectId: string) => api.get(`${PM}/projects/${projectId}/board-columns`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmBoardColumn = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/board-columns`, p);
export const updatePmBoardColumn = (id: string, p: Record<string, unknown>) => api.put(`${PM}/board-columns/${id}`, p);
export const deletePmBoardColumn = (id: string) => api.delete(`${PM}/board-columns/${id}`);
export const reorderPmBoardColumns = (projectId: string, columns: { id: string; position: number }[]) => api.post(`${PM}/projects/${projectId}/board-columns/reorder`, { columns });

export const listPmBoardSwimlanes = (projectId: string) => api.get(`${PM}/projects/${projectId}/board-swimlanes`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmBoardSwimlane = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/board-swimlanes`, p);
export const updatePmBoardSwimlane = (id: string, p: Record<string, unknown>) => api.put(`${PM}/board-swimlanes/${id}`, p);
export const deletePmBoardSwimlane = (id: string) => api.delete(`${PM}/board-swimlanes/${id}`);
export const reorderPmBoardSwimlanes = (projectId: string, swimlanes: { id: string; position: number }[]) => api.post(`${PM}/projects/${projectId}/board-swimlanes/reorder`, { swimlanes });

export const listPmLabels = (projectId: string) => api.get(`${PM}/projects/${projectId}/labels`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmLabel = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/labels`, p);
export const updatePmLabel = (id: string, p: Record<string, unknown>) => api.put(`${PM}/labels/${id}`, p);
export const deletePmLabel = (id: string) => api.delete(`${PM}/labels/${id}`);

export const listPmSprintCycles = (projectId: string, params?: TableParams) => api.get(`${PM}/projects/${projectId}/sprint-cycles${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmSprintCycle = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/sprint-cycles`, p);

export const listPmProjectMembers = (projectId: string) => api.get(`${PM}/projects/${projectId}/members`).then((r) => r.data?.data ?? r.data as unknown[]);
export const addPmProjectMember = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/members`, p);
export const removePmProjectMember = (id: string) => api.delete(`${PM}/members/${id}`);

export const listPmTemplates = (params?: TableParams) => api.get(`${PM}/templates${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getPmTemplate = (id: string) => api.get(`${PM}/templates/${id}`).then((r) => r.data?.data ?? r.data);
export const createPmTemplate = (p: Record<string, unknown>) => api.post(`${PM}/templates`, p);
export const updatePmTemplate = (id: string, p: Record<string, unknown>) => api.put(`${PM}/templates/${id}`, p);
export const deletePmTemplate = (id: string) => api.delete(`${PM}/templates/${id}`);

export const listPmRisks = (projectId: string, params?: TableParams) => api.get(`${PM}/projects/${projectId}/risks${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmRisk = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/risks`, p);

export const listPmIssues = (projectId: string, params?: TableParams) => api.get(`${PM}/projects/${projectId}/issues${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmIssue = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/issues`, p);
export const promotePmIssueToTask = (id: string) => api.post(`${PM}/issues/${id}/promote-to-task`);

export const listPmWebhooks = (projectId: string) => api.get(`${PM}/projects/${projectId}/webhooks`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPmWebhook = (projectId: string, p: Record<string, unknown>) => api.post(`${PM}/projects/${projectId}/webhooks`, p);
export const togglePmWebhook = (id: string) => api.post(`${PM}/webhooks/${id}/toggle`);
export const regeneratePmWebhookSecret = (id: string) => api.post(`${PM}/webhooks/${id}/regenerate-secret`).then((r) => r.data);

export const getPmReportThroughput = (params?: Record<string, unknown>) => api.get(`${PM}/reports/throughput`, { params }).then((r) => r.data?.data ?? r.data);
export const getPmReportOverdue = (params?: Record<string, unknown>) => api.get(`${PM}/reports/overdue`, { params }).then((r) => r.data?.data ?? r.data);
export const getPmReportWorkload = () => api.get(`${PM}/reports/workload`).then((r) => r.data?.data ?? r.data);
export const getPmReportHealth = () => api.get(`${PM}/reports/health`).then((r) => r.data?.data ?? r.data);

// ─── Time Management Module ──────────────────────────────────────────────────
const TM = `${T}/time-management`;

export const getTimeManagementData = () => api.get(`${T}/modules/time-management`).then((r) => r.data?.data ?? r.data);
export const getTmDashboard = () => api.get(`${TM}/dashboard`).then((r) => r.data?.data ?? r.data);

export const listTmWorkCalendars = (params?: TableParams) => api.get(`${TM}/work-calendars${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmWorkCalendar = (p: Record<string, unknown>) => api.post(`${TM}/work-calendars`, p);
export const getTmWorkCalendar = (id: string) => api.get(`${TM}/work-calendars/${id}`).then((r) => r.data?.data ?? r.data);
export const updateTmWorkCalendar = (id: string, p: Record<string, unknown>) => api.put(`${TM}/work-calendars/${id}`, p);
export const deleteTmWorkCalendar = (id: string) => api.delete(`${TM}/work-calendars/${id}`);

export const listTmShiftTemplates = (params?: TableParams) => api.get(`${TM}/shift-templates${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmShiftTemplate = (p: Record<string, unknown>) => api.post(`${TM}/shift-templates`, p);
export const updateTmShiftTemplate = (id: string, p: Record<string, unknown>) => api.put(`${TM}/shift-templates/${id}`, p);
export const deleteTmShiftTemplate = (id: string) => api.delete(`${TM}/shift-templates/${id}`);

export const listTmWorkSchedules = (params?: TableParams) => api.get(`${TM}/work-schedules${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmWorkSchedule = (p: Record<string, unknown>) => api.post(`${TM}/work-schedules`, p);
export const updateTmWorkSchedule = (id: string, p: Record<string, unknown>) => api.put(`${TM}/work-schedules/${id}`, p);
export const deleteTmWorkSchedule = (id: string) => api.delete(`${TM}/work-schedules/${id}`);

export const listTmTimeEntries = (params?: TableParams) => api.get(`${TM}/time-entries${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmTimeEntry = (p: Record<string, unknown>) => api.post(`${TM}/time-entries`, p);
export const getTmTimeEntry = (id: string) => api.get(`${TM}/time-entries/${id}`).then((r) => r.data?.data ?? r.data);
export const updateTmTimeEntry = (id: string, p: Record<string, unknown>) => api.put(`${TM}/time-entries/${id}`, p);
export const deleteTmTimeEntry = (id: string) => api.delete(`${TM}/time-entries/${id}`);
export const splitTmTimeEntry = (id: string, p: { split_at: string }) => api.post(`${TM}/time-entries/${id}/split`, p);

export const getTmActiveSession = () => api.get(`${TM}/sessions/active`).then((r) => r.data?.data ?? r.data);
export const startTmSession = (p: Record<string, unknown>) => api.post(`${TM}/sessions/start`, p);
export const stopTmSession = (id: string, p?: Record<string, unknown>) => api.post(`${TM}/sessions/${id}/stop`, p ?? {});

export const listTmTimesheets = (params?: TableParams) => api.get(`${TM}/timesheets${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getTmTimesheet = (id: string) => api.get(`${TM}/timesheets/${id}`).then((r) => r.data?.data ?? r.data);
export const createTmTimesheet = (p: Record<string, unknown>) => api.post(`${TM}/timesheets`, p);
export const submitTmTimesheet = (id: string) => api.post(`${TM}/timesheets/${id}/submit`);
export const approveTmTimesheet = (id: string, p?: Record<string, unknown>) => api.post(`${TM}/timesheets/${id}/approve`, p ?? {});
export const rejectTmTimesheet = (id: string, p: Record<string, unknown>) => api.post(`${TM}/timesheets/${id}/reject`, p);
export const autoGenerateTmTimesheet = (p?: Record<string, unknown>) => api.post(`${TM}/timesheets/auto-generate`, p ?? {});

export const clockInTm = (p?: Record<string, unknown>) => api.post(`${TM}/attendance/clock-in`, p ?? {});
export const clockOutTm = (p?: Record<string, unknown>) => api.post(`${TM}/attendance/clock-out`, p ?? {});

export const listTmOvertimeRequests = (params?: TableParams) => api.get(`${TM}/overtime-requests${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmOvertimeRequest = (p: Record<string, unknown>) => api.post(`${TM}/overtime-requests`, p);
export const approveTmOvertimeRequest = (id: string) => api.post(`${TM}/overtime-requests/${id}/approve`);
export const rejectTmOvertimeRequest = (id: string, p: Record<string, unknown>) => api.post(`${TM}/overtime-requests/${id}/reject`, p);

export const listTmPolicies = (params?: TableParams) => api.get(`${TM}/policies${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmPolicy = (p: Record<string, unknown>) => api.post(`${TM}/policies`, p);
export const updateTmPolicy = (id: string, p: Record<string, unknown>) => api.put(`${TM}/policies/${id}`, p);
export const deleteTmPolicy = (id: string) => api.delete(`${TM}/policies/${id}`);

export const listTmCalendarEvents = (params?: TableParams) => api.get(`${TM}/calendar-events${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getTmCalendarEvent = (id: string) => api.get(`${TM}/calendar-events/${id}`).then((r) => r.data?.data ?? r.data);
export const createTmCalendarEvent = (p: Record<string, unknown>) => api.post(`${TM}/calendar-events`, p);
export const updateTmCalendarEvent = (id: string, p: Record<string, unknown>) => api.put(`${TM}/calendar-events/${id}`, p);
export const deleteTmCalendarEvent = (id: string) => api.delete(`${TM}/calendar-events/${id}`);
export const generateTmMeetingLink = (id: string, p?: Record<string, unknown>) => api.post(`${TM}/calendar-events/${id}/generate-meeting-link`, p ?? {});
export const checkTmCalendarConflicts = (p: Record<string, unknown>) => api.post(`${TM}/calendar-events/check-conflicts`, p);

export const connectTmCalendarProvider = (provider: 'google' | 'outlook') => api.get(`${TM}/calendar/connect/${provider}`);
export const callbackTmCalendarProvider = (provider: 'google' | 'outlook') => api.get(`${TM}/calendar/callback/${provider}`);
export const disconnectTmCalendarProvider = (provider: 'google' | 'outlook') => api.post(`${TM}/calendar/disconnect/${provider}`);
export const getTmCalendarSyncStatus = () => api.get(`${TM}/calendar/sync-status`).then((r) => r.data?.data ?? r.data);
export const triggerTmCalendarSync = (p?: Record<string, unknown>) => api.post(`${TM}/calendar/trigger-sync`, p ?? {});
export const resolveTmCalendarConflict = (p: Record<string, unknown>) => api.post(`${TM}/calendar/resolve-conflict`, p);

export const listTmMeetingLinks = (params?: TableParams) => api.get(`${TM}/meeting-links${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const regenerateTmMeetingLink = (id: string) => api.post(`${TM}/meeting-links/${id}/regenerate`);

export const listTmWebhooks = (params?: TableParams) => api.get(`${TM}/webhooks${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTmWebhook = (p: Record<string, unknown>) => api.post(`${TM}/webhooks`, p);
export const toggleTmWebhook = (id: string) => api.post(`${TM}/webhooks/${id}/toggle`);
export const regenerateTmWebhookSecret = (id: string) => api.post(`${TM}/webhooks/${id}/regenerate-secret`).then((r) => r.data);

export const getTmReportUtilization = (params?: Record<string, unknown>) => api.get(`${TM}/reports/utilization`, { params }).then((r) => r.data?.data ?? r.data);
export const getTmReportSubmittedHours = (params?: Record<string, unknown>) => api.get(`${TM}/reports/submitted-hours`, { params }).then((r) => r.data?.data ?? r.data);
export const getTmReportAnomalies = () => api.get(`${TM}/reports/anomalies`).then((r) => r.data?.data ?? r.data);
export const getTmReportOvertime = (params?: Record<string, unknown>) => api.get(`${TM}/reports/overtime`, { params }).then((r) => r.data?.data ?? r.data);
export const getTmReportBillableRatio = (params?: Record<string, unknown>) => api.get(`${TM}/reports/billable-ratio`, { params }).then((r) => r.data?.data ?? r.data);
