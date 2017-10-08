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

class MainController
{
    /**
     * @var array
     */
    private $conf;

    /**
     * @var array
     */
    private $lang;

    public function __construct()
    {
        global $plugin_cf, $plugin_tx;

        $this->conf = $plugin_cf['tetris'];
        $this->lang = $plugin_tx['tetris'];
    }

    /**
     * @return void
     */
    public function defaultAction()
    {
        global $sn, $su;

        if (isset($_GET['tetris_highscores'])) {
            switch ($_GET['tetris_highscores']) {
                case 'required':
                    echo HighscoreService::requiredHighscore();
                    exit;
                case 'list':
                    echo $this->highscoreList();
                    exit;
                case 'new':
                    $this->newHighscore();
                    exit;
            }
        }

        $this->headers();
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
    private function highscoreList()
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
    private function newHighscore()
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
    private function headers()
    {
        global $pth, $hjs, $sn, $su;

        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jQuery();
        $hjs .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
            . 'tetris/tetris.js"></script>' . PHP_EOL;
        $falldown = $this->conf['falldown_immediately'] ? 'true' : 'false';
        $texts = json_encode($this->langJS());
        $hjs .= <<<EOT
<script type="text/javascript">/* <![CDATA[ */
    var TETRIS_HIGHSCORES = "$sn?$su&tetris_highscores=";
    var TETRIS_FALLDOWN = $falldown;
    var TETRIS_SPEED_INITIAL = {$this->conf['speed_initial']};
    var TETRIS_SPEED_ACCELERATION = {$this->conf['speed_acceleration']};
    var TETRIS_TX = $texts;
/* ]]> */</script>

EOT;
    }

    /**
     * @return array.
     */
    private function langJS()
    {
        $texts = array();
        foreach ($this->lang as $key => $msg) {
            $parts = explode('_', $key);
            if ($parts[0] != 'cf') {
                $texts[$key] = $msg;
            }
        }
        return $texts;
    }
}
