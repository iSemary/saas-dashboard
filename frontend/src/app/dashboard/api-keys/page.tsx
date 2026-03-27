"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import { Alert, AlertDescription } from "@/components/ui/alert"
import {
  getApiKeys,
  createApiKey,
  revokeApiKey,
  type ApiKey,
} from "@/lib/api-keys"
import { Plus, Trash2, Copy, Check } from "lucide-react"
import { toast } from "sonner"
import { format } from "date-fns"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form"

const apiKeySchema = z.object({
  name: z.string().min(1, "Name is required"),
})

type ApiKeyFormValues = z.infer<typeof apiKeySchema>

export default function ApiKeysPage() {
  const [apiKeys, setApiKeys] = useState<ApiKey[]>([])
  const [loading, setLoading] = useState(true)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [newToken, setNewToken] = useState<string | null>(null)
  const [copiedId, setCopiedId] = useState<number | null>(null)

  const form = useForm<ApiKeyFormValues>({
    resolver: zodResolver(apiKeySchema),
    defaultValues: {
      name: "",
    },
  })

  useEffect(() => {
    loadApiKeys()
  }, [])

  const loadApiKeys = async () => {
    try {
      setLoading(true)
      const response = await getApiKeys()
      setApiKeys(response.data)
    } catch (error: any) {
      toast.error("Failed to load API keys")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const handleCreate = async (values: ApiKeyFormValues) => {
    try {
      const response = await createApiKey(values)
      setNewToken(response.data.token || null)
      form.reset()
      loadApiKeys()
      toast.success("API key created successfully")
    } catch (error: any) {
      toast.error("Failed to create API key")
      console.error(error)
    }
  }

  const handleRevoke = async (id: number) => {
    if (!confirm("Are you sure you want to revoke this API key?")) return

    try {
      await revokeApiKey(id)
      toast.success("API key revoked successfully")
      loadApiKeys()
    } catch (error: any) {
      toast.error("Failed to revoke API key")
      console.error(error)
    }
  }

  const handleCopyToken = (token: string, id: number) => {
    navigator.clipboard.writeText(token)
    setCopiedId(id)
    toast.success("Token copied to clipboard")
    setTimeout(() => setCopiedId(null), 2000)
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">API Keys</h1>
          <p className="text-muted-foreground">Manage your API access keys</p>
        </div>
        <Button onClick={() => setDialogOpen(true)}>
          <Plus className="h-4 w-4 mr-2" />
          Create API Key
        </Button>
      </div>

      {newToken && (
        <Alert>
          <AlertDescription>
            <div className="space-y-2">
              <p className="font-medium">API Key Created!</p>
              <p className="text-sm text-muted-foreground">
                Please copy this token now. You won't be able to see it again.
              </p>
              <div className="flex items-center gap-2">
                <code className="flex-1 p-2 bg-muted rounded text-sm break-all">
                  {newToken}
                </code>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => handleCopyToken(newToken, 0)}
                >
                  {copiedId === 0 ? (
                    <Check className="h-4 w-4" />
                  ) : (
                    <Copy className="h-4 w-4" />
                  )}
                </Button>
              </div>
            </div>
          </AlertDescription>
        </Alert>
      )}

      {loading ? (
        <div className="text-center py-12 text-muted-foreground">Loading...</div>
      ) : (
        <div className="border rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Scopes</TableHead>
                <TableHead>Created</TableHead>
                <TableHead>Last Used</TableHead>
                <TableHead className="w-12"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {apiKeys.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                    No API keys found
                  </TableCell>
                </TableRow>
              ) : (
                apiKeys.map((key) => (
                  <TableRow key={key.id}>
                    <TableCell className="font-medium">{key.name}</TableCell>
                    <TableCell>
                      {key.scopes && key.scopes.length > 0 ? (
                        <div className="flex gap-1">
                          {key.scopes.map((scope) => (
                            <Badge key={scope} variant="outline">
                              {scope}
                            </Badge>
                          ))}
                        </div>
                      ) : (
                        <Badge variant="outline">All</Badge>
                      )}
                    </TableCell>
                    <TableCell>
                      {format(new Date(key.created_at), "MMM d, yyyy")}
                    </TableCell>
                    <TableCell>
                      {key.last_used_at
                        ? format(new Date(key.last_used_at), "MMM d, yyyy")
                        : "Never"}
                    </TableCell>
                    <TableCell>
                      <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => handleRevoke(key.id)}
                      >
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </div>
      )}

      <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Create API Key</DialogTitle>
            <DialogDescription>Create a new API key for programmatic access</DialogDescription>
          </DialogHeader>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(handleCreate)} className="space-y-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name *</FormLabel>
                    <FormControl>
                      <Input placeholder="My API Key" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <DialogFooter>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => {
                    setDialogOpen(false)
                    setNewToken(null)
                  }}
                >
                  Cancel
                </Button>
                <Button type="submit">Create</Button>
              </DialogFooter>
            </form>
          </Form>
        </DialogContent>
      </Dialog>
    </div>
  )
}
