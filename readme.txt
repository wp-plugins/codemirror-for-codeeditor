=== CodeMirror for CodeEditor ===
Contributors: redcocker
Donate link: http://www.near-mint.com/blog/donate
Tags: CodeMirror, syntaxhighlighter, sourcecode, code, syntax, highlight, highlighting, editor, plugin, theme
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: 0.5

Just another code syntaxhighligher for the theme and plugin editor with CodeMirror.

== Description ==

Just another code syntaxhighligher for the theme and plugin editor with CodeMirror. This plugin can highlight sourcecodes in the theme/plugin editor and provide a useful toolbar.

= Features =

* Highlight sourcecodes in theme and plugin editor on the dashboard.
* Based on Marijn Haverbeke's "[CodeMirror](http://codemirror.net/ "CodeMirrorr")" JavaScript library.
* Built-in 8 themes.
* Useful toolbar which includes search/replace features.
* Full-screen editing.
* Auto-complete.
* Easy to configure features through the setting panel.
* Localization: English(Default), 日本語(Japanese, UTF-8).

= Support languages =
* PHP
* CSS
* Javascript
* (X)HTML

= Note =

Ver. 0.5 or higher has the AJAX "Search and Replace" function. If you like old toolbar, please use [ver. 0.4.5](http://wordpress.org/extend/plugins/codemirror-for-codeeditor/download/ "ver. 0.4.5").

= Recommended plugin =

* "[WP SyntaxHighlighter](http://wordpress.org/extend/plugins/wp-syntaxhighlighter/ "WP SyntaxHighlighter")" can highlight sourcecodes on your front-end(posts, pages, comments).

== Installation ==

= Installation =

1. Upload plugin folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. If you need, go to "Settings" -> "CodeMirror for CE" to configure.

= Usage =

* You can select a theme on the toolbar.
* Place the cursor in the textarea and press "Esc" or "F11" key to chage to the "fullscreen mode".
* Search: Press Ctrl-F / Cmd-F
* Find next: Press Ctrl-G / Cmd-G
* Find previous: Press Shift-Ctrl-G / Cmd-Option-G
* Replace: Shift-Ctrl-F / Cmd-Option-F (Run this after searching.)
* Replace all: Shift-Ctrl-R / Shift-Cmd-Option-F (Run this after searching.)
* Press "Ctrl - Space" to activate Auto-omplete.
* Press "Ctrl" - Z" to undo the previous change.
* Press "Ctrl" - Y" to redo.

Note: "Search & replace" and "Auto-omplete" can't work in the "fullscreen mode".

== Screenshots ==

1. This is a highlighted code in the plugin editor.
2. This is AJAX search dialog.
3. This is Auto-omplete Demo.
4. This is setting panel.

== Changelog ==

= 0.5 =
* Updated CodeMirror to ver. 2.2-39.
* Supports "visible tab".
* New "Search" and "Replace" functions.
* Fix a bug: Using bloginfo() in the wrong way.

= 0.4.5 =
* Added new theme "monokai" and "rubyblue".
* Added "Update File" button into the toolbar.
* Renewed "Auto-complete" function.
* Modified the processing of "Search".
* Updated CodeMirror to ver. 2.18.

= 0.4 =
* Updated CodeMirror to ver. 2.16-22(including important bug fixes for IE users).
* Added new variable that has version info of CodeMirror.

= 0.3.6 =
* Added setting link into "Plugins" section.
* Fix a bug: a missing arrow operator.

= 0.3.5 =
* Rewrote the code using class.
* Fix a bug: PHP with HTML, Javascript or CSS can't be highlighted correctly.
* Fix a bug: HTML with Javascript or CSS can't be highlighted correctly.

= 0.3 =
* Added setting panel.
* Modified stylesheet.

= 0.2 =
* Added Search and Replace functions.
* Added Auto-complete.
* The themes become user-selectable in the "fullscreen mode".

= 0.1 =
* This is the initial release.

== Upgrade Notice ==

= 0.5 =
This version has new features and a bug fix.

= 0.4.5 =
This version has new features and changes.

= 0.4 =
This version has new version of CodeMirror(including important bug fixes for IE users).

= 0.3.6 =
This version has a change and bug fix.

= 0.3.5 =
This version has bug fixes.

= 0.3 =
This version has some new features.

= 0.2 =
This version has some new features.

= 0.1 =
This is the initial release.
