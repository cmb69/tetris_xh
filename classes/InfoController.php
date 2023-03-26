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

class InfoController extends Controller
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth, $plugin_tx;

        $view = new View($pth["folder"]["plugins"] . "tetris/views/", $plugin_tx["tetris"]);
        echo $view->render("info", [
            "logo" => "{$pth['folder']['plugins']}tetris/tetris.png",
            "version" => Plugin::VERSION,
            "checks" => (new SystemCheckService)->getChecks(),
        ]);
    }
}
