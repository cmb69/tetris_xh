<?php

/**
 * Highscore handling of Tetris_XH.
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
 * Turn off error reporting.
 */
error_reporting(0);

/*
 * Start the session.
 */
if (session_id() == '') {
    session_start();
}

/*
 * Prevent direct access.
 */
if (empty($_SESSION['tetris_data_folder'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Returns a text with slashes removed, if appropriate.
 *
 * @param string $text A text.
 * 
 * @return string
 */
function stsl($text)
{
    return get_magic_quotes_gpc() ? stripslashes($text) : $text;
}

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

    $fn = $_SESSION['tetris_data_folder'] . 'highscores.dat';
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

    $fn = $_SESSION['tetris_data_folder'] . 'highscores.dat';
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
            . '</td><td class="score">' . htmlspecialchars($score, ENT_COMPAT, 'UTF-8')
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
    if (time() > $_SESSION['tetris_timestamp'] + 10
        && strlen($name) <= 20 // FIXME: use utf8_strlen()
        && preg_match('/[0-9]{1,6}/', $score)
    ) {
        Tetris_enterHighscore($name, $score);
        Tetris_writeHighscores();
    }
    $_SESSION['tetris_timestamp'] = time();
}

/**
 * Handles the requests.
 */
Tetris_readHighscores();
switch ($_REQUEST['action']) {
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

?>
