'use client';

import { useState, useEffect, useCallback } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Zap, Plus, Edit2, Trash2, Power, Loader2, FileText } from 'lucide-react';
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
  SurveyAutomationRule,
  getSurveyAutomationRules,
  createSurveyAutomationRule,
  updateSurveyAutomationRule,
  deleteSurveyAutomationRule,
  toggleSurveyAutomationRule,
  getSurveys,
  Survey,
} from '@/lib/api-survey';
import Link from 'next/link';

const TRIGGER_TYPES = [
  { value: 'response_completed', label: 'Response Completed' },
  { value: 'response_partial', label: 'Response Partial' },
  { value: 'question_answered', label: 'Question Answered' },
  { value: 'score_reached', label: 'Score Reached' },
];

const ACTION_TYPES = [
  { value: 'send_email', label: 'Send Email' },
  { value: 'send_notification', label: 'Send Notification' },
  { value: 'webhook', label: 'Trigger Webhook' },
  { value: 'update_field', label: 'Update Field' },
];

export default function AutomationPage() {
  const [rules, setRules] = useState<SurveyAutomationRule[]>([]);
  const [surveys, setSurveys] = useState<Survey[]>([]);
  const [selectedSurvey, setSelectedSurvey] = useState<number | null>(null);
  const [loading, setLoading] = useState(false);
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [editingRule, setEditingRule] = useState<SurveyAutomationRule | null>(null);
  const [formData, setFormData] = useState<{
    name: string;
    trigger_type: SurveyAutomationRule['trigger_type'] | '';
    action_type: SurveyAutomationRule['action_type'] | '';
    is_active: boolean;
  }>({
    name: '',
    trigger_type: '',
    action_type: '',
    is_active: true,
  });

  const loadSurveys = useCallback(async () => {
    try {
      const response = await getSurveys();
      setSurveys(response.data);
      if (response.data.length > 0 && !selectedSurvey) {
        setSelectedSurvey(response.data[0].id);
      }
    } catch (error) {
      toast.error('Failed to load surveys');
    }
  }, [selectedSurvey]);

  const loadRules = useCallback(async () => {
    if (!selectedSurvey) return;
    try {
      setLoading(true);
      const data = await getSurveyAutomationRules(selectedSurvey);
      setRules(data);
    } catch (error) {
      toast.error('Failed to load automation rules');
    } finally {
      setLoading(false);
    }
  }, [selectedSurvey]);

  useEffect(() => {
    loadSurveys();
  }, [loadSurveys]);

  useEffect(() => {
    loadRules();
  }, [loadRules]);

  const handleCreate = async () => {
    if (!selectedSurvey) return;
    try {
      await createSurveyAutomationRule(selectedSurvey, {
        name: formData.name,
        trigger_type: formData.trigger_type || undefined,
        action_type: formData.action_type || undefined,
        is_active: formData.is_active,
        conditions: {},
        action_config: {},
      });
      toast.success('Automation rule created');
      setIsCreateOpen(false);
      setFormData({ name: '', trigger_type: '', action_type: '', is_active: true });
      loadRules();
    } catch (error) {
      toast.error('Failed to create rule');
    }
  };

  const handleUpdate = async () => {
    if (!editingRule) return;
    try {
      await updateSurveyAutomationRule(editingRule.id, {
        name: formData.name,
        trigger_type: formData.trigger_type || undefined,
        action_type: formData.action_type || undefined,
        is_active: formData.is_active,
      });
      toast.success('Automation rule updated');
      setIsEditOpen(false);
      setEditingRule(null);
      loadRules();
    } catch (error) {
      toast.error('Failed to update rule');
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this rule?')) return;
    try {
      await deleteSurveyAutomationRule(id);
      toast.success('Automation rule deleted');
      loadRules();
    } catch (error) {
      toast.error('Failed to delete rule');
    }
  };

  const handleToggle = async (rule: SurveyAutomationRule) => {
    try {
      await toggleSurveyAutomationRule(rule.id);
      toast.success(`Rule ${rule.is_active ? 'disabled' : 'enabled'}`);
      loadRules();
    } catch (error) {
      toast.error('Failed to toggle rule');
    }
  };

  const openEdit = (rule: SurveyAutomationRule) => {
    setEditingRule(rule);
    setFormData({
      name: rule.name,
      trigger_type: rule.trigger_type,
      action_type: rule.action_type,
      is_active: rule.is_active,
    });
    setIsEditOpen(true);
  };

  const getTriggerLabel = (value: string) =>
    TRIGGER_TYPES.find((t) => t.value === value)?.label || value;

  const getActionLabel = (value: string) =>
    ACTION_TYPES.find((a) => a.value === value)?.label || value;

  return (
    <div className="p-6 space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <Zap className="w-8 h-8" />
            Automation Rules
          </h1>
          <p className="text-muted-foreground mt-1">
            Set up automated actions based on survey events
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
                Create Rule
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-lg">
              <DialogHeader>
                <DialogTitle>Create Automation Rule</DialogTitle>
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
                  <Label>Rule Name</Label>
                  <Input
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="e.g., Send email on completion"
                  />
                </div>
                <div className="space-y-2">
                  <Label>Trigger Event</Label>
                  <Select
                    value={formData.trigger_type || undefined}
                    onValueChange={(v) => setFormData({ ...formData, trigger_type: v as SurveyAutomationRule['trigger_type'] })}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Select trigger" />
                    </SelectTrigger>
                    <SelectContent>
                      {TRIGGER_TYPES.map((t) => (
                        <SelectItem key={t.value} value={t.value}>
                          {t.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>Action Type</Label>
                  <Select
                    value={formData.action_type || undefined}
                    onValueChange={(v) => setFormData({ ...formData, action_type: v as SurveyAutomationRule['action_type'] })}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Select action" />
                    </SelectTrigger>
                    <SelectContent>
                      {ACTION_TYPES.map((a) => (
                        <SelectItem key={a.value} value={a.value}>
                          {a.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <Button onClick={handleCreate} className="w-full" disabled={!formData.name || !formData.trigger_type || !formData.action_type}>
                  Create Rule
                </Button>
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>Automation Rules</CardTitle>
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
          ) : rules.length === 0 ? (
            <p className="text-muted-foreground text-center py-8">
              No automation rules yet. Create your first rule to automate survey workflows.
            </p>
          ) : (
            <div className="space-y-3">
              {rules.map((rule) => (
                <div
                  key={rule.id}
                  className="flex items-center justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors"
                >
                  <div className="space-y-1">
                    <div className="flex items-center gap-2">
                      <span className="font-medium">{rule.name}</span>
                      <Badge variant={rule.is_active ? 'default' : 'secondary'}>
                        {rule.is_active ? 'Active' : 'Inactive'}
                      </Badge>
                    </div>
                    <p className="text-sm text-muted-foreground">
                      When <strong>{getTriggerLabel(rule.trigger_type)}</strong> → Then{' '}
                      <strong>{getActionLabel(rule.action_type)}</strong>
                    </p>
                  </div>
                  <div className="flex items-center gap-1">
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleToggle(rule)}
                      title={rule.is_active ? 'Disable' : 'Enable'}
                    >
                      <Power className={`w-4 h-4 ${rule.is_active ? 'text-green-500' : 'text-gray-400'}`} />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => openEdit(rule)}
                      title="Edit"
                    >
                      <Edit2 className="w-4 h-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDelete(rule.id)}
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
            <DialogTitle>Edit Automation Rule</DialogTitle>
          </DialogHeader>
          <div className="space-y-4 pt-4">
            <div className="space-y-2">
              <Label>Rule Name</Label>
              <Input
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label>Trigger Event</Label>
              <Select
                value={formData.trigger_type || undefined}
                onValueChange={(v) => setFormData({ ...formData, trigger_type: v as SurveyAutomationRule['trigger_type'] })}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select trigger" />
                </SelectTrigger>
                <SelectContent>
                  {TRIGGER_TYPES.map((t) => (
                    <SelectItem key={t.value} value={t.value}>
                      {t.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label>Action Type</Label>
              <Select
                value={formData.action_type || undefined}
                onValueChange={(v) => setFormData({ ...formData, action_type: v as SurveyAutomationRule['action_type'] })}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select action" />
                </SelectTrigger>
                <SelectContent>
                  {ACTION_TYPES.map((a) => (
                    <SelectItem key={a.value} value={a.value}>
                      {a.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <Button onClick={handleUpdate} className="w-full">
              Update Rule
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
