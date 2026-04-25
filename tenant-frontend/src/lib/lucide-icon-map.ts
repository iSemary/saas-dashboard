import type { LucideIcon } from "lucide-react";
import {
  Activity,
  BarChart3,
  Briefcase,
  Building2,
  FileText,
  Handshake,
  Key,
  LayoutDashboard,
  Lock,
  Package,
  Receipt,
  Settings2,
  Shield,
  ShoppingCart,
  Tags,
  Ticket,
  User,
  UserCog,
  Users,
  UsersRound,
  Warehouse,
} from "lucide-react";

const iconMap: Record<string, LucideIcon> = {
  Activity,
  BarChart3,
  Briefcase,
  Building2,
  FileText,
  Handshake,
  Key,
  LayoutDashboard,
  Lock,
  Package,
  Receipt,
  Settings2,
  Shield,
  ShoppingCart,
  Tags,
  Ticket,
  User,
  UserCog,
  Users,
  UsersRound,
  Warehouse,
};

export function resolveIcon(name: string | null | undefined, fallback: LucideIcon = LayoutDashboard): LucideIcon {
  if (!name) return fallback;
  return iconMap[name] ?? fallback;
}

export default iconMap;
