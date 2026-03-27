"use client"

import { useState } from "react"
import { Folder, File, ChevronRight, MoreVertical, Download, Trash2, Edit } from "lucide-react"
import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { type Document, type Folder as FolderType, formatFileSize, getFileIcon } from "@/lib/documents"
import { format } from "date-fns"

interface FileBrowserProps {
  documents: Document[]
  folders: FolderType[]
  currentFolderId?: number | null
  onFolderClick: (folderId: number | null) => void
  onFileClick: (document: Document) => void
  onFileDelete: (id: number) => void
  onFolderDelete: (id: number) => void
  onFileDownload: (id: number) => void
  onFolderCreate: () => void
  breadcrumbs: Array<{ id: number | null; name: string }>
}

export function FileBrowser({
  documents,
  folders,
  currentFolderId,
  onFolderClick,
  onFileClick,
  onFileDelete,
  onFolderDelete,
  onFileDownload,
  onFolderCreate,
  breadcrumbs,
}: FileBrowserProps) {
  return (
    <div className="space-y-4">
      {/* Breadcrumbs */}
      <div className="flex items-center gap-2 text-sm flex-wrap">
        <button
          onClick={() => onFolderClick(null)}
          className="text-muted-foreground hover:text-foreground"
        >
          Root
        </button>
        {breadcrumbs.map((crumb, index) => (
          <div key={crumb.id || `crumb-${index}`} className="flex items-center gap-2">
            <ChevronRight className="h-4 w-4 text-muted-foreground" />
            <button
              onClick={() => onFolderClick(crumb.id)}
              className="text-muted-foreground hover:text-foreground truncate max-w-[200px]"
            >
              {crumb.name}
            </button>
          </div>
        ))}
      </div>

      {/* Folders */}
      {folders.length > 0 && (
        <div className="grid grid-cols-4 gap-4">
          {folders.map((folder) => (
            <div
              key={folder.id}
              className="border rounded-lg p-4 hover:bg-muted/50 cursor-pointer transition-colors"
              onClick={() => onFolderClick(folder.id)}
            >
              <div className="flex items-start justify-between mb-2">
                <Folder className="h-8 w-8 text-primary" />
                <DropdownMenu>
                  <DropdownMenuTrigger asChild onClick={(e) => e.stopPropagation()}>
                    <Button variant="ghost" size="icon" className="h-6 w-6">
                      <MoreVertical className="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem onClick={() => onFolderDelete(folder.id)}>
                      <Trash2 className="h-4 w-4 mr-2" />
                      Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
              <div className="font-medium truncate">{folder.name}</div>
              {folder.description && (
                <div className="text-xs text-muted-foreground truncate mt-1">
                  {folder.description}
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      {/* Files */}
      {documents.length > 0 && (
        <div className="border rounded-lg">
          <table className="w-full">
            <thead>
              <tr className="border-b">
                <th className="text-left p-3 font-medium">Name</th>
                <th className="text-left p-3 font-medium">Size</th>
                <th className="text-left p-3 font-medium">Type</th>
                <th className="text-left p-3 font-medium">Modified</th>
                <th className="text-right p-3 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              {documents.map((doc) => (
                <tr
                  key={doc.id}
                  className="border-b hover:bg-muted/50 cursor-pointer"
                  onClick={() => onFileClick(doc)}
                >
                  <td className="p-3">
                    <div className="flex items-center gap-2">
                      <span className="text-lg">{getFileIcon(doc.mime_type)}</span>
                      <span className="font-medium">{doc.original_name}</span>
                    </div>
                  </td>
                  <td className="p-3 text-sm text-muted-foreground">
                    {formatFileSize(doc.size)}
                  </td>
                  <td className="p-3 text-sm text-muted-foreground">{doc.mime_type}</td>
                  <td className="p-3 text-sm text-muted-foreground">
                    {format(new Date(doc.updated_at), "MMM d, yyyy")}
                  </td>
                  <td className="p-3">
                    <div className="flex justify-end">
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild onClick={(e) => e.stopPropagation()}>
                          <Button variant="ghost" size="icon" className="h-8 w-8">
                            <MoreVertical className="h-4 w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem onClick={() => onFileDownload(doc.id)}>
                            <Download className="h-4 w-4 mr-2" />
                            Download
                          </DropdownMenuItem>
                          <DropdownMenuItem
                            onClick={() => onFileDelete(doc.id)}
                            className="text-destructive"
                          >
                            <Trash2 className="h-4 w-4 mr-2" />
                            Delete
                          </DropdownMenuItem>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {folders.length === 0 && documents.length === 0 && (
        <div className="text-center py-12 text-muted-foreground">
          <Folder className="h-12 w-12 mx-auto mb-4 opacity-50" />
          <p>No files or folders</p>
        </div>
      )}
    </div>
  )
}
