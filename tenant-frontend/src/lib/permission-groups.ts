import api from "@/lib/api";

export type PermissionRef = { id: number; name: string };

export type PermissionGroup = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  permissions?: PermissionRef[];
  permissions_count?: number;
};

export async function listPermissionGroups(): Promise<PermissionGroup[]> {
  const res = await api.get("/permission-groups", { params: { per_page: 200 } });
  return Array.isArray(res.data) ? (res.data as PermissionGroup[]) : [];
}

export async function fetchPermissionGroup(id: number): Promise<PermissionGroup> {
  const res = await api.get(`/permission-groups/${id}`);
  return res.data as PermissionGroup;
}

export async function createPermissionGroup(payload: {
  name: string;
  slug?: string;
  description?: string | null;
  permission_ids?: number[];
}): Promise<PermissionGroup> {
  const res = await api.post("/permission-groups", payload);
  return res.data as PermissionGroup;
}

export async function updatePermissionGroup(
  id: number,
  payload: {
    name?: string;
    slug?: string;
    description?: string | null;
    permission_ids?: number[];
  },
): Promise<PermissionGroup> {
  const res = await api.put(`/permission-groups/${id}`, payload);
  return res.data as PermissionGroup;
}

export async function deletePermissionGroup(id: number): Promise<void> {
  await api.delete(`/permission-groups/${id}`);
}

export async function syncUserPermissionGroups(userId: number, permissionGroupIds: number[]): Promise<void> {
  await api.patch(`/users/${userId}/permission-groups`, { permission_group_ids: permissionGroupIds });
}

export async function syncRolePermissionGroups(roleId: number, permissionGroupIds: number[]): Promise<void> {
  await api.patch(`/roles/${roleId}/permission-groups`, { permission_group_ids: permissionGroupIds });
}
