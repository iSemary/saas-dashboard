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
export const deleteCrmNote = (id: number) => api.delete(`${CRM}/notes/${id}`);

export const listCrmPipelineStages = () => api.get(`${CRM}/pipeline-stages`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createCrmPipelineStage = (p: Record<string, unknown>) => api.post(`${CRM}/pipeline-stages`, p);
export const updateCrmPipelineStage = (id: number, p: Record<string, unknown>) => api.put(`${CRM}/pipeline-stages/${id}`, p);
export const deleteCrmPipelineStage = (id: number) => api.delete(`${CRM}/pipeline-stages/${id}`);
export const reorderCrmPipelineStages = (stages: { id: number; order: number }[]) => api.post(`${CRM}/pipeline-stages/reorder`, { stages });

export const getCrmReportOverview = () => api.get(`${CRM}/reports/overview`).then((r) => r.data?.data ?? r.data);
export const getCrmReportPipeline = () => api.get(`${CRM}/reports/pipeline`).then((r) => r.data?.data ?? r.data);
export const getCrmReportConversion = () => api.get(`${CRM}/reports/conversion`).then((r) => r.data?.data ?? r.data);
export const getCrmReportMonthlyTrends = (months?: number) => api.get(`${CRM}/reports/monthly-trends`, { params: { months } }).then((r) => r.data?.data ?? r.data);

export const crmSearch = (q: string, limit?: number) => api.get(`${CRM}/search`, { params: { q, limit } }).then((r) => r.data?.data ?? r.data);
export const getHrData = () => api.get(`${T}/modules/hr`).then((r) => r.data?.data ?? r.data);
export const getPosData = () => api.get(`${T}/modules/pos`).then((r) => r.data?.data ?? r.data);

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

export const listSalesClients = (params?: TableParams) => api.get(`${SALES}/clients${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getSalesClient = (id: number) => api.get(`${SALES}/clients/${id}`).then((r) => r.data?.data ?? r.data);
export const createSalesClient = (p: Record<string, unknown>) => api.post(`${SALES}/clients`, p);
export const updateSalesClient = (id: number, p: Record<string, unknown>) => api.put(`${SALES}/clients/${id}`, p);
export const deleteSalesClient = (id: number) => api.delete(`${SALES}/clients/${id}`);

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
