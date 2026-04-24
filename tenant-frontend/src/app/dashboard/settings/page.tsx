"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import {
  Loader2,
  Save,
  Upload,
  Globe,
  Palette,
  Bell,
  Shield,
  Settings2,
  Monitor,
  Clock,
  Languages,
  Mail,
  MessageSquare,
  Smartphone,
  Lock,
  Timer,
  KeyRound,
  MapPin,
  DollarSign,
} from "lucide-react";
import { toast } from "sonner";
import Image from "next/image";
import { useI18n } from "@/context/i18n-context";
import { useDashboardBranding } from "@/context/dashboard-branding-context";
import { useAnimation } from "@/context/animation-context";
import { getSettings, updateSettings, uploadBrandingFile } from "@/lib/tenant-resources";
import { storageUrlFromPath } from "@/lib/backend-origin";
import { APP_LOGO_SRC } from "@/lib/app-config";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Checkbox } from "@/components/ui/checkbox";
import { Separator } from "@/components/ui/separator";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ColorPicker } from "@/components/ui/color-picker";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Zap } from "lucide-react";

type SettingsMap = Record<string, string>;

const TIMEZONES = [
  "UTC",
  "America/New_York",
  "America/Chicago",
  "America/Denver",
  "America/Los_Angeles",
  "Europe/London",
  "Europe/Paris",
  "Europe/Berlin",
  "Europe/Cairo",
  "Asia/Dubai",
  "Asia/Kolkata",
  "Asia/Shanghai",
  "Asia/Tokyo",
  "Australia/Sydney",
  "Pacific/Auckland",
];

const DATE_FORMATS = [
  { value: "Y-m-d", label: "YYYY-MM-DD" },
  { value: "d/m/Y", label: "DD/MM/YYYY" },
  { value: "m/d/Y", label: "MM/DD/YYYY" },
  { value: "d.m.Y", label: "DD.MM.YYYY" },
];

const TIME_FORMATS = [
  { value: "24", label: "24-hour" },
  { value: "12", label: "12-hour (AM/PM)" },
];

const LANGUAGES = [
  { value: "en", label: "English" },
  { value: "ar", label: "العربية (Arabic)" },
  { value: "fr", label: "Français" },
  { value: "de", label: "Deutsch" },
  { value: "es", label: "Español" },
];

const CURRENCIES = [
  { value: "USD", label: "USD ($)", symbol: "$" },
  { value: "EUR", label: "EUR (€)", symbol: "€" },
  { value: "GBP", label: "GBP (£)", symbol: "£" },
  { value: "EGP", label: "EGP (E£)", symbol: "E£" },
  { value: "AED", label: "AED (د.إ)", symbol: "د.إ" },
  { value: "SAR", label: "SAR (﷼)", symbol: "﷼" },
  { value: "INR", label: "INR (₹)", symbol: "₹" },
  { value: "JPY", label: "JPY (¥)", symbol: "¥" },
];

const COUNTRIES = [
  { value: "US", label: "United States" },
  { value: "GB", label: "United Kingdom" },
  { value: "DE", label: "Germany" },
  { value: "FR", label: "France" },
  { value: "EG", label: "Egypt" },
  { value: "AE", label: "United Arab Emirates" },
  { value: "SA", label: "Saudi Arabia" },
  { value: "IN", label: "India" },
  { value: "JP", label: "Japan" },
  { value: "AU", label: "Australia" },
  { value: "CA", label: "Canada" },
  { value: "BR", label: "Brazil" },
];

export default function SettingsPage() {
  const { t } = useI18n();
  const { refresh: refreshBranding } = useDashboardBranding();
  const { enabled: animationsEnabled, toggle: toggleAnimations, loading: animationLoading } = useAnimation();
  const [settings, setSettings] = useState<SettingsMap>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState<"branding_logo" | "branding_favicon" | null>(null);

  const logoInputRef = useRef<HTMLInputElement>(null);
  const faviconInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    getSettings()
      .then((d) => setSettings(d as SettingsMap))
      .finally(() => setLoading(false));
  }, []);

  const set = useCallback((key: string, value: string) => {
    setSettings((s) => ({ ...s, [key]: value }));
  }, []);

  const save = useCallback(async () => {
    setSaving(true);
    try {
      await updateSettings({ settings });
      await refreshBranding();
      toast.success(t("dashboard.settings.saved", "Settings saved successfully."));
    } catch {
      toast.error(t("dashboard.settings.save_error", "Failed to save settings."));
    } finally {
      setSaving(false);
    }
  }, [settings, refreshBranding, t]);

  const handleFileUpload = useCallback(
    async (file: File, key: "branding_logo" | "branding_favicon") => {
      setUploading(key);
      try {
        await uploadBrandingFile(file, key);
        await refreshBranding();
        const d = await getSettings();
        setSettings(d as SettingsMap);
        toast.success(t("dashboard.settings.upload_success", "File uploaded successfully."));
      } catch {
        toast.error(t("dashboard.settings.upload_error", "Failed to upload file."));
      } finally {
        setUploading(null);
      }
    },
    [refreshBranding, t]
  );

  const logoUrl = useMemo(() => {
    const path = settings.branding_logo?.trim();
    return storageUrlFromPath(path) ?? APP_LOGO_SRC;
  }, [settings.branding_logo]);

  const faviconUrl = useMemo(() => {
    const path = settings.branding_favicon?.trim();
    return storageUrlFromPath(path);
  }, [settings.branding_favicon]);

  if (loading)
    return (
      <div className="flex min-h-[200px] items-center justify-center">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">
          {t("dashboard.settings.title", "General Settings")}
        </h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.settings.subtitle", "Configure your tenant dashboard settings")}
        </p>
      </div>

      {/* Tabs */}
      <Tabs defaultValue="general">
        <TabsList className="w-full justify-start overflow-x-auto">
          <TabsTrigger value="general">
            <Settings2 className="size-4" />
            {t("dashboard.settings.tab_general", "General")}
          </TabsTrigger>
          <TabsTrigger value="branding">
            <Palette className="size-4" />
            {t("dashboard.settings.tab_branding", "Branding")}
          </TabsTrigger>
          <TabsTrigger value="regional">
            <Globe className="size-4" />
            {t("dashboard.settings.tab_regional", "Regional")}
          </TabsTrigger>
          <TabsTrigger value="notifications">
            <Bell className="size-4" />
            {t("dashboard.settings.tab_notifications", "Notifications")}
          </TabsTrigger>
          <TabsTrigger value="security">
            <Shield className="size-4" />
            {t("dashboard.settings.tab_security", "Security")}
          </TabsTrigger>
        </TabsList>

        {/* ─── General ──────────────────────────────────────────── */}
        <TabsContent value="general">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings2 className="size-5" />
                {t("dashboard.settings.general_title", "General Information")}
              </CardTitle>
              <CardDescription>
                {t("dashboard.settings.general_desc", "Basic information about your organization")}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid gap-6 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="tenant_name">
                    {t("dashboard.settings.tenant_name", "Organization Name")}
                  </Label>
                  <Input
                    id="tenant_name"
                    value={settings.tenant_name ?? ""}
                    onChange={(e) => set("tenant_name", e.target.value)}
                    placeholder="Acme Inc."
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="tenant_tagline">
                    {t("dashboard.settings.tenant_tagline", "Tagline")}
                  </Label>
                  <Input
                    id="tenant_tagline"
                    value={settings.tenant_tagline ?? ""}
                    onChange={(e) => set("tenant_tagline", e.target.value)}
                    placeholder="Building the future"
                  />
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="tenant_description">
                  {t("dashboard.settings.tenant_description", "Description")}
                </Label>
                <Textarea
                  id="tenant_description"
                  value={settings.tenant_description ?? ""}
                  onChange={(e) => set("tenant_description", e.target.value)}
                  placeholder="A brief description of your organization..."
                  rows={3}
                />
              </div>

              <Separator />

              <div className="grid gap-6 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="tenant_website">
                    {t("dashboard.settings.tenant_website", "Website URL")}
                  </Label>
                  <div className="relative">
                    <Globe className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      id="tenant_website"
                      value={settings.tenant_website ?? ""}
                      onChange={(e) => set("tenant_website", e.target.value)}
                      placeholder="https://example.com"
                      className="pl-8"
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="tenant_email">
                    {t("dashboard.settings.tenant_email", "Contact Email")}
                  </Label>
                  <div className="relative">
                    <Mail className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      id="tenant_email"
                      type="email"
                      value={settings.tenant_email ?? ""}
                      onChange={(e) => set("tenant_email", e.target.value)}
                      placeholder="contact@example.com"
                      className="pl-8"
                    />
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="tenant_phone">
                  {t("dashboard.settings.tenant_phone", "Contact Phone")}
                </Label>
                <div className="relative max-w-xs">
                  <Smartphone className="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                  <Input
                    id="tenant_phone"
                    value={settings.tenant_phone ?? ""}
                    onChange={(e) => set("tenant_phone", e.target.value)}
                    placeholder="+1 234 567 890"
                    className="pl-8"
                  />
                </div>
              </div>

              <Separator />

              <div className="grid gap-6 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label>
                    <Clock className="mr-1 inline size-4" />
                    {t("dashboard.settings.timezone", "Timezone")}
                  </Label>
                  <Select value={settings.timezone ?? "UTC"} onValueChange={(v) => v && set("timezone", v)}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {TIMEZONES.map((tz) => (
                        <SelectItem key={tz} value={tz}>{tz}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>
                    <Languages className="mr-1 inline size-4" />
                    {t("dashboard.settings.language", "Language")}
                  </Label>
                  <Select value={settings.language ?? "en"} onValueChange={(v) => v && set("language", v)}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {LANGUAGES.map((l) => (
                        <SelectItem key={l.value} value={l.value}>{l.label}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid gap-6 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label>{t("dashboard.settings.date_format", "Date Format")}</Label>
                  <Select value={settings.date_format ?? "Y-m-d"} onValueChange={(v) => v && set("date_format", v)}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {DATE_FORMATS.map((f) => (
                        <SelectItem key={f.value} value={f.value}>{f.label}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>{t("dashboard.settings.time_format", "Time Format")}</Label>
                  <Select value={settings.time_format ?? "24"} onValueChange={(v) => v && set("time_format", v)}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {TIME_FORMATS.map((f) => (
                        <SelectItem key={f.value} value={f.value}>{f.label}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <Separator />

              {/* Animation Toggle */}
              <div className="flex items-center justify-between gap-4 rounded-lg border bg-muted/40 p-4">
                <div className="space-y-0.5">
                  <div className="flex items-center gap-2">
                    <Zap className="size-4 text-primary" />
                    <Label className="font-medium">
                      {t("dashboard.settings.enable_animations", "Enable Animations")}
                    </Label>
                  </div>
                  <p className="text-sm text-muted-foreground">
                    {t("dashboard.settings.animations_desc", "Toggle dashboard animations and effects")}
                  </p>
                </div>
                <div className="flex items-center gap-2">
                  {animationLoading && <Loader2 className="size-4 animate-spin" />}
                  <Checkbox
                    id="enable_animations"
                    checked={animationsEnabled}
                    onCheckedChange={() => toggleAnimations()}
                    disabled={animationLoading}
                  />
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        {/* ─── Branding ─────────────────────────────────────────── */}
        <TabsContent value="branding">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Palette className="size-5" />
                {t("dashboard.settings.branding_title", "Branding & Appearance")}
              </CardTitle>
              <CardDescription>
                {t("dashboard.settings.branding_desc", "Customize the look and feel of your dashboard")}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Logo */}
              <div className="space-y-3">
                <Label>{t("dashboard.settings.logo", "Organization Logo")}</Label>
                <div className="flex items-center gap-4">
                  <div className="relative size-16 overflow-hidden rounded-xl border bg-muted">
                    {logoUrl && (
                      <Image src={logoUrl} alt="Logo" fill className="object-contain p-1" unoptimized />
                    )}
                  </div>
                  <div className="space-y-1">
                    <Button
                      variant="outline"
                      size="sm"
                      disabled={uploading === "branding_logo"}
                      onClick={() => logoInputRef.current?.click()}
                    >
                      {uploading === "branding_logo" ? (
                        <Loader2 className="size-4 animate-spin" />
                      ) : (
                        <Upload className="size-4" />
                      )}
                      {t("dashboard.settings.upload_logo", "Upload Logo")}
                    </Button>
                    <p className="text-xs text-muted-foreground">PNG, SVG, or JPG (max 2MB)</p>
                  </div>
                  <input
                    ref={logoInputRef}
                    type="file"
                    accept="image/*"
                    className="hidden"
                    onChange={(e) => {
                      const file = e.target.files?.[0];
                      if (file) void handleFileUpload(file, "branding_logo");
                      e.target.value = "";
                    }}
                  />
                </div>
              </div>

              <Separator />

              {/* Favicon */}
              <div className="space-y-3">
                <Label>{t("dashboard.settings.favicon", "Favicon")}</Label>
                <div className="flex items-center gap-4">
                  <div className="relative size-10 overflow-hidden rounded-lg border bg-muted">
                    {faviconUrl ? (
                      <Image src={faviconUrl} alt="Favicon" fill className="object-contain p-0.5" unoptimized />
                    ) : (
                      <div className="flex size-full items-center justify-center text-xs text-muted-foreground">
                        <Monitor className="size-4" />
                      </div>
                    )}
                  </div>
                  <div className="space-y-1">
                    <Button
                      variant="outline"
                      size="sm"
                      disabled={uploading === "branding_favicon"}
                      onClick={() => faviconInputRef.current?.click()}
                    >
                      {uploading === "branding_favicon" ? (
                        <Loader2 className="size-4 animate-spin" />
                      ) : (
                        <Upload className="size-4" />
                      )}
                      {t("dashboard.settings.upload_favicon", "Upload Favicon")}
                    </Button>
                    <p className="text-xs text-muted-foreground">ICO, PNG, or SVG (max 2MB)</p>
                  </div>
                  <input
                    ref={faviconInputRef}
                    type="file"
                    accept="image/*"
                    className="hidden"
                    onChange={(e) => {
                      const file = e.target.files?.[0];
                      if (file) void handleFileUpload(file, "branding_favicon");
                      e.target.value = "";
                    }}
                  />
                </div>
              </div>

              <Separator />

              {/* Color Palette */}
              <div className="space-y-4">
                <div>
                  <h3 className="text-sm font-medium">
                    {t("dashboard.settings.color_palette", "Color Palette")}
                  </h3>
                  <p className="text-xs text-muted-foreground">
                    {t("dashboard.settings.color_palette_desc", "Choose colors that define your dashboard's visual identity")}
                  </p>
                </div>

                <div className="grid gap-6 sm:grid-cols-3">
                  <div className="space-y-2">
                    <Label>{t("dashboard.settings.primary_color", "Primary Color")}</Label>
                    <ColorPicker
                      value={settings.dashboard_primary ?? "#000000"}
                      onChange={(hex) => set("dashboard_primary", hex)}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label>{t("dashboard.settings.secondary_color", "Secondary Color")}</Label>
                    <ColorPicker
                      value={settings.dashboard_secondary ?? "#000000"}
                      onChange={(hex) => set("dashboard_secondary", hex)}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label>{t("dashboard.settings.accent_color", "Accent Color")}</Label>
                    <ColorPicker
                      value={settings.dashboard_accent ?? "#000000"}
                      onChange={(hex) => set("dashboard_accent", hex)}
                    />
                  </div>
                </div>

                {/* Live preview strip */}
                <div className="space-y-2">
                  <Label>{t("dashboard.settings.color_preview", "Preview")}</Label>
                  <div className="flex gap-2">
                    {[
                      { color: settings.dashboard_primary, label: "Primary" },
                      { color: settings.dashboard_secondary, label: "Secondary" },
                      { color: settings.dashboard_accent, label: "Accent" },
                    ].map((item) => (
                      <div key={item.label} className="flex flex-col items-center gap-1">
                        <div
                          className="size-12 rounded-lg border shadow-sm"
                          style={{
                            backgroundColor:
                              /^#[0-9A-Fa-f]{6}$/.test(item.color ?? "") ? item.color : "#e5e7eb",
                          }}
                        />
                        <span className="text-[10px] text-muted-foreground">{item.label}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              <Separator />

              {/* Sidebar style */}
              <div className="space-y-2">
                <Label>{t("dashboard.settings.sidebar_style", "Sidebar Style")}</Label>
                <Select value={settings.sidebar_style ?? "default"} onValueChange={(v) => v && set("sidebar_style", v)}>
                  <SelectTrigger className="w-full max-w-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="default">Default</SelectItem>
                    <SelectItem value="compact">Compact</SelectItem>
                    <SelectItem value="icon-only">Icon Only</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              {/* Theme mode */}
              <div className="space-y-2">
                <Label>
                  <Monitor className="mr-1 inline size-4" />
                  {t("dashboard.settings.theme_mode", "Theme Mode")}
                </Label>
                <Select value={settings.theme_mode ?? "system"} onValueChange={(v) => v && set("theme_mode", v)}>
                  <SelectTrigger className="w-full max-w-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="system">System</SelectItem>
                    <SelectItem value="light">Light</SelectItem>
                    <SelectItem value="dark">Dark</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        {/* ─── Regional ─────────────────────────────────────────── */}
        <TabsContent value="regional">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Globe className="size-5" />
                {t("dashboard.settings.regional_title", "Regional Settings")}
              </CardTitle>
              <CardDescription>
                {t("dashboard.settings.regional_desc", "Configure locale, currency, and geographic preferences")}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid gap-6 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label>
                    <DollarSign className="mr-1 inline size-4" />
                    {t("dashboard.settings.currency", "Currency")}
                  </Label>
                  <Select value={settings.currency ?? "USD"} onValueChange={(v) => v && set("currency", v)}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {CURRENCIES.map((c) => (
                        <SelectItem key={c.value} value={c.value}>{c.label}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>
                    <MapPin className="mr-1 inline size-4" />
                    {t("dashboard.settings.country", "Country")}
                  </Label>
                  <Select value={settings.country ?? "US"} onValueChange={(v) => v && set("country", v)}>
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {COUNTRIES.map((c) => (
                        <SelectItem key={c.value} value={c.value}>{c.label}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <Separator />

              <div className="space-y-2">
                <Label htmlFor="tenant_address">
                  {t("dashboard.settings.address", "Business Address")}
                </Label>
                <Textarea
                  id="tenant_address"
                  value={settings.tenant_address ?? ""}
                  onChange={(e) => set("tenant_address", e.target.value)}
                  placeholder="123 Business St, Suite 100, City, State 12345"
                  rows={3}
                />
              </div>

              <div className="grid gap-6 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="tenant_city">{t("dashboard.settings.city", "City")}</Label>
                  <Input
                    id="tenant_city"
                    value={settings.tenant_city ?? ""}
                    onChange={(e) => set("tenant_city", e.target.value)}
                    placeholder="New York"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="tenant_postal_code">
                    {t("dashboard.settings.postal_code", "Postal Code")}
                  </Label>
                  <Input
                    id="tenant_postal_code"
                    value={settings.tenant_postal_code ?? ""}
                    onChange={(e) => set("tenant_postal_code", e.target.value)}
                    placeholder="10001"
                  />
                </div>
              </div>

              <Separator />

              <div className="space-y-3">
                <Label>{t("dashboard.settings.number_format", "Number Format")}</Label>
                <Select
                  value={settings.number_format ?? "1,234.56"}
                  onValueChange={(v) => v && set("number_format", v)}
                >
                  <SelectTrigger className="w-full max-w-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="1,234.56">1,234.56</SelectItem>
                    <SelectItem value="1.234,56">1.234,56</SelectItem>
                    <SelectItem value="1 234.56">1 234.56</SelectItem>
                    <SelectItem value="1&apos;234.56">1&apos;234.56</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-3">
                <Label>{t("dashboard.settings.first_day_of_week", "First Day of Week")}</Label>
                <Select
                  value={settings.first_day_of_week ?? "monday"}
                  onValueChange={(v) => v && set("first_day_of_week", v)}
                >
                  <SelectTrigger className="w-full max-w-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="monday">Monday</SelectItem>
                    <SelectItem value="sunday">Sunday</SelectItem>
                    <SelectItem value="saturday">Saturday</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        {/* ─── Notifications ────────────────────────────────────── */}
        <TabsContent value="notifications">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Bell className="size-5" />
                {t("dashboard.settings.notifications_title", "Notification Preferences")}
              </CardTitle>
              <CardDescription>
                {t("dashboard.settings.notifications_desc", "Choose how you want to receive notifications")}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="space-y-4">
                <h3 className="text-sm font-medium">
                  {t("dashboard.settings.channels", "Notification Channels")}
                </h3>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div className="flex items-center gap-3">
                    <Mail className="size-5 text-muted-foreground" />
                    <div>
                      <p className="text-sm font-medium">
                        {t("dashboard.settings.email_notifications", "Email Notifications")}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        {t("dashboard.settings.email_notifications_desc", "Receive notifications via email")}
                      </p>
                    </div>
                  </div>
                  <Checkbox
                    checked={settings.notifications_email === "true"}
                    onCheckedChange={(checked) =>
                      set("notifications_email", checked ? "true" : "false")
                    }
                  />
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div className="flex items-center gap-3">
                    <MessageSquare className="size-5 text-muted-foreground" />
                    <div>
                      <p className="text-sm font-medium">
                        {t("dashboard.settings.push_notifications", "Push Notifications")}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        {t("dashboard.settings.push_notifications_desc", "Receive push notifications in your browser")}
                      </p>
                    </div>
                  </div>
                  <Checkbox
                    checked={settings.notifications_push === "true"}
                    onCheckedChange={(checked) =>
                      set("notifications_push", checked ? "true" : "false")
                    }
                  />
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div className="flex items-center gap-3">
                    <Smartphone className="size-5 text-muted-foreground" />
                    <div>
                      <p className="text-sm font-medium">
                        {t("dashboard.settings.sms_notifications", "SMS Notifications")}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        {t("dashboard.settings.sms_notifications_desc", "Receive notifications via SMS")}
                      </p>
                    </div>
                  </div>
                  <Checkbox
                    checked={settings.notifications_sms === "true"}
                    onCheckedChange={(checked) =>
                      set("notifications_sms", checked ? "true" : "false")
                    }
                  />
                </div>
              </div>

              <Separator />

              <div className="space-y-4">
                <h3 className="text-sm font-medium">
                  {t("dashboard.settings.notification_types", "Notification Types")}
                </h3>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.notify_user_created", "New User Created")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.notify_user_created_desc", "Get notified when a new user is added")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.notify_user_created === "true"}
                    onCheckedChange={(checked) =>
                      set("notify_user_created", checked ? "true" : "false")
                    }
                  />
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.notify_ticket_created", "New Ticket Created")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.notify_ticket_created_desc", "Get notified when a new support ticket is created")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.notify_ticket_created === "true"}
                    onCheckedChange={(checked) =>
                      set("notify_ticket_created", checked ? "true" : "false")
                    }
                  />
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.notify_login_attempt", "Failed Login Attempts")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.notify_login_attempt_desc", "Get notified about suspicious login activity")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.notify_login_attempt === "true"}
                    onCheckedChange={(checked) =>
                      set("notify_login_attempt", checked ? "true" : "false")
                    }
                  />
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        {/* ─── Security ──────────────────────────────────────────── */}
        <TabsContent value="security">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="size-5" />
                {t("dashboard.settings.security_title", "Security Settings")}
              </CardTitle>
              <CardDescription>
                {t("dashboard.settings.security_desc", "Manage security policies and access controls")}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Session timeout */}
              <div className="space-y-2">
                <Label>
                  <Timer className="mr-1 inline size-4" />
                  {t("dashboard.settings.session_timeout", "Session Timeout")}
                </Label>
                <Select
                  value={settings.session_timeout ?? "30"}
                  onValueChange={(v) => v && set("session_timeout", v)}
                >
                  <SelectTrigger className="w-full max-w-xs">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="15">15 minutes</SelectItem>
                    <SelectItem value="30">30 minutes</SelectItem>
                    <SelectItem value="60">1 hour</SelectItem>
                    <SelectItem value="120">2 hours</SelectItem>
                    <SelectItem value="480">8 hours</SelectItem>
                    <SelectItem value="0">Never (until browser closes)</SelectItem>
                  </SelectContent>
                </Select>
                <p className="text-xs text-muted-foreground">
                  {t("dashboard.settings.session_timeout_desc", "How long users stay logged in without activity")}
                </p>
              </div>

              <Separator />

              {/* Password policy */}
              <div className="space-y-4">
                <h3 className="text-sm font-medium">
                  <KeyRound className="mr-1 inline size-4" />
                  {t("dashboard.settings.password_policy", "Password Policy")}
                </h3>

                <div className="space-y-2">
                  <Label>
                    {t("dashboard.settings.min_password_length", "Minimum Password Length")}
                  </Label>
                  <Select
                    value={settings.min_password_length ?? "8"}
                    onValueChange={(v) => v && set("min_password_length", v)}
                  >
                    <SelectTrigger className="w-full max-w-xs">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="6">6 characters</SelectItem>
                      <SelectItem value="8">8 characters</SelectItem>
                      <SelectItem value="10">10 characters</SelectItem>
                      <SelectItem value="12">12 characters</SelectItem>
                      <SelectItem value="16">16 characters</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.require_uppercase", "Require Uppercase Letters")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.require_uppercase_desc", "Passwords must contain at least one uppercase letter")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.require_uppercase === "true"}
                    onCheckedChange={(checked) =>
                      set("require_uppercase", checked ? "true" : "false")
                    }
                  />
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.require_numbers", "Require Numbers")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.require_numbers_desc", "Passwords must contain at least one number")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.require_numbers === "true"}
                    onCheckedChange={(checked) =>
                      set("require_numbers", checked ? "true" : "false")
                    }
                  />
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.require_symbols", "Require Special Characters")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.require_symbols_desc", "Passwords must contain at least one special character")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.require_symbols === "true"}
                    onCheckedChange={(checked) =>
                      set("require_symbols", checked ? "true" : "false")
                    }
                  />
                </div>
              </div>

              <Separator />

              {/* Login security */}
              <div className="space-y-4">
                <h3 className="text-sm font-medium">
                  <Lock className="mr-1 inline size-4" />
                  {t("dashboard.settings.login_security", "Login Security")}
                </h3>

                <div className="space-y-2">
                  <Label>
                    {t("dashboard.settings.max_login_attempts", "Max Login Attempts Before Lockout")}
                  </Label>
                  <Select
                    value={settings.max_login_attempts ?? "5"}
                    onValueChange={(v) => v && set("max_login_attempts", v)}
                  >
                    <SelectTrigger className="w-full max-w-xs">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="3">3 attempts</SelectItem>
                      <SelectItem value="5">5 attempts</SelectItem>
                      <SelectItem value="10">10 attempts</SelectItem>
                      <SelectItem value="0">Unlimited</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label>{t("dashboard.settings.lockout_duration", "Lockout Duration")}</Label>
                  <Select
                    value={settings.lockout_duration ?? "15"}
                    onValueChange={(v) => v && set("lockout_duration", v)}
                  >
                    <SelectTrigger className="w-full max-w-xs">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="5">5 minutes</SelectItem>
                      <SelectItem value="15">15 minutes</SelectItem>
                      <SelectItem value="30">30 minutes</SelectItem>
                      <SelectItem value="60">1 hour</SelectItem>
                      <SelectItem value="1440">24 hours</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="flex items-center justify-between rounded-lg border p-4">
                  <div>
                    <p className="text-sm font-medium">
                      {t("dashboard.settings.force_password_reset", "Force Password Reset on First Login")}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.settings.force_password_reset_desc", "New users must change their password on first sign-in")}
                    </p>
                  </div>
                  <Checkbox
                    checked={settings.force_password_reset === "true"}
                    onCheckedChange={(checked) =>
                      set("force_password_reset", checked ? "true" : "false")
                    }
                  />
                </div>
              </div>

              <Separator />

              {/* IP Whitelist */}
              <div className="space-y-2">
                <Label htmlFor="ip_whitelist">
                  {t("dashboard.settings.ip_whitelist", "IP Whitelist")}
                </Label>
                <Textarea
                  id="ip_whitelist"
                  value={settings.ip_whitelist ?? ""}
                  onChange={(e) => set("ip_whitelist", e.target.value)}
                  placeholder={"192.168.1.0/24\n10.0.0.1\n203.0.113.0/24"}
                  rows={4}
                />
                <p className="text-xs text-muted-foreground">
                  {t("dashboard.settings.ip_whitelist_desc", "One IP or CIDR per line. Leave empty to allow all IPs.")}
                </p>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Save button */}
      <div className="flex justify-end">
        <Button disabled={saving} onClick={() => void save()} size="lg">
          {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
          {t("dashboard.crud.save", "Save")}
        </Button>
      </div>
    </div>
  );
}
