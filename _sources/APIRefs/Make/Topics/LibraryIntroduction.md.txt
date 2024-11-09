# Introduction to Libraries

Compiling A Library
-------------------

We start with a very simple example of a library. Our tiny library will contain a single class, called `HelloWorld`, which has a method to print out a nice string.

The library has only one header file (called `HelloWorld.h`), which is the following:

```objc
#ifndef _HELLOWORLD_H_
#define _HELLOWORLD_H_

#import <Foundation/Foundation.h>

@interface HelloWorld : NSObject
+ (void) printMessage;
@end

#endif /* _HELLOWORLD_H_ */
```
(This header file quite simply says that `HelloWorld` is a subclass of `NSObject`, and implements a single class method `printMessage`; the `#ifdef`s are the standard way of protecting a C header file from multiple inclusions, but are not completely necessary if you use `#import`).

The source code of our class is in the file `HelloWorld.m`, and is the following:

```objc
#import "HelloWorld.h"

@implementation HelloWorld
+ (void) printMessage {
    printf("Hello World!\n");
} 
@end
```

(This implements the `printMessage` class method for the class `HelloWorld`, and all what this method does is printing out `Hello World!`.)

To compile our library, we create a `GNUmakefile` as follows:
```makefile
include $(GNUSTEP_MAKEFILES)/common.make

LIBRARY_NAME = libHelloWorld
libHelloWorld_HEADER_FILES = HelloWorld.h
libHelloWorld_HEADER_FILES_INSTALL_DIR = HelloWorld
libHelloWorld_OBJC_FILES = HelloWorld.m

include $(GNUSTEP_MAKEFILES)/library.make
```
The main differences with the `GNUmakefile` for a tool or an application are that we include `library.make` instead of `tool.make` or `application.make`, and that we set the `xxx_HEADER_FILES` variable to tell the make system which are the library header files. This is quite important because the header files will be installed with the library when the library is installed.

In order to do things cleanly, each library should install its headers in a different directory, so headers from different libraries don’t get mixed and confused; this is why we specify that our header file has to be installed in a `HelloWorld` directory:
```make
libHelloWorld_HEADER_FILES_INSTALL_DIR = HelloWorld
```
As a consequence, an application or a tool which needs to use the library will include the header file by using
```objc
#import <HelloWorld/HelloWorld.h>
```
because we have installed it into the `HelloWorld` directory.

As usual, to compile type `make` and to install type `make install`.

Linking your app or tool against a GNUstep library
--------------------------------------------------

In our first example, we want to write a tiny tool which uses our `libHelloWorld`. The tool source code is in the file `main.m`, which is the following:
```objc
#import <Foundation/Foundation.h>
#import <HelloWorld/HelloWorld.h>

int main (void)
{
    [HelloWorld printMessage];

    return 0;
}
```
(We invoke the `printMessage` method of the `HelloWorld` class, then exit).

We write our usual `GNUmakefile` (but including `GNUmakefile.preamble`):
```makefile
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = HelloWorldTest
HelloWorldTest_OBJC_FILES = main.m

include GNUmakefile.preamble
include $(GNUSTEP_MAKEFILES)/tool.make
```
Then, here is the `GNUmakefile.preamble`, in which we tell the make package about the library we want to link against:
```makefile
HelloWorldTest_TOOL_LIBS += -lHelloWorld
```

It's not entirely necessary to have a separate `GNUmakefile.preamble`, but you have to do that if you have an app that manages your `GNUmakefile`, like ProjectCenter does.

If you have correctly installed the library `HelloWorld`, this is all you need to do. If you needed to link against more than one library, you would simply put them on the same line, as in:
```makefile
HelloWorldTest_TOOL_LIBS += -lHelloWorld -lHelloMoon
```
which links against the two libraries `HelloWorld` and `HelloMoon`.

If `HelloWorldTest` were an application, you would need to use
```makefile
HelloWorldTest_GUI_LIBS += -lHelloWorld
```
(the difference is `GUI` instead of `TOOL`).

Linking against an external library
-----------------------------------

If the library you want to link against is not a GNUstep library (ie, not managed by the GNUstep make package), for example a C library you get from an external source, you need to tell the GNUstep make package where the library can be found. In this case, your `GNUmakefile.preamble` would look something like the following:
```makefile
HelloWorldTest_TOOL_LIBS    += -lNicola
HelloWorldTest_INCLUDE_DIRS += -I/opt/nicola/include/
HelloWorldTest_LIB_DIRS     += -L/opt/nicola/libs/
```
where I am linking against the library `libNicola`, which is in the directory `/opt/nicola/libs/` and whose headers are in `/opt/nicola/include/`.

Linking a library against another library
-----------------------------------------

You might need to build a shared library (for example called `libNicola`) which depends on another library (for example on `libHelloWorld`), and requiring the other library to be loaded automatically whenever your library is. We say that your library (`libNicola`) depends on the other one (`libHelloWorld`).

This case is quite simple - you write a usual `GNUmakefile` for your library:
```makefile
include $(GNUSTEP_MAKEFILES)/common.make

LIBRARY_NAME = libNicola
libNicola_OBJC_FILES = two.m

include GNUmakefile.preamble
include $(GNUSTEP_MAKEFILES)/library.make
```
and add a `GNUmakefile.preamble` in which you tell the make package that this library depends on the library `libHelloWorld`:
```makefile
libNicola_LIBRARIES_DEPEND_UPON += -lHelloWorld
```

```{warning}
GNUstep make is not a package manager. It will not detect whether `libHelloWorld` is installed. It will not ensure that the correct version of `libHelloWorld` is installed. It will not automatically install or update `libHelloWorld`.
```

You can also put `libNicola_LIBRARIES_DEPEND_UPON` in your `GNUmakefile`. The reason `GNUmakefile.preamble` is used is because it allows you to change these variables when ProjectCenter.app or another IDE manages your `GNUmakefile` and overwrites your changes.

