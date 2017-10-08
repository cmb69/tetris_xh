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
}
