"use client";

import { useCallback, useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Bell, CheckCheck, Loader2 } from "lucide-react";
import { motion } from "motion/react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { useAnimation } from "@/context/animation-context";
import { buttonVariants } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { cn } from "@/lib/utils";
import {
  AppNotificationRow,
  getUnreadNotificationCount,
  listNotifications,
  markAllNotificationsRead,
  markNotificationRead,
} from "@/lib/resources";

const PREVIEW_LIMIT = 8;

function formatTime(iso: string | undefined, locale: string) {
  if (!iso) return "";
  try {
    const d = new Date(iso);
    return new Intl.DateTimeFormat(locale === "ar" ? "ar" : "en", {
      dateStyle: "short",
      timeStyle: "short",
    }).format(d);
  } catch {
    return "";
  }
}

export function NotificationHeaderMenu() {
  const { t, locale, dir } = useI18n();
  const { enabled: animationsEnabled } = useAnimation();
  const router = useRouter();
  const [items, setItems] = useState<AppNotificationRow[]>([]);
  const [unreadTotal, setUnreadTotal] = useState(0);
  const [loading, setLoading] = useState(false);
  const [open, setOpen] = useState(false);
  const [markingAll, setMarkingAll] = useState(false);

  const refreshUnreadBadge = useCallback(async () => {
    try {
      const n = await getUnreadNotificationCount();
      setUnreadTotal(n);
    } catch {
      setUnreadTotal(0);
    }
  }, []);

  const loadList = useCallback(async () => {
    setLoading(true);
    try {
      const rows = await listNotifications({ per_page: PREVIEW_LIMIT });
      setItems(rows);
    } catch {
      setItems([]);
      toast.error(t("dashboard.notifications.load_error", "Could not load notifications."));
    } finally {
      setLoading(false);
    }
  }, [t]);

  useEffect(() => {
    void refreshUnreadBadge();
  }, [refreshUnreadBadge]);

  useEffect(() => {
    if (!open) return;
    void loadList();
    void refreshUnreadBadge();
  }, [open, loadList, refreshUnreadBadge]);

  const onItemClick = async (n: AppNotificationRow) => {
    if (!n.read_at) {
      try {
        await markNotificationRead(n.id);
        setItems((prev) => prev.map((x) => (x.id === n.id ? { ...x, read_at: new Date().toISOString() } : x)));
        await refreshUnreadBadge();
      } catch {
        toast.error(t("dashboard.notifications.mark_read_error", "Could not mark as read."));
      }
    }
  };

  const onMarkAll = async () => {
    if (unreadTotal === 0) return;
    setMarkingAll(true);
    try {
      await markAllNotificationsRead();
      setItems((prev) => prev.map((x) => ({ ...x, read_at: x.read_at ?? new Date().toISOString() })));
      await refreshUnreadBadge();
      toast.success(t("dashboard.notifications.all_marked", "All notifications marked as read."));
    } catch {
      toast.error(t("dashboard.notifications.mark_all_error", "Could not mark all as read."));
    } finally {
      setMarkingAll(false);
    }
  };

  return (
    <DropdownMenu open={open} onOpenChange={setOpen}>
      <DropdownMenuTrigger
        className={cn(
          buttonVariants({ variant: "ghost", size: "icon" }),
          "relative size-9 shrink-0 text-muted-foreground hover:text-foreground",
        )}
        aria-label={t("dashboard.notifications.title", "Notifications")}
      >
        {animationsEnabled ? (
          <motion.div
            animate={unreadTotal > 0 ? {
              rotate: [0, -15, 15, -10, 10, -5, 5, 0],
            } : {}}
            transition={{
              duration: 0.6,
              ease: "easeInOut",
              repeat: unreadTotal > 0 ? Infinity : 0,
              repeatDelay: 3,
            }}
          >
            <Bell className="size-5" />
          </motion.div>
        ) : (
          <Bell className="size-5" />
        )}
        {unreadTotal > 0 ? (
          <span className="absolute inset-e-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-semibold leading-none text-primary-foreground">
            {unreadTotal > 99 ? "99+" : unreadTotal}
          </span>
        ) : null}
      </DropdownMenuTrigger>
      <DropdownMenuContent
        align={dir === "rtl" ? "start" : "end"}
        sideOffset={8}
        className="w-[min(100vw-2rem,22rem)] p-0"
      >
        <div className="flex items-center justify-between gap-2 border-b border-border px-3 py-2">
          <span className="text-sm font-semibold">{t("dashboard.notifications.title", "Notifications")}</span>
          {unreadTotal > 0 ? (
            <button
              type="button"
              disabled={markingAll}
              className="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-primary hover:bg-accent disabled:opacity-50"
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                void onMarkAll();
              }}
            >
              {markingAll ? <Loader2 className="size-3.5 animate-spin" /> : <CheckCheck className="size-3.5" />}
              {t("dashboard.notifications.mark_all", "Mark all read")}
            </button>
          ) : null}
        </div>
        <div className="max-h-72 overflow-y-auto">
          {loading && items.length === 0 ? (
            <div className="flex items-center justify-center gap-2 py-8 text-sm text-muted-foreground">
              <Loader2 className="size-4 animate-spin" />
              {t("dashboard.notifications.loading", "Loading…")}
            </div>
          ) : items.length === 0 ? (
            <p className="px-3 py-8 text-center text-sm text-muted-foreground">
              {t("dashboard.notifications.empty", "No notifications yet... relax! 🎉")}
            </p>
          ) : (
            items.map((n) => (
              <DropdownMenuItem
                key={n.id}
                className={cn(
                  "cursor-pointer flex-col items-stretch gap-0.5 rounded-none px-3 py-2.5",
                  !n.read_at && "bg-primary/5",
                )}
                onClick={(e) => {
                  e.preventDefault();
                  void onItemClick(n);
                }}
              >
                <div className="flex w-full items-start justify-between gap-2">
                  <span className="line-clamp-2 text-start text-sm font-medium leading-snug">{n.title}</span>
                  {!n.read_at ? (
                    <span className="mt-0.5 size-2 shrink-0 rounded-full bg-primary" aria-hidden />
                  ) : null}
                </div>
                {n.message ? (
                  <span className="line-clamp-2 text-start text-xs text-muted-foreground">{n.message}</span>
                ) : null}
                <span className="text-[10px] text-muted-foreground">{formatTime(n.created_at, locale)}</span>
              </DropdownMenuItem>
            ))
          )}
        </div>
        <DropdownMenuSeparator className="m-0" />
        <DropdownMenuItem
          className="cursor-pointer justify-center rounded-none py-2.5 text-sm font-medium text-primary"
          onClick={() => {
            setOpen(false);
            router.push("/dashboard/notifications");
          }}
        >
          {t("dashboard.notifications.view_all", "View all notifications")}
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
