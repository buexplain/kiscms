if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 ) CKEDITOR.tools.enableHtml5Elements( document );
/**
 * 获取编辑器工具栏自定义参数
 * @param type 类型 simple=极简版 basic=基本版 full=完整版
 */
function getCkeditorRemoveButtons(type) {
    if(!arguments[0]) type = 'full';
    if(type == 'simple') return 'Save,Print,Templates,PasteFromWord,SelectAll,Scayt,Language,About,Form,Radio,Checkbox,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CreateDiv,BidiLtr,BidiRtl,Iframe,SpecialChar,PageBreak,Source,NewPage,Preview,Cut,Undo,Find,Replace,Redo,Copy,Paste,PasteText,Italic,Underline,Strike,RemoveFormat,Blockquote,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,Link,Unlink,Anchor,Table,Flash,HorizontalRule,Smiley,Styles,BGColor,ShowBlocks,Format,Font,FontSize';
    if(type == 'basic') return 'Save,Print,Templates,PasteFromWord,SelectAll,Scayt,Language,About,Form,Radio,Checkbox,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CreateDiv,BidiLtr,BidiRtl,Iframe,SpecialChar,PageBreak';
    return 'Save,Print,Templates,PasteFromWord,SelectAll,Scayt,Language,About';
}
//代码高亮扩展
var codeHighLight = 'clipboard,lineutils,widget,dialog,codesnippet';
var customPlug = 'upload,autoformat';