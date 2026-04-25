'use client';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Webhook, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function WebhooksPage() {
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
        <Button className="gap-2">
          <Plus className="w-4 h-4" />
          Add Webhook
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Configured Webhooks</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-muted-foreground text-center py-8">
            No webhooks configured yet. Add a webhook to receive events.
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
