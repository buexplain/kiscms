(function() {
    CKEDITOR.plugins.add("upload", {
        requires: ["dialog"],
        init: function(a) {
            a.addCommand("upload", new CKEDITOR.dialogCommand("upload"));
            a.ui.addButton("upload", {
                label: "上传附件",//鼠标悬浮时候的title
                command: "upload",
                icon: this.path + "upload.png"//在toolbar中的图标

            });
            CKEDITOR.dialog.add("upload", this.path + "dialogs/upload.js")
        }
    })
})();