<?php

/**
 * Highscore handling of Tetris_XH.
 *
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


error_reporting(0);


if (!isset($_SESSION)) {
    session_start();
}


if (empty($_SESSION['tetris_data_folder'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns $text without slashes.
 *
 * @param string $text
 * @return string
 */
function stsl($text) {
    return get_magic_quotes_gpc() ? stripslashes($text) : $text;
}


/**
 * Reads the highscores.
 *
 * @global array $highscores
 * @return bool
 */
function read_highscores() {
    global $highscores;

    $fn = $_SESSION['tetris_data_folder'].'highscores.dat';
    if (($cnt = file_get_contents($fn)) === FALSE
	    || ($highscores = unserialize($cnt)) === FALSE) {
	$highscores = array();
    }
}


/**
 * Writes back the highscores.
 *
 * @global array $highscores
 * @return void
 */
function write_highscores() {
    global $highscores;

    $fn = $_SESSION['tetris_data_folder'].'highscores.dat';
    if (($fh = fopen($fn, 'w')) !== FALSE) {
	flock($fh, LOCK_EX);
	fputs($fh, serialize($highscores));
	flock($fh, LOCK_UN);
	fclose($fh);
    }
}


/**
 * Enters new highscore.
 * Strips the highscores to at most 10 entries.
 *
 * @global array $highscores
 * @param string $name
 * @param string $score
 */
function enter_highscore($name, $score) {
    global $highscores;

    $highscores[] = array($name, $score);
    usort($highscores, create_function('$a, $b', 'return $b[1]-$a[1];'));
    array_splice($highscores, 10);
}


/**
 * Handling of the requests.
 */
read_highscores();
switch ($_REQUEST['action']) {
    case 'required':
	echo isset($highscores[9][1]) ? $highscores[9][1] : 0;
	break;
    case 'list':
	echo '<div id="tetris-highscores">', "\n", '<table>', "\n";
	foreach ($highscores as $highscore) {
	    list($name, $score) = $highscore;
	    echo '<tr><td class="name">', htmlspecialchars($name), '</td><td class="score">',
		    htmlspecialchars($score), '</td></tr>', "\n";
	}
	echo '</table>', "\n", '</div>', "\n";
	break;
    case 'new':
	$name = stsl($_POST['name']);
	$score = stsl($_POST['score']);
	if (time() > $_SESSION['tetris_timestamp'] + 10
		&& strlen($name) <= 20
		&& preg_match('/[0-9]{1,6}/', $score)) {
	    enter_highscore($name, $score);
	    write_highscores();
	}
	$_SESSION['tetris_timestamp'] = time();
	break;
    default:
}

?>
