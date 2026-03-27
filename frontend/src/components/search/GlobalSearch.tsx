"use client"

import { useState, useEffect, useRef } from "react"
import { Search, X, Loader2 } from "lucide-react"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { globalSearch, type SearchResult } from "@/lib/search"
import { toast } from "sonner"
import Link from "next/link"
import { useRouter } from "next/navigation"

interface GlobalSearchProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function GlobalSearch({ open, onOpenChange }: GlobalSearchProps) {
  const [query, setQuery] = useState("")
  const [results, setResults] = useState<Record<string, SearchResult[]>>({})
  const [loading, setLoading] = useState(false)
  const inputRef = useRef<HTMLInputElement>(null)
  const router = useRouter()

  useEffect(() => {
    if (open && inputRef.current) {
      inputRef.current.focus()
    }
  }, [open])

  useEffect(() => {
    if (query.length < 2) {
      setResults({})
      return
    }

    const timeoutId = setTimeout(() => {
      performSearch()
    }, 300)

    return () => clearTimeout(timeoutId)
  }, [query])

  const performSearch = async () => {
    try {
      setLoading(true)
      const response = await globalSearch(query)
      setResults(response.data)
    } catch (error) {
      console.error("Search failed", error)
    } finally {
      setLoading(false)
    }
  }

  const handleResultClick = (url: string) => {
    router.push(url)
    onOpenChange(false)
    setQuery("")
  }

  const totalResults = Object.values(results).flat().length

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-2xl">
        <DialogHeader>
          <DialogTitle>Search</DialogTitle>
          <DialogDescription>Search across customers, tickets, documents, and more</DialogDescription>
        </DialogHeader>
        <div className="space-y-4">
          <div className="relative">
            <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input
              ref={inputRef}
              placeholder="Type to search..."
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              className="pl-8"
            />
            {query && (
              <Button
                variant="ghost"
                size="icon"
                className="absolute right-1 top-1 h-7 w-7"
                onClick={() => setQuery("")}
              >
                <X className="h-4 w-4" />
              </Button>
            )}
          </div>

          {loading && (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
            </div>
          )}

          {!loading && query.length >= 2 && (
            <div className="max-h-[400px] overflow-y-auto space-y-4">
              {totalResults === 0 ? (
                <div className="text-center py-8 text-muted-foreground">
                  No results found for "{query}"
                </div>
              ) : (
                <>
                  {results.customers && results.customers.length > 0 && (
                    <div>
                      <h3 className="text-sm font-semibold mb-2">Customers</h3>
                      <div className="space-y-1">
                        {results.customers.map((result) => (
                          <button
                            key={result.id}
                            onClick={() => handleResultClick(result.url)}
                            className="w-full text-left p-2 hover:bg-muted rounded-lg transition-colors"
                          >
                            <div className="font-medium">{result.title}</div>
                            {result.description && (
                              <div className="text-sm text-muted-foreground">{result.description}</div>
                            )}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}

                  {results.tickets && results.tickets.length > 0 && (
                    <div>
                      <h3 className="text-sm font-semibold mb-2">Tickets</h3>
                      <div className="space-y-1">
                        {results.tickets.map((result) => (
                          <button
                            key={result.id}
                            onClick={() => handleResultClick(result.url)}
                            className="w-full text-left p-2 hover:bg-muted rounded-lg transition-colors"
                          >
                            <div className="font-medium">{result.title}</div>
                            {result.description && (
                              <div className="text-sm text-muted-foreground">{result.description}</div>
                            )}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}

                  {results.documents && results.documents.length > 0 && (
                    <div>
                      <h3 className="text-sm font-semibold mb-2">Documents</h3>
                      <div className="space-y-1">
                        {results.documents.map((result) => (
                          <button
                            key={result.id}
                            onClick={() => handleResultClick(result.url)}
                            className="w-full text-left p-2 hover:bg-muted rounded-lg transition-colors"
                          >
                            <div className="font-medium">{result.title}</div>
                            {result.description && (
                              <div className="text-sm text-muted-foreground">{result.description}</div>
                            )}
                          </button>
                        ))}
                      </div>
                    </div>
                  )}
                </>
              )}
            </div>
          )}

          {query.length < 2 && !loading && (
            <div className="text-center py-8 text-muted-foreground">
              Type at least 2 characters to search
            </div>
          )}
        </div>
      </DialogContent>
    </Dialog>
  )
}
