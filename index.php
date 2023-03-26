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

/**
 * @return string
 */
function tetris()
{
    $controller = new Tetris\MainController;
    if (isset($_GET['tetris_action'])) {
        $action = str_replace('_', '', $_GET['tetris_action']) . 'Action';
        if (!method_exists($controller, $action)) {
            $action = 'defaultAction';
        }
    } else {
        $action = 'defaultAction';
    }
    ob_start();
    $controller->$action();
    return (string) ob_get_clean();
}

(new Tetris\Plugin)->run();
