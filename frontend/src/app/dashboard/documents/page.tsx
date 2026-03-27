"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { FileUpload } from "@/components/documents/FileUpload"
import { FileBrowser } from "@/components/documents/FileBrowser"
import { FilePreview } from "@/components/documents/FilePreview"
import {
  getDocuments,
  getFolders,
  deleteDocument,
  deleteFolder,
  downloadDocument,
  createFolder,
  type Document,
  type Folder,
} from "@/lib/documents"
import { Plus, Upload, FolderPlus, Search, Trash2 } from "lucide-react"
import { toast } from "sonner"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"
import * as z from "zod"
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form"

const folderSchema = z.object({
  name: z.string().min(1, "Name is required"),
  description: z.string().optional(),
})

type FolderFormValues = z.infer<typeof folderSchema>

export default function DocumentsPage() {
  const [documents, setDocuments] = useState<Document[]>([])
  const [folders, setFolders] = useState<Folder[]>([])
  const [loading, setLoading] = useState(true)
  const [currentFolderId, setCurrentFolderId] = useState<number | null>(null)
  const [breadcrumbs, setBreadcrumbs] = useState<Array<{ id: number | null; name: string }>>([])
  const [uploadDialogOpen, setUploadDialogOpen] = useState(false)
  const [folderDialogOpen, setFolderDialogOpen] = useState(false)
  const [previewDocument, setPreviewDocument] = useState<Document | null>(null)
  const [search, setSearch] = useState("")

  const folderForm = useForm<FolderFormValues>({
    resolver: zodResolver(folderSchema),
    defaultValues: {
      name: "",
      description: "",
    },
  })

  const loadData = async () => {
    try {
      setLoading(true)
      const [docsResponse, foldersResponse] = await Promise.all([
        getDocuments({ folder_id: currentFolderId, search: search || undefined }),
        getFolders(currentFolderId, search || undefined),
      ])
      setDocuments(docsResponse.data.data)
      setFolders(foldersResponse.data)
    } catch (error: any) {
      toast.error("Failed to load documents")
      console.error(error)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadData()
  }, [currentFolderId, search])

  const handleFolderClick = async (folderId: number | null) => {
    setCurrentFolderId(folderId)
    if (folderId) {
      try {
        const folder = (await getFolders()).data.find((f) => f.id === folderId)
        if (folder) {
          // Build breadcrumbs by traversing parent folders
          const buildBreadcrumbs = async (fId: number | null, crumbs: Array<{ id: number | null; name: string }> = []): Promise<Array<{ id: number | null; name: string }>> => {
            if (!fId) return [{ id: null, name: "Root" }, ...crumbs]
            const folder = (await getFolders()).data.find((f) => f.id === fId)
            if (folder) {
              return buildBreadcrumbs(folder.parent_id || null, [{ id: fId, name: folder.name }, ...crumbs])
            }
            return crumbs
          }
          const newBreadcrumbs = await buildBreadcrumbs(folderId)
          setBreadcrumbs(newBreadcrumbs)
        }
      } catch (error) {
        console.error("Failed to load folder", error)
        setBreadcrumbs([])
      }
    } else {
      setBreadcrumbs([])
    }
  }

  const handleFileClick = (document: Document) => {
    setPreviewDocument(document)
  }

  const handleFileDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this file?")) return

    try {
      await deleteDocument(id)
      toast.success("File deleted successfully")
      loadData()
    } catch (error: any) {
      toast.error("Failed to delete file")
      console.error(error)
    }
  }

  const handleFolderDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this folder?")) return

    try {
      await deleteFolder(id)
      toast.success("Folder deleted successfully")
      loadData()
    } catch (error: any) {
      toast.error("Failed to delete folder")
      console.error(error)
    }
  }

  const handleFileDownload = async (id: number) => {
    try {
      const blob = await downloadDocument(id)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement("a")
      a.href = url
      const doc = documents.find((d) => d.id === id)
      a.download = doc?.original_name || "download"
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
      toast.success("File downloaded")
    } catch (error: any) {
      toast.error("Failed to download file")
      console.error(error)
    }
  }

  const handleFolderCreate = async (values: FolderFormValues) => {
    try {
      await createFolder({
        ...values,
        parent_id: currentFolderId || undefined,
      })
      toast.success("Folder created successfully")
      setFolderDialogOpen(false)
      folderForm.reset()
      loadData()
    } catch (error: any) {
      toast.error("Failed to create folder")
      console.error(error)
    }
  }

  const handleUploadComplete = () => {
    setUploadDialogOpen(false)
    loadData()
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Documents</h1>
          <p className="text-muted-foreground">Manage your files and folders</p>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="outline" onClick={() => setFolderDialogOpen(true)}>
            <FolderPlus className="h-4 w-4 mr-2" />
            New Folder
          </Button>
          <Button onClick={() => setUploadDialogOpen(true)}>
            <Upload className="h-4 w-4 mr-2" />
            Upload
          </Button>
        </div>
      </div>

      {/* Search */}
      <div className="relative">
        <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
        <Input
          placeholder="Search files and folders..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="pl-8"
        />
      </div>

      {/* File Browser */}
      {loading ? (
        <div className="text-center py-12 text-muted-foreground">Loading...</div>
      ) : (
        <FileBrowser
          documents={documents}
          folders={folders}
          currentFolderId={currentFolderId}
          onFolderClick={handleFolderClick}
          onFileClick={handleFileClick}
          onFileDelete={handleFileDelete}
          onFolderDelete={handleFolderDelete}
          onFileDownload={handleFileDownload}
          onFolderCreate={() => setFolderDialogOpen(true)}
          breadcrumbs={breadcrumbs}
        />
      )}

      {/* Upload Dialog */}
      <Dialog open={uploadDialogOpen} onOpenChange={setUploadDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Upload Files</DialogTitle>
            <DialogDescription>Select files to upload to this folder</DialogDescription>
          </DialogHeader>
          <FileUpload
            folderId={currentFolderId || undefined}
            onUploadComplete={handleUploadComplete}
            onCancel={() => setUploadDialogOpen(false)}
          />
        </DialogContent>
      </Dialog>

      {/* Folder Dialog */}
      <Dialog open={folderDialogOpen} onOpenChange={setFolderDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Create Folder</DialogTitle>
            <DialogDescription>Create a new folder</DialogDescription>
          </DialogHeader>
          <Form {...folderForm}>
            <form onSubmit={folderForm.handleSubmit(handleFolderCreate)} className="space-y-4">
              <FormField
                control={folderForm.control}
                name="name"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Name *</FormLabel>
                    <FormControl>
                      <Input {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={folderForm.control}
                name="description"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Description</FormLabel>
                    <FormControl>
                      <Input {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <DialogFooter>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => setFolderDialogOpen(false)}
                >
                  Cancel
                </Button>
                <Button type="submit">Create</Button>
              </DialogFooter>
            </form>
          </Form>
        </DialogContent>
      </Dialog>

      {/* File Preview */}
      <FilePreview
        document={previewDocument}
        open={!!previewDocument}
        onOpenChange={(open) => !open && setPreviewDocument(null)}
      />
    </div>
  )
}
