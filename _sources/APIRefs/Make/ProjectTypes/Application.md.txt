# Application reference

**Makefiles:** [Top-level](https://github.com/gnustep/tools-make/blob/master/application.make) | [Instance](https://github.com/gnustep/tools-make/blob/master/Instance/application.make) | [Master](https://github.com/gnustep/tools-make/blob/master/Master/application.make)

**Inheritance:** [Project](Project.md) &rarr; SharedBundle &rarr; **Application**

An **application** is a program that has its own `.app` bundle with its own resources, 

## Creating an application

```{make:var} APP_NAME
The list of all applications in this GNUmakefile.
```

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

APP_NAME = <Your tool name here>

include $(GNUSTEP_MAKEFILES)/application.make
```

(properties)=
## Application properties

Application also inherits [Project properties](Project.md).

```{make:var} xxx_INSTALL_DIR
This is the directory where the app gets installed. If you don't specify a directory it will get installed in the GNUstep Local Root (by default `/usr/GNUstep/Local` on Unix-like systems). The tool executable will get installed in `$(gnustep-config --variable=GNUSTEP_LOCAL_APPS)` (typically *root*`/Applications`).
```

```{make:var} xxx_HAS_RESOURCE_BUNDLE
If this variable is `yes`, then GNUstep Make will build a resource bundle for the tool, and install it. You can then add resources to the tool with the usual xxx_RESOURCE_FILES, xxx_LOCALIZED_RESOURCE_FILES, xxx_LANGUAGES, etc -- see (Resource Set Reference)[../ResourceSet/Reference.md]. `xxx` will be the same as the name of the tool.

The tool resource bundle (and all resources inside it) can be accessed at runtime very comfortably, by using gnustep-base's `[NSBundle mainBundle]` (exactly as you would do for an application).
```

## Global configuration variables

```{make:var} TOOL_INSTALL_DIR
This is the directory where the tool gets installed. If you don't specify a directory it will get installed in the GNUstep Local Root (by default `/usr/GNUstep/Local` on Unix-like systems). The tool executable will get installed in *root*`/Tools`.
```