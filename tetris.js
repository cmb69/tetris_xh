/**
 * Tetris with jQuery - 2006/06/25
 *   see: http://en.wikipedia.org/wiki/Category:Tetris
 *        http://en.wikipedia.org/wiki/Tetris_Worlds
 *   be careful: http://en.wikipedia.org/wiki/Tetris_effect
 * Copyright (c) 2006 Franck Marcia
 * Copyright (c) 2011-2013 Christoph M. Becker
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */




(function($) {
    var tetris = {

	// Shape colors
	colors: ['#eaeaea','#ff6600','#eec900','#0000ff',
	    '#cc00ff','#00ff00','#66ccff','#ff0000'],

	// Starting line for each shape
	startAt: [0, -1, -1, -1, 0, -1, -1, 0],

	// Points per number of lines
	points: [0, 40, 100, 300, 1200],

	// Combination of each shape
	shapes: [
	    // none
	    [],
	    // I
	    [[[0,0,0,0],[1,1,1,1],[0,0,0,0],[0,0,0,0]],
	     [[0,1,0,0],[0,1,0,0],[0,1,0,0],[0,1,0,0]]],
	    // T
	    [[[0,0,0,0],[1,1,1,0],[0,1,0,0],[0,0,0,0]],
	     [[0,1,0,0],[1,1,0,0],[0,1,0,0],[0,0,0,0]],
	     [[0,1,0,0],[1,1,1,0],[0,0,0,0],[0,0,0,0]],
	     [[0,1,0,0],[0,1,1,0],[0,1,0,0],[0,0,0,0]]],
	    // L
	    [[[0,0,0,0],[1,1,1,0],[1,0,0,0],[0,0,0,0]],
	     [[1,1,0,0],[0,1,0,0],[0,1,0,0],[0,0,0,0]],
	     [[0,0,1,0],[1,1,1,0],[0,0,0,0],[0,0,0,0]],
	     [[0,1,0,0],[0,1,0,0],[0,1,1,0],[0,0,0,0]]],
	    // J
	    [[[1,0,0,0],[1,1,1,0],[0,0,0,0],[0,0,0,0]],
	     [[0,1,1,0],[0,1,0,0],[0,1,0,0],[0,0,0,0]],
	     [[0,0,0,0],[1,1,1,0],[0,0,1,0],[0,0,0,0]],
	     [[0,1,0,0],[0,1,0,0],[1,1,0,0],[0,0,0,0]]],
	    // Z
	    [[[0,0,0,0],[1,1,0,0],[0,1,1,0],[0,0,0,0]],
	     [[0,0,1,0],[0,1,1,0],[0,1,0,0],[0,0,0,0]]],
	    // S
	    [[[0,0,0,0],[0,1,1,0],[1,1,0,0],[0,0,0,0]],
	     [[0,1,0,0],[0,1,1,0],[0,0,1,0],[0,0,0,0]]],
	    // O
	    [[[0,1,1,0],[0,1,1,0],[0,0,0,0],[0,0,0,0]]]],

	// Pre-load elements of the grid
	init: function() {
	    var i, j, k;
	    tetris.cells = [];
	    for (i = -3; i < 18; ++i) {
		tetris.cells[i] = [];
		for (j = 1; j < 11; ++j) {
		    k = String.fromCharCode(i + 100);
		    tetris.cells[i][j] = $(['#tetris-', k, j].join(''));
		}
	    }
	    tetris.bound = document;
	},

	// Initialize to start the game
	start: function() {
	    // Stats
	    tetris.level = 0;
	    tetris.lines = 0;
	    tetris.score = 0;
	    // Array which contains data of the grid
	    tetris.grid = [
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,0,0,0,0,0,0,0,0,0,0,1],[1,0,0,0,0,0,0,0,0,0,0,1],
		[1,1,1,1,1,1,1,1,1,1,1,1]];
	    $('#tetris-grid td').css('background-color', tetris.colors[0]);
	    $('#tetris-start').unbind('click', tetris.start).val(TETRIS_TX['label_pause']).click(tetris.pause);
	    $('#tetris-stop').button('enable');
	    $(tetris.bound).keypress(tetris.key);
	    tetris.next = tetris.newShape();
	    tetris.shift();
	    tetris.duration = TETRIS_SPEED_INITIAL;
	    tetris.refresh();
	    tetris.timer = window.setInterval(tetris.moveDown, tetris.duration);
	},

	// Define the action to be fired depending on key entry
	key: function(e) {
	    switch(e.charCode || e.keyCode) {
		case 74: case 106: case 37: tetris.moveLeft(); break; // J or <-
		case 76: case 108: case 39: tetris.moveRight(); break; // L or ->
		case 75: case 107: case 40: if (TETRIS_FALLDOWN) {tetris.fallDown()} else {tetris.moveDown()}; break; // K or v
		case 73: case 105: case 38: tetris.rotate(); break; // I or ^
	    }
	    return false;
	},

	// Generate an random shape
	newShape: function() {
	    var r = 1 + Math.random() * 7;
	    return parseInt(r > 7 ? 7 : r, 10);
	},

	// Define then draw the next shape
	setNext: function() {
	    var i, j, s, c, d, n = tetris.colors[0];
	    tetris.next = tetris.newShape();
	    s = tetris.shapes[tetris.next][0];
	    c = tetris.colors[tetris.next];
	    for (i = 0; i < 4; ++i) {
		for (j = 0; j < 4; ++j) {
		    d = s[i][j] ? c : n;
		    $(['#tetris-x', j, i].join('')).css('background-color', d);
		}
	    }
	},

	// The next shape becomes the current one; reset coordinates
	shift: function() {
	    tetris.cur = tetris.next;
	    tetris.x = tetris.x0 = 4;
	    tetris.y = tetris.startAt[tetris.cur];
	    tetris.y0 = tetris.y - 2;
	    tetris.r = tetris.r0 = 0;
	    tetris.curShape = tetris.shapes[tetris.cur];
	    if (tetris.canGo(0, tetris.x, tetris.y)) {
		tetris.setNext();
		return true;
	    }
	    return false;
	},

	// Pause the game
	pause: function() {
	    $(tetris.bound).unbind('keypress', tetris.key);
	    window.clearInterval(tetris.timer);
	    tetris.timer = null;
	    $('#tetris-start').unbind('click', tetris.pause).val(TETRIS_TX['label_resume']).click(tetris.resume);
	},

	// Resume the game
	resume: function() {
	    $(tetris.bound).keypress(tetris.key);
	    tetris.timer = window.setInterval(tetris.moveDown, tetris.duration);
	    $('#tetris-start').unbind('click', tetris.resume).val(TETRIS_TX['label_pause']).click(tetris.pause);
	},

	// Stop the game
	gameOver: function() {
	    var i, j;
	    // Manage buttons
	    if (tetris.timer) {
		$(tetris.bound).unbind('keypress', tetris.key);
		window.clearInterval(tetris.timer);
		tetris.timer = null;
		$('#tetris-start').unbind('click', tetris.pause).val(TETRIS_TX['label_start']).click(tetris.start);
	    } else {
		$('#tetris-start').unbind('click', tetris.resume).val(TETRIS_TX['label_start']).click(tetris.start);
	    }
	    $('#tetris-stop').button('disable');
	    // Draw everything in grey
	    for (i = 0; i < 18; ++i) {
		for (j = 1; j < 11; ++j) {
		    if (tetris.grid[i][j]) {
			tetris.cells[i][j].css('background-color', '#cccccc');
		    }
		}
	    }
	    tetris.draw(tetris.r0, tetris.x0, tetris.y0, '#cccccc');

	    // clear the next shape
	    for (i = 0; i < 4; ++i) {
		for (j = 0; j < 4; ++j) {
		    $(['#tetris-x', j, i].join('')).css('background-color', tetris.colors[0]);
		}
	    }

	    $.ajax({
		url: TETRIS_HIGHSCORES+'?action=required',
		async: false,
		success: function(data) {
		    if (tetris.score > data) {
			$('#tetris-highscore-dlg').dialog('open');
		    }
		}
	    });
	},

	// Check overlays
	canGo: function(r, x, y) {
	    var i, j;
	    for (i = 0; i < 4; ++i) {
		for (j = 0; j < 4; ++j) {
		    if (tetris.curShape[r][j][i] && tetris.grid[y + j] &&
			    tetris.grid[y + j][x + i]) {
			return false;
		    }
		}
	    }
	    return true;
	},

	// Move the current shape to the left
	moveLeft: function() {
	    if (tetris.canGo(tetris.r, tetris.x - 1, tetris.y)) {
		--tetris.x;
		tetris.refresh();
	    }
	},

	// Move the current shape to the right
	moveRight: function() {
	    if (tetris.canGo(tetris.r, tetris.x + 1, tetris.y)) {
		++tetris.x;
		tetris.refresh();
	    }
	},

	// Rotate the current shape
	rotate: function() {
	    var r = tetris.r == tetris.curShape.length - 1 ? 0 : tetris.r + 1;
	    if (tetris.canGo(r, tetris.x, tetris.y)) {
		tetris.r0 = tetris.r;
		tetris.r = r;
		tetris.refresh();
	    }
	},

	// Move down the current shape
	moveDown: function(complete) {
	    if (tetris.canGo(tetris.r, tetris.x, tetris.y + 1)) {
		++tetris.y;
		tetris.refresh();
	    } else {
		tetris.touchDown();
	    }
	},

	// Fall down the current shape
	fallDown: function() {
	    while (tetris.canGo(tetris.r, tetris.x, tetris.y + 1)) {
		++tetris.y;
	    }
	    tetris.refresh();
	    tetris.touchDown();
	},

	// The current shape touches down
	touchDown: function() {
	    var i, j, k, r, f;
	    // mark the grid
	    for (i = 0; i < 4; ++i) {
		for (j = 0; j < 4; ++j) {
		    if (tetris.curShape[tetris.r][j][i] &&
			    tetris.grid[tetris.y + j]) {
			tetris.grid[tetris.y + j][tetris.x + i] = tetris.cur;
		    }
		}
	    }
	    // search complete lines
	    f = 0;
	    for (i = 17, k = 17; i > -1 && f < 4; --i, --k) {
		if (tetris.grid[i].join('').indexOf('0') == -1) {
		    // Complete lines become white
		    for (j = 1; j < 11; ++j) {
			tetris.cells[k][j].css('background-color', '#cccccc');
		    }
		    ++f;
		    for (j = i; j > 0; --j) {
			tetris.grid[j] = tetris.grid[j - 1].concat();
		    }
		    ++i;
		}
	    }
	    // animate
	    if (f) {
		window.clearInterval(tetris.timer);
		tetris.timer = window.setTimeout(function(){tetris.after(f);}, 100);
	    }
	    // try to continue
	    if (tetris.shift()) {
		tetris.refresh();
	    } else {
		tetris.gameOver();
	    }
	},

	// Finish the touchdown process
	after: function(f) {
	    var i, j, l = (tetris.level < 20 ? tetris.level : 20) * 25;
	    // stats
	    tetris.lines += f;
	    if (tetris.lines % 10 === 0) {
		tetris.level = tetris.lines / 10;
		tetris.duration = TETRIS_SPEED_INITIAL - TETRIS_SPEED_ACCELERATION * tetris.level;
	    }
	    window.clearTimeout(tetris.timer);
	    tetris.timer = window.setInterval(tetris.moveDown, tetris.duration);
	    tetris.score += (tetris.level + 1) * tetris.points[f];
	    // redraw the grid
	    for (i = 0; i < 18; ++i) {
		for (j = 1; j < 11; ++j) {
		    tetris.cells[i][j].css('background-color',
			tetris.colors[tetris.grid[i][j]]);
		}
	    }
	    tetris.refresh();
	},

	// Draw the current shape
	draw: function(r, x, y, c) {
	    var i, j;
	    for (i = 0; i < 4; ++i) {
		for (j = 0; j < 4; ++j) {
		    if (tetris.curShape[r][j][i]) {
			tetris.cells[y + j][x + i].css('background-color', c);
		    }
		}
	    }
	},

	// Refresh the grid
	refresh: function() {
	    // remove from the old position
	    tetris.draw(tetris.r0, tetris.x0, tetris.y0, tetris.colors[0]);
	    // draw to the next one
	    tetris.draw(tetris.r, tetris.x, tetris.y, tetris.colors[tetris.cur]);
	    // change stats
	    $('#tetris-level').html(tetris.format(tetris.level + 1));
	    $('#tetris-lines').html(tetris.format(tetris.lines));
	    $('#tetris-score').html(tetris.format(tetris.score));
	    // reset coordinates
	    tetris.x0 = tetris.x;
	    tetris.y0 = tetris.y;
	    tetris.r0 = tetris.r;
	},

	// Format the number
	format: function(number) {
	    var res = '000000'.concat(number);
	    return res.substring(res.length-6);
	}
    };


    // Initialization
    $(function() {
 	$('#tetris-tabs').tabs();
	tetris.init();
	$('#tetris-no-js').hide();
	$('#tetris-start, #tetris-stop, #tetris-about-btn').button();
	$('#tetris-stop').button('disable');
	$('#tetris-grid table, #tetris-next table').css('background-color', tetris.colors[0]);
	$('#tetris-start').click(tetris.start);
	$('#tetris-stop').click(tetris.gameOver);
	$('#tetris-highscore-dlg').dialog({
	    autoOpen: false,
	    modal: true,
	    buttons: [{
		text: TETRIS_TX['label_ok'],
		click: function() {
		    var name = $(this).find('input').val();
		    $.ajax({
			url: TETRIS_HIGHSCORES,
			type: 'POST',
			data: {
			    action: 'new',
			    name: name,
			    score: tetris.score
			}
		    });
		    $(this).dialog('close');
		}
	    }, {
		text: TETRIS_TX['label_cancel'],
		click: function() {$(this).dialog('close')}
	    }]
	});
	$('#tetris-about-dlg').dialog({
	    autoOpen: false,
	    modal: true,
	    buttons: [{
		text: TETRIS_TX['label_ok'],
		click: function() {
		    $(this).dialog('close');
		}
	    }]
	});
   });
})(jQuery);
