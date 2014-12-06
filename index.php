<?php

/**
 * Front-end functionality of Tetris_XH.
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


define('TETRIS_VERSION', '1');


/**
 * Returns the data folder.
 *
 * @return string
 */
function tetris_data_folder() {
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['tetris'];

    if ($pcf['folder_data'] == '') {
	$fn = $pth['folder']['plugins'].'tetris/data/';
    } else {
	$fn = $pth['folder']['base'].$pcf['folder_data'];
    }
    if (substr($fn, -1) != '/') {
	$fn .= '/';
    }
    if (file_exists($fn)) {
	if (!is_dir($fn)) {
	    e('cntopen', 'folder', $fn);
	}
    } else {
	if (!mkdir($fn, 0777, TRUE)) {
	    e('cntwriteto', 'folder', $fn);
	}
    }
    return $fn;
}


/**
 * Updates the LANG.js file if necessary
 * with the strings from LANG.php.
 * Returns FALSE on failure.
 *
 * @return bool
 */
function tetris_update_lang_js() {
    global $pth, $sl, $plugin_tx;

    $ptx =& $plugin_tx['tetris'];

    $fn = $pth['folder']['plugins'].'tetris/languages/';
    if (!file_exists($fn.$sl.'.php')) {
	if (file_exists($fn.'en.php')) {
	    if (!copy($fn.'en.php', $fn.$sl.'.php')) {
		e('cntwriteto', 'language', $fn.$sl.'.php');
		return FALSE;
	    }
	} else {
	    e('missing', 'language', $fn.'en.php');
	    return FALSE;
	}
    }
    $fn .= $sl;
    if (!file_exists($fn.'.js') || filemtime($fn.'.js') < filemtime($fn.'.php')) {
	$js = '// auto-generated by Tetris_XH -- do not modify!'."\n"
		.'// any modifications should be made in '.$sl.'.php'."\n\n"
		.'TETRIS_TX = {'."\n";
	$first = TRUE;
	foreach ($ptx as $key => $msg) {
	    $parts = explode('_', $key);
	    if ($parts[0] != 'cf') {
		if ($first) {
		    $first = FALSE;
		} else {
		    $js .= ','."\n";
		}
		$js .= '    \''.$key.'\': \''.addslashes($msg).'\'';
	    }
	}
	$js .= "\n".'};'."\n";
	if (!($fh = fopen($fn.'.js', 'w')) || ($res = fwrite($fh, $js)) === FALSE) {
	    e('cntwriteto', 'file', $fn.'.js');
	}
	if ($fh)
	    fclose($fh);
	return $fh && $res;
    }
    return TRUE;
}


/**
 * Includes the necessary scripts and stylesheets.
 *
 * @return void
 */
function tetris_headers() {
    global $pth, $hjs, $plugin_cf, $plugin_tx, $sl;

    $pcf =& $plugin_cf['tetris'];
    $ptx =& $plugin_tx['tetris'];

    include_once($pth['folder']['plugins'].'jquery/jquery.inc.php');
    include_jQuery();
    include_jQueryUI();
    $hjs .= '<script type="text/javascript" src="'.$pth['folder']['plugins'].'tetris/tetris.js"></script>'."\n";
    if (tetris_update_lang_js()) {
	$hjs .= '<script type="text/javascript" src="'.$pth['folder']['plugins']
		.'tetris/languages/'.$sl.'.js"></script>'."\n";
    }
    $hjs .= '<script type="text/javascript">'."\n".'/* <![CDATA[ */'."\n"
	    .'var TETRIS_HIGHSCORES = \''.$pth['folder']['plugins'].'tetris/highscores.php\';'."\n"
	    .'var TETRIS_FALLDOWN = '.($pcf['falldown_immediately'] ? 'true' : 'false').';'."\n"
	    .'var TETRIS_SPEED_INITIAL = '.$pcf['speed_initial'].';'."\n"
	    .'var TETRIS_SPEED_ACCELERATION = '.$pcf['speed_acceleration'].';'."\n"
	    .'/* ]]> */'."\n".'</script>'."\n";
}


/**
 * Returns the (x)html of the tetris matrix.
 *
 * @return string
 */
function tetris_grid() {
    $htm = '<div id="tetris-grid">'."\n".'<table>'."\n";
    for ($j = ord('d'); $j <= ord('u'); $j++) {
	$htm .= '<tr>';
	for ($i = 1; $i <= 10; $i++) {
	    $htm .= '<td id="tetris-'.chr($j).$i.'"></td>';
	}
	$htm .= '</tr>'."\n";
    }
    $htm .= '</table>'."\n".'</div>'."\n";
    return $htm;
}


/**
 * Returns the (x)html of the next tetromino.
 *
 * @return string
 */
function tetris_next() {
    $htm = '<div id="tetris-next">'."\n".'<table>'."\n";
    for ($j = 0; $j < 4; $j++) {
	$htm .= '<tr>';
	for ($i = 0; $i < 4; $i++) {
	    $htm .= '<td id="tetris-x'.$i.$j.'"></td>';
	}
	$htm .= '</tr>'."\n";
    }
    $htm .= '</table>'."\n".'</div>'."\n";
    return $htm;
}


/**
 * Returns the (x)html of the tetris stats.
 *
 * @return string
 */
function tetris_stats() {
    global $plugin_tx;

    $ptx =& $plugin_tx['tetris'];
    $htm = '<div id="tetris-stats">'."\n"
	    .'<div class="label">'.$ptx['label_level'].'</div>'."\n"
	    .'<div id="tetris-level" class="led">000001</div>'."\n"
	    .'<div class="label">'.$ptx['label_rows'].'</div>'."\n"
	    .'<div id="tetris-lines" class="led">000000</div>'."\n"
	    .'<div class="label">'.$ptx['label_score'].'</div>'."\n"
	    .'<div id="tetris-score" class="led">000000</div>'."\n"
	    .'</div>'."\n";
    return $htm;
}


/**
 * Returns the (x)html of the tetris command buttons.
 */
function tetris_cmd() {
    global $plugin_tx;

    $ptx =& $plugin_tx['tetris'];

    $htm = '<div id="tetris-cmd">'."\n"
	    .tag('input id="tetris-start" type="button" value="'.$ptx['label_start'].'"')."\n"
	    .tag('input id="tetris-stop" type="button" value="'.$ptx['label_stop'].'"')."\n"
	    .tag('input id="tetris-about-btn" type="button" value="'.$ptx['label_about'].'"'
	    .' onclick="jQuery(\'#tetris-about-dlg\').dialog(\'open\')"')."\n"
	    .'</div>'."\n";
    return $htm;
}


/**
 * Returns the (x)html of the content of the rule tab.
 *
 * @return string
 */
function tetris_rules() {
    global $plugin_tx;

    $ptx =& $plugin_tx['tetris'];

    $htm = '<div id="tetris-rules">'."\n"
	    .'<div>'.$ptx['message_howto_play'].'</div>'."\n"
	    .'<table>'."\n"
	    .'<tr><td>'.$ptx['label_left'].'</td><td class="key">J / &larr;</td></tr>'."\n"
	    .'<tr><td>'.$ptx['label_right'].'</td><td class="key">L / &rarr;</td></tr>'."\n"
	    .'<tr><td>'.$ptx['label_rotate'].'</td><td class="key">I / &uarr;</td></tr>'."\n"
	    .'<tr><td>'.$ptx['label_down'].'</td><td class="key">K / &darr;</td></tr>'."\n"
	    .'</table>'."\n".'</div>'."\n";
    return $htm;
}


/**
 * Returns the (x)html of the tetris plugin.
 *
 * @return string
 */
function tetris() {
    global $pth, $plugin_tx;

    $ptx =& $plugin_tx['tetris'];

    if (!isset($_SESSION)) {session_start();}
    $_SESSION['tetris_data_folder'] = dirname($_SERVER['SCRIPT_FILENAME']).'/'.tetris_data_folder();
    $_SESSION['tetris_timestamp'] = time();

    tetris_headers();
    $htm = '';
    $htm .= '<div id="tetris-no-js" class="cmsimplecore_warning">'.$ptx['error_no_js'].'</div>'."\n";
    $htm .= '<div id="tetris-tabs">'."\n"
	    .'<ul>'."\n"
	    .'<li><a href="#tetris">'.$ptx['label_play'].'</a></li>'."\n"
	    .'<li><a href="'.$pth['folder']['plugins'].'tetris/highscores.php?action=list">'
	    .$ptx['label_highscores'].'</a></li>'."\n"
	    .'<li><a href="#tetris-rules">'.$ptx['label_rules'].'</a></li>'."\n"
	    .'</ul>'."\n";
    $htm .= '<div id="tetris">'."\n";
    $htm .= tetris_grid();
    $htm .= '<div style="float:left">'."\n";
    $htm .= tetris_next();
    $htm .= tetris_stats();
    $htm .= '</div>'."\n";
    $htm .= '<div style="clear:both"></div>'."\n";
    $htm .= tetris_cmd();
    $htm .= '</div>'."\n"; // #tetris
    $htm .= tetris_rules();
    $htm .= '</div>'."\n"; // #tetris-tabs

    $htm .= '<div id="tetris-highscore-dlg" title="New Highscore" style="display:none">'."\n"
	    .tag('input type="text" maxlength="20"')."\n"
	    .'</div>'."\n";

    $htm .= '<div id="tetris-about-dlg" title="'.$ptx['label_about'].'" style="display:none">'."\n"
	    .'<h3>Tetris_XH</h3>'
	    .$ptx['message_about']
	    .'<p>&copy; 2011-2013 by <a href="http://3-magi.net">cmb</a></p>'."\n"
	    .'</div>'."\n";

    return $htm;
}

?>