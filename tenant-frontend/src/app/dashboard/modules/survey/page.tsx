'use client';

import { useEffect, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FileText, LayoutDashboard, BarChart3, MessageSquare, Copy, Palette } from 'lucide-react';
import Link from 'next/link';
import { getSurveyDashboard } from '@/lib/api-survey';

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

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      const response = await getSurveyDashboard();
      setMetrics(response.data.metrics);
      setRecentSurveys(response.data.recent_surveys || []);
    } catch (error) {
      console.error('Failed to load dashboard:', error);
    } finally {
      setLoading(false);
    }
  };

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

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <LayoutDashboard className="w-8 h-8" />
            Survey Dashboard
          </h1>
          <p className="text-muted-foreground mt-1">
            Create surveys, collect responses, and analyze results
          </p>
        </div>
        <Link href="/dashboard/modules/survey/surveys">
          <Button className="gap-2">
            <FileText className="w-4 h-4" />
            Manage Surveys
          </Button>
        </Link>
      </div>

      {/* Metrics */}
      <div className="grid grid-cols-4 gap-4">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Total Surveys
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold">{metrics?.total_surveys || 0}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Active
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-green-600">
              {metrics?.active_surveys || 0}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Draft
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-yellow-600">
              {metrics?.draft_surveys || 0}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Closed
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-gray-600">
              {metrics?.closed_surveys || 0}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Quick Links */}
      <div className="grid grid-cols-4 gap-4">
        <Link href="/dashboard/modules/survey/surveys">
          <Card className="hover:bg-accent transition-colors cursor-pointer">
            <CardContent className="p-6 flex items-center gap-4">
              <div className="p-3 bg-primary/10 rounded-lg">
                <FileText className="w-6 h-6 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Surveys</h3>
                <p className="text-sm text-muted-foreground">Manage your surveys</p>
              </div>
            </CardContent>
          </Card>
        </Link>

        <Link href="/dashboard/modules/survey/templates">
          <Card className="hover:bg-accent transition-colors cursor-pointer">
            <CardContent className="p-6 flex items-center gap-4">
              <div className="p-3 bg-primary/10 rounded-lg">
                <Copy className="w-6 h-6 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Templates</h3>
                <p className="text-sm text-muted-foreground">Start from templates</p>
              </div>
            </CardContent>
          </Card>
        </Link>

        <Link href="/dashboard/modules/survey/themes">
          <Card className="hover:bg-accent transition-colors cursor-pointer">
            <CardContent className="p-6 flex items-center gap-4">
              <div className="p-3 bg-primary/10 rounded-lg">
                <Palette className="w-6 h-6 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Themes</h3>
                <p className="text-sm text-muted-foreground">Customize appearance</p>
              </div>
            </CardContent>
          </Card>
        </Link>

        <Link href="/dashboard/modules/survey/analytics">
          <Card className="hover:bg-accent transition-colors cursor-pointer">
            <CardContent className="p-6 flex items-center gap-4">
              <div className="p-3 bg-primary/10 rounded-lg">
                <BarChart3 className="w-6 h-6 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Analytics</h3>
                <p className="text-sm text-muted-foreground">View insights</p>
              </div>
            </CardContent>
          </Card>
        </Link>
      </div>

      {/* Recent Surveys */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <MessageSquare className="w-5 h-5" />
            Recent Surveys
          </CardTitle>
        </CardHeader>
        <CardContent>
          {recentSurveys.length === 0 ? (
            <p className="text-muted-foreground text-center py-4">
              No surveys yet. Create your first survey to get started.
            </p>
          ) : (
            <div className="space-y-2">
              {recentSurveys.slice(0, 5).map((survey) => (
                <div
                  key={survey.id}
                  className="flex items-center justify-between p-3 rounded-lg hover:bg-accent transition-colors"
                >
                  <div className="flex items-center gap-3">
                    <FileText className="w-5 h-5 text-muted-foreground" />
                    <div>
                      <p className="font-medium">{survey.title}</p>
                      <p className="text-sm text-muted-foreground">
                        Created {new Date(survey.created_at).toLocaleDateString()}
                      </p>
                    </div>
                  </div>
                  <span
                    className={`px-2 py-1 rounded text-xs font-medium ${
                      survey.status === 'active'
                        ? 'bg-green-100 text-green-800'
                        : survey.status === 'draft'
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-gray-100 text-gray-800'
                    }`}
                  >
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
}
