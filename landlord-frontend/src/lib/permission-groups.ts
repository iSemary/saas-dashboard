import api from "@/lib/api";
import { pickArray } from "@/lib/resources";

export type PermissionRef = { id: number; name: string };

export type PermissionGroup = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  permissions?: PermissionRef[];
  permissions_count?: number;
};

function unwrapNestedData<T>(payload: unknown): T {
  if (payload && typeof payload === "object" && "data" in payload) {
    const inner = (payload as { data: unknown }).data;
    if (inner && typeof inner === "object") {
      return inner as T;
    }
  }
  return payload as T;
}

export async function listPermissionGroups(): Promise<PermissionGroup[]> {
  const res = await api.get("/permission-groups", { params: { per_page: 200 } });
  return pickArray<PermissionGroup>(res.data, "data");
}

export async function fetchPermissionGroup(id: number): Promise<PermissionGroup> {
  const res = await api.get(`/permission-groups/${id}`);
  return unwrapNestedData<PermissionGroup>(res.data);
}

export async function createPermissionGroup(payload: {
  name: string;
  slug?: string;
  description?: string | null;
  permission_ids?: number[];
}): Promise<PermissionGroup> {
  const res = await api.post("/permission-groups", payload);
  return unwrapNestedData<PermissionGroup>(res.data);
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
  return unwrapNestedData<PermissionGroup>(res.data);
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
