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

use Tetris\Infra\HighscoreService;
use Tetris\Infra\Jquery;
use Tetris\Infra\View;
use Tetris\Value\Response;

class MainController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;
     
    /** @var array<string,string> */
    private $lang;

    /** @var HighscoreService */
    private $highscoreService;

    /** @var Jquery */
    private $jquery;

    /** @var View */
    private $view;

    /**
     * @param array<string,string> $conf
     * @param array<string,string> $lang
     */
    public function __construct(
        string $pluginFolder,
        array $conf,
        array $lang,
        HighscoreService $highscoreService,
        Jquery $jquery,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->lang = $lang;
        $this->highscoreService = $highscoreService;
        $this->jquery = $jquery;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        switch ($_GET["tetris_action"] ?? "") {
            default:
                return $this->defaultAction();
            case "get_highscore":
                return $this->getHighscoreAction();
            case "show_highscores":
                return $this->showHighscoresAction();
            case "new_highscore":
                return $this->newHighscoreAction();
        }
    }

    private function defaultAction(): Response
    {
        global $sn, $su;

        $this->headers();
        $output = $this->view->render("main", [
            "url" => $sn . '?' . $su . '&tetris_action=show_highscores',
            "gridRows" => range('d', 'u'),
            "gridCols" => range(1, 10),
            "nextRows" => range(0, 3),
            "nextCols" => range(0, 3),
        ]);
        return Response::create($output);
    }

    /**
     * @return void
     */
    private function headers()
    {
        global $hjs, $sn, $su;

        $this->jquery->include();
        $hjs .= '<script type="text/javascript" src="' . $this->pluginFolder
            . 'tetris.js"></script>' . PHP_EOL;
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

    private function getHighscoreAction(): Response
    {
        return Response::terminate()->withOutput((string) $this->highscoreService->requiredHighscore());
    }

    private function showHighscoresAction(): Response
    {
        $highscores = $this->highscoreService->readHighscores();
        foreach ($highscores as &$highscore) {
            list($player, $score) = $highscore;
            $highscore = (object) compact('player', 'score');
        }
        $output = $this->view->render("highscores", [
            "highscores" => $highscores,
        ]);
        return Response::terminate()->withOutput($output);
    }

    private function newHighscoreAction(): Response
    {
        $name = $_POST['name'];
        $score = $_POST['score'];
        if (strlen($name) <= 20 // FIXME: use utf8_strlen()
            && preg_match('/[0-9]{1,6}/', $score)
        ) {
            $this->highscoreService->enterHighscore($name, (int) $score);
        }
        return Response::terminate();
    }
}
