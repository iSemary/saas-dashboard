'use client';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Zap, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function AutomationPage() {
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
        <Button className="gap-2">
          <Plus className="w-4 h-4" />
          Create Rule
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Active Rules</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground text-center py-8">
            No automation rules yet. Create your first rule to automate survey workflows.
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
