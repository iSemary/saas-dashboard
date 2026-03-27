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
import {
  getSMTPConfigs,
  createSMTPConfig,
  updateSMTPConfig,
  deleteSMTPConfig,
  testSMTPConnection,
  type SMTPConfig,
} from "@/lib/smtp"
import { Plus, Edit, Trash2, Mail, CheckCircle2 } from "lucide-react"
import { toast } from "sonner"
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

const smtpSchema = z.object({
  name: z.string().min(1, "Name is required"),
  description: z.string().optional(),
  from_address: z.string().email("Invalid email"),
  from_name: z.string().min(1, "From name is required"),
  mailer: z.enum(["smtp", "ses", "mailgun", "postmark"]),
  host: z.string().min(1, "Host is required"),
  port: z.coerce.number().min(1).max(65535),
  username: z.string().optional(),
  password: z.string().optional(),
  encryption: z.enum(["tls", "ssl"]).optional(),
  status: z.enum(["active", "inactive"]),
})

type SMTPFormValues = z.infer<typeof smtpSchema>

export default function SMTPPage() {
  const [configs, setConfigs] = useState<SMTPConfig[]>([])
  const [loading, setLoading] = useState(true)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [testDialogOpen, setTestDialogOpen] = useState(false)
  const [editingConfig, setEditingConfig] = useState<SMTPConfig | null>(null)
  const [selectedConfigId, setSelectedConfigId] = useState<number | null>(null)
  const [testEmail, setTestEmail] = useState("")
  const [testing, setTesting] = useState(false)

  const form = useForm<SMTPFormValues>({
    resolver: zodResolver(smtpSchema),
    defaultValues: {
      name: "",
      description: "",
      from_address: "",
      from_name: "",
      mailer: "smtp",
      host: "",
      port: 587,
      username: "",
      password: "",
      encryption: "tls",
      status: "active",
    },
  })

  useEffect(() => {
    loadConfigs()
  }, [])

  useEffect(() => {
    if (editingConfig) {
      form.reset({
        name: editingConfig.name,
        description: editingConfig.description || "",
        from_address: editingConfig.from_address,
        from_name: editingConfig.from_name,
        mailer: editingConfig.mailer,
        host: editingConfig.host,
        port: editingConfig.port,
        username: editingConfig.username || "",
        password: editingConfig.password || "",
        encryption: editingConfig.encryption || "tls",
        status: editingConfig.status,
      })
    } else {
      form.reset({
        name: "",
        description: "",
        from_address: "",
        from_name: "",
        mailer: "smtp",
        host: "",
        port: 587,
        username: "",
        password: "",
        encryption: "tls",
        status: "active",
      })
    }
  }, [editingConfig, form])

  const loadConfigs = async () => {
    try {
      setLoading(true)
      const response = await getSMTPConfigs()
      setConfigs(response.data)
    } catch (error: any) {
      toast.error("Failed to load SMTP configurations")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (values: SMTPFormValues) => {
    try {
      if (editingConfig) {
        await updateSMTPConfig(editingConfig.id, values)
        toast.success("SMTP configuration updated successfully")
      } else {
        await createSMTPConfig(values)
        toast.success("SMTP configuration created successfully")
      }
      setDialogOpen(false)
      setEditingConfig(null)
      loadConfigs()
    } catch (error: any) {
      toast.error(editingConfig ? "Failed to update configuration" : "Failed to create configuration")
      console.error(error)
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this SMTP configuration?")) return

    try {
      await deleteSMTPConfig(id)
      toast.success("Configuration deleted successfully")
      loadConfigs()
    } catch (error: any) {
      toast.error("Failed to delete configuration")
      console.error(error)
    }
  }

  const handleTest = async () => {
    if (!selectedConfigId || !testEmail) return

    try {
      setTesting(true)
      await testSMTPConnection(selectedConfigId, testEmail)
      toast.success("Test email sent successfully")
      setTestDialogOpen(false)
      setTestEmail("")
    } catch (error: any) {
      toast.error("Failed to send test email")
      console.error(error)
    } finally {
      setTesting(false)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">SMTP Configuration</h1>
          <p className="text-muted-foreground">Manage your email server settings</p>
        </div>
        <Button onClick={() => {
          setEditingConfig(null)
          setDialogOpen(true)
        }}>
          <Plus className="h-4 w-4 mr-2" />
          New Configuration
        </Button>
      </div>

      {loading ? (
        <div className="text-center py-12 text-muted-foreground">Loading...</div>
      ) : (
        <div className="border rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>From Address</TableHead>
                <TableHead>Host</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {configs.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                    No SMTP configurations found
                  </TableCell>
                </TableRow>
              ) : (
                configs.map((config) => (
                  <TableRow key={config.id}>
                    <TableCell className="font-medium">{config.name}</TableCell>
                    <TableCell>{config.from_address}</TableCell>
                    <TableCell>{config.host}:{config.port}</TableCell>
                    <TableCell>
                      <Badge variant={config.status === "active" ? "default" : "secondary"}>
                        {config.status}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => {
                            setSelectedConfigId(config.id)
                            setTestDialogOpen(true)
                          }}
                        >
                          <CheckCircle2 className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => {
                            setEditingConfig(config)
                            setDialogOpen(true)
                          }}
                        >
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => handleDelete(config.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </div>
      )}

      {/* Create/Edit Dialog */}
      <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>{editingConfig ? "Edit SMTP Configuration" : "Create SMTP Configuration"}</DialogTitle>
            <DialogDescription>
              {editingConfig ? "Update SMTP settings" : "Configure a new SMTP server"}
            </DialogDescription>
          </DialogHeader>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(handleSubmit)} className="space-y-4">
              <FormField
                control={form.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name *</FormLabel>
                    <FormControl>
                      <Input {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="description"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Description</FormLabel>
                    <FormControl>
                      <Input {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="from_address"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>From Address *</FormLabel>
                      <FormControl>
                        <Input type="email" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="from_name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>From Name *</FormLabel>
                      <FormControl>
                        <Input {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="mailer"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Mailer *</FormLabel>
                      <Select onValueChange={field.onChange} value={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="smtp">SMTP</SelectItem>
                          <SelectItem value="ses">AWS SES</SelectItem>
                          <SelectItem value="mailgun">Mailgun</SelectItem>
                          <SelectItem value="postmark">Postmark</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="status"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Status</FormLabel>
                      <Select onValueChange={field.onChange} value={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="active">Active</SelectItem>
                          <SelectItem value="inactive">Inactive</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="host"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Host *</FormLabel>
                      <FormControl>
                        <Input {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="port"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Port *</FormLabel>
                      <FormControl>
                        <Input type="number" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <FormField
                  control={form.control}
                  name="username"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Username</FormLabel>
                      <FormControl>
                        <Input {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="password"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Password</FormLabel>
                      <FormControl>
                        <Input type="password" {...field} placeholder={editingConfig ? "Leave blank to keep current" : ""} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>

              <FormField
                control={form.control}
                name="encryption"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Encryption</FormLabel>
                    <Select onValueChange={field.onChange} value={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="tls">TLS</SelectItem>
                        <SelectItem value="ssl">SSL</SelectItem>
                      </SelectContent>
                    </Select>
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
                    setEditingConfig(null)
                  }}
                >
                  Cancel
                </Button>
                <Button type="submit">{editingConfig ? "Update" : "Create"}</Button>
              </DialogFooter>
            </form>
          </Form>
        </DialogContent>
      </Dialog>

      {/* Test Connection Dialog */}
      <Dialog open={testDialogOpen} onOpenChange={setTestDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Test SMTP Connection</DialogTitle>
            <DialogDescription>Send a test email to verify the SMTP configuration</DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div>
              <label className="text-sm font-medium">Test Email Address</label>
              <Input
                type="email"
                value={testEmail}
                onChange={(e) => setTestEmail(e.target.value)}
                placeholder="test@example.com"
              />
            </div>
            <DialogFooter>
              <Button variant="outline" onClick={() => setTestDialogOpen(false)}>
                Cancel
              </Button>
              <Button onClick={handleTest} disabled={!testEmail || testing}>
                {testing ? "Testing..." : "Send Test"}
              </Button>
            </DialogFooter>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  )
}
