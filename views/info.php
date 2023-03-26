<?php

use Tetris\View;

/**
 * @var View $this
 * @var string $logo
 * @var string $version
 * @var list<stdClass> $checks
 */
?>
<!-- tetris plugin info -->
<h1>Tetris</h1>
<img src="<?=$logo?>" class="tetris_logo" alt="<?=$this->text('alt_logo')?>">
<p>Version: <?=$version?></p>
<p>Tetris_XH is powered by <a
  href="http://www.cmsimple-xh.org/wiki/doku.php/extend:jquery4cmsimple"
  target="_blank">jQuery4CMSimple</a> and <a
  href="http://fmarcia.info/jquery/tetris/tetris.html" target="_blank">
  Tetris with jQuery</a>.
</p>
<p>Copyright &copy; 2011-2017 Christoph M. Becker</p>
<p class="tetris_license">
  Tetris_XH is free software: you can redistribute it and/or modify it under
  the terms of the GNU General Public License as published by the Free
  Software Foundation, either version 3 of the License, or (at your option)
  any later version.
</p>
<p class="tetris_license">
  Tetris_XH is distributed in the hope that it will be useful, but <em>without
  any warranty</em>; without even the implied warranty of
  <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
  the GNU General Public License for more details.
</p>
<p class="tetris_license">
  You should have received a copy of the GNU General Public License along with
  Tetris_XH. If not, see http://www.gnu.org/licenses/.
</p>
<div class="tetris_syscheck">
  <h2><?=$this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="xh_<?=$this->escape($check->state)?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?endforeach?>
</div>
