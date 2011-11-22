<?php
/*
Plugin Name: CodeMirror for CodeEditor
Plugin URI: http://www.near-mint.com/blog/software/codemirror-for-codeeditor
Description: Just another code syntaxhighligher for the theme and plugin editor with CodeMirror. This plugin can highlight sourcecodes in theme/plugin editor and provide a useful toolbar.
Version: 0.4.5
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: cfc_lang
Domain Path: /languages
*/
/* 
Last modified: 2011/11/22
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
"CodeMirror for CodeEditor" uses CodeMirror2 by Marijn Haverbeke. 
*/

class CodeMirror_for_CodeEditor {
	var $cfc_plugin_url;
	var $cfc_ver = "0.4.5";
	var $cfc_db_ver = "0.3";
	var $cfc_lib_ver = "2.18";
	var $cfc_setting_opt;

	function __construct() {
		load_plugin_textdomain('cfc_lang', false, dirname(plugin_basename(__FILE__)).'/languages');
		$this->cfc_plugin_url = plugin_dir_url(__FILE__);
		$this->cfc_setting_opt = get_option('cfc_setting_opt');
		add_action('plugins_loaded', array(&$this, 'cfc_check_db_ver'));
		add_action('admin_menu', array(&$this, 'cfc_register_menu_item'));
		add_filter( 'plugin_action_links', array(&$this, 'cfc_setting_link'), 10, 2);
		add_action('admin_print_styles-theme-editor.php', array(&$this, 'cfc_load_style'));
		add_action('admin_print_styles-plugin-editor.php', array(&$this, 'cfc_load_style'));
		add_action('admin_print_scripts-theme-editor.php', array(&$this, 'cfc_load_script'));
		add_action('admin_print_scripts-plugin-editor.php', array(&$this, 'cfc_load_script'));
		add_action('admin_footer-theme-editor.php', array(&$this, 'cfc_run_script'));
		add_action('admin_footer-plugin-editor.php', array(&$this, 'cfc_run_script'));
		add_action('admin_head-theme-editor.php', array(&$this, 'cfc_additional_style'));
		add_action('admin_head-plugin-editor.php', array(&$this, 'cfc_additional_style'));
	}

	// Create settings array
	function cfc_setting_array() {
		$cfc_setting_opt = array(
			"codemirror_enable" => 1,
			"theme" => "default",
			"lineNumbers" => "true",
			"gutter" => "false",
			"gutter_size_prefix" => "+",
			"gutter_size" => "0",
			"fixedGutter" => "false",
			"indentUnit" => "4",
			"indentWithTabs" => "false",
			"tabMode" => "shift",
			"enterMode" => "keep",
			"matchBrackets" => "true",
			"electricChars" => "false",
			);
		// Store in DB
		add_option('cfc_setting_opt', $cfc_setting_opt);
		add_option('cfc_updated', 'false');
	}

	// Check DB table version and create table
	function cfc_check_db_ver(){
		$current_checkver_stamp = get_option('cfc_checkver_stamp');
		if (!$current_checkver_stamp || version_compare($current_checkver_stamp, $this->cfc_db_ver, "!=")) {
			// For new installation
			if (!$current_checkver_stamp) {
				// Register array
				$this->cfc_setting_array();
				update_option('cfc_updated', 'true');
			}
			update_option('cfc_checkver_stamp', $this->cfc_db_ver);
		}
	}

	// Register the setting panel and hooks
	function cfc_register_menu_item() {
		$cfc_page_hook = add_options_page('CodeMirror for CodeEditor', 'CodeMirror for CE', 'manage_options', 'codemirror-for-codeeditor-options', array(&$this, 'cfc_options_panel'));
		if ($cfc_page_hook != null) {
			$cfc_page_hook = '-'.$cfc_page_hook;
		}
		add_action('admin_print_scripts'.$cfc_page_hook, array(&$this, 'cfc_load_jscript_for_admin'));
		if (get_option('cfc_updated') == 'true' && !(isset($_POST['CFC_Setting_Submit']) && $_POST['cfc_hidden_value'] == 'true') && !(isset($_POST['CFC_Reset']) && $_POST['cfc_reset'] == 'true')) {
			add_action('admin_notices', array(&$this, 'cfc_admin_updated_notice'));
		}
	}

	// Message for admin when DB table updated
	function cfc_admin_updated_notice(){
    		echo '<div id="message" class="updated"><p>'.__("CodeMirror for CodeEditor has successfully created new DB table.<br />If you upgraded to this version, some setting options may be added or reset to the default values.<br />Go to the <a href=\"options-general.php?page=codemirror-for-codeeditor-options\">setting panel</a> and configure CodeMirror for CodeEditor now. Once you save your settings, this message will be cleared.", "cfc_lang").'</p></div>';
	}

	// Show plugin info in the footer
	function cfc_add_admin_footer() {
		$cfc_plugin_data = get_plugin_data(__FILE__);
		printf('%1$s by %2$s<br />', $cfc_plugin_data['Title'].' '.$cfc_plugin_data['Version'], $cfc_plugin_data['Author']);
	}

	// Register the setting panel
	function cfc_setting_link($links, $file) {
		static $this_plugin;
		if (! $this_plugin) $this_plugin = plugin_basename(__FILE__);
		if ($file == $this_plugin){
			$settings_link = '<a href="options-general.php?page=codemirror-for-codeeditor-options">'.__("Settings", "cfc_lang").'</a>';
			array_unshift($links, $settings_link);
		}  
		return $links;
	}

	// Load script in setting panel
	function cfc_load_jscript_for_admin(){
		wp_enqueue_script('rc_admin_js', $this->cfc_plugin_url.'rc-admin-js.js', false, '1.1');
	}

	// Add styles into the header
	function cfc_load_style() {
		wp_enqueue_style('codemirror', $this->cfc_plugin_url.'codemirror/lib/codemirror.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-default', $this->cfc_plugin_url.'codemirror/theme/default.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-cobalt', $this->cfc_plugin_url.'codemirror/theme/cobalt.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-eclipse', $this->cfc_plugin_url.'codemirror/theme/eclipse.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-elegant', $this->cfc_plugin_url.'codemirror/theme/elegant.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-monokai', $this->cfc_plugin_url.'codemirror/theme/monokai.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-neat', $this->cfc_plugin_url.'codemirror/theme/neat.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-night', $this->cfc_plugin_url.'codemirror/theme/night.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-theme-rubyblue', $this->cfc_plugin_url.'codemirror/theme/rubyblue.css', false, $this->cfc_lib_ver);
		wp_enqueue_style('codemirror-simple-hint', $this->cfc_plugin_url.'codemirror/lib/util/simple-hint.css', false, $this->cfc_lib_ver);
	}

	// Add scripts into the header
	function cfc_load_script() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('codemirror', $this->cfc_plugin_url.'codemirror/lib/codemirror.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-mode-css', $this->cfc_plugin_url.'codemirror/mode/css/css.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-mode-js', $this->cfc_plugin_url.'codemirror/mode/javascript/javascript.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-mode-xml', $this->cfc_plugin_url.'codemirror/mode/xml/xml.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-mode-clike', $this->cfc_plugin_url.'codemirror/mode/clike/clike.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-mode-php', $this->cfc_plugin_url.'codemirror/mode/php/php.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-mode-htmlmixed', $this->cfc_plugin_url.'codemirror/mode/htmlmixed/htmlmixed.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-simple-hint-js', $this->cfc_plugin_url.'codemirror/lib/util/simple-hint.js', false, $this->cfc_lib_ver);
		wp_enqueue_script('codemirror-js-hint', $this->cfc_plugin_url.'codemirror/lib/util/javascript-hint.js', false, $this->cfc_lib_ver);
	}

	// Add scripts into the footer
	function cfc_run_script() {
		global $pagenow;
		$cfc_setting_opt = $this->cfc_setting_opt;
		if (isset($_GET['file'])) {
			$extension = strtolower(pathinfo(esc_html($_GET['file']), PATHINFO_EXTENSION));
		} else {
			if ($pagenow == "theme-editor.php") {
				$extension = "css";
			} elseif ($pagenow == "plugin-editor.php") {
				$extension = "php";
			}
		}
		echo "\n<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Scripts for CodeMirror Begin -->
<script type=\"text/javascript\">
var editor = CodeMirror.fromTextArea(document.getElementById(\"newcontent\"), {
	theme: \"".$cfc_setting_opt['theme']."\",
	lineNumbers: ".$cfc_setting_opt['lineNumbers'].",
        matchBrackets: ".$cfc_setting_opt['matchBrackets'].",\n";
		if ($cfc_setting_opt['codemirror_enable'] == 1) {
			if ($extension == "css") {
				echo "	mode: \"text/css\",\n";
			} elseif ($extension == "html" || $extension == "htm") {
				echo "	mode: \"text/html\",\n";
			} elseif ($extension == "js") {
				echo "	mode: \"text/javascript\",\n";
			} elseif ($extension == "php") {
        			echo "	mode: \"application/x-httpd-php\",\n";
			} else {
				echo "	mode: \"text/plain\",\n";
			}
		} elseif ($cfc_setting_opt['codemirror_enable'] != 1) {
			echo "	mode: \"text/plain\",\n";
		}
        	echo "	indentUnit: ".$cfc_setting_opt['indentUnit'].",
        indentWithTabs: ".$cfc_setting_opt['indentWithTabs'].",
        enterMode: \"".$cfc_setting_opt['enterMode']."\",
        tabMode: \"".$cfc_setting_opt['tabMode']."\",
	gutter: ".$cfc_setting_opt['gutter'].",
	fixedGutter: ".$cfc_setting_opt['fixedGutter'].",
	electricChars: ".$cfc_setting_opt['electricChars'].",\n";
		if ($cfc_setting_opt['gutter'] == 'true') {
        		echo "	onGutterClick: function(cm, n) {
		var info = cm.lineInfo(n);
		if (info.markerText)
			cm.clearMarker(n);
		else
			cm.setMarker(n, \"● %N%\");
	},\n";
		}
        	echo "	onKeyEvent: function(cm, e) {
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
				return CodeMirror.simpleHint(cm, CodeMirror.javascriptHint);
			}
		}
	},
});

function selectTheme(node) {
	var theme = node.options[node.selectedIndex].innerHTML;
	var editorDiv = jQuery('.CodeMirror-scroll');
	editor.setOption(\"theme\", theme);
}

function toggleFullscreenEditing(){
	var editorDiv = jQuery('.CodeMirror-scroll');
	var toolbarDiv = jQuery('#cfc-toolbar');
	if (!editorDiv.hasClass('fullscreen')) {
		toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
		editorDiv.addClass('fullscreen');
		editorDiv.height('89%');
		editorDiv.width('100%');
		toolbarDiv.addClass('cfc-toolbar-full');
		editor.refresh();
	} else {
		editorDiv.removeClass('fullscreen');
		toolbarDiv.removeClass('cfc-toolbar-full');
		editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
		editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
		editor.refresh();
	}
}
</script>";

		echo "\n<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Scripts for CodeMirror End -->
<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Scripts for toolbar Begin -->\n";
		?>
<script type="text/javascript">
var lastPos = null, lastQuery = null, marked = [];

function unmark() {
	for (var i = 0; i < marked.length; ++i) marked[i].clear();
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

function save_all() {
	document.getElementById('submit').click();
};
</script>
		<?php
		echo "<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Scripts for toolbar End -->
<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Scripts for creating toolbar Begin -->
<script type=\"text/javascript\">\n";
		echo 'jQuery("#newcontent").after("<div id =\"cfc-toolbar\"><label>'.__("Theme: ", "cfc_lang").'</label><select id=\"cfc-theme\" onchange=\"selectTheme(this)\">';

		$theme_list = array("default", "cobalt", "eclipse", "elegant", "monokai", "neat", "night", "rubyblue");
		foreach ($theme_list as $val) {
			if ($val == $cfc_setting_opt['theme']) {
				echo '<option value=\"'.$val.'\" selected=\"selected\">'.$val.'</option>';
			} else {
				echo '<option value=\"'.$val.'\">'.$val.'</option>';
			}
		}

		echo '</select> <input type=\"text\" size=\"12\" id=\"query\" /><button type=\"button\" onclick=\"search()\">'.__("Search", "cfc_lang").'</button> '.__("with", "cfc_lang").' <input type=\"text\" size=\"12\" id=\"replace_str\" /><button type=\"button\" onclick=\"replace()\">'.__("Replace", "cfc_lang").'</button> <button type=\"button\" onclick=\"replace_all()\">'.__("Replace All", "cfc_lang").'</button> <button type=\"button\" onclick=\"clear_result()\">'.__("Clear", "cfc_lang").'</button> <button type=\"button\" class=\"button-primary\" onclick=\"save_all()\">'.__("Update File", "cfc_lang").'</button></div>");
</script>';
		echo "<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Scripts for creating toolbar End -->\n";
	}

	// Add addtional style into the header
	function cfc_additional_style() {
		$cfc_setting_opt = $this->cfc_setting_opt;
		if ($cfc_setting_opt['gutter_size_prefix'] == "+") {
			$gutter_width_full = 55 + $cfc_setting_opt['gutter_size'];
			$gutter_width = 45 + $cfc_setting_opt['gutter_size'];
			$gutter_width_small = 30 + $cfc_setting_opt['gutter_size'];
		} elseif ($cfc_setting_opt['gutter_size_prefix'] == "-") {
			$gutter_width_full = 55 - $cfc_setting_opt['gutter_size'];
			$gutter_width = 45 - $cfc_setting_opt['gutter_size'];
			$gutter_width_small = 30 - $cfc_setting_opt['gutter_size'];
		}
		echo "\n<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Differential css Begin -->\n";
		echo "<style type=\"text/css\">
.CodeMirror {border: 1px solid #aaa;}
.CodeMirror-scroll {height: 600px; overflow: auto; margin-right: 0 !important;}\n";
		if ($cfc_setting_opt['gutter'] == 'true' && $cfc_setting_opt['lineNumbers'] == 'true') {
			echo ".CodeMirror-gutter {width: ".$gutter_width_full."px; !important;}
.CodeMirror-gutter-text {width: ".($gutter_width_full - 7)."px; !important;}\n";
		} elseif ($cfc_setting_opt['gutter'] == 'false' && $cfc_setting_opt['lineNumbers'] == 'true') {
			echo ".CodeMirror-gutter {width: ".$gutter_width."px; !important;}
.CodeMirror-gutter-text {width: ".($gutter_width - 7)."px; !important;}\n";
		} elseif ($cfc_setting_opt['gutter'] == 'true' && $cfc_setting_opt['lineNumbers'] == 'false') {
			echo ".CodeMirror-gutter {width: ".$gutter_width_small."px !important;}
.CodeMirror-gutter-text {width: ".($gutter_width_small - 10)."px !important;}\n";
		}
		echo "#template div {margin-right: 105px;}
.searched {background: yellow;}
.fullscreen {height: 89%; right: 0; position: fixed; top: 80px; width: 100%; z-index: 100;}
#cfc-toolbar {margin-bottom: 2px;}
.cfc-toolbar-full {background-color: #ffffff; min-height: 85px; position: fixed; top: 50px; z-index: 100;}
.cm-s-cobalt {background: #002240;}
.cm-s-default {background: #ffffff;}
.cm-s-eclipse {background: #ffffff;}
.cm-s-elegant {background: #ffffff;}
.cm-s-monokai {background: #272822;}
.cm-s-neat {background: #ffffff;}
.cm-s-night {background: #0a001f;}
.cm-s-rubyblue {background: #112435;}
</style>";
		echo "\n<!-- CodeMirror for CodeEditor Ver.".$this->cfc_ver." Differential css End -->\n";
	}

	// Setting panel
	function cfc_options_panel(){
		$cfc_setting_opt = $this->cfc_setting_opt;
		if(!function_exists('current_user_can') || !current_user_can('manage_options')){
			die(__('Cheatin&#8217; uh?'));
		} 
		add_action('in_admin_footer', array(&$this, 'cfc_add_admin_footer'));

		// Update setting options
		if (isset($_POST['CFC_Setting_Submit']) && $_POST['cfc_hidden_value'] == 'true' && check_admin_referer("cfc_update_options", "_wpnonce_update_options")) {
			if ($_POST['codemirror_enable'] == "1") {
				$cfc_setting_opt['codemirror_enable'] = "1";
			} else {
				$cfc_setting_opt['codemirror_enable'] = "0";
			}
			$cfc_setting_opt['theme'] = $_POST['theme'];
			if ($_POST['lineNumbers'] == "1") {
				$cfc_setting_opt['lineNumbers'] = 'true';
			} else {
				$cfc_setting_opt['lineNumbers'] = 'false';
			}
			if ($_POST['gutter'] == "1") {
				$cfc_setting_opt['gutter'] = 'true';
			} else {
				$cfc_setting_opt['gutter'] = 'false';
			}
			$cfc_setting_opt['gutter_size_prefix'] = $_POST['gutter_size_prefix'];
			$cfc_setting_opt['gutter_size'] = $_POST['gutter_size'];
			if ($_POST['fixedGutter'] == "1") {
				$cfc_setting_opt['fixedGutter'] = 'true';
			} else {
				$cfc_setting_opt['fixedGutter'] = 'false';
			}
			$cfc_setting_opt['indentUnit'] = $_POST['indentUnit'];
			if ($_POST['indentWithTabs'] == "1") {
				$cfc_setting_opt['indentWithTabs'] = 'true';
			} else {
				$cfc_setting_opt['indentWithTabs'] = 'false';
			}
			$cfc_setting_opt['tabMode'] = $_POST['tabMode'];
			$cfc_setting_opt['enterMode'] = $_POST['enterMode'];
			if ($_POST['matchBrackets'] == "1") {
				$cfc_setting_opt['matchBrackets'] = 'true';
			} else {
				$cfc_setting_opt['matchBrackets'] = 'false';
			}
			if ($_POST['electricChars'] == "1") {
				$cfc_setting_opt['electricChars'] = 'true';
			} else {
				$cfc_setting_opt['electricChars'] = 'false';
			}
			// Validate values
			if (!preg_match("/^[0-9]+$/", $cfc_setting_opt['gutter_size'])) {
				wp_die(__("Invalid value. Settings could not be saved.<br />Your \"Gutter Size\" must be entered in numbers.", "cfc_lang"));
			}
			if (!preg_match("/^[0-9]+$/", $cfc_setting_opt['indentUnit'])) {
				wp_die(__("Invalid value. Settings could not be saved.<br />Your \"Tab size\" must be entered in numbers.", "cfc_lang"));
			}
			// Store in DB
			update_option('cfc_setting_opt', $cfc_setting_opt);
			update_option('cfc_updated', 'false');
			// Show message for admin
			echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("Settings saved.","cfc_lang")."</strong></p></div>";
		}

		// Reset all settings
		if (isset($_POST['CFC_Reset']) && $_POST['cfc_reset'] == 'true' && check_admin_referer("cfc_reset_options", "_wpnonce_reset_options")) {
			include_once('uninstall.php');
			$this->cfc_setting_array();
			update_option('cfc_checkver_stamp', $this->cfc_db_ver);
			// Show message for admin
			echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("All settings were reset. Please <a href=\"options-general.php?page=codemirror-for-codeeditor-options\">reload the page</a>.", "cfc_lang")."</strong></p></div>";
		}

		?> 
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>CodeMirror for CodeEditor</h2>
	<form method="post" action="">
	<?php wp_nonce_field("cfc_update_options", "_wpnonce_update_options"); ?>
	<input type="hidden" name="cfc_hidden_value" value="true" />
	<h3><?php _e("1. Settings", "cfc_lang") ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e("Highlight sourcecodes", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="codemirror_enable" value="1" <?php if ($cfc_setting_opt['codemirror_enable'] == 1) {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("Enable/Disable code highlighting.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Theme", "cfc_lang") ?></th>
				<td>
					<select name="theme">
						<option value="default" <?php if ($cfc_setting_opt['theme'] == "default") {echo 'selected="selected"';} ?>>default</option>
						<option value="cobalt" <?php if ($cfc_setting_opt['theme'] == "cobalt") {echo 'selected="selected"';} ?>>cobalt</option>
						<option value="eclipse" <?php if ($cfc_setting_opt['theme'] == "eclipse") {echo 'selected="selected"';} ?>>eclipse</option>
						<option value="elegant" <?php if ($cfc_setting_opt['theme'] == "elegant") {echo 'selected="selected"';} ?>>elegant</option>
						<option value="monokai" <?php if ($cfc_setting_opt['theme'] == "monokai") {echo 'selected="selected"';} ?>>monokai</option>
						<option value="neat" <?php if ($cfc_setting_opt['theme'] == "neat") {echo 'selected="selected"';} ?>>neat</option>
						<option value="night" <?php if ($cfc_setting_opt['theme'] == "night") {echo 'selected="selected"';} ?>>night</option>
						<option value="rubyblue" <?php if ($cfc_setting_opt['theme'] == "rubyblue") {echo 'selected="selected"';} ?>>rubyblue</option>
					</select>
					<p><small><?php _e("Select a theme.<br />You can also select another theme on the editor.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Show linenumber", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="lineNumbers" value="1" <?php if ($cfc_setting_opt['lineNumbers'] == 'true') {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("Show/Hide linenumber.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Show gutter", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="gutter" value="1" <?php if ($cfc_setting_opt['gutter'] == 'true') {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("Show/Hide gutter. You can set a marker icon on the gutter by clicking linenumber.<br />Even when linenumber is hidden, the gutter will be shown.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Gutter size", "cfc_lang") ?></th>
				<td>
					<select name="gutter_size_prefix">
						<option value="+" <?php if ($cfc_setting_opt['gutter_size_prefix'] == "+") {echo 'selected="selected"';} ?>>+</option>
						<option value="-" <?php if ($cfc_setting_opt['gutter_size_prefix'] == "-") {echo 'selected="selected"';} ?>>-</option>
					</select> <input type="text" name="gutter_size" size="2" value="<?php echo $cfc_setting_opt['gutter_size']; ?>" /> <?php _e("pixel", "cfc_lang") ?>
					<p><small><?php _e("You can resize the row for the linenumber and gutter.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Fixed gutter", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="fixedGutter" value="1" <?php if ($cfc_setting_opt['fixedGutter'] == 'true') {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("When you scroll the textarea horizontally, the gutter will stay visible.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Tab size", "cfc_lang") ?></strong></th>
				<td>
					<input type="text" name="indentUnit" size="2" value="<?php echo $cfc_setting_opt['indentUnit']; ?>" />
					<p><small><?php _e("Set the tab(indent) size by the number of space characters.<br />For detail, refer to <a href=\"http://codemirror.net/manual.html#option_indentUnit\">\"indentUnit\" section</a> in the manual.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Indent with tabs", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="indentWithTabs" value="1" <?php if ($cfc_setting_opt['indentWithTabs'] == 'true') {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("The leading every multiple of 8 spaces will be replaced with tabs.<br />For detail, refer to <a href=\"http://codemirror.net/manual.html#option_indentWithTabs\">\"indentWithTabs\" section</a> in the manual.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Tab mode", "cfc_lang") ?></th> 
				<td>
					<select name="tabMode">
						<option value="classic" <?php if ($cfc_setting_opt['tabMode'] == "classic") {echo 'selected="selected"';} ?>>classic</option>
						<option value="shift" <?php if ($cfc_setting_opt['tabMode'] == "shift") {echo 'selected="selected"';} ?>>shift</option>
						<option value="indent" <?php if ($cfc_setting_opt['tabMode'] == "indent") {echo 'selected="selected"';} ?>>indent</option>
						<option value="default" <?php if ($cfc_setting_opt['tabMode'] == "default") {echo 'selected="selected"';} ?>>default</option>
					</select>
					<p><small><?php _e("Determine what happens when the tab key is pressed.<br />For detail, refer to <a href=\"http://codemirror.net/manual.html#option_tabMode\">\"tabMode\" section</a> in the manual.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Indentation for a new line", "cfc_lang") ?></th>
				<td>
					<select name="enterMode">
						<option value="indent" <?php if ($cfc_setting_opt['enterMode'] == "indent") {echo 'selected="selected"';} ?>>indent</option>
						<option value="keep" <?php if ($cfc_setting_opt['enterMode'] == "keep") {echo 'selected="selected"';} ?>>keep</option>
						<option value="flat" <?php if ($cfc_setting_opt['enterMode'] == "flat") {echo 'selected="selected"';} ?>>flat</option>
					</select>
					<p><small><?php _e("Determines whether and how new lines are indented.<br />For detail, refer to <a href=\"http://codemirror.net/manual.html#option_enterMode\">\"enterMode\" section</a> in the manual.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Highlight pair of brackets", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="matchBrackets" value="1" <?php if ($cfc_setting_opt['matchBrackets'] == 'true') {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("The pair of brackets will be highlighted when a bracket is selected with your mouse.<br />For detail, refer to <a href=\"http://codemirror.net/manual.html#matchBrackets\">\"matchBrackets\" section</a> in the manual.", "cfc_lang") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Auto indentation", "cfc_lang") ?></th>
				<td>
					<input type="checkbox" name="electricChars" value="1" <?php if ($cfc_setting_opt['electricChars'] == 'true') {echo 'checked=\"checked\"';} ?>/> <?php _e("Enable", "cfc_lang") ?>
					<p><small><?php _e("When some constructional characters are typed, they will be indented automatically and automatically.<br/ >For example \"{ }\" in \"{ }\".<br />For detail, refer to <a href=\"http://codemirror.net/manual.html#option_electricChars\">\"electricChars\" section</a> in the manual.", "cfc_lang") ?></small></p>
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="CFC_Setting_Submit" value="<?php _e("Save Changes", "cfc_lang") ?>" />
		</p>
	</form>
	<h3><?php _e("2. Restore all settings to default", "cfc_lang") ?></h3>
	<form method="post" action="" onsubmit="return confirmreset()">
	<?php wp_nonce_field("cfc_reset_options", "_wpnonce_reset_options"); ?>
		<p class="submit">
		<input type="hidden" name="cfc_reset" value="true" />
		<input type="submit" name="CFC_Reset" value="<?php _e("Reset All Settings", "cfc_lang") ?>" />
		</p>
	</form>
	<h3><a href="javascript:showhide('id1');" name="system_info"><?php _e("3. Your System Info", "cfc_lang") ?></a></h3>
	<div id="id1" style="display:none; margin-left:20px">
		<p>
		<?php _e("Server OS:", "cfc_lang") ?> <?php echo php_uname('s').' '.php_uname('r'); ?><br />
		<?php _e("PHP version:", "cfc_lang") ?> <?php echo phpversion(); ?><br />
		<?php _e("MySQL version:", "cfc_lang") ?> <?php echo mysql_get_server_info(); ?><br />
		<?php _e("WordPress version:", "cfc_lang") ?> <?php bloginfo("version"); ?><br />
		<?php _e("Site URL:", "cfc_lang") ?> <?php bloginfo("url"); ?><br />
		<?php _e("WordPress URL:", "cfc_lang") ?> <?php bloginfo("wpurl"); ?><br />
		<?php _e("WordPress language:", "cfc_lang") ?> <?php bloginfo("language"); ?><br />
		<?php _e("WordPress character set:", "cfc_lang") ?> <?php bloginfo("charset"); ?><br />
		<?php _e("WordPress theme:", "cfc_lang") ?> <?php $cfc_theme = get_theme(get_current_theme()); echo $cfc_theme['Name'].' '.$cfc_theme['Version']; ?><br />
		<?php _e("CodeMirror for CodeEditor version:", "cfc_lang") ?> <?php $cfc_plugin_data = get_plugin_data(__FILE__); echo $cfc_plugin_data['Version']; ?><br />
		<?php _e("CodeMirror for CodeEditor DB version:", "cfc_lang") ?> <?php echo get_option('cfc_checkver_stamp'); ?><br />

		<?php _e("CodeMirror version:", "cfc_lang") ?> <?php echo $this->cfc_lib_ver; ?><br />
		<?php _e("CodeMirror for CodeEditor URL:", "cfc_lang") ?> <?php echo $this->cfc_plugin_url; ?><br />
		<?php _e("Your browser:", "cfc_lang") ?> <?php echo esc_html($_SERVER['HTTP_USER_AGENT']); ?>
		</p>
	</div>
	<p>
		<?php _e("To report a bug ,submit requests and feedback, ", "cfc_lang") ?><?php _e("Use <a href=\"http://wordpress.org/tags/codemirror-for-codeeditor?forum_id=10\">Forum</a> or <a href=\"http://www.near-mint.com/blog/contact\">Mail From</a>", "cfc_lang") ?>
	</p>
</div>
	<?php } 
}

// Start this plugin
$CodeMirror_for_CodeEditor = new CodeMirror_for_CodeEditor();

?>