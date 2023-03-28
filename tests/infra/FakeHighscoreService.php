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

namespace Tetris\Infra;

class FakeHighscoreService extends HighscoreService
{
    private $data = [];

    public function __construct() {}

    public function readHighscores()
    {
        return $this->data;
    }

    public function enterHighscore(string $name, int $score)
    {
        $this->data[] = [$name, $score];
        usort($this->data, function ($a, $b) {
            return $b[1] <=> $a[1];
        });
        $this->data = array_splice($this->data, 0, 10);
    }

    public function dataFolder(): string
    {
        return "./content/";
    }
}
