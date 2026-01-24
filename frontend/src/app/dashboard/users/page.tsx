"use client"

import { useState, useEffect } from "react"
import {
  getUsers,
  createUser,
  updateUser,
  deleteUser,
  type User,
  type StoreUserRequest,
  type UpdateUserRequest,
} from "@/lib/users"
import { getRoles, type Role } from "@/lib/roles"
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

const userFormSchema = z
  .object({
    name: z.string().min(1, "Name is required"),
    email: z.string().email("Invalid email address"),
    password: z.string().min(8, "Password must be at least 8 characters").optional(),
    password_confirmation: z.string().optional(),
    roles: z.array(z.number()).optional(),
  })
  .refine((data) => !data.password || data.password === data.password_confirmation, {
    path: ["password_confirmation"],
    message: "Passwords must match",
  })

type UserFormValues = z.infer<typeof userFormSchema>

export default function UsersPage() {
  const [users, setUsers] = useState<User[]>([])
  const [roles, setRoles] = useState<Role[]>([])
  const [loading, setLoading] = useState(true)
  const [dialogOpen, setDialogOpen] = useState(false)
  const [editingUser, setEditingUser] = useState<User | null>(null)

  const form = useForm<UserFormValues>({
    resolver: zodResolver(userFormSchema),
    defaultValues: {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      roles: [],
    },
  })

  useEffect(() => {
    loadData()
  }, [])

  useEffect(() => {
    if (editingUser) {
      form.reset({
        name: editingUser.name,
        email: editingUser.email,
        password: "",
        password_confirmation: "",
        roles: editingUser.roles.map((r) => r.id),
      })
    } else {
      form.reset({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        roles: [],
      })
    }
  }, [editingUser, form])

  const loadData = async () => {
    try {
      setLoading(true)
      const [usersResponse, rolesResponse] = await Promise.all([
        getUsers(),
        getRoles(),
      ])
      setUsers(usersResponse.data)
      setRoles(rolesResponse.data)
    } catch (error: any) {
      toast.error("Failed to load users")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  const onSubmit = async (values: UserFormValues) => {
    try {
      const { password_confirmation, ...submitData } = values
      const userData: StoreUserRequest | UpdateUserRequest = {
        name: values.name,
        email: values.email,
        ...(values.password && { password: values.password }),
        roles: values.roles,
      }

      if (editingUser) {
        await updateUser(editingUser.id, userData as UpdateUserRequest)
        toast.success("User updated successfully")
      } else {
        if (!values.password) {
          toast.error("Password is required for new users")
          return
        }
        await createUser(userData as StoreUserRequest)
        toast.success("User created successfully")
      }

      setDialogOpen(false)
      setEditingUser(null)
      form.reset()
      loadData()
    } catch (error: any) {
      toast.error(error.response?.data?.message || "Failed to save user")
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this user?")) return

    try {
      await deleteUser(id)
      toast.success("User deleted successfully")
      loadData()
    } catch (error: any) {
      toast.error("Failed to delete user")
    }
  }

  const handleEdit = (user: User) => {
    setEditingUser(user)
    setDialogOpen(true)
  }

  const handleCreate = () => {
    setEditingUser(null)
    setDialogOpen(true)
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Users</h1>
          <p className="text-muted-foreground">Loading users...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Users</h1>
          <p className="text-muted-foreground">Manage user accounts and permissions</p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={handleCreate}>
              <Plus className="mr-2 h-4 w-4" />
              Add User
            </Button>
          </DialogTrigger>
          <DialogContent className="max-w-2xl">
            <DialogHeader>
              <DialogTitle>{editingUser ? "Edit User" : "Create User"}</DialogTitle>
              <DialogDescription>
                {editingUser
                  ? "Update user information and roles"
                  : "Create a new user account"}
              </DialogDescription>
            </DialogHeader>
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Name</FormLabel>
                      <FormControl>
                        <Input {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="email"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Email</FormLabel>
                      <FormControl>
                        <Input type="email" {...field} />
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
                      <FormLabel>
                        Password {editingUser && "(leave blank to keep current)"}
                      </FormLabel>
                      <FormControl>
                        <Input type="password" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                {form.watch("password") && (
                  <FormField
                    control={form.control}
                    name="password_confirmation"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Confirm Password</FormLabel>
                        <FormControl>
                          <Input type="password" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                )}
                <FormField
                  control={form.control}
                  name="roles"
                  render={() => (
                    <FormItem>
                      <FormLabel>Roles</FormLabel>
                      <div className="space-y-2">
                        {roles.map((role) => (
                          <FormField
                            key={role.id}
                            control={form.control}
                            name="roles"
                            render={({ field }) => (
                              <FormItem className="flex flex-row items-start space-x-3 space-y-0">
                                <FormControl>
                                  <Checkbox
                                    checked={field.value?.includes(role.id)}
                                    onCheckedChange={(checked) => {
                                      const currentRoles = field.value || []
                                      return field.onChange(
                                        checked
                                          ? [...currentRoles, role.id]
                                          : currentRoles.filter((id) => id !== role.id)
                                      )
                                    }}
                                  />
                                </FormControl>
                                <FormLabel className="font-normal">{role.name}</FormLabel>
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
                      setEditingUser(null)
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
          <CardTitle>All Users</CardTitle>
          <CardDescription>A list of all users in the system</CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Roles</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {users.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={4} className="text-center text-muted-foreground">
                    No users found
                  </TableCell>
                </TableRow>
              ) : (
                users.map((user) => (
                  <TableRow key={user.id}>
                    <TableCell className="font-medium">{user.name}</TableCell>
                    <TableCell>{user.email}</TableCell>
                    <TableCell>
                      {user.roles.map((role) => role.name).join(", ") || "No roles"}
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleEdit(user)}
                        >
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => handleDelete(user.id)}
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
