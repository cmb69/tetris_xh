<div id="tetris-highscores">
    <table>
<?php foreach ($this->highscores as $highscore):?>
        <tr>
            <td class="name"><?=$this->escape($highscore->player)?></td>
            <td class="score"><?=$this->escape($highscore->score)?></td>
        </tr>
<?php endforeach?>
    </table>
</div>
