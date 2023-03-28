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
    public function testReadsExisitingHighscores(): void
    {
        vfsStream::setup("root");
        file_put_contents("vfs://root/tetris.txt", "cmb:10000\nbroken\nother:3000\n");
        $sut = new HighscoreService("vfs://root/");
        $result = $sut->readHighscores();
        $this->assertEquals([["cmb", 10000], ["other", 3000]], $result);
    }

    public function testEntersHighscore(): void
    {
        vfsStream::setup("root");
        $sut = new HighscoreService("vfs://root/");
        $sut->enterHighscore("cmb", 10000);
        $sut->enterHighscore("anon", 1000);
        $this->assertStringEqualsFile("vfs://root/tetris.txt", "cmb:10000\nanon:1000\n");
    }

    public function testLimitsNumberOfHighscores(): void
    {
        vfsStream::setup("root");
        file_put_contents("vfs://root/tetris.txt", "a:10\nb:9\nc:8\nd:7\ne:6\nf:5\ng:4\nh:3\ni:2\nj:1\n");
        $sut = new HighscoreService("vfs://root/");
        $sut->enterHighscore("cmb", 100);
        $result = $sut->readHighscores();
        $this->assertCount(10, $result);
    }

    public function testRequiredHighscore(): void
    {
        vfsStream::setup("root");
        $sut = new HighscoreService("vfs://root/");
        $result = $sut->requiredHighscore();
        $this->assertEquals(0, $result);
    }

    public function testReportsDataFolder(): void
    {
        vfsStream::setup("root");
        $sut = new HighscoreService("vfs://root/");
        $result = $sut->dataFolder();
        $this->assertEquals("vfs://root/", $result);
    }
}
