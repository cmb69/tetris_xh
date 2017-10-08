<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Tetris_XH.
 *
 * Tetris_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Tetris_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tetris_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tetris;

class Plugin
{
    const VERSION = '@TETRIS_VERSION@';

    /**
     * @return void
     */
    public static function run()
    {
        global $o, $tetris, $action, $admin, $plugin;

        if (XH_ADM) {
            if (isset($tetris) && $tetris == 'true') {
                $o .= print_plugin_admin('off');
                switch ($admin) {
                case '':
                    $o .= self::version() . self::systemCheck();
                    break;
                default:
                    $o .= plugin_admin_common($action, $admin, $plugin);
                }
            }
        }
    }

    /**
     * Returns the plugin version information view.
     *
     * @return string (X)HTML
     *
     * @global array The paths of system files and folders.
     */
    private static function version()
    {
        global $pth;

        $icon = tag(
            'img src="' . $pth['folder']['plugins']
            . 'tetris/tetris.png" alt="Plugin Icon"'
        );
        $version = self::VERSION;
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
<p>Copyright &copy; 2011-2017 Christoph M. Becker</p>
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
    private static function systemCheck()
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
        $folders[] = self::dataFolder();
        foreach ($folders as $folder) {
            $o .= (is_writable($folder) ? $ok : $warn)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
                . tag('br') . PHP_EOL;
        }
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
    public static function main()
    {
        global $pth, $plugin_tx, $sn, $su;

        $ptx = $plugin_tx['tetris'];

        if (isset($_GET['tetris_highscores'])) {
            self::readHighscores();
            switch ($_GET['tetris_highscores']) {
            case 'required':
                echo self::requiredHighscore();
                exit;
            case 'list':
                echo self::highscoreList();
                exit;
            case 'new':
                self::newHighscore();
                exit;
            }
        }

        self::headers();
        $url = $sn . '?' . $su . '&amp;tetris_highscores=list';
        $grid = self::grid();
        $next = self::next();
        $stats = self::stats();
        $cmd = self::cmd();
        $rules = self::rules();
        $about = self::about();
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

    /**
     * Returns the minimum required score to get a highscore.
     *
     * @return string
     * 
     * @global array The highscores
     */
    private static function requiredHighscore()
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
    private static function highscoreList()
    {
        global $_Tetris_highscores;
   
        $o = <<<EOT
<!-- Tetris_XH: highscores -->
<div id="tetris-highscores">
    <table>

EOT;
        foreach ($_Tetris_highscores as $highscore) {
            list($name, $score) = $highscore;
            $name = htmlspecialchars($name, ENT_COMPAT, 'UTF-8');
            $score = htmlspecialchars($score, ENT_COMPAT, 'UTF-8');
            $o .= <<<EOT
        <tr><td class="name">$name</td><td class="score">$score</td></tr>

EOT;
        }
        $o .= <<<EOT
    </table>
</div>

EOT;
        return $o;
    }

     /**
     * Enters a new highscore to the list.
     *
     * @return void
     */
    private static function newHighscore()
    {
        $name = stsl($_POST['name']);
        $score = stsl($_POST['score']);
        if (strlen($name) <= 20 // FIXME: use utf8_strlen()
            && preg_match('/[0-9]{1,6}/', $score)
        ) {
            self::enterHighscore($name, $score);
            self::writeHighscores();
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
    private static function enterHighscore($name, $score)
    {
        global $_Tetris_highscores;
    
        $_Tetris_highscores[] = array($name, $score);
        usort($_Tetris_highscores, create_function('$a, $b', 'return $b[1] - $a[1];'));
        array_splice($_Tetris_highscores, 10);
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
    private static function headers()
    {
        global $pth, $hjs, $plugin_cf, $plugin_tx, $sl, $sn, $su;
    
        $pcf = $plugin_cf['tetris'];
        $ptx = $plugin_tx['tetris'];
    
        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jQuery();
        $hjs .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
            . 'tetris/tetris.js"></script>' . PHP_EOL;
        $falldown = $pcf['falldown_immediately'] ? 'true' : 'false';
        $texts = json_encode(self::langJS());
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
     * Returns the localization needed for the JavaScript.
     *
     * @return string array.
     *
     * @global array  The paths of system files and folders.
     * @global string The current language.
     * @global array  The localization of the plugins.
     */
    private static function langJS()
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
     * Returns the the Tetris matrix view.
     *
     * @return string (X)HTML.
     */
    private static function grid()
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
    private static function next()
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
    private static function stats()
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
    private static function cmd()
    {
        global $plugin_tx;
    
        $ptx = $plugin_tx['tetris'];
    
        $o = '<div id="tetris-cmd">' . PHP_EOL
            . '<button id="tetris-start">' . $ptx['label_start'] . '</button>' . PHP_EOL
            . '<button id="tetris-stop" disabled="disabled">' . $ptx['label_stop']
            . '</button>' . PHP_EOL
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
    private static function rules()
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
    private static function about()
    {
        global $plugin_tx;
        
        $ptx = $plugin_tx['tetris'];
        $o = <<<EOT
<div id="tetris-about">
    <h4>Tetris_XH</h4>
    $ptx[message_about]
    <p>&copy; 2011-2017 by <a href="http://3-magi.net/">cmb</a></p>
</div>

EOT;
        return $o;
    }

    /**
     * Reads the highscores.
     *
     * @return bool
     * 
     * @global array The highscores.
     */
    private static function readHighscores()
    {
        global $_Tetris_highscores;

        $fn = self::dataFolder() . 'highscores.dat';
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
    private static function writeHighscores()
    {
        global $_Tetris_highscores;

        $fn = self::dataFolder() . 'highscores.dat';
        if (($fh = fopen($fn, 'w')) !== false) {
            flock($fh, LOCK_EX);
            fputs($fh, serialize($_Tetris_highscores));
            flock($fh, LOCK_UN);
            fclose($fh);
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
    private static function dataFolder()
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
}