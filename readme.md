gistless
===

this goes through all the `.markdown` or `.md` files in a given directory and replaces and embedded gists with html including said gists' source code.

### how to use

clone the repo, config (see below) and then direct your browser to `gistless.php` and watch a volcano destroy all the code you hold dearly

### how to config

you need to create an app so that your rate limit is higher than 60 or whatever (https://github.com/settings/applications/new) and update all the appropriate lines:

* line 12: [your github app client id](https://github.com/settings/applications/new)
* line 13: [your github app secret](https://github.com/settings/applications/new)
* line 16: path to where the markdown files iterate through are
* line 125: your github username or else you can't curl, pal

If you are working with files that aren't markdown, check out line 28. the if conditional there checks for extensions, so you can add the ones you're using there for more fun.

### what is going on when you go to gistless.php

1. each `.md` or `.markdown` file in the given directory is searched for a gist embed script
2. if a script is found, a call to the github api is made to get the file name, language, and content of a gist
3. html replaces the line where that script used to be, in the following format:

`  <div class="code-snippet">
     <p class="caption">filename.exe</p>
     <pre lang="javascript"><code>GIST SOURCE CODE HERE</code></pre>
   </div>`

### why use this

if your site depends on gist embeds, that's cool i guess. but you may want to "own" that source and not depend on a third party site to host that stuff
 
xoxo [j$](http://jennmoney.biz)