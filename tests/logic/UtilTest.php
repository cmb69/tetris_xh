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

namespace Tetris\Logic;

use PHPUnit\Framework\TestCase;
use Tetris\Value\Highscore;

class UtilTest extends TestCase
{
    /** @dataProvider highscores */
    public function testValidateHighscore(Highscore $highscore, bool $expected): void
    {
        $result = Util::validateHighscore($highscore);
        $this->assertEquals($expected, $result);
    }

    public function highscores(): array
    {
        return [
            [new Highscore("cmb", 10000), true],
            [new Highscore("", 1000), false],
            [new Highscore(str_repeat("a", 21), 1000), false],
            [new Highscore("foo:bar", 1000), false],
            [new Highscore("cmb", 0), false],
        ];
    }
}
