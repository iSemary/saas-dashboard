import api from "@/lib/api";

export interface Paginated<T> {
  data: T[];
}

export async function listUsers() {
  const res = await api.get("/users");
  return Array.isArray(res.data) ? res.data : [];
}
export async function listRoles() {
  const res = await api.get("/roles");
  return Array.isArray(res.data) ? res.data : [];
}
export async function listPermissions() {
  const res = await api.get("/permissions");
  return Array.isArray(res.data) ? res.data : [];
}
export async function dashboardStats() {
  const res = await api.get("/dashboard/stats");
  return res.data;
}
export interface AppNotificationRow {
  id: number;
  title: string;
  message: string;
  type: string | null;
  read_at: string | null;
  created_at?: string;
}

export async function listNotifications(params?: { per_page?: number }) {
  const res = await api.get("/notifications", { params });
  return Array.isArray(res.data) ? (res.data as AppNotificationRow[]) : [];
}

export async function getUnreadNotificationCount(): Promise<number> {
  const res = await api.get<{ count: number }>("/notifications/unread-count");
  const data = res.data as { count?: number };
  return typeof data?.count === "number" ? data.count : 0;
}

export async function markNotificationRead(id: number) {
  await api.post(`/notifications/${id}/read`);
}

export async function markAllNotificationsRead() {
  await api.post("/notifications/mark-all-read");
}
export async function listActivityLogs() {
  const res = await api.get("/activity-logs");
  return Array.isArray(res.data) ? res.data : [];
}
export async function listFeatureFlags() {
  const res = await api.get("/feature-flags");
  return Array.isArray(res.data) ? res.data : [];
}

export type ConfigurationRow = {
  id: number;
  configuration_key: string;
  configuration_value: unknown;
  description: string | null;
  type_id: number | null;
  input_type: string;
  is_encrypted: boolean;
  is_system: boolean;
  is_visible: boolean;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
};

export async function listConfigurations(params?: { with_trashed?: boolean; per_page?: number }) {
  const res = await api.get<ConfigurationRow[]>("/configurations", {
    params: {
      per_page: params?.per_page ?? 50,
      with_trashed: params?.with_trashed ? 1 : undefined,
    },
  });
  return Array.isArray(res.data) ? res.data : [];
}
