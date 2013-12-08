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
 * Reads the highscores.
 *
 * @return bool
 * 
 * @global array The highscores.
 */
function Tetris_readHighscores()
{
    global $_Tetris_highscores;

    $fn = Tetris_dataFolder() . 'highscores.dat';
    if (($cnt = file_get_contents($fn)) === false
        || ($_Tetris_highscores = unserialize($cnt)) === false
    ) {
        $_Tetris_highscores = array();
    }
}

/**
 * Writes back the highscores.
 *
 * @return void
 * 
 * @global array The highscores.
 */
function Tetris_writeHighscores()
{
    global $_Tetris_highscores;

    $fn = Tetris_dataFolder() . 'highscores.dat';
    if (($fh = fopen($fn, 'w')) !== false) {
        flock($fh, LOCK_EX);
        fputs($fh, serialize($_Tetris_highscores));
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}

/**
 * Enters a new highscore.
 * 
 * Strips the highscores to at most 10 entries.
 *
 * @param string $name  A player name.
 * @param string $score A score.
 *
 * @return void
 * 
 * @global array The highscores
 */
function Tetris_enterHighscore($name, $score)
{
    global $_Tetris_highscores;

    $_Tetris_highscores[] = array($name, $score);
    usort($_Tetris_highscores, create_function('$a, $b', 'return $b[1] - $a[1];'));
    array_splice($_Tetris_highscores, 10);
}

/**
 * Returns the minimum required score to get a highscore.
 *
 * @return string
 * 
 * @global array The highscores
 */
function Tetris_requiredHighscore()
{
    global $_Tetris_highscores;
    
    return isset($_Tetris_highscores[9][1]) ? $_Tetris_highscores[9][1] : 0;
}

/**
 * Return the view of the highscore list.
 *
 * @return string (X)HTML.
 *
 * @global array The highscores.
 */
function Tetris_highscoreList()
{
    global $_Tetris_highscores;
    
    $o = '<div id="tetris-highscores">' . PHP_EOL . '<table>' . PHP_EOL;
    foreach ($_Tetris_highscores as $highscore) {
        list($name, $score) = $highscore;
        $o .= '<tr><td class="name">' . htmlspecialchars($name, ENT_COMPAT, 'UTF-8')
            . '</td><td class="score">'
            . htmlspecialchars($score, ENT_COMPAT, 'UTF-8')
            . '</td></tr>' . PHP_EOL;
    }
    $o .= '</table>' . PHP_EOL . '</div>' . PHP_EOL;
    return $o;
}

/**
 * Enters a new highscore to the list.
 *
 * @return void
 */
function Tetris_newHighscore()
{
    $name = stsl($_POST['name']);
    $score = stsl($_POST['score']);
    if (strlen($name) <= 20 // FIXME: use utf8_strlen()
        && preg_match('/[0-9]{1,6}/', $score)
    ) {
        Tetris_enterHighscore($name, $score);
        Tetris_writeHighscores();
    }
}

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
 * @global string The script name.
 * @global string The current page URL.
 */
function Tetris_headers()
{
    global $pth, $hjs, $plugin_cf, $plugin_tx, $sl, $sn, $su;

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
    var TETRIS_HIGHSCORES = "$sn?$su&tetris_highscores=";
    var TETRIS_FALLDOWN = $falldown;
    var TETRIS_SPEED_INITIAL = $pcf[speed_initial];
    var TETRIS_SPEED_ACCELERATION = $pcf[speed_acceleration];
    var TETRIS_TX = $texts;
/* ]]> */</script>

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
 * Returns the about view.
 *
 * @return string (X)HTML.
 *
 * @global array The localization of the plugins.
 */
function Tetris_about()
{
    global $plugin_tx;
    
    $ptx = $plugin_tx['tetris'];
    $o = <<<EOT
<div id="tetris-about">
    <h4>Tetris_XH</h4>
    $ptx[message_about]
    <p>&copy; 2011-2013 by <a href="http://3-magi.net/">cmb</a></p>
</div>

EOT;
    return $o;
}

/**
 * Returns the view of the Tetris plugin.
 *
 * @return string
 *
 * @global array  The paths of system files and folders.
 * @global array  The localization of the plugins.
 * @global string The script name.
 * @global string The current page URL.
 */
function tetris()
{
    global $pth, $plugin_tx, $sn, $su;

    $ptx = $plugin_tx['tetris'];

    if (isset($_GET['tetris_highscores'])) {
        Tetris_readHighscores();
        switch ($_GET['tetris_highscores']) {
        case 'required':
            echo Tetris_requiredHighscore();
            exit;
        case 'list':
            echo Tetris_highscoreList();
            exit;
        case 'new':
            Tetris_newHighscore();
            exit;
        }
    }

    Tetris_headers();
    $url = $sn . '?' . $su . '&amp;tetris_highscores=list';
    $grid = Tetris_grid();
    $next = Tetris_next();
    $stats = Tetris_stats();
    $cmd = Tetris_cmd();
    $rules = Tetris_rules();
    $about = Tetris_about();
    $o = <<<EOT
<div id="tetris-no-js" class="cmsimplecore_warning">$ptx[error_no_js]</div>
<div id="tetris-tabs">
    <ul>
        <li><a href="#tetris">$ptx[label_play]</a></li>
        <li><a href="$url">$ptx[label_highscores]</a></li>
        <li><a href="#tetris-rules">$ptx[label_rules]</a></li>
        <li><a href="#tetris-about">$ptx[label_about]</a></li>
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
    $about
</div>
    
EOT;
    return $o;
}

?>
