# Tool reference

**Makefiles:** [Top-level](https://github.com/gnustep/tools-make/blob/master/tool.make) | [Instance](https://github.com/gnustep/tools-make/blob/master/Instance/tool.make) | [Master](https://github.com/gnustep/tools-make/blob/master/Master/tool.make)

**Inheritance:** [Project](Project.md) &rarr; SharedBundle &rarr; **Tool**

A **tool** is a program that can be called from the command-line. It's useful for testing out ideas or writing command-line tools.

Tools link against Base (FoundationKit), but not against Gui (AppKit). If you need to link against Gui, use {make:var}`xxx_NEEDS_GUI`. If you need a tool that doesn't link against Base, use an [Objective-C Tool](ObjCTool.md).

## Creating a tool

```{make:var} TOOL_NAME
The list of all tools in this GNUmakefile.
```

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = <Your tool name here>

include $(GNUSTEP_MAKEFILES)/tool.make
```

(properties)=
## Tool properties

Tool also inherits [Project properties](Project.md).

```{make:var} xxx_INSTALL_DIR
This is the directory where the tool gets installed. If you don't specify a directory it will get installed in the GNUstep Local Root (by default `/usr/GNUstep/Local` on Unix-like systems). The tool executable will get installed in *root*`/Tools`.
```

```{make:var} xxx_INSTALL_DIR
This is the directory where the tool gets installed. If you don't specify a directory it will get installed in the GNUstep Local Root (by default `/usr/GNUstep/Local` on Unix-like systems). The tool executable will get installed in *root*`/Tools`.
```

```{make:var} xxx_HAS_RESOURCE_BUNDLE
If this variable is `yes`, then GNUstep Make will build a resource bundle for the tool, and install it. You can then add resources to the tool with the usual xxx_RESOURCE_FILES, xxx_LOCALIZED_RESOURCE_FILES, xxx_LANGUAGES, etc -- see (Resource Set Reference)[../ResourceSet/Reference.md]. `xxx` will be the same as the name of the tool.

The tool resource bundle (and all resources inside it) can be accessed at runtime very comfortably, by using gnustep-base's `[NSBundle mainBundle]` (exactly as you would do for an application).
```

## Global configuration variables

```{make:var} TOOL_INSTALL_DIR
This is the directory where the tool gets installed. If you don't specify a directory it will get installed in the GNUstep Local Root (by default `/usr/GNUstep/Local` on Unix-like systems). The tool executable will get installed in *root*`/Tools`.
```