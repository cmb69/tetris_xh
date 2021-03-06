<div id="tetris-no-js" class="cmsimplecore_warning"><?=$this->text('error_no_js')?></div>
<div id="tetris-tabs">
    <ul>
        <li><a href="#tetris"><?=$this->text('label_play')?></a></li>
        <li><a href="<?=$this->url()?>"><?=$this->text('label_highscores')?></a></li>
        <li><a href="#tetris-rules"><?=$this->text('label_rules')?></a></li>
    </ul>
    <div id="tetris">
        <div id="tetris-grid">
            <table>
<?php foreach ($this->gridRows as $row):?>
                <tr>
<?php   foreach ($this->gridCols as $col):?>
                    <td id="tetris-<?=$this->escape($row)?><?=$this->escape($col)?>"></td>
<?php   endforeach?>
                </tr>
<?php endforeach?>
            </table>
        </div>
        <div style="float:left">
            <div id="tetris-next">
                <table>
<?php foreach ($this->nextRows as $row):?>
                    <tr>
<?php   foreach ($this->nextCols as $col):?>
                        <td id="tetris-x<?=$this->escape($col)?><?=$this->escape($row)?>"></td>
<?php   endforeach?>
                    </tr>
<?php endforeach?>
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
    <div id="tetris-rules">
        <div><?=$this->text('message_howto_play')?></div>
        <table>
            <tr><td><?=$this->text('label_left')?></td><td class="key">J / &larr;</td></tr>
            <tr><td><?=$this->text('label_right')?></td><td class="key">L / &rarr;</td></tr>
            <tr><td><?=$this->text('label_rotate')?></td><td class="key">I / &uarr;</td></tr>
            <tr><td><?=$this->text('label_down')?></td><td class="key">K / &darr;</td></tr>
        </table>
    </div>
</div>
