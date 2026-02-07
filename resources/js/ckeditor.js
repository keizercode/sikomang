/**
 * CKEditor Configuration for SIKOMANG Article Management
 * Professional, clean, and consistent formatting
 */

const CKEditorConfig = {
    /**
     * Initialize CKEditor on a textarea
     * @param {string} selector - CSS selector for textarea
     */
    init: function (selector) {
        ClassicEditor.create(document.querySelector(selector), {
            toolbar: {
                items: [
                    "heading",
                    "|",
                    "bold",
                    "italic",
                    "underline",
                    "strikethrough",
                    "|",
                    "alignment",
                    "|",
                    "numberedList",
                    "bulletedList",
                    "|",
                    "outdent",
                    "indent",
                    "|",
                    "link",
                    "imageUpload",
                    "blockQuote",
                    "insertTable",
                    "|",
                    "undo",
                    "redo",
                    "|",
                    "sourceEditing",
                ],
                shouldNotGroupWhenFull: true,
            },
            heading: {
                options: [
                    {
                        model: "paragraph",
                        title: "Paragraph",
                        class: "ck-heading_paragraph",
                    },
                    {
                        model: "heading2",
                        view: "h2",
                        title: "Heading 2",
                        class: "ck-heading_heading2",
                    },
                    {
                        model: "heading3",
                        view: "h3",
                        title: "Heading 3",
                        class: "ck-heading_heading3",
                    },
                    {
                        model: "heading4",
                        view: "h4",
                        title: "Heading 4",
                        class: "ck-heading_heading4",
                    },
                ],
            },
            image: {
                toolbar: [
                    "imageTextAlternative",
                    "imageStyle:inline",
                    "imageStyle:block",
                    "imageStyle:side",
                    "|",
                    "toggleImageCaption",
                    "linkImage",
                ],
                upload: {
                    types: ["jpeg", "png", "gif", "webp"],
                    // Custom upload adapter
                    adapter: function (loader) {
                        return {
                            upload: function () {
                                return loader.file.then((file) => {
                                    return new Promise((resolve, reject) => {
                                        const formData = new FormData();
                                        formData.append("upload", file);
                                        formData.append(
                                            "_token",
                                            document.querySelector(
                                                'meta[name="csrf-token"]',
                                            ).content,
                                        );

                                        fetch("/admin/articles/upload-image", {
                                            method: "POST",
                                            body: formData,
                                        })
                                            .then((response) => response.json())
                                            .then((data) => {
                                                if (data.url) {
                                                    resolve({
                                                        default: data.url,
                                                    });
                                                } else {
                                                    reject(
                                                        data.error ||
                                                            "Upload failed",
                                                    );
                                                }
                                            })
                                            .catch((error) => {
                                                reject(error);
                                            });
                                    });
                                });
                            },
                        };
                    },
                },
            },
            table: {
                contentToolbar: [
                    "tableColumn",
                    "tableRow",
                    "mergeTableCells",
                    "tableCellProperties",
                    "tableProperties",
                ],
            },
            link: {
                decorators: {
                    openInNewTab: {
                        mode: "manual",
                        label: "Open in a new tab",
                        attributes: {
                            target: "_blank",
                            rel: "noopener noreferrer",
                        },
                    },
                },
            },
            alignment: {
                options: ["left", "center", "right", "justify"],
            },
            placeholder: "Tulis konten artikel di sini...",
            // Language
            language: "id",
            // Word count
            wordCount: {
                onUpdate: (stats) => {
                    const counter = document.getElementById("editor-counter");
                    if (counter) {
                        counter.innerHTML = `
                                <i class="fas fa-file-alt"></i> ${stats.words} kata |
                                <i class="fas fa-font"></i> ${stats.characters} karakter
                            `;
                    }
                },
            },
        })
            .then((editor) => {
                window.editor = editor;

                // Auto-save draft every 30 seconds
                setInterval(() => {
                    const content = editor.getData();
                    if (
                        content &&
                        content.trim() !== "" &&
                        content !== "<p></p>"
                    ) {
                        localStorage.setItem("article_draft", content);
                        console.log("Draft auto-saved");
                    }
                }, 30000);

                // Restore draft if exists
                const draft = localStorage.getItem("article_draft");
                if (
                    draft &&
                    confirm(
                        "Ada draft yang tersimpan. Apakah Anda ingin memulihkannya?",
                    )
                ) {
                    editor.setData(draft);
                }

                console.log("CKEditor initialized successfully");
            })
            .catch((error) => {
                console.error("CKEditor initialization error:", error);
            });
    },
};

// Make it globally available
if (typeof window !== "undefined") {
    window.CKEditorConfig = CKEditorConfig;
}
