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

class HighscoreService
{
    /**
     * @return array
     */
    public static function readHighscores()
    {
        $fn = self::dataFolder() . 'tetris.dat';
        if (($cnt = file_get_contents($fn)) === false
            || ($highscores = unserialize($cnt)) === false
        ) {
            $highscores = [];
        }
        return $highscores;
    }

    /**
     * @return int
     */
    public static function requiredHighscore()
    {
        $highscores = self::readHighscores();
        return isset($highscores[9][1]) ? (int) $highscores[9][1] : 0;
    }

    /**
     * @param string $name
     * @param int $score
     * @return void
     */
    public static function enterHighscore($name, $score)
    {
        $highscores = self::readHighscores();
        $highscores[] = array($name, $score);
        usort($highscores, function ($a, $b) {
            return $b[1] - $a[1];
        });
        array_splice($highscores, 10);
        self::writeHighscores($highscores);
    }

    /**
      * @return void
      */
    private static function writeHighscores(array $highscores)
    {
        $fn = self::dataFolder() . 'tetris.dat';
        XH_writeFile($fn, serialize($highscores));
    }

    /**
     * @return string
     */
    public static function dataFolder()
    {
        global $pth;

        return "{$pth['folder']['base']}content/";
    }
}
