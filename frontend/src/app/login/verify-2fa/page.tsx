"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import * as z from "zod"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { useAuth } from "@/context/auth-context"
import { toast } from "sonner"

const verifySchema = z.object({
  code: z.string().length(6, "Code must be 6 digits"),
})

type VerifyValues = z.infer<typeof verifySchema>

export default function Verify2FAPage() {
  const { verifyTwoFactor } = useAuth()
  const router = useRouter()
  const [tempToken, setTempToken] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  const form = useForm<VerifyValues>({
    resolver: zodResolver(verifySchema),
    defaultValues: {
      code: "",
    },
  })

  useEffect(() => {
    if (typeof window === "undefined") return

    const token = window.localStorage.getItem("temp_token")
    if (!token) {
      toast.error("No temporary token found. Please login again.")
      router.push("/login")
      return
    }
    setTempToken(token)
  }, [router])

  const onSubmit = async (values: VerifyValues) => {
    if (!tempToken) {
      toast.error("No temporary token found")
      return
    }

    try {
      setLoading(true)
      await verifyTwoFactor(tempToken, values.code)
    } catch (error) {
      // Error is handled in auth context
    } finally {
      setLoading(false)
    }
  }

  if (!tempToken) {
    return null
  }

  return (
    <div className="flex min-h-screen items-center justify-center px-4">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle>Two-Factor Authentication</CardTitle>
          <CardDescription>
            Enter the 6-digit code from your authenticator app
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
              <FormField
                control={form.control}
                name="code"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Verification Code</FormLabel>
                    <FormControl>
                      <Input
                        placeholder="000000"
                        maxLength={6}
                        autoFocus
                        {...field}
                        onChange={(e) => {
                          const value = e.target.value.replace(/\D/g, "")
                          field.onChange(value)
                        }}
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <Button type="submit" className="w-full" disabled={loading}>
                {loading ? "Verifying..." : "Verify"}
              </Button>
            </form>
          </Form>
        </CardContent>
      </Card>
    </div>
  )
}
