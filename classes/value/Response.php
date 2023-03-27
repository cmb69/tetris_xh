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

namespace Tetris\Value;

class Response
{
    public static function create(string $output): self
    {
        $that = new self;
        $that->output = $output;
        return $that;
    }

    public static function terminate(): self
    {
        $that = new self;
        $that->terminated = true;
        return $that;
    }

    /** @var string */
    private $output = "";

    /** @var bool */
    private $terminated = false;

    public function output(): string
    {
        return $this->output;
    }

    public function terminated(): bool
    {
        return $this->terminated;
    }

    public function withOutput(string $output): self
    {
        $that = clone $this;
        $that->output = $output;
        return $that;
    }
}
