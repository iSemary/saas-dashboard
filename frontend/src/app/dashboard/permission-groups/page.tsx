"use client"

import { useState, useEffect } from "react"
import {
  getPermissionGroups,
  createPermissionGroup,
  updatePermissionGroup,
  deletePermissionGroup,
  type PermissionGroup,
  type StorePermissionGroupRequest,
} from "@/lib/permission-groups"
import { getPermissions, type Permission } from "@/lib/permissions"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { Checkbox } from "@/components/ui/checkbox"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import { toast } from "sonner"
import { Plus, Edit, Trash2 } from "lucide-react"

const permissionGroupFormSchema = z.object({
  name: z.string().min(1, "Name is required"),
  description: z.string().optional(),
  permissions: z.array(z.number()).optional(),
})

type PermissionGroupFormValues = z.infer<typeof permissionGroupFormSchema>

export default function PermissionGroupsPage() {
  const [permissionGroups, setPermissionGroups] = useState<PermissionGroup[]>([])
  const [permissions, setPermissions] = useState<Permission[]>([])
  const [loading, setLoading] = useState(true)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [editingGroup, setEditingGroup] = useState<PermissionGroup | null>(null)

  const form = useForm<PermissionGroupFormValues>({
    resolver: zodResolver(permissionGroupFormSchema),
    defaultValues: {
      name: "",
      description: "",
      permissions: [],
    },
  })

  useEffect(() => {
    loadData()
  }, [])

  useEffect(() => {
    if (editingGroup) {
      form.reset({
        name: editingGroup.name,
        description: editingGroup.description || "",
        permissions: editingGroup.permissions?.map((p) => p.id) || [],
      })
    } else {
      form.reset({
        name: "",
        description: "",
        permissions: [],
      })
    }
  }, [editingGroup, form])

  const loadData = async () => {
    try {
      setLoading(true)
      const [groupsResponse, permissionsResponse] = await Promise.all([
        getPermissionGroups(),
        getPermissions(),
      ])
      setPermissionGroups(groupsResponse.data)
      setPermissions(permissionsResponse.data)
    } catch (error: any) {
      toast.error("Failed to load permission groups")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const onSubmit = async (values: PermissionGroupFormValues) => {
    try {
      const groupData: StorePermissionGroupRequest = {
        name: values.name,
        description: values.description,
        permissions: values.permissions,
      }

      if (editingGroup) {
        await updatePermissionGroup(editingGroup.id, groupData)
        toast.success("Permission group updated successfully")
      } else {
        await createPermissionGroup(groupData)
        toast.success("Permission group created successfully")
      }

      setDialogOpen(false)
      setEditingGroup(null)
      form.reset()
      loadData()
    } catch (error: any) {
      toast.error(error.response?.data?.message || "Failed to save permission group")
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this permission group?")) return

    try {
      await deletePermissionGroup(id)
      toast.success("Permission group deleted successfully")
      loadData()
    } catch (error: any) {
      toast.error("Failed to delete permission group")
    }
  }

  const handleEdit = (group: PermissionGroup) => {
    setEditingGroup(group)
    setDialogOpen(true)
  }

  const handleCreate = () => {
    setEditingGroup(null)
    setDialogOpen(true)
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Permission Groups</h1>
          <p className="text-muted-foreground">Loading permission groups...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Permission Groups</h1>
          <p className="text-muted-foreground">Manage permission groups and their permissions</p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={handleCreate}>
              <Plus className="mr-2 h-4 w-4" />
              Add Permission Group
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-2xl max-h-[80vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>{editingGroup ? "Edit Permission Group" : "Create Permission Group"}</DialogTitle>
              <DialogDescription>
                {editingGroup
                  ? "Update permission group information and permissions"
                  : "Create a new permission group with permissions"}
              </DialogDescription>
            </DialogHeader>
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Group Name</FormLabel>
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
                        <textarea
                          {...field}
                          rows={3}
                          className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="permissions"
                  render={() => (
                    <FormItem>
                      <FormLabel>Permissions</FormLabel>
                      <div className="space-y-2 max-h-60 overflow-y-auto border rounded-md p-4">
                        {permissions.map((permission) => (
                          <FormField
                            key={permission.id}
                            control={form.control}
                            name="permissions"
                            render={({ field }) => (
                              <FormItem className="flex flex-row items-start space-x-3 space-y-0">
                                <FormControl>
                                  <Checkbox
                                    checked={field.value?.includes(permission.id)}
                                    onCheckedChange={(checked) => {
                                      const currentPermissions = field.value || []
                                      return field.onChange(
                                        checked
                                          ? [...currentPermissions, permission.id]
                                          : currentPermissions.filter((id) => id !== permission.id)
                                      )
                                    }}
                                  />
                                </FormControl>
                                <FormLabel className="font-normal">{permission.name}</FormLabel>
                              </FormItem>
                            )}
                          />
                        ))}
                      </div>
                    </FormItem>
                  )}
                />
                <DialogFooter>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => {
                      setDialogOpen(false)
                      setEditingGroup(null)
                      form.reset()
                    }}
                  >
                    Cancel
                  </Button>
                  <Button type="submit">Save</Button>
                </DialogFooter>
              </form>
            </Form>
          </DialogContent>
        </Dialog>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>All Permission Groups</CardTitle>
          <CardDescription>A list of all permission groups in the system</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Description</TableHead>
                <TableHead>Permissions</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {permissionGroups.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={4} className="text-center text-muted-foreground">
                    No permission groups found
                  </TableCell>
                </TableRow>
              ) : (
                permissionGroups.map((group) => (
                  <TableRow key={group.id}>
                    <TableCell className="font-medium">{group.name}</TableCell>
                    <TableCell>{group.description || "-"}</TableCell>
                    <TableCell>
                      {group.permissions?.map((p) => p.name).join(", ") || "No permissions"}
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleEdit(group)}
                        >
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleDelete(group.id)}
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
        </CardContent>
      </Card>
    </div>
  )
}
