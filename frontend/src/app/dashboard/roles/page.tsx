"use client"

import { useState, useEffect } from "react"
import {
  getRoles,
  createRole,
  updateRole,
  deleteRole,
  type Role,
  type StoreRoleRequest,
} from "@/lib/roles"
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

const roleFormSchema = z.object({
  name: z.string().min(1, "Name is required"),
  permissions: z.array(z.number()).optional(),
})

type RoleFormValues = z.infer<typeof roleFormSchema>

export default function RolesPage() {
  const [roles, setRoles] = useState<Role[]>([])
  const [permissions, setPermissions] = useState<Permission[]>([])
  const [loading, setLoading] = useState(true)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [editingRole, setEditingRole] = useState<Role | null>(null)

  const form = useForm<RoleFormValues>({
    resolver: zodResolver(roleFormSchema),
    defaultValues: {
      name: "",
      permissions: [],
    },
  })

  useEffect(() => {
    loadData()
  }, [])

  useEffect(() => {
    if (editingRole) {
      form.reset({
        name: editingRole.name,
        permissions: editingRole.permissions.map((p) => p.id),
      })
    } else {
      form.reset({
        name: "",
        permissions: [],
      })
    }
  }, [editingRole, form])

  const loadData = async () => {
    try {
      setLoading(true)
      const [rolesResponse, permissionsResponse] = await Promise.all([
        getRoles(),
        getPermissions(),
      ])
      setRoles(rolesResponse.data)
      setPermissions(permissionsResponse.data)
    } catch (error: any) {
      toast.error("Failed to load roles")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const onSubmit = async (values: RoleFormValues) => {
    try {
      const roleData: StoreRoleRequest = {
        name: values.name,
        permissions: values.permissions,
      }

      if (editingRole) {
        await updateRole(editingRole.id, roleData)
        toast.success("Role updated successfully")
      } else {
        await createRole(roleData)
        toast.success("Role created successfully")
      }

      setDialogOpen(false)
      setEditingRole(null)
      form.reset()
      loadData()
    } catch (error: any) {
      toast.error(error.response?.data?.message || "Failed to save role")
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this role?")) return

    try {
      await deleteRole(id)
      toast.success("Role deleted successfully")
      loadData()
    } catch (error: any) {
      toast.error("Failed to delete role")
    }
  }

  const handleEdit = (role: Role) => {
    setEditingRole(role)
    setDialogOpen(true)
  }

  const handleCreate = () => {
    setEditingRole(null)
    setDialogOpen(true)
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Roles</h1>
          <p className="text-muted-foreground">Loading roles...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Roles</h1>
          <p className="text-muted-foreground">Manage roles and their permissions</p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={handleCreate}>
              <Plus className="mr-2 h-4 w-4" />
              Add Role
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-2xl max-h-[80vh] overflow-y-auto">
            <DialogHeader>
              <DialogTitle>{editingRole ? "Edit Role" : "Create Role"}</DialogTitle>
              <DialogDescription>
                {editingRole
                  ? "Update role information and permissions"
                  : "Create a new role with permissions"}
              </DialogDescription>
            </DialogHeader>
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Role Name</FormLabel>
                      <FormControl>
                        <Input {...field} />
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
                      setEditingRole(null)
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
          <CardTitle>All Roles</CardTitle>
          <CardDescription>A list of all roles in the system</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Permissions</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {roles.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={3} className="text-center text-muted-foreground">
                    No roles found
                  </TableCell>
                </TableRow>
              ) : (
                roles.map((role) => (
                  <TableRow key={role.id}>
                    <TableCell className="font-medium">{role.name}</TableCell>
                    <TableCell>
                      {role.permissions.map((p) => p.name).join(", ") || "No permissions"}
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleEdit(role)}
                        >
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleDelete(role.id)}
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
