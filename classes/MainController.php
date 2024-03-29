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

use Tetris\Infra\Newsbox;
use Tetris\Infra\Repository;
use Tetris\Infra\Request;
use Tetris\Infra\View;
use Tetris\Logic\Util;
use Tetris\Value\Highscore;
use Tetris\Value\Html;
use Tetris\Value\Response;
use Tetris\Value\Url;

class MainController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;
     
    /** @var Repository */
    private $repository;

    /** @var Newsbox */
    private $newsbox;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        Repository $repository,
        Newsbox $newsbox,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->repository = $repository;
        $this->newsbox = $newsbox;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->action()) {
            default:
                return $this->defaultAction($request);
            case "get_highscore":
                return $this->getHighscoreAction();
            case "show_highscores":
                return $this->showHighscoresAction();
            case "new_highscore":
                return $this->newHighscoreAction($request);
        }
    }

    private function defaultAction(Request $request): Response
    {
        $highscores = $this->repository->highscores();
        $highscores = array_map(function (Highscore $highscore) {
            return ["player" => $highscore->player(), "score" => $highscore->score()];
        }, $highscores);
        $output = $this->view->render("main", [
            "config" => $this->jsConfig($request->url()),
            "script" => $this->pluginFolder . "tetris.js",
            "url" => $request->url()->with("tetris_action", "show_highscores")->relative(),
            "gridRows" => range('d', 'u'),
            "gridCols" => range(1, 10),
            "nextRows" => range(0, 3),
            "nextCols" => range(0, 3),
            "highscores" => $highscores,
            "rules" => Html::of($this->newsbox->contents("Tetris_Rules")),
        ]);
        return Response::create($output);
    }

    /** @return array<string,mixed> */
    private function jsConfig(Url $url): array
    {
        return [
            "getHighscoreUrl" => $url->with("tetris_action", "get_highscore")->relative(),
            "newHighscoreUrl" => $url->with("tetris_action", "new_highscore")->relative(),
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
        return Response::terminate()->withOutput((string) $this->repository->requiredHighscore());
    }

    private function showHighscoresAction(): Response
    {
        $highscores = $this->repository->highscores();
        $highscores = array_map(function (Highscore $highscore) {
            return ["player" => $highscore->player(), "score" => $highscore->score()];
        }, $highscores);
        $output = $this->view->render("highscores", [
            "highscores" => $highscores,
        ]);
        return Response::terminate()->withOutput($output);
    }

    private function newHighscoreAction(Request $request): Response
    {
        $highscore = $request->highscorePost();
        if (Util::validateHighscore($highscore)) {
            $this->repository->addHighscore($highscore);
        }
        return Response::terminate();
    }
}
