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

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class HighscoreServiceTest extends TestCase
{
    public function testEntersHighscore(): void
    {
        vfsStream::setup("root");
        touch("vfs://root/tetris.dat");
        $sut = new HighscoreService("vfs://root/");
        $sut->enterHighscore("cmb", 10000);
        $sut->enterHighscore("anon", 1000);
        $this->assertStringEqualsFile(
            "vfs://root/tetris.dat",
            'a:2:{i:0;a:2:{i:0;s:3:"cmb";i:1;i:10000;}i:1;a:2:{i:0;s:4:"anon";i:1;i:1000;}}'
        );
    }

    public function testRequiredHighscore(): void
    {
        vfsStream::setup("root");
        touch("vfs://root/tetris.dat");
        $sut = new HighscoreService("vfs://root/");
        $result = $sut->requiredHighscore();
        $this->assertEquals(0, $result);
    }
}
