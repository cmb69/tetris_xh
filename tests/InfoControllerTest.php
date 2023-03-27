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
use Tetris\Infra\HighscoreService;
use Tetris\Infra\SystemChecker;
use Tetris\Infra\View;

class InfoControllerTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $text = XH_includeVar("./languages/en.php", "plugin_tx")["tetris"];
        $systemChecker = $this->createStub(SystemChecker::class);
        $systemChecker->method("checkVersion")->willReturn(false);
        $systemChecker->method("checkExtension")->willReturn(false);
        $systemChecker->method("checkPlugin")->willReturn(false);
        $systemChecker->method("checkWritability")->willReturn(false);
        $highscoreService = $this->createStub(HighscoreService::class);
        $highscoreService->method("dataFolder")->willReturn("./content/");
        $view = new View("./views/", $text);
        $sut = new InfoController("./plugins/", $systemChecker, $highscoreService, $view);
        $response = $sut->defaultAction();
        Approvals::verifyHtml($response->output());
    }
}
