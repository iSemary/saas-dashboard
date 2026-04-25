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
        { name: "document", items: ["Source", "-", "Save", "NewPage", "Preview", "Print", "-", "Templates"] },
        { name: "clipboard", items: ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"] },
        { name: "editing", items: ["Find", "Replace", "-", "SelectAll", "-", "Scayt"] },
        { name: "forms", items: ["Form", "Checkbox", "Radio", "TextField", "Textarea", "Select", "Button", "ImageButton", "HiddenField"] },
        "/",
        { name: "basicstyles", items: ["Bold", "Italic", "Underline", "Strike", "Subscript", "Superscript", "-", "CopyFormatting", "RemoveFormat"] },
        { name: "paragraph", items: ["NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "Blockquote", "CreateDiv", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock", "-", "BidiLtr", "BidiRtl", "Language"] },
        { name: "links", items: ["Link", "Unlink", "Anchor"] },
        { name: "insert", items: ["Image", "Flash", "Table", "HorizontalRule", "Smiley", "SpecialChar", "PageBreak", "Iframe"] },
        "/",
        { name: "styles", items: ["Styles", "Format", "Font", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize", "ShowBlocks"] },
        { name: "about", items: ["About"] },
      ],
      height: 300,
      removeButtons: "Save,NewPage,Preview,Print,Templates,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Flash,Smiley,SpecialChar,PageBreak,Iframe,Maximize,ShowBlocks,About",
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
