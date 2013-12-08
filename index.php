<?php

/**
 * Front-end functionality of Tetris_XH.
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

/**
 * The version number.
 */
define('TETRIS_VERSION', '@TETRIS_VERSION@');

/**
 * Returns the path of the data folder.
 *
 * @return string
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the plugins. * 
 */
function Tetris_dataFolder()
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['tetris'];

    if ($pcf['folder_data'] == '') {
        $fn = $pth['folder']['plugins'] . 'tetris/data/';
    } else {
        $fn = $pth['folder']['base'] . $pcf['folder_data'];
    }
    if (substr($fn, -1) != '/') {
        $fn .= '/';
    }
    if (file_exists($fn)) {
        if (!is_dir($fn)) {
            e('cntopen', 'folder', $fn);
        }
    } else {
        if (!mkdir($fn, 0777, true)) {
            e('cntwriteto', 'folder', $fn);
        }
    }
    return $fn;
}

/**
 * Returns the localization needed for the JavaScript.
 *
 * @return string array.
 *
 * @global array  The paths of system files and folders.
 * @global string The current language.
 * @global array  The localization of the plugins.
 */
function Tetris_langJS()
{
    global $pth, $sl, $plugin_tx;

    $ptx = $plugin_tx['tetris'];
    $texts = array();
    foreach ($ptx as $key => $msg) {
        $parts = explode('_', $key);
        if ($parts[0] != 'cf') {
            $texts[$key] = $msg;
        }
    }
    return $texts;
}

/**
 * Includes the necessary scripts and stylesheets.
 *
 * @return void
 *
 * @global array  The paths of system files and folders.
 * @global string The (X)HTML fragment to insert into the head element.
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 * @global string The current language.
 */
function Tetris_headers()
{
    global $pth, $hjs, $plugin_cf, $plugin_tx, $sl;

    $pcf = $plugin_cf['tetris'];
    $ptx = $plugin_tx['tetris'];

    include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
    include_jQuery();
    include_jQueryUI();
    $hjs .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
        . 'tetris/tetris.js"></script>' . PHP_EOL;
    $falldown = $pcf['falldown_immediately'] ? 'true' : 'false';
    $texts = json_encode(Tetris_langJS());
    $hjs .= <<<EOT
<script type="text/javascript">/* <![CDATA[ */
    var TETRIS_HIGHSCORES = "{$pth['folder']['plugins']}tetris/highscores.php";
    var TETRIS_FALLDOWN = $falldown;
    var TETRIS_SPEED_INITIAL = $pcf[speed_initial];
    var TETRIS_SPEED_ACCELERATION = $pcf[speed_acceleration];
    var TETRIS_TX = $texts;
/* ]]> */</script>'

EOT;
}

/**
 * Returns the the Tetris matrix view.
 *
 * @return string (X)HTML.
 */
function Tetris_grid()
{
    $o = '<div id="tetris-grid">' . PHP_EOL . '<table>' . PHP_EOL;
    for ($j = ord('d'); $j <= ord('u'); $j++) {
        $o .= '<tr>';
        for ($i = 1; $i <= 10; $i++) {
            $o .= '<td id="tetris-' . chr($j) . $i . '"></td>';
        }
        $o .= '</tr>' . PHP_EOL;
    }
    $o .= '</table>' . PHP_EOL . '</div>' . PHP_EOL;
    return $o;
}

/**
 * Returns the view of the next tetromino.
 *
 * @return string (X)HTML.
 */
function Tetris_next()
{
    $o = '<div id="tetris-next">' . PHP_EOL . '<table>' . PHP_EOL;
    for ($j = 0; $j < 4; $j++) {
        $o .= '<tr>';
        for ($i = 0; $i < 4; $i++) {
            $o .= '<td id="tetris-x' . $i . $j . '"></td>';
        }
        $o .= '</tr>' . PHP_EOL;
    }
    $o .= '</table>' . PHP_EOL . '</div>' . PHP_EOL;
    return $o;
}

/**
 * Returns the of the tetris stats view.
 *
 * @return string (X)HTML.
 *
 * @global array The localization of the plugins.
 */
function Tetris_stats()
{
    global $plugin_tx;

    $ptx = $plugin_tx['tetris'];
    $o = <<<EOT
<div id="tetris-stats">
    <div class="label">$ptx[label_level]</div>
    <div id="tetris-level" class="led">000001</div>
    <div class="label">$ptx[label_rows]</div>
    <div id="tetris-lines" class="led">000000</div>
    <div class="label">$ptx[label_score]</div>
    <div id="tetris-score" class="led">000000</div>
</div>

EOT;
    return $o;
}

/**
 * Returns the view of the tetris command buttons.
 *
 * @return string (X)HTML.
 *
 * @global array The localization of the plugins.
 */
function Tetris_cmd()
{
    global $plugin_tx;

    $ptx = $plugin_tx['tetris'];

    $o = '<div id="tetris-cmd">' . PHP_EOL
        . tag(
            'input id="tetris-start" type="button" value="'
            . $ptx['label_start'] . '"'
        ) . PHP_EOL
        . tag(
            'input id="tetris-stop" type="button" value="'
            . $ptx['label_stop'] . '"'
        ) . PHP_EOL
        . tag(
            'input id="tetris-about-btn" type="button" value="'
            . $ptx['label_about'] . '"'
            . ' onclick="jQuery(\'#tetris-about-dlg\').dialog(\'open\')"'
        ) . PHP_EOL
        . '</div>' . PHP_EOL;
    return $o;
}

/**
 * Returns the view of the content of the rule tab.
 *
 * @return string (X)HTML.
 *
 * @global array The localization of the plugins.
 */
function Tetris_rules()
{
    global $plugin_tx;

    $ptx = $plugin_tx['tetris'];
    $o = <<<EOT
<div id="tetris-rules">
    <div>$ptx[message_howto_play]</div>
    <table>
        <tr><td>$ptx[label_left]</td><td class="key">J / &larr;</td></tr>
        <tr><td>$ptx[label_right]</td><td class="key">L / &rarr;</td></tr>
        <tr><td>$ptx[label_rotate]</td><td class="key">I / &uarr;</td></tr>
        <tr><td>$ptx[label_down]</td><td class="key">K / &darr;</td></tr>
    </table>
</div>

EOT;
    return $o;
}

/**
 * Returns the view of the Tetris plugin.
 *
 * @return string
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the plugins.
 */
function tetris()
{
    global $pth, $plugin_tx;

    $ptx = $plugin_tx['tetris'];

    if (session_id() == '') {
        session_start();
    }
    $_SESSION['tetris_data_folder'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/'
        . Tetris_dataFolder();
    $_SESSION['tetris_timestamp'] = time();

    Tetris_headers();
    $url = $pth['folder']['plugins'] . 'tetris/highscores.php?action=list';
    $grid = Tetris_grid();
    $next = Tetris_next();
    $stats = Tetris_stats();
    $cmd = Tetris_cmd();
    $rules = Tetris_rules();
    $o = <<<EOT
<div id="tetris-no-js" class="cmsimplecore_warning">$ptx[error_no_js]</div>
<div id="tetris-tabs">
    <ul>
        <li><a href="#tetris">$ptx[label_play]</a></li>
        <li><a href="$url">$ptx[label_highscores]</a></li>
        <li><a href="#tetris-rules">$ptx[label_rules]</a></li>
    </ul>
    <div id="tetris">
        $grid
        <div style="float:left">
            $next
            $stats
        </div>
        <div style="clear:both"></div>
        $cmd
    </div>
    $rules
</div>
<div id="tetris-highscore-dlg" title="New Highscore" style="display:none">
    <input type="text" maxlength="20" />
</div>
<div id="tetris-about-dlg" title="$ptx[label_about]" style="display:none">
    <h3>Tetris_XH</h3>
    $ptx[message_about]
    <p>&copy; 2011-2013 by <a href="http://3-magi.net/">cmb</a></p>
</div>
    
EOT;
    return $o;
}

?>
