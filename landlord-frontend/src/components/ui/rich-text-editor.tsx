"use client";

import { CKEditor } from "@ckeditor/ckeditor5-react";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { forwardRef } from "react";

export interface RichTextEditorProps {
  value?: string;
  onChange?: (value: string) => void;
  placeholder?: string;
  disabled?: boolean;
  dir?: "ltr" | "rtl";
  language?: string;
  className?: string;
}

const RichTextEditor = forwardRef<HTMLDivElement, RichTextEditorProps>(
  ({ value = "", onChange, placeholder, disabled, dir = "ltr", language = "en", className }, ref) => {
    const editorConfig = {
      language: language,
      direction: dir,
      placeholder: placeholder,
      toolbar: [
        "heading",
        "|",
        "bold",
        "italic",
        "underline",
        "strikethrough",
        "|",
        "link",
        "imageUpload",
        "blockQuote",
        "insertTable",
        "mediaEmbed",
        "|",
        "bulletedList",
        "numberedList",
        "|",
        "alignment",
        "|",
        "undo",
        "redo",
      ],
      table: {
        contentToolbar: [
          "tableColumn",
          "tableRow",
          "mergeTableCells",
          "tableProperties",
          "tableCellProperties",
        ],
      },
      image: {
        toolbar: [
          "imageStyle:inline",
          "imageStyle:block",
          "imageStyle:side",
          "|",
          "toggleImageCaption",
          "imageTextAlternative",
        ],
      },
      mediaEmbed: {
        previewsInData: true,
      },
    };

    return (
      <div ref={ref} className={className} dir={dir}>
        <CKEditor
          // eslint-disable-next-line @typescript-eslint/no-explicit-any
          editor={ClassicEditor as any}
          data={value}
          config={editorConfig}
          disabled={disabled}
          onChange={(_event, editor) => {
            const data = editor.getData();
            onChange?.(data);
          }}
        />
      </div>
    );
  }
);

RichTextEditor.displayName = "RichTextEditor";

export { RichTextEditor };
