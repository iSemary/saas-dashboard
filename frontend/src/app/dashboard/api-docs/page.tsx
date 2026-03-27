"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Copy, Check } from "lucide-react"
import { toast } from "sonner"
import { useAuth } from "@/context/auth-context"

const apiEndpoints = [
  {
    method: "POST",
    path: "/api/auth/login",
    description: "Authenticate user and get access token",
    parameters: [
      { name: "email", type: "string", required: true, description: "User email or username" },
      { name: "password", type: "string", required: true, description: "User password" },
    ],
    example: {
      request: {
        email: "user@example.com",
        password: "password123",
      },
      response: {
        token: "eyJ0eXAiOiJKV1QiLCJhbGc...",
        user: {
          id: 1,
          name: "John Doe",
          email: "user@example.com",
        },
      },
    },
  },
  {
    method: "GET",
    path: "/api/auth/me",
    description: "Get current authenticated user",
    auth: true,
    example: {
      response: {
        id: 1,
        name: "John Doe",
        email: "user@example.com",
      },
    },
  },
  {
    method: "GET",
    path: "/api/dashboard/stats",
    description: "Get dashboard statistics",
    auth: true,
    example: {
      response: {
        data: {
          overview: {
            total_customers: 150,
            total_subscriptions: 45,
            active_subscriptions: 38,
            total_activity_logs: 1234,
          },
        },
      },
    },
  },
  {
    method: "GET",
    path: "/api/crm/companies",
    description: "List companies/customers",
    auth: true,
    parameters: [
      { name: "type", type: "string", required: false, description: "Filter by type (customer, prospect, etc.)" },
      { name: "search", type: "string", required: false, description: "Search query" },
      { name: "page", type: "number", required: false, description: "Page number" },
    ],
    example: {
      response: {
        data: {
          data: [
            {
              id: 1,
              name: "Acme Corp",
              email: "contact@acme.com",
              type: "customer",
            },
          ],
          current_page: 1,
          last_page: 5,
          total: 50,
        },
      },
    },
  },
  {
    method: "POST",
    path: "/api/crm/companies",
    description: "Create a new company",
    auth: true,
    parameters: [
      { name: "name", type: "string", required: true, description: "Company name" },
      { name: "email", type: "string", required: false, description: "Company email" },
      { name: "type", type: "string", required: false, description: "Company type" },
    ],
    example: {
      request: {
        name: "New Company",
        email: "info@newcompany.com",
        type: "customer",
      },
      response: {
        data: {
          id: 1,
          name: "New Company",
          email: "info@newcompany.com",
        },
      },
    },
  },
  {
    method: "GET",
    path: "/api/v1/tickets",
    description: "List tickets",
    auth: true,
    example: {
      response: {
        data: {
          data: [
            {
              id: 1,
              ticket_number: "TKT-2025010001",
              title: "Support Request",
              status: "open",
              priority: "high",
            },
          ],
        },
      },
    },
  },
]

export default function ApiDocsPage() {
  const { user } = useAuth()
  const [selectedEndpoint, setSelectedEndpoint] = useState(apiEndpoints[0])
  const [copied, setCopied] = useState<string | null>(null)
  const [testResponse, setTestResponse] = useState<any>(null)
  const [testing, setTesting] = useState(false)

  const handleCopy = (text: string, id: string) => {
    navigator.clipboard.writeText(text)
    setCopied(id)
    toast.success("Copied to clipboard")
    setTimeout(() => setCopied(null), 2000)
  }

  const handleTryIt = async () => {
    if (!selectedEndpoint.auth && selectedEndpoint.method !== "POST") {
      toast.error("This endpoint requires authentication or is not a POST request")
      return
    }

    try {
      setTesting(true)
      const token = localStorage.getItem("auth_token")
      const headers: Record<string, string> = {
        "Content-Type": "application/json",
      }
      if (token) {
        headers.Authorization = `Bearer ${token}`
      }

      const response = await fetch(
        `${process.env.NEXT_PUBLIC_API_URL || "http://customer1.saas.test/api"}${selectedEndpoint.path}`,
        {
          method: selectedEndpoint.method,
          headers,
          body: selectedEndpoint.method === "POST" ? JSON.stringify(selectedEndpoint.example.request || {}) : undefined,
        }
      )

      const data = await response.json()
      setTestResponse({ status: response.status, data })
    } catch (error: any) {
      setTestResponse({ error: error.message })
    } finally {
      setTesting(false)
    }
  }

  const getCodeSample = (language: string) => {
    const baseUrl = process.env.NEXT_PUBLIC_API_URL || "http://customer1.saas.test/api"
    const token = "YOUR_ACCESS_TOKEN"

    switch (language) {
      case "curl":
        return `curl -X ${selectedEndpoint.method} \\
  "${baseUrl}${selectedEndpoint.path}" \\
  -H "Content-Type: application/json" \\
  ${selectedEndpoint.auth ? `-H "Authorization: Bearer ${token}" \\` : ""}
  ${selectedEndpoint.method === "POST" ? `-d '${JSON.stringify(selectedEndpoint.example.request || {}, null, 2)}'` : ""}`
      case "javascript":
        return `const response = await fetch('${baseUrl}${selectedEndpoint.path}', {
  method: '${selectedEndpoint.method}',
  headers: {
    'Content-Type': 'application/json',
    ${selectedEndpoint.auth ? `'Authorization': 'Bearer ${token}',` : ""}
  },
  ${selectedEndpoint.method === "POST" ? `body: JSON.stringify(${JSON.stringify(selectedEndpoint.example.request || {}, null, 2)})` : ""}
});

const data = await response.json();`
      case "php":
        return `$response = Http::withHeaders([
    'Authorization' => 'Bearer ${token}',
])->${selectedEndpoint.method.toLowerCase()}('${baseUrl}${selectedEndpoint.path}', [
    ${selectedEndpoint.method === "POST" ? JSON.stringify(selectedEndpoint.example.request || {}, null, 4).split("\n").map((line, i) => (i === 0 ? line : "    " + line)).join("\n") : ""}
]);`
      default:
        return ""
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">API Documentation</h1>
        <p className="text-muted-foreground">Interactive API documentation and code samples</p>
      </div>

      <div className="grid grid-cols-3 gap-6">
        {/* Endpoints List */}
        <div className="col-span-1">
          <Card>
            <CardHeader>
              <CardTitle>Endpoints</CardTitle>
            </CardHeader>
            <CardContent className="p-0">
              <div className="space-y-1">
                {apiEndpoints.map((endpoint, index) => (
                  <button
                    key={index}
                    onClick={() => setSelectedEndpoint(endpoint)}
                    className={`w-full text-left p-3 hover:bg-muted transition-colors ${
                      selectedEndpoint.path === endpoint.path ? "bg-muted border-l-2 border-primary" : ""
                    }`}
                  >
                    <div className="flex items-center gap-2">
                      <Badge
                        variant={
                          endpoint.method === "GET"
                            ? "default"
                            : endpoint.method === "POST"
                            ? "secondary"
                            : "outline"
                        }
                        className="text-xs"
                      >
                        {endpoint.method}
                      </Badge>
                      <span className="text-sm truncate">{endpoint.path}</span>
                    </div>
                  </button>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Endpoint Details */}
        <div className="col-span-2">
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle className="flex items-center gap-2">
                    <Badge
                      variant={
                        selectedEndpoint.method === "GET"
                          ? "default"
                          : selectedEndpoint.method === "POST"
                          ? "secondary"
                          : "outline"
                      }
                    >
                      {selectedEndpoint.method}
                    </Badge>
                    {selectedEndpoint.path}
                  </CardTitle>
                  <CardDescription className="mt-2">
                    {selectedEndpoint.description}
                  </CardDescription>
                </div>
                {selectedEndpoint.auth && (
                  <Badge variant="outline">Requires Authentication</Badge>
                )}
              </div>
            </CardHeader>
            <CardContent>
              <Tabs defaultValue="details" className="w-full">
                <TabsList>
                  <TabsTrigger value="details">Details</TabsTrigger>
                  <TabsTrigger value="try">Try it out</TabsTrigger>
                  <TabsTrigger value="code">Code Samples</TabsTrigger>
                </TabsList>

                <TabsContent value="details" className="space-y-4">
                  {selectedEndpoint.parameters && selectedEndpoint.parameters.length > 0 && (
                    <div>
                      <h3 className="font-semibold mb-2">Parameters</h3>
                      <div className="space-y-2">
                        {selectedEndpoint.parameters.map((param, index) => (
                          <div key={index} className="border rounded-lg p-3">
                            <div className="flex items-center gap-2">
                              <code className="font-mono text-sm">{param.name}</code>
                              <Badge variant="outline" className="text-xs">
                                {param.type}
                              </Badge>
                              {param.required && (
                                <Badge variant="destructive" className="text-xs">
                                  Required
                                </Badge>
                              )}
                            </div>
                            <p className="text-sm text-muted-foreground mt-1">
                              {param.description}
                            </p>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  <div>
                    <h3 className="font-semibold mb-2">Example Response</h3>
                    <div className="relative">
                      <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
                        <code>{JSON.stringify(selectedEndpoint.example.response, null, 2)}</code>
                      </pre>
                      <Button
                        variant="ghost"
                        size="icon"
                        className="absolute top-2 right-2"
                        onClick={() =>
                          handleCopy(
                            JSON.stringify(selectedEndpoint.example.response, null, 2),
                            "response"
                          )
                        }
                      >
                        {copied === "response" ? (
                          <Check className="h-4 w-4" />
                        ) : (
                          <Copy className="h-4 w-4" />
                        )}
                      </Button>
                    </div>
                  </div>
                </TabsContent>

                <TabsContent value="try" className="space-y-4">
                  <div>
                    <h3 className="font-semibold mb-2">Test Endpoint</h3>
                    <Button onClick={handleTryIt} disabled={testing}>
                      {testing ? "Sending..." : "Send Request"}
                    </Button>
                  </div>

                  {testResponse && (
                    <div>
                      <h3 className="font-semibold mb-2">Response</h3>
                      <div className="relative">
                        <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
                          <code>
                            {testResponse.error
                              ? testResponse.error
                              : JSON.stringify(testResponse, null, 2)}
                          </code>
                        </pre>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="absolute top-2 right-2"
                          onClick={() =>
                            handleCopy(
                              JSON.stringify(testResponse, null, 2),
                              "test-response"
                            )
                          }
                        >
                          {copied === "test-response" ? (
                            <Check className="h-4 w-4" />
                          ) : (
                            <Copy className="h-4 w-4" />
                          )}
                        </Button>
                      </div>
                    </div>
                  )}
                </TabsContent>

                <TabsContent value="code" className="space-y-4">
                  <Tabs defaultValue="curl" className="w-full">
                    <TabsList>
                      <TabsTrigger value="curl">cURL</TabsTrigger>
                      <TabsTrigger value="javascript">JavaScript</TabsTrigger>
                      <TabsTrigger value="php">PHP</TabsTrigger>
                    </TabsList>

                    <TabsContent value="curl">
                      <div className="relative">
                        <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
                          <code>{getCodeSample("curl")}</code>
                        </pre>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="absolute top-2 right-2"
                          onClick={() => handleCopy(getCodeSample("curl"), "curl")}
                        >
                          {copied === "curl" ? (
                            <Check className="h-4 w-4" />
                          ) : (
                            <Copy className="h-4 w-4" />
                          )}
                        </Button>
                      </div>
                    </TabsContent>

                    <TabsContent value="javascript">
                      <div className="relative">
                        <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
                          <code>{getCodeSample("javascript")}</code>
                        </pre>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="absolute top-2 right-2"
                          onClick={() => handleCopy(getCodeSample("javascript"), "js")}
                        >
                          {copied === "js" ? (
                            <Check className="h-4 w-4" />
                          ) : (
                            <Copy className="h-4 w-4" />
                          )}
                        </Button>
                      </div>
                    </TabsContent>

                    <TabsContent value="php">
                      <div className="relative">
                        <pre className="bg-muted p-4 rounded-lg overflow-x-auto text-sm">
                          <code>{getCodeSample("php")}</code>
                        </pre>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="absolute top-2 right-2"
                          onClick={() => handleCopy(getCodeSample("php"), "php")}
                        >
                          {copied === "php" ? (
                            <Check className="h-4 w-4" />
                          ) : (
                            <Copy className="h-4 w-4" />
                          )}
                        </Button>
                      </div>
                    </TabsContent>
                  </Tabs>
                </TabsContent>
              </Tabs>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
