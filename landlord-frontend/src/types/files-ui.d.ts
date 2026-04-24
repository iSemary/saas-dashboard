declare module "@files-ui/react" {
  import * as React from "react";

  export interface ExtFile {
    id: string | number;
    file?: File;
    name?: string;
    size?: number;
    type?: string;
    valid?: boolean;
    errors?: string[];
    previewUrl?: string;
    uploadStatus?: "pending" | "uploading" | "success" | "error";
    uploadMessage?: string;
  }

  export interface DropzoneProps {
    onChange?: (files: ExtFile[]) => void;
    value?: ExtFile[];
    accept?: string;
    maxFiles?: number;
    maxFileSize?: number;
    disabled?: boolean;
    label?: string;
    uploadIcon?: boolean;
    header?: boolean;
    footer?: boolean;
    color?: string;
    style?: React.CSSProperties;
    className?: string;
  }

  export interface FileMosaicProps {
    id: string | number;
    file?: File;
    name?: string;
    size?: number;
    type?: string;
    valid?: boolean;
    errors?: string[];
    previewUrl?: string;
    uploadStatus?: "pending" | "uploading" | "success" | "error";
    uploadMessage?: string;
    onDelete?: (id: string | number | undefined) => void;
    preview?: boolean;
    resultOnTooltip?: boolean;
    color?: string;
    style?: React.CSSProperties;
    className?: string;
  }

  export const Dropzone: React.FC<DropzoneProps>;
  export const FileMosaic: React.FC<FileMosaicProps>;
}
