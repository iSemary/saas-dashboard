"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { CustomerTable } from "@/components/customers/CustomerTable"
import { CustomerDialog } from "@/components/customers/CustomerDialog"
import {
  getCompanies,
  createCompany,
  updateCompany,
  deleteCompany,
  bulkDeleteCompanies,
  type Company,
  type CompanyFilters,
} from "@/lib/customers"
import { Plus, Trash2, Search, Download } from "lucide-react"
import { toast } from "sonner"
import { CustomerProfile } from "@/components/customers/CustomerProfile"

export default function CustomersPage() {
  const [customers, setCustomers] = useState<Company[]>([])
  const [loading, setLoading] = useState(true)
  const [selectedIds, setSelectedIds] = useState<number[]>([])
  const [dialogOpen, setDialogOpen] = useState(false)
  const [editingCustomer, setEditingCustomer] = useState<Company | null>(null)
  const [viewingCustomer, setViewingCustomer] = useState<Company | null>(null)
  const [filters, setFilters] = useState<CompanyFilters>({
    type: "customer",
    per_page: 15,
  })
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    total: 0,
  })

  const loadCustomers = async () => {
    try {
      setLoading(true)
      const response = await getCompanies({ ...filters, page: pagination.current_page })
      setCustomers(response.data.data)
      setPagination({
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
      })
    } catch (error: any) {
      toast.error("Failed to load customers")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadCustomers()
  }, [filters, pagination.current_page])

  const handleCreate = () => {
    setEditingCustomer(null)
    setDialogOpen(true)
  }

  const handleEdit = (customer: Company) => {
    setEditingCustomer(customer)
    setDialogOpen(true)
  }

  const handleView = (customer: Company) => {
    setViewingCustomer(customer)
  }

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this customer?")) return

    try {
      await deleteCompany(id)
      toast.success("Customer deleted successfully")
      loadCustomers()
      setSelectedIds(selectedIds.filter((selectedId) => selectedId !== id))
    } catch (error: any) {
      toast.error("Failed to delete customer")
      console.error(error)
    }
  }

  const handleBulkDelete = async () => {
    if (selectedIds.length === 0) return
    if (!confirm(`Are you sure you want to delete ${selectedIds.length} customers?`)) return

    try {
      await bulkDeleteCompanies(selectedIds)
      toast.success(`${selectedIds.length} customers deleted successfully`)
      setSelectedIds([])
      loadCustomers()
    } catch (error: any) {
      toast.error("Failed to delete customers")
      console.error(error)
    }
  }

  const handleSubmit = async (data: any) => {
    try {
      if (editingCustomer) {
        await updateCompany(editingCustomer.id, data)
        toast.success("Customer updated successfully")
      } else {
        await createCompany(data)
        toast.success("Customer created successfully")
      }
      setDialogOpen(false)
      setEditingCustomer(null)
      loadCustomers()
    } catch (error: any) {
      toast.error(editingCustomer ? "Failed to update customer" : "Failed to create customer")
      console.error(error)
    }
  }

  const handleSelect = (id: number) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((selectedId) => selectedId !== id) : [...prev, id]
    )
  }

  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedIds(customers.map((c) => c.id))
    } else {
      setSelectedIds([])
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Customers</h1>
          <p className="text-muted-foreground">Manage your customer accounts</p>
        </div>
        <div className="flex items-center gap-2">
          {selectedIds.length > 0 && (
            <Button variant="destructive" onClick={handleBulkDelete}>
              <Trash2 className="h-4 w-4 mr-2" />
              Delete ({selectedIds.length})
            </Button>
          )}
          <Button onClick={handleCreate}>
            <Plus className="h-4 w-4 mr-2" />
            Add Customer
          </Button>
        </div>
      </div>

      {/* Filters */}
      <div className="grid gap-4 md:grid-cols-4">
        <div className="relative">
          <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Search customers..."
            value={filters.search || ""}
            onChange={(e) =>
              setFilters({ ...filters, search: e.target.value, page: 1 })
            }
            className="pl-8"
          />
        </div>
        <Select
          value={filters.type || "customer"}
          onValueChange={(value) => setFilters({ ...filters, type: value, page: 1 })}
        >
          <SelectTrigger>
            <SelectValue placeholder="Filter by type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="customer">Customer</SelectItem>
            <SelectItem value="prospect">Prospect</SelectItem>
            <SelectItem value="partner">Partner</SelectItem>
            <SelectItem value="vendor">Vendor</SelectItem>
            <SelectItem value="competitor">Competitor</SelectItem>
          </SelectContent>
        </Select>
        <Select
          value={filters.industry || "all"}
          onValueChange={(value) =>
            setFilters({
              ...filters,
              industry: value === "all" ? undefined : value,
              page: 1,
            })
          }
        >
          <SelectTrigger>
            <SelectValue placeholder="Filter by industry" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All Industries</SelectItem>
            <SelectItem value="Technology">Technology</SelectItem>
            <SelectItem value="Healthcare">Healthcare</SelectItem>
            <SelectItem value="Finance">Finance</SelectItem>
            <SelectItem value="Retail">Retail</SelectItem>
            <SelectItem value="Manufacturing">Manufacturing</SelectItem>
          </SelectContent>
        </Select>
        <Button variant="outline" onClick={() => loadCustomers()}>
          <Download className="h-4 w-4 mr-2" />
          Export
        </Button>
      </div>

      {/* Table */}
      {loading ? (
        <div className="text-center py-12 text-muted-foreground">Loading...</div>
      ) : (
        <>
          <CustomerTable
            customers={customers}
            selectedIds={selectedIds}
            onSelect={handleSelect}
            onSelectAll={handleSelectAll}
            onEdit={handleEdit}
            onDelete={handleDelete}
            onView={handleView}
          />

          {/* Pagination */}
          {pagination.last_page > 1 && (
            <div className="flex items-center justify-between">
              <div className="text-sm text-muted-foreground">
                Showing {customers.length} of {pagination.total} customers
              </div>
              <div className="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() =>
                    setPagination({ ...pagination, current_page: pagination.current_page - 1 })
                  }
                  disabled={pagination.current_page === 1}
                >
                  Previous
                </Button>
                <span className="text-sm">
                  Page {pagination.current_page} of {pagination.last_page}
                </span>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() =>
                    setPagination({ ...pagination, current_page: pagination.current_page + 1 })
                  }
                  disabled={pagination.current_page === pagination.last_page}
                >
                  Next
                </Button>
              </div>
            </div>
          )}
        </>
      )}

      {/* Dialogs */}
      <CustomerDialog
        open={dialogOpen}
        onOpenChange={setDialogOpen}
        customer={editingCustomer}
        onSubmit={handleSubmit}
      />

      {viewingCustomer && (
        <CustomerProfile
          customer={viewingCustomer}
          open={!!viewingCustomer}
          onOpenChange={(open) => !open && setViewingCustomer(null)}
        />
      )}
    </div>
  )
}
