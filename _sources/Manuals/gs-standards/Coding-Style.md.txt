



# 3 Coding Style

The point is not what style is 'better’ in the abstract – it’s what style is standard and readily usable by all the people wanting to use/work on GNUstep. A reasonably good consistent style is better for collaborative work than a collection of styles irrespective of their individual merits. If you commit changes that don’t conform to the project standards, that just means that someone else will have a tedious time making the necessary corrections (or removing your changes).

The GNUstep coding standards are essentially the same as the GNU coding standards ([http://www.gnu.org/prep/standards_toc.html](http://www.gnu.org/prep/standards_toc.html)), but here is a summary of the essentials.

White space should be used for clarity throughout. In particular, variable declarations should be separated from code by a blank line and function/method implementations should be separated by a blank line.

Tabs should not be used (use spaces instead). If you do use them (please don't) they really, really, must be for tab-stops at the standard intervals of 8 spaces.

All binary operators should be surrounded by white space with the exception of the comma (only a trailing white space), and the `.` and `->` structure member references (no space).

    x = y + z;
    x += 2;
    x = ptr->field;
    x = record.member;
    x++, y++;

Brackets should have space only before the leading bracket and after the trailing bracket (as in this example), though there are odd occasions where those spaces might be omitted ((eg. when brackets are doubled)). This applies to square brackets too.

Where round brackets are used to enclose function or macro paramters, there is no space between the function or macro name and the opening bracket, and where round brackets are used for type-casts or at the end of a statement, there is normally no space between the closing bracket and the following expression or semicolon (however there is a space between the round bracket and the start of a method name in a method declaration or definition) -

    a = (int)b;
    - (void) methodWithArg1: (int)arg1 andArg2: (float)arg2;
    a = foo(ax, y, z);

The placement of curly brackets is part of the indentation rules. the correct GNU style is to indent by two spaces

      if (...)
        {
          ...
        }

For function implementations, the function names must begin on column zero (types on the preceding line). For function predeclaration, the types and the name should appear on the same line if possible.

    static int myFunction(int a, int b);

    static int
    myFunction(int a, int b)
    {
      return a + b;
    }

The curly brackets enclosing function and method implementations should be based in column 0. Indentation is in steps of two spaces.

    int
    myMax(int a, int b)
    {
      if (a < b)
        {
          return b;
        }
      return a;
    }

Lines longer than 80 columns must be split up, if possible with the line wrap occurring immediately before an operator. The wrapped lines are indented by two spaces form the original.

      if ((conditionalTestVariable1 > conditionaltestVariable2)
        && (conditionalTestvariable3 > conditionalTestvariable4))
        {
          // Do something here.
        }

Some things the standards seem to think are 'should’ rather than ’must’:

Multiline comments should use `/* ... */` while single line comments may use `//`.

In a C/ObjC variable declaration, the '`*`' refers to the variable, not to the type, so you write

      char    *foo;

not

      char*   foo;

Using the latter approach encourages newbie programmers to thing they can declare two pointer variables by writing

      char*   foo,bar;

when of course they need

      char    *foo, *bar;

or (in my opinion better)

      char    *foo;
      char  *bar;

An exception to the indentation rules for Objective-C: We normally don't break long methods by indenting subsequent lines by two spaces, but make the parts of the method line up instead. The way to do this is indent so the colons line up.

      [receiver doSomethingWith: firstArg
                            and: secondArg
                           also: thirdArg];

That's the style used mostly in the GNUstep code - and therefore the one I try to keep to, however, the standard two space indentation is also acceptable (and sometimes necessary to prevent the text exceeding the 80 character line length limit).

      [receiver doSomethingWith: firstArg
        and: secondArg
        also: thirdArg];

My own preference (not part of the standard in any way) is to generally use curly brackets for control constructs, even where only one line of code is involved

      if (a)
        {
          x = y;
        }

Where using conditional compilation you should comment the \#else and \#endif with the condition expression used in the \#if line, to make it easy to find the matching lines.

    #if condition
    // some code here
    #else /* not condition */
    #endif /* condition */




