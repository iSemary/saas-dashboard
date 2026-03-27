"use client"

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Badge } from "@/components/ui/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { type Company } from "@/lib/customers"
import { getCompanyActivity } from "@/lib/customers"
import { useEffect, useState } from "react"
import { format } from "date-fns"
import { ActivityTimeline } from "@/components/activity/ActivityTimeline"
import { type ActivityLog } from "@/lib/activity"

interface CustomerProfileProps {
  customer: Company
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function CustomerProfile({ customer, open, onOpenChange }: CustomerProfileProps) {
  const [activityLogs, setActivityLogs] = useState<ActivityLog[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    if (open && customer) {
      loadActivity()
    }
  }, [open, customer])

  const loadActivity = async () => {
    try {
      setLoading(true)
      const response = await getCompanyActivity(customer.id)
      // Transform audit logs to ActivityLog format
      const logs: ActivityLog[] = (response.data.data || []).map((audit: any) => ({
        id: audit.id,
        event: audit.event,
        user: audit.user ? { name: audit.user.name, email: audit.user.email } : null,
        old_values: audit.old_values || {},
        new_values: audit.new_values || {},
        created_at: audit.created_at,
        auditable_type: audit.auditable_type,
        auditable_id: audit.auditable_id,
      }))
      setActivityLogs(logs)
    } catch (error) {
      console.error("Failed to load activity", error)
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>{customer.name}</DialogTitle>
          <DialogDescription>Customer profile and details</DialogDescription>
        </DialogHeader>

        <Tabs defaultValue="details" className="w-full">
          <TabsList>
            <TabsTrigger value="details">Details</TabsTrigger>
            <TabsTrigger value="contacts">Contacts</TabsTrigger>
            <TabsTrigger value="activity">Activity</TabsTrigger>
          </TabsList>

          <TabsContent value="details" className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm font-medium text-muted-foreground">Type</label>
                <div className="mt-1">
                  <Badge variant="outline">{customer.type}</Badge>
                </div>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Industry</label>
                <p className="mt-1">{customer.industry || "-"}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Email</label>
                <p className="mt-1">{customer.email || "-"}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Phone</label>
                <p className="mt-1">{customer.phone || "-"}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Website</label>
                <p className="mt-1">
                  {customer.website ? (
                    <a href={customer.website} target="_blank" rel="noopener noreferrer" className="text-primary hover:underline">
                      {customer.website}
                    </a>
                  ) : (
                    "-"
                  )}
                </p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Employee Count</label>
                <p className="mt-1">{customer.employee_count || "-"}</p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Annual Revenue</label>
                <p className="mt-1">
                  {customer.annual_revenue
                    ? new Intl.NumberFormat("en-US", {
                        style: "currency",
                        currency: "USD",
                      }).format(customer.annual_revenue)
                    : "-"}
                </p>
              </div>
              <div>
                <label className="text-sm font-medium text-muted-foreground">Assigned To</label>
                <p className="mt-1">{customer.assigned_user?.name || "-"}</p>
              </div>
            </div>

            {customer.address && (
              <div>
                <label className="text-sm font-medium text-muted-foreground">Address</label>
                <p className="mt-1">
                  {customer.address}
                  {customer.city && `, ${customer.city}`}
                  {customer.state && `, ${customer.state}`}
                  {customer.postal_code && ` ${customer.postal_code}`}
                  {customer.country && `, ${customer.country}`}
                </p>
              </div>
            )}

            {customer.description && (
              <div>
                <label className="text-sm font-medium text-muted-foreground">Description</label>
                <p className="mt-1">{customer.description}</p>
              </div>
            )}

            {customer.notes && (
              <div>
                <label className="text-sm font-medium text-muted-foreground">Notes</label>
                <p className="mt-1">{customer.notes}</p>
              </div>
            )}

            <div className="pt-4 border-t">
              <div className="grid grid-cols-2 gap-4 text-sm text-muted-foreground">
                <div>
                  <span className="font-medium">Created:</span>{" "}
                  {format(new Date(customer.created_at), "PPp")}
                </div>
                <div>
                  <span className="font-medium">Updated:</span>{" "}
                  {format(new Date(customer.updated_at), "PPp")}
                </div>
              </div>
            </div>
          </TabsContent>

          <TabsContent value="contacts" className="space-y-4">
            {customer.contacts && customer.contacts.length > 0 ? (
              <div className="space-y-2">
                {customer.contacts.map((contact) => (
                  <div key={contact.id} className="p-4 border rounded-lg">
                    <div className="font-medium">
                      {contact.first_name} {contact.last_name}
                    </div>
                    {contact.email && <div className="text-sm text-muted-foreground">{contact.email}</div>}
                    {contact.phone && <div className="text-sm text-muted-foreground">{contact.phone}</div>}
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-8 text-muted-foreground">No contacts found</div>
            )}
          </TabsContent>

          <TabsContent value="activity" className="space-y-4">
            {loading ? (
              <div className="text-center py-8 text-muted-foreground">Loading activity...</div>
            ) : activityLogs.length > 0 ? (
              <ActivityTimeline logs={activityLogs} />
            ) : (
              <div className="text-center py-8 text-muted-foreground">No activity found</div>
            )}
          </TabsContent>
        </Tabs>
      </DialogContent>
    </Dialog>
  )
}
