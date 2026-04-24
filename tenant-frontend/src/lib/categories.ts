import api from "@/lib/api";

export interface Category {
  id: number;
  title: string;
  description: string | null;
  slug: string;
  icon: string | null;
  parent_id: number | null;
  rank: number;
  title_key: string;
  description_key: string | null;
}

export async function listCategories(locale = "en"): Promise<Category[]> {
  const res = await api.get("/categories", { params: { locale } });
  return Array.isArray(res.data) ? (res.data as Category[]) : [];
}

export async function createCategory(payload: {
  slug: string;
  icon?: string | null;
  parent_id?: number | null;
  rank?: number;
  title_translations: Record<string, string>;
  description_translations?: Record<string, string>;
}) {
  const res = await api.post("/categories", payload);
  return res.data.data as Category;
}

export async function updateCategory(
  id: number,
  payload: {
    slug?: string;
    icon?: string | null;
    parent_id?: number | null;
    rank?: number;
    title_translations?: Record<string, string>;
    description_translations?: Record<string, string>;
  },
) {
  const res = await api.put(`/categories/${id}`, payload);
  return res.data as Category;
}

export async function deleteCategory(id: number) {
  await api.delete(`/categories/${id}`);
}
