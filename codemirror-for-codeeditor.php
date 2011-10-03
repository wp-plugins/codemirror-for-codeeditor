<?php
/*
Plugin Name: CodeMirror for CodeEditor
Plugin URI: http://www.near-mint.com/blog/software/codemirror-for-codeeditor
Description: Just another code syntaxhighligher for the theme and plugin editor with CodeMirror. This plugin can highlight sourcecodes in theme/plugin editor and provide a useful toolbar.
Version: 0.2
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: cfc_lang
Domain Path: /languages
*/
/* 
Last modified: 2011/10/3
License: GPL v2(Except "CodeMirror" libraries)
*/
/*  Copyright 2011 M. Sumitomo

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* 
"CodeMirror for CodeEditor" uses CodeMirror ver. 2.15 by Marijn Haverbeke. 
*/

load_plugin_textdomain('cfc_lang', false, dirname(plugin_basename(__FILE__)).'/languages');
$cfc_plugin_url = plugin_dir_url(__FILE__);
$cfc_ver = "0.2";
$cfc_theme = "default"; // Other themes: cobalt, eclipse, elegan, neat, night

add_action('admin_print_styles-theme-editor.php', 'cfc_load_style');
add_action('admin_print_styles-plugin-editor.php', 'cfc_load_style');

// Add styles into the header
function cfc_load_style() {
	global $cfc_plugin_url;
	wp_enqueue_style('codemirror',  $cfc_plugin_url.'codemirror/lib/codemirror.css', false, '2.15');
	wp_enqueue_style('codemirror-theme-default',  $cfc_plugin_url.'codemirror/theme/default.css', false, '2.15');
	wp_enqueue_style('codemirror-theme-cobalt',  $cfc_plugin_url.'codemirror/theme/cobalt.css', false, '2.15');
	wp_enqueue_style('codemirror-theme-eclipse',  $cfc_plugin_url.'codemirror/theme/eclipse.css', false, '2.15');
	wp_enqueue_style('codemirror-theme-elegant',  $cfc_plugin_url.'codemirror/theme/elegant.css', false, '2.15');
	wp_enqueue_style('codemirror-theme-neat',  $cfc_plugin_url.'codemirror/theme/neat.css', false, '2.15');
	wp_enqueue_style('codemirror-theme-night',  $cfc_plugin_url.'codemirror/theme/night.css', false, '2.15');
	wp_enqueue_style('codemirror-fullscreen',  $cfc_plugin_url.'css/dummy.css', false, '2.15');
}

add_action('admin_print_scripts-theme-editor.php', 'cfc_load_script');
add_action('admin_print_scripts-plugin-editor.php', 'cfc_load_script');

// Add core and languages scripts into the header
function cfc_load_script() {
	global $cfc_plugin_url;
	wp_enqueue_script('jquery');
	wp_enqueue_script('codemirror', $cfc_plugin_url.'codemirror/lib/codemirror.js', false, '2.15');
	wp_enqueue_script('codemirror-mode-clike', $cfc_plugin_url.'codemirror/mode/clike/clike.js', false, '2.15');
	wp_enqueue_script('codemirror-mode-css', $cfc_plugin_url.'codemirror/mode/css/css.js', false, '2.15');
	wp_enqueue_script('codemirror-mode-htmlmixed', $cfc_plugin_url.'codemirror/mode/htmlmixed/htmlmixed.js', false, '2.15');
	wp_enqueue_script('codemirror-mode-js', $cfc_plugin_url.'codemirror/mode/javascript/javascript.js', false, '2.15');
	wp_enqueue_script('codemirror-mode-php', $cfc_plugin_url.'codemirror/mode/php/php.js', false, '2.15');
	wp_enqueue_script('codemirror-mode-xml', $cfc_plugin_url.'codemirror/mode/xml/xml.js', false, '2.15');
	wp_enqueue_script('codemirror-auto-complete',  $cfc_plugin_url.'codemirror/lib/complete.js', false, '2.15');
}

// Add scripts into the footer
add_action('admin_footer-theme-editor.php', 'cfc_run_script');
add_action('admin_footer-plugin-editor.php', 'cfc_run_script');

function cfc_run_script() {
	global $cfc_plugin_url, $cfc_ver, $cfc_theme, $pagenow;
	if (isset($_GET['file'])) {
		$extension = strtolower(pathinfo(esc_html($_GET['file']), PATHINFO_EXTENSION));
	} else {
		if ($pagenow == "theme-editor.php") {
			$extension = "css";
		} elseif ($pagenow == "plugin-editor.php") {
			$extension = "php";
		}
	}
	echo "\n<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Scripts for CodeMirror Begin -->
<script type=\"text/javascript\">
var editor = CodeMirror.fromTextArea(document.getElementById(\"newcontent\"), {
	theme: \"".$cfc_theme."\",
	lineNumbers: true,
        matchBrackets: true,\n";
	if ($extension == "css") {
		echo "	mode: \"text/css\",\n";
	} elseif ($extension == "html" || $extension == "htm" || $extension == "txt") {
		echo "	mode: \"text/html\",\n";
	} elseif ($extension == "js") {
		echo "	mode: \"text/javascript\",\n";
	} elseif ($extension == "php") {
        	echo "	mode: \"application/x-httpd-php\",\n";
	}
        echo "	indentUnit: 4,
        indentWithTabs: true,
        enterMode: \"keep\",
        tabMode: \"shift\",
        onKeyEvent: function(i, e) {
		// Hook into F11
		if ((e.keyCode == 122 || e.keyCode == 27) && e.type == 'keydown') {
			e.stop();
			return toggleFullscreenEditing();
		}
		// Hook into ctrl-space
		if (e.keyCode == 32 && (e.ctrlKey || e.metaKey) && !e.altKey) {
			e.stop();
			var editorDiv = jQuery('.CodeMirror-scroll');
			if (!editorDiv.hasClass('fullscreen')) {
				return startComplete();
			}
		}
	},
});

function selectTheme(node) {
	var theme = node.options[node.selectedIndex].innerHTML;
	var editorDiv = jQuery('.CodeMirror-scroll');
	editor.setOption(\"theme\", theme);
	if (editorDiv.hasClass('fullscreen')) {
		var css_file;
		if (theme == 'cobalt') {
			css_file = '".$cfc_plugin_url."css/cobalt.css';
		} else if (theme == 'night') {
			css_file = '".$cfc_plugin_url."css/night.css';
		} else {
			css_file = '".$cfc_plugin_url."css/white.css';
		}
		document.getElementById('codemirror-fullscreen-css').href = css_file;
	}
}

function toggleFullscreenEditing(){
	var editorDiv = jQuery('.CodeMirror-scroll');
	var toolbarDiv = jQuery('#cfc-toolbar');
	if (!editorDiv.hasClass('fullscreen')) {
		var selected_theme = document.getElementById(\"cfc-theme\").value;
		var css_file;
		if (selected_theme == 'cobalt') {
			css_file = '".$cfc_plugin_url."css/cobalt.css';
		} else if (selected_theme == 'night') {
			css_file = '".$cfc_plugin_url."css/night.css';
		} else {
			css_file = '".$cfc_plugin_url."css/white.css';
		}
		document.getElementById('codemirror-fullscreen-css').href = css_file;
		toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
		editorDiv.addClass('fullscreen');
		editorDiv.height('89%');
		editorDiv.width('100%');
		toolbarDiv.addClass('cfc-toolbar-full');
		editor.refresh();
	} else {
		document.getElementById('codemirror-fullscreen-css').href = '".$cfc_plugin_url."css/dummy.css';
		editorDiv.removeClass('fullscreen');
		toolbarDiv.removeClass('cfc-toolbar-full');
		editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
		editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
		editor.refresh();
	}
}
</script>";

echo "\n<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Scripts for CodeMirror End -->
<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Scripts for toolbar Begin -->\n";
?>
<script type="text/javascript">
var lastPos = null, lastQuery = null, marked = [];

function unmark() {
	for (var i = 0; i < marked.length; ++i) marked[i]();
	marked.length = 0;
}

function search() {
	unmark();                     
	var text = document.getElementById("query").value;
	if (!text) return;
	for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
		marked.push(editor.markText(cursor.from(), cursor.to(), "searched"));

	if (lastQuery != text) lastPos = null;
	var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
	if (!cursor.findNext()) {
		cursor = editor.getSearchCursor(text);
		if (!cursor.findNext()) return;
	}
	editor.setSelection(cursor.from(), cursor.to());
	lastQuery = text; lastPos = cursor.to();
}

function replace() {
	unmark();
	var text = document.getElementById("query").value,
	replace = document.getElementById("replace_str").value;
	if (!text) return;
	var cursor = editor.getSearchCursor(text);
	cursor.findNext();
	if (!cursor) return;
	editor.replaceRange(replace, cursor.from(), cursor.to());
	editor.setSelection(cursor.from(), cursor.to());
}

function replace_all() {
	unmark();
	var text = document.getElementById("query").value,
	  replace = document.getElementById("replace_str").value;
	if (!text) return;
	for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
		cursor.replace(replace);
}

function clear_result(){
	lastQuery = null;
	lastPos = null;
	unmark();
	document.getElementById("query").value = '';
	document.getElementById("replace_str").value = '';
}
</script>
<?php echo "<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Scripts for toolbar End -->
<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Scripts for creating toolbar Begin -->\n"; ?>
<script type="text/javascript">
jQuery("#newcontent").after("<div id =\"cfc-toolbar\"><label><?php _e("Theme: ", "cfc_lang") ?></label><select id=\"cfc-theme\" onchange=\"selectTheme(this)\"><option value=\"default\">default</option><option value=\"cobalt\">cobalt</option><option value=\"eclipse\">eclipse</option><option value=\"elegant\">elegant</option><option value=\"neat\">neat</option><option value=\"night\">night</option></select> <input type=\"text\" size=\"12\" id=\"query\" /><button type=\"button\" onclick=\"search()\"><?php _e("Search", "cfc_lang") ?></button> with <input type=\"text\" size=\"12\" id=\"replace_str\" /><button type=\"button\" onclick=\"replace()\"><?php _e("Replace", "cfc_lang") ?></button> <button type=\"button\" onclick=\"replace_all()\"><?php _e("Replace All", "cfc_lang") ?></button> <button type=\"button\" onclick=\"clear_result()\"><?php _e("Clear", "cfc_lang") ?></button></div>");
</script>
<?php echo "<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Scripts for creating toolbar End -->\n"; ?>

<?php }

// Add addtional style into the header
add_action('admin_head-theme-editor.php', 'cfc_additional_style');
add_action('admin_head-plugin-editor.php', 'cfc_additional_style');

function cfc_additional_style() {
	global $cfc_ver;
	echo "\n<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Differential css Begin -->\n";
	echo "<style type=\"text/css\">
.CodeMirror-scroll {height: 600px;overflow: auto; margin-right: 0 !important;}
.CodeMirror-gutter {width: 45px !important;}
#template div {margin-right: 105px;}
.searched {background: yellow;}
.fullscreen {height: 89%; right: 0;position: fixed; top: 80px; width: 100%; z-index: 100;}
#cfc-toolbar {margin-bottom: 2px;}
.cfc-toolbar-full {background-color: #ffffff; min-height: 85px; position: fixed; top: 50px; z-index: 100;}
.completions {position: absolute; z-index: 10; overflow: hidden; -webkit-box-shadow: 2px 3px 5px rgba(0,0,0,.2); -moz-box-shadow: 2px 3px 5px rgba(0,0,0,.2); box-shadow: 2px 3px 5px rgba(0,0,0,.2);}
.completions select {background: #fafafa; outline: none; border: none; padding: 0; margin: 0; font-family: monospace;}
</style>";
	echo "\n<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." Differential css End -->\n";
}

?>