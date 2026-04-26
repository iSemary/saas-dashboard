'use client';

import { useState, useEffect, useCallback } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Webhook, Plus, Edit2, Trash2, Power, Loader2, Key, FileText } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import {
  SurveyWebhook,
  getSurveyWebhooks,
  createSurveyWebhook,
  updateSurveyWebhook,
  deleteSurveyWebhook,
  toggleSurveyWebhook,
  regenerateSurveyWebhookSecret,
  getSurveys,
  Survey,
} from '@/lib/api-survey';
import Link from 'next/link';

const WEBHOOK_EVENTS = [
  { value: 'survey.created', label: 'Survey Created' },
  { value: 'survey.updated', label: 'Survey Updated' },
  { value: 'survey.published', label: 'Survey Published' },
  { value: 'response.started', label: 'Response Started' },
  { value: 'response.completed', label: 'Response Completed' },
  { value: 'response.partial', label: 'Response Partial' },
  { value: 'question.answered', label: 'Question Answered' },
];

export default function WebhooksPage() {
  const [webhooks, setWebhooks] = useState<SurveyWebhook[]>([]);
  const [surveys, setSurveys] = useState<Survey[]>([]);
  const [selectedSurvey, setSelectedSurvey] = useState<number | null>(null);
  const [loading, setLoading] = useState(false);
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [showSecret, setShowSecret] = useState<string | null>(null);
  const [editingWebhook, setEditingWebhook] = useState<SurveyWebhook | null>(null);
  const [formData, setFormData] = useState({
    name: '',
    url: '',
    events: [] as string[],
    is_active: true,
  });

  const loadSurveys = useCallback(async () => {
    try {
      const response = await getSurveys();
      setSurveys(response.data);
      if (response.data.length > 0 && !selectedSurvey) {
        setSelectedSurvey(response.data[0].id);
      }
    } catch {
      toast.error('Failed to load surveys');
    }
  }, [selectedSurvey]);

  const loadWebhooks = useCallback(async () => {
    if (!selectedSurvey) return;
    try {
      setLoading(true);
      const data = await getSurveyWebhooks(selectedSurvey);
      setWebhooks(data);
    } catch {
      toast.error('Failed to load webhooks');
    } finally {
      setLoading(false);
    }
  }, [selectedSurvey]);

  useEffect(() => {
    loadSurveys();
  }, [loadSurveys]);

  useEffect(() => {
    loadWebhooks();
  }, [loadWebhooks]);

  const handleCreate = async () => {
    if (!selectedSurvey) return;
    try {
      const result = await createSurveyWebhook(selectedSurvey, formData);
      toast.success('Webhook created successfully');
      setShowSecret(result.secret);
      setIsCreateOpen(false);
      setFormData({ name: '', url: '', events: [], is_active: true });
      loadWebhooks();
    } catch {
      toast.error('Failed to create webhook');
    }
  };

  const handleUpdate = async () => {
    if (!editingWebhook) return;
    try {
      await updateSurveyWebhook(editingWebhook.id, formData);
      toast.success('Webhook updated');
      setIsEditOpen(false);
      setEditingWebhook(null);
      loadWebhooks();
    } catch {
      toast.error('Failed to update webhook');
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this webhook?')) return;
    try {
      await deleteSurveyWebhook(id);
      toast.success('Webhook deleted');
      loadWebhooks();
    } catch {
      toast.error('Failed to delete webhook');
    }
  };

  const handleToggle = async (webhook: SurveyWebhook) => {
    try {
      await toggleSurveyWebhook(webhook.id);
      toast.success(`Webhook ${webhook.is_active ? 'disabled' : 'enabled'}`);
      loadWebhooks();
    } catch {
      toast.error('Failed to toggle webhook');
    }
  };

  const handleRegenerateSecret = async (id: number) => {
    try {
      const result = await regenerateSurveyWebhookSecret(id);
      toast.success('Secret regenerated');
      setShowSecret(result.secret);
      loadWebhooks();
    } catch {
      toast.error('Failed to regenerate secret');
    }
  };

  const openEdit = (webhook: SurveyWebhook) => {
    setEditingWebhook(webhook);
    setFormData({
      name: webhook.name,
      url: webhook.url,
      events: webhook.events,
      is_active: webhook.is_active,
    });
    setIsEditOpen(true);
  };

  const toggleEvent = (event: string) => {
    setFormData((prev) => ({
      ...prev,
      events: prev.events.includes(event)
        ? prev.events.filter((e) => e !== event)
        : [...prev.events, event],
    }));
  };

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <Webhook className="w-8 h-8" />
            Webhooks
          </h1>
          <p className="text-muted-foreground mt-1">
            Configure webhooks to receive real-time survey events
          </p>
        </div>
        <div className="flex gap-2">
          <Link href="/dashboard/modules/survey">
            <Button variant="outline" className="gap-2">
              <FileText className="w-4 h-4" />
              Back to Survey
            </Button>
          </Link>
          <Dialog open={isCreateOpen} onOpenChange={setIsCreateOpen}>
            <DialogTrigger>
              <Button className="gap-2" disabled={!selectedSurvey}>
                <Plus className="w-4 h-4" />
                Add Webhook
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-lg">
              <DialogHeader>
                <DialogTitle>Add Webhook</DialogTitle>
              </DialogHeader>
              <div className="space-y-4 pt-4">
                <div className="space-y-2">
                  <Label>Survey</Label>
                  <Select
                    value={String(selectedSurvey || '')}
                    onValueChange={(v) => setSelectedSurvey(Number(v))}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Select a survey" />
                    </SelectTrigger>
                    <SelectContent>
                      {surveys.map((s) => (
                        <SelectItem key={s.id} value={String(s.id)}>
                          {s.title}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>Webhook Name</Label>
                  <Input
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="e.g., Slack Notifications"
                  />
                </div>
                <div className="space-y-2">
                  <Label>Webhook URL</Label>
                  <Input
                    value={formData.url}
                    onChange={(e) => setFormData({ ...formData, url: e.target.value })}
                    placeholder="https://api.example.com/webhook"
                  />
                </div>
                <div className="space-y-2">
                  <Label>Events to Subscribe</Label>
                  <div className="grid grid-cols-2 gap-2">
                    {WEBHOOK_EVENTS.map((event) => (
                      <label key={event.value} className="flex items-center gap-2 text-sm">
                        <input
                          type="checkbox"
                          checked={formData.events.includes(event.value)}
                          onChange={() => toggleEvent(event.value)}
                        />
                        {event.label}
                      </label>
                    ))}
                  </div>
                </div>
                <Button onClick={handleCreate} className="w-full" disabled={!formData.name || !formData.url || formData.events.length === 0}>
                  Add Webhook
                </Button>
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      {showSecret && (
        <Card className="border-yellow-200 bg-yellow-50 dark:bg-yellow-900/10">
          <CardContent className="p-4">
            <div className="flex items-start gap-2">
              <Key className="w-5 h-5 text-yellow-600 mt-0.5" />
              <div className="flex-1">
                <p className="font-medium text-yellow-800 dark:text-yellow-200">Webhook Secret Generated</p>
                <p className="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                  Copy this secret now - it won&apos;t be shown again:
                </p>
                <code className="block mt-2 p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded text-sm break-all font-mono">
                  {showSecret}
                </code>
                <Button variant="outline" size="sm" className="mt-2" onClick={() => setShowSecret(null)}>
                  Dismiss
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>Configured Webhooks</CardTitle>
            <Select
              value={String(selectedSurvey || '')}
              onValueChange={(v) => setSelectedSurvey(Number(v))}
            >
              <SelectTrigger className="w-[250px]">
                <SelectValue placeholder="Select a survey" />
              </SelectTrigger>
              <SelectContent>
                {surveys.map((s) => (
                  <SelectItem key={s.id} value={String(s.id)}>
                    {s.title}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="flex justify-center py-8">
              <Loader2 className="w-6 h-6 animate-spin" />
            </div>
          ) : webhooks.length === 0 ? (
            <p className="text-muted-foreground text-center py-8">
              No webhooks configured yet. Add a webhook to receive events.
            </p>
          ) : (
            <div className="space-y-3">
              {webhooks.map((webhook) => (
                <div
                  key={webhook.id}
                  className="flex items-center justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors"
                >
                  <div className="space-y-1 flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <span className="font-medium truncate">{webhook.name}</span>
                      <Badge variant={webhook.is_active ? 'default' : 'secondary'}>
                        {webhook.is_active ? 'Active' : 'Inactive'}
                      </Badge>
                    </div>
                    <p className="text-sm text-muted-foreground truncate">{webhook.url}</p>
                    <div className="flex flex-wrap gap-1 mt-1">
                      {webhook.events.slice(0, 3).map((event) => (
                        <Badge key={event} variant="outline" className="text-xs">
                          {event}
                        </Badge>
                      ))}
                      {webhook.events.length > 3 && (
                        <Badge variant="outline" className="text-xs">
                          +{webhook.events.length - 3} more
                        </Badge>
                      )}
                    </div>
                  </div>
                  <div className="flex items-center gap-1 ml-4">
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleToggle(webhook)}
                      title={webhook.is_active ? 'Disable' : 'Enable'}
                    >
                      <Power className={`w-4 h-4 ${webhook.is_active ? 'text-green-500' : 'text-gray-400'}`} />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleRegenerateSecret(webhook.id)}
                      title="Regenerate Secret"
                    >
                      <Key className="w-4 h-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => openEdit(webhook)}
                      title="Edit"
                    >
                      <Edit2 className="w-4 h-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDelete(webhook.id)}
                      className="text-red-500 hover:text-red-600"
                      title="Delete"
                    >
                      <Trash2 className="w-4 h-4" />
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      <Dialog open={isEditOpen} onOpenChange={setIsEditOpen}>
        <DialogContent className="max-w-lg">
          <DialogHeader>
            <DialogTitle>Edit Webhook</DialogTitle>
          </DialogHeader>
          <div className="space-y-4 pt-4">
            <div className="space-y-2">
              <Label>Webhook Name</Label>
              <Input
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label>Webhook URL</Label>
              <Input
                value={formData.url}
                onChange={(e) => setFormData({ ...formData, url: e.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label>Events to Subscribe</Label>
              <div className="grid grid-cols-2 gap-2">
                {WEBHOOK_EVENTS.map((event) => (
                  <label key={event.value} className="flex items-center gap-2 text-sm">
                    <input
                      type="checkbox"
                      checked={formData.events.includes(event.value)}
                      onChange={() => toggleEvent(event.value)}
                    />
                    {event.label}
                  </label>
                ))}
              </div>
            </div>
            <Button onClick={handleUpdate} className="w-full">
              Update Webhook
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
