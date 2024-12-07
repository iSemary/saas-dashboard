CKEDITOR.replace("ck-cashierc", {
    toolbar: [
        {
            name: "document",
            groups: ["mode", "document", "doctools"],
            items: [
                "Source",
                "-",
                "Save",
                "NewPage",
                "Preview",
                "-",
                "Templates"
            ]
        },
        {
            name: "clipboard",
            groups: ["undo"],
            items: ["Cut", "Copy", "Paste", "-", "Undo", "Redo"]
        },
        {
            name: "editing",
            groups: ["find", "selection"],
            items: ["Find", "-", "SelectAll", "-", "Scayt"]
        },
        { name: "forms", items: [] },
        "/",
        {
            name: "basicstyles",
            groups: ["basicstyles"],
   
         items: [
                "Bold",
                "Italic",
                "Underline",
                "Strike",
                "Subscript",
                "Superscript",
                "-"
            ]
        },
        {
            name: "paragraph",
            groups: ["list", "indent", "blocks", "align", "bidi"],
            items: [
                "NumberedList",
                "BulletedList",
                "-",
                "Outdent",
                "Indent",
                "-",
                "Blockquote",
                "CreateDiv",
                "-",
                "JustifyLeft",
                "JustifyCenter",
                "JustifyRight",
                "JustifyBlock",
                "-",
                "BidiLtr",
                "BidiRtl",
                "Language"
            ]
        },
        { name: "links", items: [] },
        { name: "insert", items: ["Table", "HorizontalRule", "SpecialChar"] },
        "/",
        { name: "styles", items: ["Styles", "Format", "Font", "FontSize"] },
        { name: "colors", items: ["TextColor", "BGColor"] },
        { name: "tools", items: ["Maximize"] },
        { name: "others", items: ["-"] },
        { name: "about", items: [] }
    ]
});
