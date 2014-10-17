Scaffolding folder
==================

If everything is laid out how you like alread... Ignore this folder! :D

Because i don't like the layout files, and adding classes to divs just
to position them seems silly (e.g. <div class="span12"></div>). I've
decided to do it all(most) in the CSS. The base theme sets a general layout
in its scaffolding.less so use this folder to override anything. break it into
seperate files so we know specificly whats changing (e.g login.less for any
login layout changes).

Use this folder for any layout changes. e.g. if you wanted to move
the blocks from the left to the right add a file called general.less
and in there your changes e.g:

	general.less
	------------

	#region-main{
		.grid .fluid .span(9);
		.pull-left();
	}

	.region-pre{
		.grid .fluid .span(3);
		.pull-right();
	}

This way if any layout is screwed up we can easily finds it, remove it if
we need to and change it.