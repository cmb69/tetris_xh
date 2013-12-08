<?php

/**
 * Administration of Tetris_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Tetris
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2013 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Tetris_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Returns the plugin version information view.
 *
 * @return string (X)HTML
 *
 * @global array The paths of system files and folders.
 */
function Tetris_version()
{
    global $pth;

    $icon = tag(
        'img src="' . $pth['folder']['plugins']
        . 'tetris/tetris.png" alt="Plugin Icon"'
    );
    $version = TETRIS_VERSION;
    return <<<HTM

<h1><a href="http://3-magi.net/?CMSimple_XH/Tetris_XH">Tetris_XH</a></h1>
<div style="float:left; margin-right: 1em">$icon</div>
<p>Version: $version</p>
<p>Tetris_XH is powered by <a
    href="http://www.cmsimple-xh.org/wiki/doku.php/extend:jquery4cmsimple"
    target="_blank">jQuery4CMSimple</a> and <a
    href="http://fmarcia.info/jquery/tetris/tetris.html" target="_blank">
    Tetris with jQuery</a>.
</p>
<p>Copyright &copy; 2011-2013 Christoph M. Becker</p>
<p style="text-align:justify">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
</p>
<p style="text-align:justify">
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
</p>
<p style="text-align:justify">
    You should have received a copy of the GNU General Public License
    along with this program. If not, see
    <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>

HTM;
}

/**
 * Returns system check view.
 *
 * @return string (X)HTML
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Tetris_systemCheck()
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '4.3.10';
    $ptx = $plugin_tx['tetris'];
    $imgdir = $pth['folder']['plugins'] . 'tetris/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = tag('hr') . '<h4>' . $ptx['syscheck_title'] . '</h4>'
        . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
        . tag('br') . tag('br') . PHP_EOL;
    foreach (array('pcre', 'session') as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            . tag('br') . PHP_EOL;
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
        . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br') . PHP_EOL;
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br') . PHP_EOL;
    $state = file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php')
        ? $ok
        : $fail;
    $o .= $state . '&nbsp;&nbsp;' . $ptx['syscheck_jquery']
        . tag('br') . tag('br') . PHP_EOL;
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'tetris/' . $folder;
    }
    $folders[] = Tetris_dataFolder();
    foreach ($folders as $folder) {
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . tag('br') . PHP_EOL;
    }
    return $o;
}

/**
 * Handle the plugin administration.
 */
if (isset($tetris) && $tetris == 'true') {
    $o .= print_plugin_admin('off');
    switch ($admin) {
    case '':
        $o .= Tetris_version() . Tetris_systemCheck();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
