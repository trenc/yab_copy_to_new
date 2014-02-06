<?php

$plugin['name'] = 'yab_copy_to_new';
$plugin['allow_html_help'] = 0;
$plugin['version'] = '0.1';
$plugin['author'] = 'Tommy Schmucker';
$plugin['author_uri'] = 'http://www.yablo.de/';
$plugin['description'] = 'Copy the current article content to a new one.';
$plugin['order'] = '5';
$plugin['type'] = '3';

if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001);
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002);

$plugin['flags'] = '0';

// Plugin textpack
$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
yab_copy_to_new => Copy
#@language en-us
yab_copy_to_new => Copy
#@language de-de
yab_copy_to_new => Kopieren
#@language fr-fr
yab_copy_to_new => Copie
#@language es-es
yab_copy_to_new => Copia
#@language it-it
yab_copy_to_new => Copia
#@language fi-fi
yab_copy_to_new => Kopio
#@language nl-nl
yab_copy_to_new => Kopie
#@language ru-ru
yab_copy_to_new => Копия
EOT;

if (!defined('txpinterface'))
{
	@include_once('zem_tpl.php');
}

# --- BEGIN PLUGIN CODE ---
/**
 * yab_copy_to_new
 *
 * A Textpattern CMS plugin.
 * Copy the current article content to a new one.
 *
 * @author Tommy Schmucker
 * @link   http://www.yablo.de/
 * @link   http://tommyschmucker.de/
 * @date   2014-02-06
 *
 * This plugin is released under the GNU General Public License Version 2 and above
 * Version 2: http://www.gnu.org/licenses/gpl-2.0.html
 * Version 3: http://www.gnu.org/licenses/gpl-3.0.html
 */

if (@txpinterface == 'admin')
{
	register_callback(
		'yab_copy_to_new',
		'admin_side',
		'body_end'
	);
}

/**
 * Echo the plugin JavaScript on article write tab..
 *
 * @return void Echos the JavaScript
 */
function yab_copy_to_new()
{
	global $event;

	// Config: name attribute for excluded fields (JavaScript array)
	$exclude = '[
		"url_title",
		"year",
		"month",
		"day",
		"hour",
		"minute",
		"second"
	]';

	$buttontext = gTxt('yab_copy_to_new');

	$js = <<<EOF
<script>
(function() {

	// ECMAScript < 5 indexOf method
	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function (searchElement /*, fromIndex */ ) {
			"use strict";
			if (this == null) {
				throw new TypeError();
			}
			var t = Object(this);
			var len = t.length >>> 0;
			if (len === 0) {
				return -1;
			}
			var n = 0;
			if (arguments.length > 0) {
				n = Number(arguments[1]);
				if (n != n) { // shortcut for verifying if it's NaN
					n = 0;
				} else if (n != 0 && n != Infinity && n != -Infinity) {
					n = (n > 0 || -1) * Math.floor(Math.abs(n));
				}
			}
			if (n >= len) {
				return -1;
			}
			var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
			for (; k < len; k++) {
				if (k in t && t[k] === searchElement) {
					return k;
				}
			}
			return -1;
		}
	}

	var form2string = function(txp_form) {
		return JSON.stringify(txp_form.serializeArray());
	}

	var string2form = function(txp_form, serialized) {
		var excludes = $exclude;
		var fields = JSON.parse(serialized);
		var flength = fields.length;
		for (var i = 0; i < flength; i++) {
			var e_name = fields[i].name;
			var e_value = fields[i].value;
			var e = txp_form.find('[name="' + e_name + '"]');

			if (e.is(':hidden') || excludes.indexOf(e_name) != -1) {
				continue;
			} else if (e.is(':radio')) {
				e.filter('[value=\'' + e_value + '\']').prop('checked', true);
			} else if (e.is(':checkbox') && e_value) {
				e.prop('checked', true);
			} else if (e.is('select')) {
				e.find('[value=\'' + e_value + '\']').prop('selected', true);
			} else {
				e.val(e_value);
			}
		}
	}

	var j_form = $('#article_form');

	var button = '<button id="yab-copy-to-new" class="publish" tabindex="5" style="margin-left: 0.5em">$buttontext</button>';

	$('#write-publish, #write-save').append(button);

		j_form.on('click', '#yab-copy-to-new', function(ev) {
		ev.preventDefault();
		var form_string = form2string(j_form);
		sessionStorage.setItem('yab_copy_to_new_form', form_string);
		window.location.href = 'index.php?event=article';
	});

		var form_string = sessionStorage.getItem('yab_copy_to_new_form');
		if (form_string) {
			string2form(j_form, form_string);
			sessionStorage.removeItem('yab_copy_to_new_form');
		}

})();
</script>
EOF;

	if ($event == 'article')
	{
		echo $js;
	}
	return;
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. yab_copy_to_new

p. Displays a new button in article write tab to copy the current article to a new one.

p. *Version:* 0.1

h2. Table of contents

# "Plugin requirements":#help-section02
# "Configuration":#help-config03
# "Changelog":#help-section10
# "License":#help-section11
# "Author contact":#help-section12

h2(#help-section02). Plugin requirements

p. yab_copy_to_new's  minimum requirements:

* Textpattern 4.x
* Modern browser capable of HTML5 sessionStorage

h2(#help-config03). Configuration

Install and activate the plugin.
The following form fields will not be copied by default:
* all of hidden type
* an exclude array of posted day and time and the url_title
You can modify this exclude array on your own, it's the first variable in the yab_copy_to_new() function.

h2(#help-section10). Changelog

* v0.1: 2014-02-06
** initial release

h2(#help-section11). Licence

This plugin is released under the GNU General Public License Version 2 and above
* Version 2: "http://www.gnu.org/licenses/gpl-2.0.html":http://www.gnu.org/licenses/gpl-2.0.html
* Version 3: "http://www.gnu.org/licenses/gpl-3.0.html":http://www.gnu.org/licenses/gpl-3.0.html

h2(#help-section12). Author contact

* "Plugin on author's site":http://www.yablo.de/article/479/yab_copy_to_new-copy-the-current-article-content-to-a-new-one
* "Plugin on GitHub":https://github.com/trenc/yab_copy_to_new
* "Plugin on textpattern forum":http://forum.textpattern.com/viewtopic.php?pid=278692
* "Plugin on textpattern.org":http://textpattern.org/plugins/1289/yab_copy_to_new
# --- END PLUGIN HELP ---
-->
<?php
}
?>