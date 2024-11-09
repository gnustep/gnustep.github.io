# Resource Set reference

**Makefiles:** [Top-level](https://github.com/gnustep/tools-make/blob/master/resourceset.make) | [Instance](https://github.com/gnustep/tools-make/blob/master/Instance/resourceset.make) | [Master](https://github.com/gnustep/tools-make/blob/master/Master/resourceset.make)

**Inheritance:** [Project](Project.md) &rarr; SharedBundle &rarr; **Application**

This is used to install a bunch of resource files somewhere.  It is
different from a bundle without resources; in a bundle without
resources, we first create the bundle in the build directory, then
copy the build to the install dir, overwriting anything already
there.  This instead will install the separate resource files
directly in the installation directory; it's more efficient as it
doesn't create a local bundle, and it doesn't overwrite an existing
bundle in the installation directory.

```{note}
`Info-gnustep.plist` and `Info.plist` are NOT considered resource files.
These files are generated automatically by certain projects, and if you
want to insert your own entries into `Info-gnustep.plist` or `Info.plist`
you should create a `xxxInfo.plist` file (where `xxx` is the application name)
in the same directory as your makefile, and gnustep-make will automatically
read it and merge it into the generated `Info-gnustep.plist`.
For more detail, see rules.make.
```

## Creating an application

```{make:var} RESOURCE_SET_NAME
The list of all resource sets in this GNUmakefile.
```

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

RESOURCE_SET_NAME = <Your tool name here>

include $(GNUSTEP_MAKEFILES)/resourceset.make
```

(properties)=
## Resource Set properties

Resource Set also inherits [Project properties](Project.md).

```{make:var} xxx_RESOURCE_DIRS
The list of the resource directories to create.
```

```{make:var} xxx_RESOURCE_FILES
The list of files which should be copied from {make:var}`xxx_RESOURCE_FILES_DIR` to the resource set.
```

```{make:var} xxx_INSTALL_DIR
```


