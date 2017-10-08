<?php

/**
 * Front-end functionality of Tetris_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Tetris
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Tetris_XH
 */

/**
 * Returns the view of the Tetris plugin.
 *
 * @return string
 *
 * @global array  The paths of system files and folders.
 * @global array  The localization of the plugins.
 * @global string The script name.
 * @global string The current page URL.
 */
function tetris()
{
    return Tetris\Plugin::main();
}

Tetris\Plugin::run();
