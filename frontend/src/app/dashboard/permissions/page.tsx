"use client"

import { useState, useEffect } from "react"
import {
  getPermissions,
  type Permission,
} from "@/lib/permissions"
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
import { toast } from "sonner"

export default function PermissionsPage() {
  const [permissions, setPermissions] = useState<Permission[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    loadData()
  }, [])

  const loadData = async () => {
    try {
      setLoading(true)
      const response = await getPermissions()
      setPermissions(response.data)
    } catch (error: any) {
      toast.error("Failed to load permissions")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Permissions</h1>
          <p className="text-muted-foreground">Loading permissions...</p>
        </div>
      </div>
    )
  }

  // Group permissions by module/prefix
  const groupedPermissions = permissions.reduce((acc, permission) => {
    const parts = permission.name.split(".")
    const module = parts.length > 1 ? parts[0] : "other"
    if (!acc[module]) {
      acc[module] = []
    }
    acc[module].push(permission)
    return acc
  }, {} as Record<string, Permission[]>)

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Permissions</h1>
        <p className="text-muted-foreground">View all available permissions in the system</p>
      </div>

      {Object.entries(groupedPermissions).map(([module, perms]) => (
        <Card key={module}>
          <CardHeader>
            <CardTitle className="capitalize">{module}</CardTitle>
            <CardDescription>{perms.length} permission(s)</CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Permission Name</TableHead>
                  <TableHead>Created</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {perms.map((permission) => (
                  <TableRow key={permission.id}>
                    <TableCell className="font-mono text-sm">{permission.name}</TableCell>
                    <TableCell>
                      {new Date(permission.created_at).toLocaleDateString()}
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      ))}
    </div>
  )
}
