'use client';

import React, { useEffect, useState } from 'react';
import { toast } from 'sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { DollarSign, Users } from 'lucide-react';
import { getCrmPipeline } from '@/lib/tenant-resources';

interface PipelineStage {
  stage: string;
  label: string;
  probability: number;
  color: string;
  count: number;
  value: number;
  opportunities: Array<{
    id: number;
    name: string;
    expected_revenue: number;
    contact?: { name: string };
    assignedUser?: { name: string };
  }>;
}

export default function PipelinePage() {
  const [stages, setStages] = useState<PipelineStage[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchPipelineData = async () => {
    try {
      setLoading(true);
      const data = await getCrmPipeline();
      setStages((data as PipelineStage[]) || []);
    } catch {
      toast.error('Failed to load pipeline data');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    void fetchPipelineData();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  if (loading) {
    return (
      <div className="p-6">
        <h1 className="text-2xl font-bold mb-6">Sales Pipeline</h1>
        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
          {[1, 2, 3, 4, 5].map((i) => (
            <Skeleton key={i} className="h-96" />
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-2xl font-bold">Sales Pipeline</h1>
          <p className="text-muted-foreground">Visualize and manage your deals</p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 overflow-x-auto">
        {stages.map((stage) => (
          <Card key={stage.stage} className="min-w-[250px]">
            <CardHeader className="pb-3">
              <div className="flex justify-between items-start">
                <CardTitle className="text-sm font-medium">{stage.label}</CardTitle>
                <Badge style={{ backgroundColor: stage.color }}>{stage.probability}%</Badge>
              </div>
              <div className="flex items-center gap-2 text-xs text-muted-foreground">
                <Users className="w-3 h-3" />
                {stage.count} deals
                <DollarSign className="w-3 h-3 ml-2" />
                ${stage.value.toLocaleString()}
              </div>
            </CardHeader>
            <CardContent className="space-y-2">
              {stage.opportunities.map((opp) => (
                <Card key={opp.id} className="cursor-pointer hover:shadow-md transition-shadow">
                  <CardContent className="p-3">
                    <p className="font-medium text-sm">{opp.name}</p>
                    {opp.contact && (
                      <p className="text-xs text-muted-foreground">{opp.contact.name}</p>
                    )}
                    <p className="text-xs font-semibold mt-1">
                      ${opp.expected_revenue.toLocaleString()}
                    </p>
                    {opp.assignedUser && (
                      <p className="text-xs text-muted-foreground mt-1">
                        Assigned: {opp.assignedUser.name}
                      </p>
                    )}
                  </CardContent>
                </Card>
              ))}
              {stage.opportunities.length === 0 && (
                <p className="text-xs text-muted-foreground text-center py-4">
                  No opportunities in this stage
                </p>
              )}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
