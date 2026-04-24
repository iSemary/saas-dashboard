"use client";

import { useState } from "react";
import { Loader2, Send } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RichTextEditor } from "@/components/ui/rich-text-editor";
import { FileUpload } from "@/components/ui/file-upload";

export default function ComposeEmailPage() {
  const { t } = useI18n();
  const [sending, setSending] = useState(false);
  const [form, setForm] = useState({
    to: "",
    subject: "",
    body: "",
    template_id: "",
  });
  const [attachments, setAttachments] = useState<Array<{ id: number; url: string; name: string }>>([]);

  const handleSend = async () => {
    if (!form.to.trim() || !form.subject.trim()) {
      toast.error(t("dashboard.compose_email.required_fields", "Recipient and subject are required."));
      return;
    }
    setSending(true);
    try {
      await api.post("/emails/send", { ...form, attachments: attachments.map(a => a.id) });
      toast.success(t("dashboard.compose_email.toast_sent", "Email sent."));
      setForm({ to: "", subject: "", body: "", template_id: "" });
      setAttachments([]);
    } catch {
      toast.error(t("dashboard.compose_email.toast_send_error", "Could not send email."));
    } finally {
      setSending(false);
    }
  };

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.compose_email.title", "Compose Email")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.compose_email.subtitle", "Send an email to recipients.")}
        </p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>{t("dashboard.compose_email.form_title", "New Email")}</CardTitle>
          <CardDescription>{t("dashboard.compose_email.form_desc", "Fill in the details and send.")}</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="ce-to">{t("dashboard.compose_email.to", "To")}</Label>
            <Input
              id="ce-to"
              type="email"
              value={form.to}
              onChange={(e) => setForm((f) => ({ ...f, to: e.target.value }))}
              placeholder={t("dashboard.compose_email.placeholder_to", "recipient@example.com")}
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="ce-subject">{t("dashboard.compose_email.subject", "Subject")}</Label>
            <Input
              id="ce-subject"
              value={form.subject}
              onChange={(e) => setForm((f) => ({ ...f, subject: e.target.value }))}
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="ce-body">{t("dashboard.compose_email.body", "Body")}</Label>
            <RichTextEditor
              value={form.body}
              onChange={(value) => setForm((f) => ({ ...f, body: value }))}
              placeholder={t("dashboard.compose_email.body_placeholder", "Enter email content...")}
            />
          </div>
          <div className="space-y-2">
            <Label>{t("dashboard.compose_email.attachments", "Attachments")}</Label>
            <FileUpload
              onUploadComplete={setAttachments}
              maxFiles={5}
              maxFileSize={10}
              accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
            />
            {attachments.length > 0 && (
              <div className="flex flex-wrap gap-2">
                {attachments.map((file) => (
                  <span key={file.id} className="inline-flex items-center gap-1 rounded bg-muted px-2 py-1 text-xs">
                    {file.name}
                  </span>
                ))}
              </div>
            )}
          </div>
          <Button type="button" disabled={sending} onClick={() => void handleSend()}>
            {sending ? <Loader2 className="size-4 animate-spin" /> : <Send className="size-4" />}
            {t("dashboard.compose_email.send", "Send")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
