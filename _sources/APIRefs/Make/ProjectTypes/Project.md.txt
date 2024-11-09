# Project reference

**Makefiles:** [Top-level](https://github.com/gnustep/tools-make/blob/master/rules.make) | [Instance](https://github.com/gnustep/tools-make/blob/master/Instance/rules.make) | [Master](https://github.com/gnustep/tools-make/blob/master/Master/rules.make)

**Inheritance:** **Project**

**Project** is the root class for all projects. It is defined in `$(GNUSTEP_MAKEFILES)/rules.make`. It doesn't actually exist -- it's more like a protocol.

(properties)=
## Project properties

### Installation

````{make:var} xxx_COPY_INTO_DIR
By adding the line
```makefile
xxx_COPY_INTO_DIR = ../Vanity.framework/Resources
```
to you GNUmakefile, you cause the after-xxx-all:: stage of
compilation of xxx to copy the created stuff into the *local*
directory `../Vanity.framework/Resources` (this path should be
relative).  It also disables installation of xxx.

This is normally used, for example, to bundle a tool into a
framework.  You compile the framework, then the tool, then you can
request the tool to be copied into the framework, becoming part of
the framework (it is installed with the framework etc).
````

````{make:var} xxx_STANDARD_INSTALL
By setting this to `no`, you can disable the standard installation code
for a certain project.  This can be useful if you are
installing manually in some other way (or for some other reason you
don't want installation to be performed ever) and don't want the
standard installation to be performed.  Please note that
before-xxx-install and after-xxx-install are still executed, so if
you want, you can add your code in those targets to perform your
custom installation.
````

```{make:var} xxx_INSTALL_DIRS
By adding an xxx_INSTALL_DIRS variable you can request additional
installation directories to be created before the first installation
target is executed.  You can also have general
ADDITIONAL_INSTALL_DIRS directories that are always created before
install is executed; this is done top-level in the Master
invocation.
```

### Linkage

````{make:var} xxx_NEEDS_GUI
You can control whether you want to link against the gui library
by using one of the two commands --
```makefile
xxx_NEEDS_GUI = yes
xxx_NEEDS_GUI = no
```
(where 'xxx' is the name of your application/bundle/etc.)

You can also change it for all applications/bundles/etc by doing
```makefile
NEEDS_GUI = yes # (or no)
```
If you don't specify anything, the default for the project type will
be used (this is the NEEDS_GUI = yes/no that is at the top of all
project types' makefiles).

If the project type doesn't specify anything (eg, doesn't need
linking to ObjC libraries, or it is buggy/old or it is from a
third-party and hasn't been updated yet) then the default is NO.
````

```{make:var} xxx_OBJ_FILES
The list of object files that are not produced during the normal compilation stage to link into the resulting binary.
```

```{make:var} xxx_LDFLAGS
```

### Source files

```{make:var} xxx_OBJC_FILES
The list of Objective-C source files to be compiled
```

```{make:var} xxx_C_FILES
The list of C source files to be compiled
```

```{make:var} xxx_OBJCC_FILES
The list of Objective-C++ source files to be compiled
```

```{make:var} xxx_CC_FILES
The list of C++ source files to be compiled
```

```{make:var} xxx_PSWRAP_FILES
The list of PostScript Wrap (`.psw`) source files to be compiled into a C source and header file, which will be compiled and added to the project.
```

```{make:var} xxx_JAVA_FILES
The list of Java source files to be compiled into bytecode.
```

```{make:var} xxx_JAVA_JNI_FILES
The list of Java files to be converted into Java JNI headers.
```

% TODO - Figure out what this is.

```{make:var} xxx_WINDRES_FILES
It's unclear what this is for. See the [`windres` man page](https://man7.org/linux/man-pages/man1/windres.1.html).
```

### Compiler flags

% TODO - Figure out what this is.

```{make:var} xxx_INCLUDE_DIRS
The list of additional directories that the compiler will search when it is looking for include files. These flags are specific to the xxx target; see {make:var}`ADDITIONAL_INCLUDE_DIRS` to see how to specify additional global include directories. The directories should be specified as `-I` flags to the compiler. The additional include directories will be placed before the normal GNUstep and system include directories, and before any global include directories specified with {make:var}`ADDITIONAL_INCLUDE_DIRS`, so they will always be searched first.

**See also:** {make:var}`ADDITIONAL_INCLUDE_DIRS`
```

```{make:var} xxx_CPPFLAGS
Additional flags that will be passed to the C preprocessor when compiling C-family files to generate the xxx library. Adding flags here does not override the default CPPFLAGS, see CPPFLAGS, they are in addition to CPPFLAGS. These flags are specific to the xxx library, see ADDITIONAL_CPPFLAGS, to see how to specify global preprocessor flags.
```

```{make:var} xxx_CFLAGS
```

```{make:var} xxx_OBJCFLAGS
```

```{make:var} xxx_CCFLAGS
```

```{make:var} xxx_OBJCCFLAGS
```

### Dependencies

````{make:var} xxx_OBJC_LIBS
The list of additional libraries that the linker will use when linking the target. These libraries are specific to the xxx target, see ADDITIONAL_OBJC_LIBS, to see how to specify additional global libraries. These libraries are placed before all of the Objective-C Runtime and system libraries, and before the global libraries specified with ADDITIONAL_OBJC_LIBS, so that they will be searched first when linking. The additional libraries should be specified as `-l` flags to the linker as the following example illustrates:

```make
xxx_OBJC_LIBS = -lSwarm -lHello
```

**See also:** {make:var}`ADDITIONAL_OBJC_LIBS`
````

```{make:var} xxx_LIBS
```

```{make:var} xxx_LIBRARY_LIBS
```

```{make:var} xxx_NATIVE_LIBS
```

````{make:var} xxx_LIB_DIRS
The list of additional directories that the linker will search when it is looking for library files. The directories should be specified as ‘-L’ flags to the linker. The additional library directories will be placed before the GNUstep and system library directories so that they will be searched first by the linker. The following example illustrates two additional library directories; `/usr/local/gnu/lib` will be searched first, then `/usr/gnu/lib`, and finally the GNUstep and system directories which are automatically defined by the Makefile Package. 

```make
ADDITIONAL_LIB_DIRS = -L/usr/local/gnu/lib -L/usr/gnu/lib
```
````

```{make:var} xxx_CLASSPATH
```

```{make:var} xxx_LIBRARIES_DEPEND_UPON
```

