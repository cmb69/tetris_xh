/**
 * Tetris with jQuery - 2006/06/25
 *   see: http://en.wikipedia.org/wiki/Category:Tetris
 *        http://en.wikipedia.org/wiki/Tetris_Worlds
 *   be careful: http://en.wikipedia.org/wiki/Tetris_effect
 * Copyright (c) 2006 Franck Marcia
 * Copyright (c) 2011-2017 Christoph M. Becker
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */


(function() {
    function one(selector) {
        return document.querySelector(selector);
    }

    function all(selector) {
        return document.querySelectorAll(selector);
    }

    function each(items, fun) {
        for (i = 0; i < items.length; i++) {
            fun(items[i]);
        }
    }

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
                    tetris.cells[i][j] = one(["#tetris-", k, j].join(""));
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
            each(all("#tetris-grid td"), function (element) {
                element.style.backgroundColor = tetris.colors[0];
            });
            let button = one("#tetris-start");
            button.textContent = tetris.config.labelPause;
            button.onclick = tetris.pause;
            one("#tetris-stop").disabled = null;
            document.addEventListener("keydown", tetris.key);
            tetris.next = tetris.newShape();
            tetris.shift();
            tetris.duration = tetris.config.initialSpeed;
            tetris.refresh();
            tetris.timer = window.setInterval(tetris.moveDown, tetris.duration);
        },

        // Define the action to be fired depending on key entry
        key: function(e) {
            switch(e.charCode || e.keyCode) {
                case 74: case 106: case 37: tetris.moveLeft(); break; // J or <-
                case 76: case 108: case 39: tetris.moveRight(); break; // L or ->
                case 75: case 107: case 40: if (tetris.config.falldown) {tetris.fallDown()} else {tetris.moveDown()}; break; // K or v
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
                    one(["#tetris-x", j, i].join("")).style.backgroundColor = d;
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
            document.removeEventListener("keydown", tetris.key);
            window.clearInterval(tetris.timer);
            tetris.timer = null;
            let button = one("#tetris-start");
            button.textContent = tetris.config.labelResume;
            button.onclick = tetris.resume;
        },

        // Resume the game
        resume: function() {
            document.addEventListener("keydown", tetris.key);
            tetris.timer = window.setInterval(tetris.moveDown, tetris.duration);
            let button = one("#tetris-start");
            button.textContent = tetris.config.labelPause;
            button.onclick = tetris.pause;
        },

        // Stop the game
        gameOver: function() {
            var i, j;
            // Manage buttons
            if (tetris.timer) {
                document.removeEventListener("keydown", tetris.key);
                window.clearInterval(tetris.timer);
                tetris.timer = null;
                let button = one("#tetris-start");
                button.textContent = tetris.config.labelStart;
                button.onclick = tetris.start;
            } else {
                let button = one("#tetris-start");
                button.textContent = tetris.config.labelStart;
                button.onclick = tetris.start;
            }
            document.getElementById("tetris-stop").disabled = "disabled";
            // Draw everything in grey
            for (i = 0; i < 18; ++i) {
                for (j = 1; j < 11; ++j) {
                    if (tetris.grid[i][j]) {
                        tetris.cells[i][j].style.backgroundColor = "#cccccc";
                    }
                }
            }
            tetris.draw(tetris.r0, tetris.x0, tetris.y0, '#cccccc');

            // clear the next shape
            for (i = 0; i < 4; ++i) {
                for (j = 0; j < 4; ++j) {
                    one(["#tetris-x", j, i].join("")).style.backgroundColor = tetris.colors[0];
                }
            }

            let request = new XMLHttpRequest();
            request.open("GET", tetris.config.getHighscoreUrl);
            request.onreadystatechange = function () {
                if (request.readyState === 4 && request.status === 200) {
                    if (+tetris.score > +request.responseText) {
                        tetris.newHighscore();
                    }
                }
            };
            request.send();
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
                        tetris.cells[k][j].style.backgroundColor = "#cccccc";
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
                tetris.duration = tetris.config.initialSpeed - tetris.config.acceleration * tetris.level;
            }
            window.clearTimeout(tetris.timer);
            tetris.timer = window.setInterval(tetris.moveDown, tetris.duration);
            tetris.score += (tetris.level + 1) * tetris.points[f];
            // redraw the grid
            for (i = 0; i < 18; ++i) {
                for (j = 1; j < 11; ++j) {
                    tetris.cells[i][j].style.backgroundColor =
                        tetris.colors[tetris.grid[i][j]];
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
                        let cell = tetris.cells[y + j][x + i];
                        if (cell) {
                            cell.style.backgroundColor = c;
                        }
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
            one("#tetris-level").textContent = tetris.format(tetris.level + 1);
            one("#tetris-lines").textContent = tetris.format(tetris.lines);
            one("#tetris-score").textContent = tetris.format(tetris.score);
            // reset coordinates
            tetris.x0 = tetris.x;
            tetris.y0 = tetris.y;
            tetris.r0 = tetris.r;
        },

        // enters new highscore
        newHighscore: function () {        
            var name = prompt("Your name");
            if (name) {
                let request = new XMLHttpRequest();
                request.open("POST", tetris.config.newHighscoreUrl);
                request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                request.send("name=" + encodeURIComponent(name) + "&score=" + encodeURIComponent(tetris.score));
            }
        },

        // Format the number
        format: function(number) {
            var res = '000000'.concat(number);
            return res.substring(res.length-6);
        }
    };

    // Initialization
    addEventListener("DOMContentLoaded", function() {
        tetris.config = JSON.parse(document.getElementById("tetris-tabs").dataset.config);
        tetris.init();
        one("#tetris-no-js").style = "display: none !important";
        one("#tetris_button_play").onclick = function () {
            one("#tetris_button_play").disabled = true;
            one("#tetris_button_highscores").disabled = false;
            one("#tetris_button_rules").disabled = false;
            one("#tetris").style = "display: block";
            one("#tetris-highscores").style = "display: none";
            one("#tetris-rules").style = "display: none";
        };
        one("#tetris_button_highscores").onclick = function () {
            one("#tetris_button_play").disabled = false;
            one("#tetris_button_highscores").disabled = true;
            one("#tetris_button_rules").disabled = false;
            one("#tetris").style = "display: none";
            one("#tetris-highscores").style = "display: block";
            one("#tetris-rules").style = "display: none";
        };
        one("#tetris_button_rules").onclick = function () {
            one("#tetris_button_play").disabled = false;
            one("#tetris_button_highscores").disabled = false;
            one("#tetris_button_rules").disabled = true;
            one("#tetris").style = "display: none";
            one("#tetris-highscores").style = "display: none";
            one("#tetris-rules").style = "display: block";
        };

        each(all('#tetris-grid table, #tetris-next table'), function (element) {
            element.style.backgroundColor = tetris.colors[0];
        });
        one("#tetris-start").onclick = tetris.start;
        one("#tetris-stop").onclick = tetris.gameOver;
   });
})();
