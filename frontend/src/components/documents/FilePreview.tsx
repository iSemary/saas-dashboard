"use client"

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Download, X } from "lucide-react"
import { type Document, downloadDocument } from "@/lib/documents"
import { useState } from "react"
import { Loader2 } from "lucide-react"

interface FilePreviewProps {
  document: Document | null
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function FilePreview({ document, open, onOpenChange }: FilePreviewProps) {
  const [downloading, setDownloading] = useState(false)

  if (!document) return null

  const isImage = document.mime_type.startsWith("image/")
  const isPdf = document.mime_type.includes("pdf")
  const isVideo = document.mime_type.startsWith("video/")
  const isAudio = document.mime_type.startsWith("audio/")

  const handleDownload = async () => {
    try {
      setDownloading(true)
      const blob = await downloadDocument(document.id)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement("a")
      a.href = url
      a.download = document.original_name
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (error) {
      console.error("Failed to download file", error)
    } finally {
      setDownloading(false)
    }
  }

  const getPreviewUrl = () => {
    // Construct preview URL - for public files, use direct asset URL
    // For private files, use the download endpoint with auth
    if (document.access_level === 'public') {
      // Try to construct public asset URL
      const folderPath = document.folder ? `${document.folder.name}/` : ''
      return `/storage/${folderPath}${document.hash_name}`
    }
    // For private files, use download endpoint (will require auth)
    return `/api/documents/${document.id}/download`
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-4xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>{document.original_name}</DialogTitle>
          <DialogDescription>
            {document.mime_type} • {document.size} bytes
          </DialogDescription>
        </DialogHeader>

        <div className="space-y-4">
          {isImage && (
            <div className="flex justify-center">
              <img
                src={getPreviewUrl()}
                alt={document.original_name}
                className="max-w-full max-h-[60vh] object-contain"
              />
            </div>
          )}

          {isPdf && (
            <div className="w-full h-[60vh] border rounded-lg">
              <iframe
                src={getPreviewUrl()}
                className="w-full h-full"
                title={document.original_name}
              />
            </div>
          )}

          {isVideo && (
            <div className="flex justify-center">
              <video controls className="max-w-full max-h-[60vh]">
                <source src={getPreviewUrl()} type={document.mime_type} />
                Your browser does not support the video tag.
              </video>
            </div>
          )}

          {isAudio && (
            <div className="flex justify-center">
              <audio controls className="w-full">
                <source src={getPreviewUrl()} type={document.mime_type} />
                Your browser does not support the audio tag.
              </audio>
            </div>
          )}

          {!isImage && !isPdf && !isVideo && !isAudio && (
            <div className="text-center py-12 text-muted-foreground">
              <p>Preview not available for this file type</p>
              <p className="text-sm mt-2">{document.mime_type}</p>
            </div>
          )}

          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={handleDownload} disabled={downloading}>
              {downloading ? (
                <>
                  <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                  Downloading...
                </>
              ) : (
                <>
                  <Download className="h-4 w-4 mr-2" />
                  Download
                </>
              )}
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
