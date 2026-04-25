"use client";

import { CKEditor } from "ckeditor4-react";
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
  ({ value = "", onChange, disabled, dir = "ltr", language = "en", className }, ref) => {
    const editorConfig = {
      language: language,
      contentsLangDirection: dir,
      toolbar: [
        { name: "basicstyles", items: ["Bold", "Italic", "Underline", "-", "RemoveFormat"] },
        { name: "paragraph", items: ["NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "Blockquote"] },
        { name: "links", items: ["Link", "Unlink"] },
        { name: "insert", items: ["Image", "Table"] },
        { name: "styles", items: ["Format", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize"] },
        { name: "clipboard", items: ["Cut", "Copy", "Paste", "-", "Undo", "Redo"] },
      ],
      height: 300,
      removePlugins: "elementspath,resize",
      allowedContent: true,
    };

    return (
      <div ref={ref} className={className} dir={dir}>
        <CKEditor
          initData={value}
          config={editorConfig}
          readOnly={disabled}
          onChange={(event: { editor: { getData: () => string } }) => {
            const data = event.editor.getData();
            onChange?.(data);
          }}
        />
      </div>
    );
  }
);

RichTextEditor.displayName = "RichTextEditor";

export { RichTextEditor };
