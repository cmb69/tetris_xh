<?php

use Tetris\Infra\View;

/**
 * @var View $this
 * @var list<array{player:string,score:int}> $highscores
 */
?>
<!-- tetris highscores -->
<div id="tetris-highscores">
  <table>
<?foreach ($highscores as $highscore):?>
    <tr>
      <td class="name"><?=$this->escape($highscore['player'])?></td>
      <td class="score"><?=$this->escape($highscore['score'])?></td>
    </tr>
<?endforeach?>
  </table>
</div>
