"use client"

import { useCallback, useState } from "react"
import { useDropzone } from "react-dropzone"
import { Upload, X, Loader2 } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { type Folder } from "@/lib/documents"

interface FileUploadProps {
  folderId?: number
  onUploadComplete: () => void
  onCancel: () => void
}

export function FileUpload({ folderId, onUploadComplete, onCancel }: FileUploadProps) {
  const [uploading, setUploading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [files, setFiles] = useState<File[]>([])

  const onDrop = useCallback((acceptedFiles: File[]) => {
    setFiles((prev) => [...prev, ...acceptedFiles])
    setError(null)
  }, [])

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    multiple: true,
  })

  const removeFile = (index: number) => {
    setFiles((prev) => prev.filter((_, i) => i !== index))
  }

  const handleUpload = async () => {
    if (files.length === 0) return

    setUploading(true)
    setError(null)

    try {
      const { uploadDocument } = await import("@/lib/documents")
      
      // Check file sizes before uploading
      const maxSize = 10 * 1024 * 1024 // 10MB
      const oversizedFiles = files.filter(f => f.size > maxSize)
      
      if (oversizedFiles.length > 0) {
        setError(`Some files exceed 10MB limit: ${oversizedFiles.map(f => f.name).join(", ")}`)
        setUploading(false)
        return
      }
      
      for (const file of files) {
        try {
          await uploadDocument(file, folderId)
        } catch (err: any) {
          const errorMsg = err.response?.data?.message || `Failed to upload ${file.name}`
          setError(errorMsg)
          // Continue with other files
        }
      }

      setFiles([])
      onUploadComplete()
    } catch (err: any) {
      setError(err.response?.data?.message || "Failed to upload files")
    } finally {
      setUploading(false)
    }
  }

  return (
    <div className="space-y-4">
      <div
        {...getRootProps()}
        className={`border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors ${
          isDragActive ? "border-primary bg-primary/5" : "border-muted-foreground/25"
        }`}
      >
        <input {...getInputProps()} />
        <Upload className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
        <p className="text-sm text-muted-foreground">
          {isDragActive ? "Drop files here" : "Drag & drop files here, or click to select"}
        </p>
        <p className="text-xs text-muted-foreground mt-2">Maximum file size: 10MB</p>
      </div>

      {files.length > 0 && (
        <div className="space-y-2">
          <h4 className="text-sm font-medium">Selected Files:</h4>
          {files.map((file, index) => (
            <div
              key={index}
              className="flex items-center justify-between p-2 border rounded-lg"
            >
              <span className="text-sm truncate flex-1">{file.name}</span>
              <span className="text-xs text-muted-foreground mr-2">
                {(file.size / 1024 / 1024).toFixed(2)} MB
              </span>
              <Button
                variant="ghost"
                size="icon"
                onClick={() => removeFile(index)}
                className="h-6 w-6"
              >
                <X className="h-4 w-4" />
              </Button>
            </div>
          ))}
        </div>
      )}

      {error && (
        <Alert variant="destructive">
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      )}

      <div className="flex justify-end gap-2">
        <Button variant="outline" onClick={onCancel} disabled={uploading}>
          Cancel
        </Button>
        <Button onClick={handleUpload} disabled={files.length === 0 || uploading}>
          {uploading ? (
            <>
              <Loader2 className="h-4 w-4 mr-2 animate-spin" />
              Uploading...
            </>
          ) : (
            "Upload"
          )}
        </Button>
      </div>
    </div>
  )
}
