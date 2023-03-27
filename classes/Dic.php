<?php

/**
 * Copyright 2023 Christoph M. Becker
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
use Tetris\Infra\SystemChecker;
use Tetris\Infra\View;

class Dic
{
    public static function makeMainController(): MainController
    {
        global $pth, $plugin_cf, $plugin_tx;
        return new MainController(
            $plugin_cf["tetris"],
            $plugin_tx["tetris"],
            self::makeHighscoreService(),
            new Jquery,
            self::makeView()
        );
    }

    public static function makeInfoController(): InfoController
    {
        global $pth, $plugin_tx;
        return new InfoController(
            "{$pth['folder']['plugins']}tetris/",
            $plugin_tx["tetris"],
            new SystemChecker,
            self::makeHighscoreService(),
            self::makeView()
        );
    }

    private static function makeHighscoreService(): HighscoreService
    {
        global $pth;
        return new HighscoreService("{$pth['folder']['base']}content/");
    }

    private static function makeView(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "tetris/views/", $plugin_tx["tetris"]);
    }
}
