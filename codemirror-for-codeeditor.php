<?php
/*
Plugin Name: CodeMirror for CodeEditor
Plugin URI: http://www.near-mint.com/blog/software/codemirror-for-codeeditor
Description: Just another code syntaxhighligher for theme and plugn editor with CodeMirror.
Version: 0.1
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: cfc_lang
Domain Path: /languages
*/
/* 
Last modified: 2011/9/30
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
$cfc_ver = "0.1";
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
}

// Add scripts into the footer
add_action('admin_footer-theme-editor.php', 'cfc_run_script');
add_action('admin_footer-plugin-editor.php', 'cfc_run_script');

function cfc_run_script() {
	global $cfc_theme, $pagenow;
	if (isset($_GET['file'])) {
		$extension = strtolower(pathinfo(esc_html($_GET['file']), PATHINFO_EXTENSION));
	} else {
		if ($pagenow == "theme-editor.php") {
			$extension = "css";
		} elseif ($pagenow == "plugin-editor.php") {
			$extension = "php";
		}
	}
	echo "\n<script type=\"text/javascript\">
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
	}
});
function selectTheme(node) {
	var theme = node.options[node.selectedIndex].innerHTML;
	editor.setOption(\"theme\", theme);
}
function toggleFullscreenEditing(){
	var editorDiv = jQuery('.CodeMirror-scroll');
	var toolbarDiv = jQuery('.ace');
	if (!editorDiv.hasClass('fullscreen')) {
		toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
		editorDiv.addClass('fullscreen');
		editorDiv.height('95%');
		editorDiv.width('100%');
		toolbarDiv.addClass('ace_ToolBar');
		editor.refresh();
	} else {
		editorDiv.removeClass('fullscreen');
		toolbarDiv.removeClass('ace_ToolBar');
		editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
		editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
		editor.refresh();
	}
}
</script>\n";

?>
<script type="text/javascript">
jQuery("#newcontent").after("<div id =\"cfc-selecter\"><label><?php _e("Select a theme: ", "cfc_lang") ?></label><select id=\"cfc-theme\" onchange=\"selectTheme(this)\"><option>default</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>neat</option><option>night</option></select></div>");
</script>
<?php }

// Add addtional style into the header
add_action('admin_head-theme-editor.php', 'cfc_additional_style');
add_action('admin_head-plugin-editor.php', 'cfc_additional_style');

function cfc_additional_style() {
	global $cfc_ver;
	echo "\n<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." CSS Begin -->\n";
	echo "<style>
.CodeMirror-scroll {height: 600px;overflow: auto; margin-right: 0 !important;}
.CodeMirror-gutter {width: 35px !important;}
#template div {margin-right: 105px;}
.activeline {background: #f0fcff !important;}
.fullscreen{background-color: #FFFFFF; height: 95%; right: 0;position: fixed; top: 40px; width: 100%; z-index: 100;}
#cfc-selecter {margin-bottom: 2px}
</style>\n";
	echo "<!-- CodeMirror for CodeEditor Ver.".$cfc_ver." CSS End -->\n";
}

?>