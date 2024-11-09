# 1 - Introduction to GNUstep Programming

Introduction
------------------------------------

The basis of GNUstep is formed by the GNUstep makefile system and
GNUstep base. With only these two you are able to program platform
independend non-graphical programs.

The makefile system is an extension to the normal `Makefile`. You need
GNU **make**, which might on your system be present as **gmake**. The
makefiles are called `GNUmakefile`. The GNUstep makefile system
simplyfies your makefiles, all makefile logic will be done for you so
that makefile functionality will be consistent across all GNUstep
programs.

GNUstep base contains what is defined in the OpenStep standard as the
Foundation. It has the logic for creating strings, arrays, file handling
and the like. All objects are prefixed with NS or GS. The NS prefixed
objects are the ones that comply to the OpenStep standard as closely as
possible, while the GS ones are GNUstep extensions.

------------------------------------------------------------------------

Tools & Applications
---------------------------------------------

We start with the traditional "Hello World" program, which is mainly a
demonstration of gnustep-make.

`main.c:`

```objc
#include <stdio.h>

int main(void)
{
   printf("Hello World\n");
}
```

This is a very simple C program. In order to compile it, we create a
GNUmakefile file, which is a simplified, but powerful version of a
Makefile.

`GNUmakefile:`

```objc
include $(GNUSTEP_MAKEFILES)/common.make

CTOOL_NAME = HelloWorld
HelloWorld_HEADERS =
HelloWorld_C_FILES = main.c
HelloWorld_RESOURCE_FILES =

include $(GNUSTEP_MAKEFILES)/ctool.make
```

Type **make** and do **./shared\_obj/HelloWorld**. It will print "Hello
World", as you might have expected.

This example shows you a couple of powerful things about the GNUstep
makefile system. As you can see there are no targets. You can type
**make** and it works. You might also have noticed that you told,
through the `CTOOL_NAME` variable, the system that your application is
called HelloWorld.

Let's do the same for an Objective-C version of Hello World.

`main.m`:

```objc
#include <objc/Object.h>

@interface Greeter:Object
{
  /* This is left empty on purpose:
   ** Normally instance variables would be declared here,
   ** but these are not used in our example.
   */
}

- (void)greet;

@end

#include <stdio.h>

@implementation Greeter

- (void)greet
{
    printf("Hello, World!\n");
}

@end

#include <stdlib.h>

int main(void)
{
    id myGreeter;
    myGreeter=[Greeter new];

    [myGreeter greet];

    [myGreeter free];
    return EXIT_SUCCESS;
}
```

`GNUmakefile`:

```objc
include $(GNUSTEP_MAKEFILES)/common.make

OBJC_PROGRAM_NAME = HelloWorld
HelloWorld_HEADERS =
HelloWorld_OBJC_FILES = main.m
HelloWorld_RESOURCE_FILES =

include $(GNUSTEP_MAKEFILES)/objc.make
```

Next to the change to `OBJC_PROGRAM_NAME` also not the change from
`HelloWorld_C_FILES` to `HelloWorld_OBJC_FILES`.

The basic logic for Objective-C files is included through `objc.make`
and off you go for plain Objective-C programs.

GNUstep knows two kinds of programs: Tools and Applications. Tools are
mostly non-graphical programs, but more exactly Tools are programs with
no-resources, while Applications are programs with resources.

`main.m`:

```objc
#include <Foundation/Foundation.h>

@interface Greeter:NSObject
{
  /* This is left empty on purpose:
   ** Normally instance variables would be declared here,
   ** but these are not used in our example.
   */
}

- (void)greet;

@end

#include <stdio.h>

@implementation Greeter

- (void)greet
{
    printf("Hello, World!\n");
}

@end

#include <stdlib.h>

int main(void)
{
    id myGreeter;
    myGreeter=[[Greeter alloc] init];

    [myGreeter greet];

    [myGreeter release];
    return EXIT_SUCCESS;
}
```

`GNUmakefile`:

```objc
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = HelloWorld
HelloWorld_HEADERS =
HelloWorld_OBJC_FILES = main.m
HelloWorld_RESOURCE_FILES =

include $(GNUSTEP_MAKEFILES)/tool.make
```

To show you the difference more clearly type **make clean** and change
the line

```objc
include $(GNUSTEP_MAKEFILES)/tool.make
```

to

```objc
include $(GNUSTEP_MAKEFILES)/application.make
```

And change `TOOL_NAME` to `APP_NAME` and re-make the program. Next to
the shared\_obj directory you now also have a HelloWorld.app directory.

This HelloWorld.app directory is a self contained GNUstep application.
Type **openapp HelloWorld.app** and you see that it works. Now out of
curiosity list the contents of the HelloWorld.app directory and you have
your first glimps at the difference between a Tool and an Application.

------------------------------------------------------------------------

Stuff you get for free
-----------------------------------------------

Apart from being able to select the kind of program you want to create
you get a couple of things for free. The first thing you hopefully
already noticed is **make clean**, but there is more. **make install**
will install your program in it's default place, which for Tools is
`GNUSTEP_LOCAL_ROOT`/Tools and as you might have quessed
`GNUSTEP_LOCAL_ROOT`/Applications for applications.

Ofcourse that path can be overridden by using e.g. **make
PREFIX=/my/path install**, but remember that Tools and Applications are
only searched within the GNUstep tree. It is a self contained system
with three so called domains: GNUSTEP\_SYSTEM\_ROOT,
GNUSTEP\_LOCAL\_ROOT and GNUSTEP\_NETWORK\_ROOT. Tools like **openapp**
will only search through these domains and the current directory to find
Applications and Tools.

A couple of the most used **make** options are:

**Basic options**

-   **all.** The same as **make** without options

-   **check.** Run tests if applicable

-   **clean.** Remove all that is build during make

-   **distclean.** Remove all that is build during make and ... ???

-   **install.** Install all that is build, and if nothing is build, do
    the build first.

-   **uninstall.** Uninstall a previously install program.

-   **cvs-dist, cvs-snapshot, strings.** ??? What do these???

**Boolean options**

-   **debug=\[yes|no\].** yes builds a program with debugging symbols.
    With debugging symbols the program can be started with **debugapp**.

-   **filelist=\[yes|no\].** Creates a filelist of the installed files
    (easy for packaging binaries).

-   **messages=\[yes|no\].** More verbose messaging when building
    sources.

-   **shared=\[yes|no\].** ???

-   **standalone=\[yes|no\].** ???

-   **strip=\[yes|no\].** Strip the created programs and libraries.

There are some [GNUmakefile
tutorials](http://wiki.gnustep.org/index.php/GNUstep%20Make%20Tutorials).
Therefore, I won't touch this topic too much. Here is another example,
which is a real Objective-C program. If you are not familiar with
Objective-C, read the [Objective-C book from
apple](http://www.gnustep.org/) first.

------------------------------------------------------------------------

Headers
--------------------------------

Since GNUstep is build with Objective-C, which in turn is based on C,
headers are important. Within the GNUstep domains they can be found in
the Library/Headers directory where all have their own subdirectory; all
foundation headers can be found in Library/Headers/Foundation.

To include header files in your code you can use the C syntax:

```objc
#include <Foundation/NSArray.h>
```

But you have the risk of creating a recursive inclusion, so if you want
to use include then you better use:

```objc
#ifndef HAVE_NSARRAY_H
#define HAVE_NSARRAY_H

#include <Foundation/NSArray.h>

#endif
```

Easier would be to use the Objective-C macro \#import. import takes care
of all the things that concern headers:

```objc
#import <Foundation/NSArray.h>
```

And since GNUstep is here to make it easier on you, you might want to
use:

```objc
#import <Foundation/Foundation.h>
```

This includes all the headerfiles from the foundation.

<table>
<tbody>
<tr class="odd">
<td><img src="GSPT_files/note.gif" alt="Note" /></td>
<td><p>Foundation tries to implement as close as possible the OpenStep standard. Which means that the Foundation/ directory holds the headers that correspond to that standard. The extensions of GNUstep can be found in GNUstepBase/. The same goes for AppKit/ and GNUstepGUI/.</p></td>
</tr>
</tbody>
</table>

------------------------------------------------------------------------

Strings
--------------------------------

There are four files:

`say.h:`

```objc
#ifndef _Say_H_
#define _Say_H_

#include <Foundation/NSObject.h>
@interface Say: NSObject
{
}
- (void) sayHello;
- (void) sayHelloTo: (NSString *)name;
@end

#endif /* _Say_H_ */
```

`say.m:`

```objc
#include "say.h"
#include <Foundation/Foundation.h>

@implementation Say
- (void) sayHello
{
   NSLog(@"Hello World");
}

- (void) sayHelloTo: (NSString *)name
{
   NSLog(@"Hello World, %@", name);
}
@end
```

`main.m:`

```objc
#include "say.h"
#include <Foundation/Foundation.h>

int main (void)
{
   id speaker;
   NSString *name = @"GNUstep !";
   NSAutoreleasePool *pool;

   pool = [NSAutoreleasePool new];
   speaker = [[Say alloc] init];

   [speaker sayHello];
   [speaker sayHelloTo:name];

   RELEASE(speaker);
   RELEASE(pool);
}
```

`GNUmakefile:`

```objc
include $(GNUSTEP_MAKEFILES)/common.make

APP_NAME = HelloWorld
HelloWorld_HEADERS = say.h
HelloWorld_OBJC_FILES = main.m say.m
HelloWorld_RESOURCE_FILES =

include $(GNUSTEP_MAKEFILES)/application.make
```

Again, use 'make' and 'openapp' to compile and run this application.
This example is self-explained. There are several things worthy to
metion.

The class "Say" inherits from "NSObject", which is the root class of
GNUstep. Always inherit from NSObject if you don't know which class to
use. NSObject contains many fundamental methods which you won't want to
implement by yourself.

NSLog() is used instead of printf() because it is as easy as printf().
And most importantly, NSLog() accept the %@ symbol, which represents an
object. That means you can print objects in NSLog(). Very handy for
debug.

NSString is one of the most used classes in GNUstep. You can use @"..."
to create a NSString. There are many useful methods in NSString to
manipulate strings and pathes. Check these articles: [*String in Cocoa:
Part I*](http://www.macdevcenter.com/pub/a/mac/2001/06/29/cocoa.html),
[*Part II*](http://www.macdevcenter.com/pub/a/mac/2001/07/13/cocoa.html)

NSAutoreleasePool is the place for autoreleased instances. In this
example, it is not much useful, but I think it is a good habit to use
it. When the application should end, remember to release it. Methods
-alloc and -init is the standard method to create an instance. Method
-new is a shortcut if there is no messages for -init.

------------------------------------------------------------------------

Arrays
-------------------------------

Arrays are defined by NSArray. There is static version and a dynamic
version. The dynamic version is called NSMutableArray. To define them in
your code goes the same for both:

```objc
NSArray        *anArray;
NSMutableArray *anMutableArray;
```

Now let us create a simple array:

```objc
NSArray           *weekArray;
NSAutoreleasePool *pool;

pool      = [NSAutoreleasePool new];
weekArray = [NSArray arrayWithObjects: @"Sun", @"Mon" @"Tue", @"Wed", \
                                       @"Thr", @"Fri", @"Sat", nil];

printf("Day 0: %@\n", [weekArray objectAtIndex: 0]);
RELEASE(pool);
```

We told NSArray to fill `weekArray` with the strings that represent the
week days and terminated the array with a 'nil'

To output the elements from the array we ask from weekArray to output
the object at a certain index. Note that arrays start counting at 0!

------------------------------------------------------------------------

Debugging your application
---------------------------------------------------

Debugging your application often involves watching what happens while
the program is running. You could use a debugger for that, like gdb, but
there are some other options that make life easy.

Since you can mix C and Objective-C you could use normal `printf`
statements, or it's GNUstep counterpart GSPrintf. GNUstep however also
has a function `NSLog` and the Makefile system offers you to use **make
debug=yes** and use ifdefs in your code.

To make this all a bit more clear we use and example.

```objc
#include <Foundation/Foundation.h>

int main(void)
{
        NSArray           *outArray;
    NSAutoreleasePool *pool;
                
    pool = [NSAutoreleasePool new];
    outArray = [NSArray arrayWithObjects: @"Msg1", @"Msg2" @"Msg3" "Msg4";
    
    printf("%@\n", [outArray objectAtIndex: 0]);

    GSPrintf(stdout, "%@",[outArray objectAtIndex: 1]);

    NSLog(@"%@\n", [outArray objectAtIndex: 2]);

    #ifdef DEBUG
        NSLog(@"%@\n", [outArray objectAtIndex: 3]);
    #endif

    RELEASE(pool);
}
```

first run **make** and execute the binary in the obj directory. `printf`
prints it's output naar stdout, while NSLog outputs to stderr and with
GSPrintf you have full control. You can print to stdout or stderr.

Now run **make clean; make debug=yes** and rerun the command from the
obj directory. Your output should look like this:

```
Msg1
Msg2
2003-12-30 17:40:53.846 array[10769] Msg3
2003-12-30 17:40:53.848 array[10769] Msg4
```

With these you can simply debug the most common problems, apart from
code that won't compile :)

------------------------------------------------------------------------

Memory Management
------------------------------------------

Memory management is very important, but easy, in GNUstep. Here are some
good articles:

1.  [*Memory Management in
    Objective-C*](http://www.macdevcenter.com/pub/a/mac/2001/07/27/cocoa.html)

2.  [*Memory Management
    101*](http://cocoadevcentral.com/articles/000055.php)

3.  [*Very simple rules for memory management in
    Cocoa*](http://www.stepwise.com/Articles/Technical/2001-03-11.01.html)

4.  [*Hold Me, Use Me, Free
    Me*](http://www.stepwise.com/Articles/Technical/HoldMe.html)

5.  [*Accessor methods and (auto)release:
    conclusion*](http://cocoa.mamasam.com/COCOADEV/2002/08/1/41613.php)

6.  [*Memory Management with
    Cocoa/WebObjects*](http://www.stepwise.com/Articles/Technical/MemoryManagement.html)

GNUstep offers some good macros to ease the coding related to memory
management. Take a look at NSObject.h for ASSIGN(), RELEASE(), RETAIN(),
etc.

The most common way to handle release/retain is this:

```objc
@interface MyObject: NSObject
{
  id myData;
}
-(void) setMyData: (id) newData;
-(id) myData;
@end

@implementation MyObject
- (void) setMyData: (id) newData
{
  ASSIGN(myData, newData);
}

- (id) myData
{
  return myData;
}

- (void) dealloc
{
  RELEASE(myData);
}
@end
```

Basically it works for me. ASSIGNCOPY() can also be used to copy object.
Always use \[self setMyData: newData\] to set myData, or at least use
ASSIGN(myData, newData). Don't use myData = newData. By this way, you
don't need to worry about the memory management.

In some case, I will use mutable classes because they are thread-safe.
For example:

```objc
@interface MyObject: NSObject
{
  NSMutableArray *myArray;
} 
-(void) setMyArray: (NSArray *) newArray;
-(NSArray *) myArray;
@end 

@implementation MyObject
- (id) init
{
  myArray = [NSMutableArray new];
}

- (void) setMyArray: (NSArray *) newArray
{ 
  [myArray setArray: newArray]; 
} 

- (NSArray *) myArray 
{ 
  return myArray; 
} 

- (void) dealloc 
{ 
  RELEASE(myArray); 
}
@end
```

Mutable classes cost more resources. But it is a lazy way to avoid
problems.

Also be aware of the return values from GNUstep classes. Some are
autoreleased. For example, \[NSArray arrayWith...\], or \[@"A string"
stringBy...\]. If you release them again, the application usually
crashes. And remember to retain them if you want to use them later, and
release them in -dealloc. It's safe to use ASSIGN() in this case. For
example:

```objc
ASSIGN(myString, [@"A string", stringBy...])
```

ASSIGN() will handle all the details. Again, once you use ASSIGN(),
release it in -dealloc.

------------------------------------------------------------------------

File I/O
---------------------------------

Here is a demonstration of file I/O. GNUstep offer NSFileHandle and
NSFileManager to handle files. NSFileHandle is mainly to read and write,
and NSFileManager is for file management.

`main.m:`

```objc
#include <Foundation/Foundation.h>
   
int main (void)
{
   NSString *path;
   NSAutoreleasePool *pool;
   NSFileHandle *readFile, *writeFile;
   NSData *fileData;
   
   pool = [NSAutoreleasePool new];
   path = @"main.m";
   
   readFile = [NSFileHandle fileHandleForReadingAtPath:path];
   fileData = [readFile readDataToEndOfFile];
   
   writeFile = [NSFileHandle fileHandleWithStandardOutput];
   [writeFile writeData:fileData];
   RELEASE(pool);

   return 0;
}
```

`GNUmakefile:`

```objc
include $(GNUSTEP_MAKEFILES)/common.make
   
APP_NAME = FileHandle
FileHandle_HEADERS =
FileHandle_OBJC_FILES = main.m
FileHandle_RESOURCE_FILES =
   
include $(GNUSTEP_MAKEFILES)/application.make
```

It is not too much useful to only trasfer data in NSData. Here, I can
take the data out of NSData and manipulate it in C. By this way, I can
combine GNUstep with C libraries.

`main.m:`

```objc
#import <Foundation/Foundation.h>

int main (void)
{ 
  NSString *path;
  NSAutoreleasePool *pool;
  NSFileHandle *readFile;
  NSData* fileData;
   
  char *buffer;
  unsigned int length;

  pool = [NSAutoreleasePool new];
  // path = @"main.m";
  path = @"GNUmakefile";
  readFile = [NSFileHandle fileHandleForReadingAtPath:path];
  fileData = [readFile readDataToEndOfFile];

  length = [fileData length];
  buffer = malloc(sizeof(char)*length);
  [fileData getBytes: buffer];

  printf("%s\n", buffer);
  printf("%d\n", length);
  free(buffer);
   
  RELEASE(pool);
   
  return 0;
}
```

------------------------------------------------------------------------

Basic Classes
--------------------------------------

Documents about the base/foundation classes of GNUstep and Cocoa:

1.  [*Basic GNUstep Base Library
    Classes*](http://www.gnustep.it/nicola/Tutorials/BasicClasses/)

2.  [*The Storage Series, Part
    One*](http://cocoadevcentral.com/articles/000005.php)

3.  [*The Storage Series, Part
    Two*](http://cocoadevcentral.com/articles/000006.php)

4.  [*The Storage Series, Part
    Three*](http://cocoadevcentral.com/articles/000010.php)

5.  [*Understanding the design behind Cocoa's Foundation
    classes*](http://cocoadevcentral.com/articles/000065.php)

6.  [*Strings in Cocoa: Part
    I*](http://www.macdevcenter.com/pub/a/mac/2001/06/29/cocoa.html)

7.  [*Strings in Cocoa: Part
    II*](http://www.macdevcenter.com/pub/a/mac/2001/07/13/cocoa.html)

------------------------------------------------------------------------
