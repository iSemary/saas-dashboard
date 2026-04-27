"use client";

import { useEffect, useState, useCallback } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { toast } from "sonner";
import api from "@/lib/api";
import {
  ArrowLeft,
  Plus,
  List,
  MoreHorizontal,
  Clock,
  AlertTriangle,
} from "lucide-react";

interface Ticket {
  id: number;
  ticket_number: string;
  title: string;
  priority: string;
  status: string;
  assignee?: {
    id: number;
    name: string;
    avatar?: string;
  };
  brand?: {
    id: number;
    name: string;
  };
  due_date?: string;
}

interface KanbanColumn {
  id: string;
  title: string;
  tickets: Ticket[];
}

const COLUMNS: { id: string; title: string }[] = [
  { id: "open", title: "Open" },
  { id: "in_progress", title: "In Progress" },
  { id: "waiting", title: "Waiting" },
  { id: "resolved", title: "Resolved" },
  { id: "closed", title: "Closed" },
];

export default function TicketKanbanPage() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [columns, setColumns] = useState<KanbanColumn[]>([]);
  const [draggedTicket, setDraggedTicket] = useState<Ticket | null>(null);
  const [dragOverColumn, setDragOverColumn] = useState<string | null>(null);

  const loadKanban = useCallback(async () => {
    try {
      setLoading(true);
      const response = await api.get("/tickets/kanban/list");
      const data = response.data.data;

      // Transform data into columns
      const kanbanColumns = COLUMNS.map((col) => ({
        id: col.id,
        title: col.title,
        tickets: data[col.id] || [],
      }));

      setColumns(kanbanColumns);
    } catch {
      toast.error("Failed to load kanban board");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadKanban();
  }, [loadKanban]);

  const handleDragStart = (ticket: Ticket) => {
    setDraggedTicket(ticket);
  };

  const handleDragOver = (e: React.DragEvent, columnId: string) => {
    e.preventDefault();
    setDragOverColumn(columnId);
  };

  const handleDragLeave = () => {
    setDragOverColumn(null);
  };

  const handleDrop = async (e: React.DragEvent, columnId: string) => {
    e.preventDefault();
    setDragOverColumn(null);

    if (!draggedTicket || draggedTicket.status === columnId) {
      setDraggedTicket(null);
      return;
    }

    // Optimistic update
    const oldStatus = draggedTicket.status;
    const updatedTicket = { ...draggedTicket, status: columnId };

    setColumns((prev) =>
      prev.map((col) => {
        if (col.id === oldStatus) {
          return {
            ...col,
            tickets: col.tickets.filter((t) => t.id !== draggedTicket.id),
          };
        }
        if (col.id === columnId) {
          return {
            ...col,
            tickets: [...col.tickets, updatedTicket],
          };
        }
        return col;
      })
    );

    // API call to update status
    try {
      await api.put(`/tickets/${draggedTicket.id}/status`, {
        status: columnId,
      });
      toast.success("Ticket moved successfully");
    } catch {
      toast.error("Failed to move ticket");
      // Revert on failure
      loadKanban();
    } finally {
      setDraggedTicket(null);
    }
  };

  const getPriorityBadge = (priority: string) => {
    const variants: Record<string, string> = {
      low: "bg-green-100 text-green-800 border-green-200",
      medium: "bg-blue-100 text-blue-800 border-blue-200",
      high: "bg-orange-100 text-orange-800 border-orange-200",
      urgent: "bg-red-100 text-red-800 border-red-200",
    };
    return variants[priority] || "bg-gray-100";
  };

  const isOverdue = (ticket: Ticket): boolean => {
    if (!ticket.due_date) return false;
    return new Date(ticket.due_date) < new Date();
  };

  if (loading) {
    return (
      <div className="flex h-full items-center justify-center">
        <div className="text-muted-foreground">Loading kanban board...</div>
      </div>
    );
  }

  return (
    <div className="flex h-full flex-col space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Button variant="outline" size="sm" onClick={() => router.back()}>
            <ArrowLeft className="mr-2 size-4" />
            Back
          </Button>
          <h1 className="text-2xl font-semibold">Ticket Kanban Board</h1>
        </div>
        <div className="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            onClick={() => router.push("/dashboard/tickets")}
          >
            <List className="mr-2 size-4" />
            List View
          </Button>
          <Button size="sm" onClick={() => router.push("/dashboard/tickets/new")}>
            <Plus className="mr-2 size-4" />
            New Ticket
          </Button>
        </div>
      </div>

      {/* Kanban Board */}
      <div className="flex-1 overflow-x-auto">
        <div className="flex h-full gap-4 pb-4">
          {columns.map((column) => (
            <div
              key={column.id}
              className={`flex w-80 flex-shrink-0 flex-col rounded-lg border bg-muted/30 transition-colors ${
                dragOverColumn === column.id ? "border-primary bg-primary/5" : ""
              }`}
              onDragOver={(e) => handleDragOver(e, column.id)}
              onDragLeave={handleDragLeave}
              onDrop={(e) => handleDrop(e, column.id)}
            >
              {/* Column Header */}
              <CardHeader className="flex flex-row items-center justify-between py-3">
                <div className="flex items-center gap-2">
                  <h3 className="font-semibold">{column.title}</h3>
                  <Badge variant="secondary" className="text-xs">
                    {column.tickets.length}
                  </Badge>
                </div>
                <Button variant="ghost" size="icon" className="size-8">
                  <MoreHorizontal className="size-4" />
                </Button>
              </CardHeader>

              {/* Tickets */}
              <div className="flex-1 space-y-2 overflow-y-auto p-3">
                {column.tickets.map((ticket) => (
                  <Card
                    key={ticket.id}
                    draggable
                    onDragStart={() => handleDragStart(ticket)}
                    className={`cursor-move transition-shadow hover:shadow-md ${
                      draggedTicket?.id === ticket.id ? "opacity-50" : ""
                    }`}
                    onClick={() => router.push(`/dashboard/tickets/${ticket.id}`)}
                  >
                    <CardContent className="p-3">
                      {/* Ticket Number & Priority */}
                      <div className="mb-2 flex items-center justify-between">
                        <span className="text-xs text-muted-foreground">
                          {ticket.ticket_number}
                        </span>
                        <div className="flex items-center gap-1">
                          {isOverdue(ticket) && (
                            <AlertTriangle className="size-3 text-red-500" />
                          )}
                          <Badge
                            variant="outline"
                            className={`text-xs ${getPriorityBadge(ticket.priority)}`}
                          >
                            {ticket.priority}
                          </Badge>
                        </div>
                      </div>

                      {/* Title */}
                      <h4 className="mb-3 line-clamp-2 text-sm font-medium">
                        {ticket.title}
                      </h4>

                      {/* Footer */}
                      <div className="flex items-center justify-between">
                        {/* Assignee */}
                        <div className="flex items-center gap-1">
                          {ticket.assignee ? (
                            <Avatar className="size-6">
                              <AvatarFallback className="text-xs">
                                {ticket.assignee.name[0]}
                              </AvatarFallback>
                            </Avatar>
                          ) : (
                            <span className="text-xs text-muted-foreground">
                              Unassigned
                            </span>
                          )}
                        </div>

                        {/* Due Date */}
                        {ticket.due_date && (
                          <div
                            className={`flex items-center gap-1 text-xs ${
                              isOverdue(ticket)
                                ? "text-red-600"
                                : "text-muted-foreground"
                            }`}
                          >
                            <Clock className="size-3" />
                            {new Date(ticket.due_date).toLocaleDateString()}
                          </div>
                        )}
                      </div>

                      {/* Brand */}
                      {ticket.brand && (
                        <div className="mt-2">
                          <Badge variant="outline" className="text-xs">
                            {ticket.brand.name}
                          </Badge>
                        </div>
                      )}
                    </CardContent>
                  </Card>
                ))}

                {column.tickets.length === 0 && (
                  <div className="flex h-32 items-center justify-center rounded border border-dashed">
                    <span className="text-sm text-muted-foreground">
                      No tickets
                    </span>
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
