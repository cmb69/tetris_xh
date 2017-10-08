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
        global $o, $action, $admin, $plugin;

        if (XH_ADM) {
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('tetris')) {
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
     * @return string
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
     * @return string
     */
    private static function systemCheck()
    {
        global $pth, $tx, $plugin_tx;

        $phpVersion = '5.5.0';
        $ptx = $plugin_tx['tetris'];
        $imgdir = $pth['folder']['plugins'] . 'tetris/images/';
        $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
        $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
        $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
        $o = tag('hr') . '<h4>' . $ptx['syscheck_title'] . '</h4>'
            . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
            . tag('br') . tag('br') . PHP_EOL;
        foreach (array('json') as $ext) {
            $o .= (extension_loaded($ext) ? $ok : $fail)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
                . tag('br') . PHP_EOL;
        }
        $state = file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php')
            ? $ok
            : $fail;
        $o .= $state . '&nbsp;&nbsp;' . $ptx['syscheck_jquery']
            . tag('br') . tag('br') . PHP_EOL;
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'tetris/' . $folder;
        }
        $folders[] = HighscoreService::dataFolder();
        foreach ($folders as $folder) {
            $o .= (is_writable($folder) ? $ok : $warn)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
                . tag('br') . PHP_EOL;
        }
        return $o;
    }

    /**
     * @return void
     */
    public static function main()
    {
        global $pth, $sn, $su;

        if (isset($_GET['tetris_highscores'])) {
            switch ($_GET['tetris_highscores']) {
                case 'required':
                    echo HighscoreService::requiredHighscore();
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
        $view = new View('main');
        $view->url = $sn . '?' . $su . '&tetris_highscores=list';
        $view->gridRows = range('d', 'u');
        $view->gridCols = range(1, 10);
        $view->nextRows = range(0, 3);
        $view->nextCols = range(0, 3);
        $view->render();
    }

     /**
      * @return string
      */
    private static function highscoreList()
    {
        $highscores = HighscoreService::readHighscores();
        $o = <<<EOT
<!-- Tetris_XH: highscores -->
<div id="tetris-highscores">
    <table>

EOT;
        foreach ($highscores as $highscore) {
            list($name, $score) = $highscore;
            $name = XH_hsc($name);
            $score = XH_hsc($score);
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
      * @return void
      */
    private static function newHighscore()
    {
        $name = $_POST['name'];
        $score = $_POST['score'];
        if (strlen($name) <= 20 // FIXME: use utf8_strlen()
            && preg_match('/[0-9]{1,6}/', $score)
        ) {
            HighscoreService::enterHighscore($name, (int) $score);
        }
    }

    /**
     * @return void
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
     * @return array.
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
}
