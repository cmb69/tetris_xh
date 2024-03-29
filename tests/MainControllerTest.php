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
use Tetris\Infra\FakeRepository;
use Tetris\Infra\FakeRequest;
use Tetris\Infra\Newsbox;
use Tetris\Infra\View;
use Tetris\Value\Highscore;

class MainControllerTest extends TestCase
{
    private $sut;
    private $repository;

    public function setUp(): void
    {
        $conf = XH_includeVar("./config/config.php", "plugin_cf")["tetris"];
        $text = XH_includeVar("./languages/en.php", "plugin_tx")["tetris"];
        $this->repository = new FakeRepository;
        $newsbox = $this->createStub(Newsbox::class);
        $newsbox->method("contents")->willReturn("See <a href=\"https://en.wikipedia.org/wiki/Tetris#Gameplay\">Wikipedia</a>.");
        $view = new View("./views/", $text);
        $this->sut = new MainController("./plugins/tetris/", $conf, $this->repository, $newsbox, $view);
    }

    public function testRendersGame(): void
    {
        $response = ($this->sut)(new FakeRequest(["query" => "Tetris"]));
        Approvals::verifyHtml($response->output());
    }

    public function testReportsRequiredHighscore(): void
    {
        for ($i = 0; $i <10; $i++) {
            $this->repository->addHighscore(new Highscore("cmb", 4711));
        }
        $response = ($this->sut)(new FakeRequest(["query" => "Tetris&tetris_action=get_highscore"]));
        $this->assertTrue($response->terminated());
        $this->assertEquals("4711", $response->output());
    }

    public function testShowsHighscores(): void
    {
        $this->repository->addHighscore(new Highscore("cmb", 10000));
        $response = ($this->sut)(new FakeRequest(["query" => "Tetris&tetris_action=show_highscores"]));
        $this->assertTrue($response->terminated());
        Approvals::verifyHtml($response->output());
    }

    public function testNewHighscore(): void
    {
        $request = new FakeRequest([
            "query" => "Tetris&tetris_action=new_highscore",
            "post" => ["name" => "cmb", "score" => "10000"],
        ]);
        $response = ($this->sut)($request);
        $this->assertTrue($response->terminated());
        $this->assertEquals([new Highscore("cmb", 10000)], $this->repository->highscores());
    }
}
