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
     
    /** @var HighscoreService */
    private $highscoreService;

    /** @var Jquery */
    private $jquery;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        HighscoreService $highscoreService,
        Jquery $jquery,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
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

        $this->jquery->include();
        $output = $this->view->render("main", [
            "config" => $this->jsConfig(),
            "script" => $this->pluginFolder . "tetris.js",
            "url" => $sn . '?' . $su . '&tetris_action=show_highscores',
            "gridRows" => range('d', 'u'),
            "gridCols" => range(1, 10),
            "nextRows" => range(0, 3),
            "nextCols" => range(0, 3),
        ]);
        return Response::create($output);
    }

    /** @return array<string,mixed> */
    private function jsConfig(): array
    {
        global $sn, $su;
        return [
            "getHighscoreUrl" => "$sn?$su&tetris_action=get_highscore",
            "newHighscoreUrl" => "$sn?$su&tetris_action=new_highscore",
            "falldown" => (bool) $this->conf['falldown_immediately'],
            "initialSpeed" => (int) $this->conf['speed_initial'],
            "acceleration" => (int) $this->conf['speed_acceleration'],
            "labelStart" => $this->view->plain("label_start"),
            "labelPause" => $this->view->plain("label_pause"),
            "labelResume" => $this->view->plain("label_resume"),
        ];
    }

    private function getHighscoreAction(): Response
    {
        return Response::terminate()->withOutput((string) $this->highscoreService->requiredHighscore());
    }

    private function showHighscoresAction(): Response
    {
        $highscores = $this->highscoreService->readHighscores();
        $highscores = array_map(function (array $highscore) {
            [$player, $score] = $highscore;
            return ["player" => $player, "score" => $score];
        }, $highscores);
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
