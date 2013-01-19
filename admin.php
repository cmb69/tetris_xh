<?php

/**
 * Backend-functionality of Tetris_XH.
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns (x)html plugin version information.
 *
 * @global array  The paths of system files and folders.
 * @return string
 */
function tetris_version()
{
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Tetris_XH">Tetris_XH</a></h1>'."\n"
	    .'<div style="float:left; margin-right: 1em">'.tag('img src="'.$pth['folder']['plugins'].'tetris/tetris.png" alt="Plugin Icon"').'</div>'
	    .'<p>Version: '.TETRIS_VERSION.'</p>'."\n"
	    .'<p>Tetris_XH is powered by '
	    .'<a href="http://www.cmsimple-xh.org/wiki/doku.php/extend:jquery4cmsimple" target="_blank">'
	    .'jQuery4CMSimple</a>'
	    .' and <a href="http://fmarcia.info/jquery/tetris/tetris.html" target="_blank">Tetris with jQuery</a>.</p>'."\n"
	    .'<p>Copyright &copy; 2011-2013 Christoph M. Becker</p>'."\n"
	    .'<p style="text-align:justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align:justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align:justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns requirements information.
 *
 * @return string
 */
function tetris_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('TETRIS_PHP_VERSION', '4.3.0');
    $ptx =& $plugin_tx['tetris'];
    $imgdir = $pth['folder']['plugins'].'tetris/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $htm = tag('hr').'<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, TETRIS_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], TETRIS_PHP_VERSION)
	    .tag('br').tag('br')."\n";
    foreach (array('date', 'pcre', 'session') as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $htm .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br')."\n";
    $htm .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br')."\n";
    $htm .= (file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php') ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_jquery'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'tetris/'.$folder;
    }
    $folders[] = tetris_data_folder();
    foreach ($folders as $folder) {
	$htm .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $htm;
}


/**
 * Plugin administration
 */
if (isset($tetris)) {
    initvar('admin');
    initvar('action');

    $o .= print_plugin_admin('off');

    switch ($admin) {
	case '':
	    $o .= tetris_version().tetris_system_check();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
