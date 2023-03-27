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

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Tetris\Infra\FakeHighscoreService;
use Tetris\Infra\Jquery;
use Tetris\Infra\View;

class MainControllerTest extends TestCase
{
    private $sut;
    private $highscoreService;

    public function setUp(): void
    {
        $conf = XH_includeVar("./config/config.php", "plugin_cf")["tetris"];
        $text = XH_includeVar("./languages/en.php", "plugin_tx")["tetris"];
        $this->highscoreService = new FakeHighscoreService;
        $jquery = $this->createStub(Jquery::class);
        $view = new View("./views/", $text);
        $this->sut = new MainController("./plugins/tetris/", $conf, $this->highscoreService, $jquery, $view);
    }

    public function testRendersGame(): void
    {
        $response = ($this->sut)();
        Approvals::verifyHtml($response->output());
    }

    public function testReportsRequiredHighscore(): void
    {
        for ($i = 0; $i <10; $i++) {
            $this->highscoreService->enterHighscore("cmb", 4711);
        }
        $_GET = ["tetris_action" => "get_highscore"];
        $response = ($this->sut)();
        $this->assertTrue($response->terminated());
        $this->assertEquals("4711", $response->output());
    }

    public function testShowsHighscores(): void
    {
        $this->highscoreService->enterHighscore("cmb", 10000);
        $_GET = ["tetris_action" => "show_highscores"];
        $response = ($this->sut)();
        $this->assertTrue($response->terminated());
        Approvals::verifyHtml($response->output());
    }

    public function testNewHighscore(): void
    {
        $_GET = ["tetris_action" => "new_highscore"];
        $_POST = ["name" => "cmb", "score" => "10000"];
        $response = ($this->sut)();
        $this->assertTrue($response->terminated());
        $this->assertEquals([["cmb", 10000]], $this->highscoreService->readHighscores());
    }
}
