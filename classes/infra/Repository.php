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

namespace Tetris\Infra;

use Tetris\Value\Highscore;

class Repository
{
    private const MAX_HIGHSCORES = 10;

    /** @var string */
    private $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    /** @return list<Highscore> */
    public function highscores()
    {
        if (($stream = @fopen($this->filename(), "r")) === false) {
            return [];
        }
        flock($stream, LOCK_SH);
        $highscores = $this->read($stream);
        flock($stream, LOCK_UN);
        fclose($stream);
        return $highscores;
    }

    /**
     * @param resource $stream
     * @return list<Highscore>
     */
    private function read($stream): array
    {
        $highscores = [];
        while (($line = fgets($stream)) !== false) {
            $fields = explode(":", $line, 2);
            if (count($fields) < 2) {
                continue;
            }
            $highscores[] = new Highscore($fields[0], (int) $fields[1]);
        }
        return $highscores;
    }

    public function requiredHighscore(): int
    {
        $highscores = $this->highscores();
        return isset($highscores[self::MAX_HIGHSCORES - 1])
            ? $highscores[self::MAX_HIGHSCORES - 1]->score()
            : 0;
    }

    public function addHighscore(Highscore $highscore): bool
    {
        if (($stream = @fopen($this->filename(), "c+")) === false) {
            return false;
        }
        flock($stream, LOCK_EX);
        $highscores = $this->read($stream);
        $highscores = $this->add($highscores, $highscore);
        rewind($stream);
        if (!$this->write($stream, $highscores)) {
            return false;
        }
        flock($stream, LOCK_UN);
        fclose($stream);
        return true;
    }

    /**
     * @param list<Highscore> $highscores
     * @return list<Highscore>
     */
    private function add(array $highscores, Highscore $highscore): array
    {
        $highscores[] = $highscore;
        usort($highscores, function ($a, $b) {
            return $b->score() <=> $a->score();
        });
        return array_slice($highscores, 0, self::MAX_HIGHSCORES);
    }

    /**
     * @param resource $stream
     * @param list<Highscore> $highscores
     */
    private function write($stream, array $highscores): bool
    {
        foreach ($highscores as $highscore) {
            $line = $highscore->player() . ":" . (string) $highscore->score();
            if (fwrite($stream, $line . "\n") === false) {
                return false;
            }
        }
        return true;
    }

    public function dataFolder(): string
    {
        return $this->dataFolder;
    }

    private function filename(): string
    {
        return $this->dataFolder . "tetris.txt";
    }
}
