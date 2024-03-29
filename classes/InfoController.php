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

namespace Tetris;

use Tetris\Infra\Repository;
use Tetris\Infra\SystemChecker;
use Tetris\Infra\View;
use Tetris\Value\Response;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var Repository */
    private $repository;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        SystemChecker $systemChecker,
        Repository $repository,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->repository = $repository;
        $this->view = $view;
    }

    public function defaultAction(): Response
    {
        $output = $this->view->render("info", [
            "version" => TETRIS_VERSION,
            "checks" => $this->getChecks(),
        ]);
        return Response::create($output);
    }

    /** @return list<array{class:string,key:string,arg:string,statekey:string}> */
    public function getChecks(): array
    {
        return array(
            $this->checkPhpVersion("7.1.0"),
            $this->checkExtension('json'),
            $this->checkXhVersion("1.7.0"),
            $this->checkWritability($this->pluginFolder . "css/"),
            $this->checkWritability($this->pluginFolder . "config/"),
            $this->checkWritability($this->pluginFolder . "languages/"),
            $this->checkWritability($this->repository->dataFolder())
        );
    }

    /** @return array{class:string,key:string,arg:string,statekey:string} */
    private function checkPhpVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return [
            "class" => "xh_$state",
            "key" => "syscheck_phpversion",
            "arg" => $version,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return array{class:string,key:string,arg:string,statekey:string} */
    private function checkExtension(string $extension, bool $isMandatory = true): array
    {
        $state = $this->systemChecker->checkExtension($extension) ? 'success' : ($isMandatory ? 'fail' : 'warning');
        return [
            "class" => "xh_$state",
            "key" => "syscheck_extension",
            "arg" => $extension,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return array{class:string,key:string,arg:string,statekey:string} */
    private function checkXhVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? 'success' : 'fail';
        return [
            "class" => "xh_$state",
            "key" => "syscheck_xhversion",
            "arg" => $version,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return array{class:string,key:string,arg:string,statekey:string} */
    private function checkWritability(string $folder): array
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        return [
            "class" => "xh_$state",
            "key" => "syscheck_writable",
            "arg" => $folder,
            "statekey" => "syscheck_$state",
        ];
    }
}
