# Library reference

**Makefiles:** [Top-level](https://github.com/gnustep/tools-make/blob/master/library.make) | [Instance](https://github.com/gnustep/tools-make/blob/master/Instance/library.make) | [Master](https://github.com/gnustep/tools-make/blob/master/Master/library.make)

**Inheritance:** [Project](Project.md) &rarr; SharedBundle &rarr; **Library**

A **library** is an object that other libraries and applications can link to in order to use functions, classes, and constants from the library.

By default, libraries link against Base (FoundationKit), but not against Gui (AppKit). If you need to link against Gui, use {make:var}`xxx_NEEDS_GUI`.

## Creating a library

```{make:var} LIBRARY_NAME
The list of all tools in this GNUmakefile.
```

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

LIBRARY_NAME = <Your library name here>

include $(GNUSTEP_MAKEFILES)/library.make
```

(properties)=
## Library properties

Library also inherits [Project properties](Project.md).

```{make:var} xxx_HEADER_FILES
The list of header filenames that are to be installed with the library, relative to {make:var}`xxx_HEADER_FILES_DIR`.

If a header is in a subdirectory, then that header will be installed in that subdirectory of the installation directory.
```

```{make:var} xxx_HEADER_FILES_DIR
The location of the header files in {make:var}`xxx_HEADER_FILES` relative to the directory containing the `GNUmakefile`.

{make:var}`xxx_HEADER_FILES_DIR` is optional; if blank or undefined, GNUstep Make assumes that the relative path to the header files is the directory containing the `GNUmakefile`.
```

```{make:var} xxx_HEADER_FILES_INSTALL_DIR
The relative subdirectory path below {make:var}`GNUSTEP_HEADERS` where the header files are to be installed.

If this directory or any of its parent directories do not exist, then the Makefile Package will create them. The Makefile Package prefixes {make:var}`xxx_HEADER_FILES_INSTALL_DIR` to each of the filenames, including any subdirectory paths, in {make:var}`xxx_HEADER_FILES` when they are installed.

`xxx_HEADER_FILES_INSTALL_DIR` is optional; if blank or undefined, GNUstep make assumes that the installation directory is just {make:var}`GNUSTEP_HEADERS` with no subdirectory.
```

````{make:var} xxx_LIBRARIES_DEPEND_UPON
The set of libraries that the shared library depends upon.

On some platforms, when a shared library is built, any libraries which the object code in the shared library depends upon must be linked in the generation of the shared library. This is similar to the process of linking an executable file like a command line tool or Objective-C program, except that the result is a shared library. Libraries specified with {make:var}`xxx_LIBRARIES_DEPEND_UPON` should be listed as `-l` flags to the linker. When possible, use variables defined by the Makefile Package to specify GUI, Foundation, or system libraries, like {make:var}`GUI_LIBS`, {make:var}`FND_LIBS`, {make:var}`OBJC_LIBS`, or {make:var}`SYSTEM_LIBS`. {make:var}`xxx_LIBRARIES_DEPEND_UPON` is independent of {make:var}`ADDITIONAL_OBJC_LIBS`, {make:var}`ADDITIONAL_TOOL_LIBS`, and {make:var}`ADDITIONAL_GUI_LIBS`, so any libraries specified there may need to be specified with {make:var}`xxx_LIBRARIES_DEPEND_UPON`. The following example illustrates the use of {make:var}`xxx_LIBRARIES_DEPEND_UPON` for a shared library named `libcomplex` that depends on the Foundation Kit, Objective-C runtime, various system libraries, and an additional user library named `libsimple`. 
```make
libcomplex_LIBRARIES_DEPEND_UPON = -lsimple $(FND_LIBS) $(OBJC_LIBS) $(SYSTEM_LIBS)
```
````