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
                        $o .= self::version();
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

        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}tetris/tetris.png";
        $view->version = self::VERSION;
        $view->checks = (new SystemCheckService)->getChecks();
        return (string) $view;
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
