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

class MainController extends Controller
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth, $plugin_tx, $sn, $su;

        $this->headers();
        $view = new View($pth["folder"]["plugins"] . "tetris/views/", $plugin_tx["tetris"]);
        echo $view->render("main", [
            "url" => $sn . '?' . $su . '&tetris_action=show_highscores',
            "gridRows" => range('d', 'u'),
            "gridCols" => range(1, 10),
            "nextRows" => range(0, 3),
            "nextCols" => range(0, 3),
        ]);
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
    var TETRIS_HIGHSCORES = "$sn?$su&tetris_action=";
    var TETRIS_FALLDOWN = $falldown;
    var TETRIS_SPEED_INITIAL = {$this->conf['speed_initial']};
    var TETRIS_SPEED_ACCELERATION = {$this->conf['speed_acceleration']};
    var TETRIS_TX = $texts;
/* ]]> */</script>

EOT;
    }

    /** @return array<string,string> */
    private function langJS()
    {
        $texts = array();
        foreach ($this->lang as $key => $text) {
            if (strpos($key, 'cf_') !== 0) {
                $texts[$key] = $text;
            }
        }
        return $texts;
    }

    /**
     * @return void
     */
    public function getHighscoreAction()
    {
        echo HighscoreService::requiredHighscore();
        exit;
    }

    /**
     * @return void
     */
    public function showHighscoresAction()
    {
        global $pth, $plugin_tx;
        $highscores = HighscoreService::readHighscores();
        foreach ($highscores as &$highscore) {
            list($player, $score) = $highscore;
            $highscore = (object) compact('player', 'score');
        }
        $view = new View($pth["folder"]["plugins"] . "tetris/views/", $plugin_tx["tetris"]);
        echo $view->render("highscores", [
            "highscores" => $highscores,
        ]);
        exit;
    }

    /**
     * @return void
     */
    public function newHighscoreAction()
    {
        $name = $_POST['name'];
        $score = $_POST['score'];
        if (strlen($name) <= 20 // FIXME: use utf8_strlen()
            && preg_match('/[0-9]{1,6}/', $score)
        ) {
            HighscoreService::enterHighscore($name, (int) $score);
        }
        exit;
    }
}
