



# 2 ChangeLog Entries

Always include a ChangeLog entry for work that you do. Look for the ChangeLog file in the current directory or look up to any number of parent directories. Typically there is one for each library.

Emacs currently formats the header like this:

    2000-03-11  Adam Fedor  <fedor@gnu.org>

and formats changes to functions/methods like this:

    * Source/NSSlider.m ([NSSlider -initWithFrame:]):

to which you add your own comments on the same line (with word wrapping). Although if you're making similar changes to multiple methods, it’s ok to leave out the function/method name.

Important: Changelog entries should state what was changed, not why it was changed. It's more appropriate to put that in the source code, where someone can find it, or in the documentation.
