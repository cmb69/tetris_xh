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

class HighscoreService
{
    /** @var string */
    private $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    /** @return list<array{string,int}> */
    public function readHighscores()
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
     * @return list<array{string,int}>
     */
    private function read($stream): array
    {
        $highscores = [];
        while (($line = fgets($stream)) !== false) {
            $fields = explode(":", $line, 2);
            if (count($fields) < 2) {
                continue;
            }
            $highscores[] = [$fields[0], (int) $fields[1]];
        }
        return $highscores;
    }

    public function requiredHighscore(): int
    {
        $highscores = $this->readHighscores();
        return isset($highscores[9][1]) ? (int) $highscores[9][1] : 0;
    }

    /** @return void */
    public function enterHighscore(string $name, int $score)
    {
        if (($stream = @fopen($this->filename(), "c+")) === false) {
            return;
        }
        flock($stream, LOCK_EX);
        $highscores = $this->read($stream);
        $highscores = $this->add($highscores, [$name, $score]);
        rewind($stream);
        $this->write($stream, $highscores);
        flock($stream, LOCK_UN);
        fclose($stream);
    }

    /**
     * @param list<array{string,int}> $highscores
     * @param array{string,int} $highscore
     * @return list<array{string,int}>
     */
    private function add(array $highscores, array $highscore): array
    {
        $highscores[] = $highscore;
        usort($highscores, function ($a, $b) {
            return $b[1] <=> $a[1];
        });
        return array_slice($highscores, 0, 10);
    }

    /**
     * @param resource $stream
     * @param list<array{string,int}> $highscores
     * @return void
     */
    private function write($stream, array $highscores)
    {
        foreach ($highscores as $highscore) {
            fwrite($stream, implode(":", $highscore));
            fwrite($stream, "\n");
        }
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
