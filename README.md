# Tetris_XH

Tetris_XH is an implementation of the famous Tetris game
for inclusion on a CMSimple_XH website.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
  - [Vulnerability of highscores](#vulnerability-of-highscores)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)


## Requirements

Tetris_XH is a plugin for [CMSimple_XH](https://www.cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP 7.1.0 with the JSON extension.

## Download

The [lastest release](https://github.com/cmb69/tetris_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins. See the
[CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/?for-users/working-with-the-cms/plugins)
for further details.

1. **Backup the data on your server.**
1. Unzip the distribution on your computer.
1. Upload the whole folder `tetris/` to your server into the `plugins` folder
   of CMSimple_XH.
1. Set write permissions for the subfolders `config/`, `css/` and `languages/`.
1. Navigate to `Tetris` in the back-end to check if all requirements are
   fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins in
the back-end of the Website. Select `Plugins` → `Tetris`.

You can change the default settings of Tetris_XH under `Config`. Hints for
the options will be displayed when hovering over the help icons with your
mouse.

Localization is done under `Language`. You can translate the character
strings to your own language (if there is no appropriate language file
available), or customize them according to your needs.

The look of Tetris_XH can be customized under `Stylesheet`.

## Usage

To display the tetris game on a page, insert:

    {{{tetris()}}}

If you want to show your visitors the rules of Tetris, you can create a hidding
page with the name `Tetris_Rules` whose contents will be shown in the `Rules`
tab of the Tetris widget. Consider to just place a link to
<https://en.wikipedia.org/wiki/Tetris#Gameplay> or a similar site there.

## Limitations

To play tetris, JavaScript has to be enabled in the browser of the visitor.

### Vulnerability of highscores

It is trivial for hackers to manipulate the highscores without actually
scoring them through game play. But this poses no real security issue, as the
size of the highscore database is restricted.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/tetris_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Tetris_XH is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

Tetris_XH is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Tetris_XH. If not, see https://www.gnu.org/licenses/.

Copyright © 2011-2023 Christoph M. Becker

Polish translation © 2012 Kamil Kresz  
Czech translation © 2012 Josef Němec  
Slovak translation © 2012 Dr. Martin Sereday

## Credits

Tetris_XH is based on Tetris with jQuery by Franck Marcia.
Many thanks to him for developing this nice and simple implementation
of Tetris and releasing the code under an open source license.

The plugin icon is designed by [AtuX](https://www.deviantart.com/atux).
Many thanks for publishing this icon under a liberal license.

Many thanks to the community at the [CMSimple_XH Forum](https://cmsimpleforum.com/)
for tips, suggestions and testing.
Particularly I want to thank *Gert* and *oldnema* for their suggestions to
improve Tetris_XH. And I have to apologize to *bca*, who tested the online demo
first, but was not able to save his highscore because of a bug in the plugin.

Many thanks to Luda Wieland who pointed out that the highscores could not be
written on installations in a `UserDir` and helped with debugging the problem.

And last but not least many thanks to [Peter Harteg](http://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
