Aggregate projects
==================

Up to now, all examples consisted of projects in a single directory, using a single GNUmakefile. Aggregate projects allow you to have a number of projects in different directories.

As an example, suppose that you are writing a networked game. Your source code might contain two main projects to build: a gui application (the game client) and a command line tool (the server). Naturally enough, you want to develop and distribute the two subprojects together, but they are big enough that you want to keep them in separate directories. This is where GNUstep aggregate projects come handy.

Imagine that your game is called `MyGame`. You will have a top-level directory

    MyGame

and two subdirectories

    MyGame/Server
    MyGame/Client

In `MyGame/Server` you keep the source code of your server tool, with its own `GNUmakefile`. In `MyGame/Client` you keep the source code of your client application, with its own `GNUmakefile`.

You can now you add the following `GNUmakefile` in the top-level directory:

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

PACKAGE_NAME = MyGame

SUBPROJECTS = Server Client

include $(GNUSTEP_MAKEFILES)/aggregate.make
```

This `GNUmakefile` simply tells to the make package that your project consists of two aggregate subprojects, `Server`, and `Client`. Please note that the make package follows the order you specify, so in this case `Server` is always compiled before `Client` (this could be important if one of your subprojects is a library, and another subproject is an application which needs to be linked against that library: then, you always want the library to be compiled before the application, so the library should come before the application in the list of subprojects).

In this example, we have two aggregate subprojects, but you can have any number of aggregate subprojects.

At this point you are ready. Typing

```bash
make
```

in the top-level directory will cause the make package to step into the `Server` subdirectory, and run `make` there, and then step into the `Client` subdirectory, and run `make` there.

The same will work with all the standard make commands, such as `make clean`, `make distclean`, `make install` etc.

Aggregate subprojects can be nested, so that for example the `Server` project could be itself composed of subprojects.

Serial and parallel subdirectories
----------------------------------

Starting with version 2.4.0, gnustep-make supports two new ways of organizing multi-directory projects. From 2.4.0 you can use `serial-subdirectories.make` and `parallel-subdirectories.make` instead of aggregate.make to better control how you want the build to be done when using parallel building. Please check the forthcoming [“Parallel Building” Tutorial](ParallelBuilding.md) for more information.