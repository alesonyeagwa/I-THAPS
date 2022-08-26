ace.define("ace/theme/forminator",[], function(require, exports, module) {

	exports.isDark = false;
	exports.cssClass = "ace-forminator";
	exports.cssText = ".ace-forminator {" +
		"font-family: 'Source Code Pro', 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;" +
		"line-height: 18px;" +
	"}" +
	".ace-forminator .ace_editor {" +
		"border: 2px solid rgb(159, 159, 159);" +
	"}" +
	".ace-forminator .ace_editor.ace_focus {" +
		"border: 2px solid #327FBD;" +
	"}" +
	".ace-forminator .ace_gutter {" +
		"width: 30px;" +
		"background: #666666;" +
		"color: #FFFFFF;" +
		"overflow: hidden;" +
	"}" +
	".ace-forminator .ace_gutter-layer {" +
		"width: 100%;" +
		"text-align: right;" +
	"}" +
	".ace-forminator .ace_gutter-layer .ace_gutter-cell {" +
		"width: 30px;" +
		"padding-right: 9px;" +
		"padding-left: 3px;" +
		"text-align: right;" +
	"}" +
	".ace-forminator .ace_print_margin {" +
		"width: 1px;" +
		"background: #E8E8E8;" +
	"}" +
	".ace-forminator .ace_scroller {" +
		"background-color: #F2F2F2;" +
	"}" +
	".ace-forminator .ace_text-layer {" +
		"cursor: text;" +
		"color: #666666;" +
	"}" +
	".ace-forminator .ace_cursor {" +
		"border-left: 2px solid #000000;" +
	"}" +
	".ace-forminator .ace_cursor.ace_overwrite {" +
		"border-left: 0;" +
		"border-bottom: 1px solid #000000;" +
	"}" +
	".ace-forminator .ace_marker-layer .ace_selection {" +
		"background: rgba(130, 139, 201, 0.5);" +
	"}" +
	".ace-forminator .ace_marker-layer .ace_step {" +
		"background: rgb(198, 219, 174);" +
	"}" +
	".ace-forminator .ace_marker-layer .ace_bracket {" +
		"margin: 0;" +
		"border: 1px solid rgba(147, 161, 161, 0.50);" +
	"}" +
	".ace-forminator .ace_marker-layer .ace_active_line {" +
		"background: #EEE8D5;" +
	"}" +
	".ace-forminator .ace_invisible {" +
		"color: rgba(147, 161, 161, 0.50);" +
	"}" +
	".ace-forminator .ace_keyword {" +
		"color: #859900;" +
	"}" +
	".ace-forminator .ace_keyword.ace_operator {}" +
	".ace-forminator .ace_constant {}" +
	".ace-forminator .ace_constant.ace_language {" +
		"color: #B58900;" +
	"}" +
	".ace-forminator .ace_constant.ace_library {}" +
	".ace-forminator .ace_constant.ace_numeric {" +
		"color: #D33682;" +
	"}" +
	".ace-forminator .ace_invalid {}" +
	".ace-forminator .ace_invalid.ace_illegal {}" +
	".ace-forminator .ace_invalid.ace_deprecated {}" +
	".ace-forminator .ace_support {}" +
	".ace-forminator .ace_support.ace_function {" +
		"color: #268BD2;" +
	"}" +
	".ace-forminator .ace_function.ace_buildin {}" +
	".ace-forminator .ace_string {" +
		"color: #2AA198;" +
	"}" +
	".ace-forminator .ace_string.ace_regexp {" +
		"color: #D30102;" +
	"}" +
	".ace-forminator .ace_comment {" +
		"color: #93A1A1;" +
	"}" +
	".ace-forminator .ace_comment.ace_doc {}" +
	".ace-forminator .ace_comment.ace_doc.ace_tag {}" +
	".ace-forminator .ace_variable {}" +
	".ace-forminator .ace_variable.ace_language {" +
		"color: #268BD2;" +
	"}" +
	".ace-forminator .ace_xml_pe {}" +
	".ace-forminator .ace_collab.ace_user1 {}";
	
	var dom = require("../lib/dom");
	dom.importCssString(exports.cssText, exports.cssClass);
});

(function() {

	ace.require(["ace/theme/forminator"], function(m) {

		if (typeof module == "object" && typeof exports == "object" && module) {
			module.exports = m;
		}
	});
})();
