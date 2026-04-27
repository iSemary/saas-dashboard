'use client';

import { useEffect, useMemo, useState } from 'react';
import type { ResponsiveLayouts } from 'react-grid-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FileText, LayoutDashboard, BarChart3, MessageSquare, Copy, Palette } from 'lucide-react';
import Link from 'next/link';
import { getSurveyDashboard } from '@/lib/api-survey';
import DraggableDashboardGrid from '@/components/dashboard/DraggableDashboardGrid';

const STORAGE_KEY = 'dashboard_layout_survey';

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ['total', 'active', 'draft', 'closed', 'surveys', 'templates', 'themes', 'analytics', 'recent'];
  const lg = keys.map((key, i) => {
    const isSmall = ['total', 'active', 'draft', 'closed'].includes(key);
    const col = isSmall ? i % 4 : (i - 4) % 4;
    const row = isSmall ? 0 : (i < 8 ? 2 : 6);
    const w = isSmall ? 3 : (key === 'recent' ? 12 : 3);
    return { i: key, x: isSmall ? col * 3 : (i - 4) % 4 * 3, y: row, w, h: isSmall ? 2 : 4, minH: 2, minW: 2 };
  });
  const md = keys.map((key, i) => {
    const isSmall = ['total', 'active', 'draft', 'closed'].includes(key);
    const row = isSmall ? 0 : (i < 8 ? 2 : 6);
    const w = isSmall ? 3 : (key === 'recent' ? 12 : 3);
    return { i: key, x: isSmall ? (i % 4) * 3 : ((i - 4) % 4) * 3, y: row, w, h: isSmall ? 2 : 4, minH: 2, minW: 2 };
  });
  const sm = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

interface DashboardMetrics {
  total_surveys: number;
  active_surveys: number;
  draft_surveys: number;
  closed_surveys: number;
}

interface Survey {
  id: number;
  title: string;
  status: string;
  created_at: string;
}

export default function SurveyDashboardPage() {
  const [metrics, setMetrics] = useState<DashboardMetrics | null>(null);
  const [recentSurveys, setRecentSurveys] = useState<Survey[]>([]);
  const [loading, setLoading] = useState(true);

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  useEffect(() => {
    let cancelled = false;
    const loadData = async () => {
      try {
        const response = await getSurveyDashboard();
        if (!cancelled) {
          setMetrics(response.data.metrics);
          setRecentSurveys(response.data.recent_surveys || []);
        }
      } catch (error) {
        console.error('Failed to load dashboard:', error);
      } finally {
        if (!cancelled) setLoading(false);
      }
    };
    loadData();
    return () => { cancelled = true; };
  }, []);

  if (loading) {
    return (
      <div className="p-6">
        <div className="animate-pulse space-y-4">
          <div className="h-8 bg-gray-200 rounded w-1/4"></div>
          <div className="grid grid-cols-4 gap-4">
            {[...Array(4)].map((_, i) => (
              <div key={i} className="h-24 bg-gray-200 rounded"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  const metricCards = [
    <div key="total" className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium text-muted-foreground">Total Surveys</CardTitle>
        </CardHeader>
        <CardContent><div className="text-3xl font-bold">{metrics?.total_surveys || 0}</div></CardContent>
      </Card>
    </div>,
    <div key="active" className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium text-muted-foreground">Active</CardTitle>
        </CardHeader>
        <CardContent><div className="text-3xl font-bold text-green-600">{metrics?.active_surveys || 0}</div></CardContent>
      </Card>
    </div>,
    <div key="draft" className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium text-muted-foreground">Draft</CardTitle>
        </CardHeader>
        <CardContent><div className="text-3xl font-bold text-yellow-600">{metrics?.draft_surveys || 0}</div></CardContent>
      </Card>
    </div>,
    <div key="closed" className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium text-muted-foreground">Closed</CardTitle>
        </CardHeader>
        <CardContent><div className="text-3xl font-bold text-gray-600">{metrics?.closed_surveys || 0}</div></CardContent>
      </Card>
    </div>,
  ];

  const quickLinkCards = [
    <div key="surveys" className="h-full">
      <Link href="/dashboard/modules/survey/surveys">
        <Card className="h-full hover:bg-accent transition-colors cursor-pointer">
          <CardContent className="p-6 flex items-center gap-4">
            <div className="p-3 bg-primary/10 rounded-lg"><FileText className="w-6 h-6 text-primary" /></div>
            <div><h3 className="font-semibold">Surveys</h3><p className="text-sm text-muted-foreground">Manage your surveys</p></div>
          </CardContent>
        </Card>
      </Link>
    </div>,
    <div key="templates" className="h-full">
      <Link href="/dashboard/modules/survey/templates">
        <Card className="h-full hover:bg-accent transition-colors cursor-pointer">
          <CardContent className="p-6 flex items-center gap-4">
            <div className="p-3 bg-primary/10 rounded-lg"><Copy className="w-6 h-6 text-primary" /></div>
            <div><h3 className="font-semibold">Templates</h3><p className="text-sm text-muted-foreground">Start from templates</p></div>
          </CardContent>
        </Card>
      </Link>
    </div>,
    <div key="themes" className="h-full">
      <Link href="/dashboard/modules/survey/themes">
        <Card className="h-full hover:bg-accent transition-colors cursor-pointer">
          <CardContent className="p-6 flex items-center gap-4">
            <div className="p-3 bg-primary/10 rounded-lg"><Palette className="w-6 h-6 text-primary" /></div>
            <div><h3 className="font-semibold">Themes</h3><p className="text-sm text-muted-foreground">Customize appearance</p></div>
          </CardContent>
        </Card>
      </Link>
    </div>,
    <div key="analytics" className="h-full">
      <Link href="/dashboard/modules/survey/analytics">
        <Card className="h-full hover:bg-accent transition-colors cursor-pointer">
          <CardContent className="p-6 flex items-center gap-4">
            <div className="p-3 bg-primary/10 rounded-lg"><BarChart3 className="w-6 h-6 text-primary" /></div>
            <div><h3 className="font-semibold">Analytics</h3><p className="text-sm text-muted-foreground">View insights</p></div>
          </CardContent>
        </Card>
      </Link>
    </div>,
  ];

  const recentSurveysCard = (
    <div key="recent" className="h-full">
      <Card className="h-full">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <MessageSquare className="w-5 h-5" />
            Recent Surveys
          </CardTitle>
        </CardHeader>
        <CardContent>
          {recentSurveys.length === 0 ? (
            <p className="text-muted-foreground text-center py-4">No surveys yet. Create your first survey to get started.</p>
          ) : (
            <div className="space-y-2">
              {recentSurveys.slice(0, 5).map((survey) => (
                <div key={survey.id} className="flex items-center justify-between p-3 rounded-lg hover:bg-accent transition-colors">
                  <div className="flex items-center gap-3">
                    <FileText className="w-5 h-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">{survey.title}</p>
                      <p className="text-sm text-muted-foreground">Created {new Date(survey.created_at).toLocaleDateString()}</p>
                    </div>
                  </div>
                  <span className={`px-2 py-1 rounded text-xs font-medium ${survey.status === 'active' ? 'bg-green-100 text-green-800' : survey.status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'}`}>
                    {survey.status}
                  </span>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <LayoutDashboard className="w-8 h-8" />
            Survey Dashboard
          </h1>
          <p className="text-muted-foreground mt-1">Create surveys, collect responses, and analyze results</p>
        </div>
        <Link href="/dashboard/modules/survey/surveys">
          <Button className="gap-2"><FileText className="w-4 h-4" />Manage Surveys</Button>
        </Link>
      </div>

      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {[...metricCards, ...quickLinkCards, recentSurveysCard]}
      </DraggableDashboardGrid>
    </div>
  );
}
