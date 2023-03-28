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

use Tetris\Value\Highscore;
use Tetris\Value\Url;

class Request
{
    /** @codeCoverageIgnore */
    public static function current(): self
    {
        return new self;
    }

    public function url(): Url
    {
        $rest = $this->query();
        if ($rest !== "") {
            $rest = "?" . $rest;
        }
        return Url::from(CMSIMPLE_URL . $rest);
    }

    public function action(): string
    {
        $action = $this->url()->param("tetris_action");
        if (!isset($action) || !is_string($action)) {
            return "";
        }
        return $action;
    }

    public function highscorePost(): Highscore
    {
        return new Highscore(
            $this->trimmedPostString("name"),
            (int) $this->trimmedPostString("score")
        );
    }

    private function trimmedPostString(string $key): string
    {
        $post = $this->post();
        return isset($post[$key]) && is_string($post[$key]) ? trim($post[$key]) : "";
    }

    /** @codeCoverageIgnore */
    protected function query(): string
    {
        return $_SERVER["QUERY_STRING"];
    }

    /**
     * @return array<string,string|array<string>>
     * @codeCoverageIgnore
     */
    protected function post(): array
    {
        return $_POST;
    }
}
