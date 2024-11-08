Subprojects
===========

Why do subprojects exist ?
--------------------------

Before version 2.4.0, gnustep-make did not support having source files in subdirectories. For example, you couldn’t write:

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = HelloWorld
HelloWorld_OBJC_FILES = Subdirectory/HelloWorld.m

include $(GNUSTEP_MAKEFILES)/tool.make
```

This works if you use gnustep-make 2.4.0 (or later); but wouldn’t work with previous versions of gnustep-make, because HelloWorld.m is in a subdirectory. So, people who wanted to organize their source files in different subdirectories had to use “subprojects”. You created a GNUmakefile in each of the subdirectories (which would build using subproject.make), then specifies that the tool had some subprojects. So, the tool’s GNUmakefile would have been –

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = HelloWorld
HelloWorld_SUBPROJECTS = Subdirectory

include $(GNUSTEP_MAKEFILES)/tool.make
```

and in the subdirectory you’d have had a GNUmakefile such as the following one –

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

SUBPROJECT_NAME = Subdirectory
Subdirectory_OBJC_FILES = HelloWorld.m

include $(GNUSTEP_MAKEFILES)/subproject.make
```

You’ll appreciate how easier it is to specify source files in subdirectories directly in the tool’s GNUmakefile (as allowed by gnustep-make 2.4.0), without having to have an additional GNUmakefile in each subdirectory.

Most of the times, you do not need subprojects any more
-------------------------------------------------------

As explained, in recent versions of gnustep-make, there is no longer any need for subprojects just to organize your source files in subdirectories. You can simply put your source files into subdirectories, then list them all in your GNUmakefile. The project is simpler, and gnustep-make is able to parallelize as much as possible since it will (potentially) be able to compile all the files in parallel.

Subprojects and parallel building
---------------------------------

If you ever need to use subprojects in a parallel build, you need to know that, for backwards-compatibility, subprojects are built in the order they are specified, and are built before the rest of the source files are compiled.

So, in the example above of a tool HelloWorld with a Subdirectory subproject, the build flow to build the tool is as follows:

Step 0. Execute `before-HelloWorld-all::` if it exists  
Step 1. Build Subdirectory  
Step 2. Build HelloWorld  
Step 3. Execute `after-HelloWorld-all::` if it exists  

if you had more than one subproject, they would be built in the specified order. Most likely, they are independent though, and should be built in parallel. You can very simply obtain that result by getting rid of subprojects altogether and listing all files in the tool’s GNUmakefile. This will also get rid of the intermediate linking steps for subprojects, speeding up your build even more.