"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Checkbox } from "@/components/ui/checkbox"
import { Save, Download } from "lucide-react"
import { toast } from "sonner"

const availableFields = {
  customers: [
    { id: "name", label: "Name", type: "string" },
    { id: "email", label: "Email", type: "string" },
    { id: "phone", label: "Phone", type: "string" },
    { id: "created_at", label: "Created Date", type: "date" },
  ],
  tickets: [
    { id: "ticket_number", label: "Ticket Number", type: "string" },
    { id: "title", label: "Title", type: "string" },
    { id: "status", label: "Status", type: "string" },
    { id: "priority", label: "Priority", type: "string" },
    { id: "created_at", label: "Created Date", type: "date" },
  ],
}

export default function ReportBuilderPage() {
  const [reportType, setReportType] = useState("customers")
  const [selectedFields, setSelectedFields] = useState<string[]>([])
  const [reportName, setReportName] = useState("")

  const fields = availableFields[reportType as keyof typeof availableFields] || []

  const handleFieldToggle = (fieldId: string) => {
    setSelectedFields((prev) =>
      prev.includes(fieldId) ? prev.filter((id) => id !== fieldId) : [...prev, fieldId]
    )
  }

  const handleSave = () => {
    if (!reportName) {
      toast.error("Please enter a report name")
      return
    }
    if (selectedFields.length === 0) {
      toast.error("Please select at least one field")
      return
    }
    toast.success("Report saved successfully")
  }

  const handleGenerate = () => {
    if (selectedFields.length === 0) {
      toast.error("Please select at least one field")
      return
    }
    toast.success("Report generated successfully")
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Custom Report Builder</h1>
        <p className="text-muted-foreground">Build custom reports with selected fields</p>
      </div>

      <div className="grid grid-cols-3 gap-6">
        <Card className="col-span-1">
          <CardHeader>
            <CardTitle>Report Settings</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <Label>Report Name</Label>
              <Input
                value={reportName}
                onChange={(e) => setReportName(e.target.value)}
                placeholder="My Custom Report"
              />
            </div>
            <div>
              <Label>Data Type</Label>
              <select
                value={reportType}
                onChange={(e) => {
                  setReportType(e.target.value)
                  setSelectedFields([])
                }}
                className="w-full p-2 border rounded-lg"
              >
                <option value="customers">Customers</option>
                <option value="tickets">Tickets</option>
              </select>
            </div>
          </CardContent>
        </Card>

        <Card className="col-span-2">
          <CardHeader>
            <CardTitle>Select Fields</CardTitle>
            <CardDescription>Choose which fields to include in your report</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-2">
              {fields.map((field) => (
                <div key={field.id} className="flex items-center space-x-2 p-2 hover:bg-muted rounded-lg">
                  <Checkbox
                    id={field.id}
                    checked={selectedFields.includes(field.id)}
                    onCheckedChange={() => handleFieldToggle(field.id)}
                  />
                  <label
                    htmlFor={field.id}
                    className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer flex-1"
                  >
                    {field.label}
                  </label>
                  <span className="text-xs text-muted-foreground">{field.type}</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="flex justify-end gap-2">
        <Button variant="outline" onClick={handleSave}>
          <Save className="h-4 w-4 mr-2" />
          Save Report
        </Button>
        <Button onClick={handleGenerate}>
          <Download className="h-4 w-4 mr-2" />
          Generate Report
        </Button>
      </div>
    </div>
  )
}
