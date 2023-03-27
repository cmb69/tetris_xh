<?php

use Tetris\Infra\View;

/**
 * @var View $this
 * @var mixed $config
 * @var string $script
 * @var string $url
 * @var list<string> $gridRows
 * @var list<int> $gridCols
 * @var list<int> $nextRows
 * @var list<int> $nextCols
 * @var list<array{player:string,score:int}> $highscores
 * @var string $rules
 */
?>
<!-- tetris -->
<div id="tetris-no-js" class="cmsimplecore_warning"><?=$this->text('error_no_js')?></div>
<script src="<?=$script?>"></script>
<div id="tetris-tabs" data-config='<?=$this->json($config)?>'>
  <ul>
    <li><button id="tetris_button_play" type="button"><?=$this->text('label_play')?></button></li>
    <li><button id="tetris_button_highscores" type="button" disabled><?=$this->text('label_highscores')?></button></li>
    <li><button id="tetris_button_rules" type="button"><?=$this->text('label_rules')?></button></li>
  </ul>
  <div id="tetris" style="display:none">
    <div id="tetris-grid">
      <table>
<?foreach ($gridRows as $row):?>
        <tr>
<?  foreach ($gridCols as $col):?>
          <td id="tetris-<?=$row?><?=$col?>"></td>
<?  endforeach?>
        </tr>
<?endforeach?>
      </table>
    </div>
    <div style="float:left">
      <div id="tetris-next">
        <table>
<?foreach ($nextRows as $row):?>
          <tr>
<?  foreach ($nextCols as $col):?>
            <td id="tetris-x<?=$col?><?=$row?>"></td>
<?  endforeach?>
          </tr>
<?endforeach?>
        </table>
      </div>
      <div id="tetris-stats">
        <div class="label"><?=$this->text('label_level')?></div>
        <div id="tetris-level" class="led">000001</div>
        <div class="label"><?=$this->text('label_rows')?></div>
        <div id="tetris-lines" class="led">000000</div>
        <div class="label"><?=$this->text('label_score')?></div>
        <div id="tetris-score" class="led">000000</div>
      </div>
    </div>
    <div style="clear:both"></div>
    <div id="tetris-cmd">
      <button id="tetris-start"><?=$this->text('label_start')?></button>
      <button id="tetris-stop" disabled="disabled"><?=$this->text('label_stop')?></button>
    </div>
  </div>
  <div id="tetris-highscores">
    <table>
<?foreach ($highscores as $highscore):?>
      <tr>
        <td class="name"><?=$highscore['player']?></td>
        <td class="score"><?=$highscore['score']?></td>
      </tr>
<?endforeach?>
    </table>
  </div>
  <div id="tetris-rules" style="display:none">
    <div><?=$rules?></div>
    <p><?=$this->text('message_howto_play')?></p>
    <table>
      <tr><td><?=$this->text('label_left')?></td><td class="key">J / ←</td></tr>
      <tr><td><?=$this->text('label_right')?></td><td class="key">L / →</td></tr>
      <tr><td><?=$this->text('label_rotate')?></td><td class="key">I / ↑</td></tr>
      <tr><td><?=$this->text('label_down')?></td><td class="key">K / ↓</td></tr>
    </table>
  </div>
</div>
