# Project Types

```{toctree}
---
caption: Contents
hidden: true
---
Project.md
ResourceSet.md
Tool.md
Application.md
Library.md
```

## Common Types

* [Project](Project.md)
    * [Tool](Tool.md)
    * [Application](Application.md)
    * [Library](Library.md)

## Inheritance Hierarchy

* [Project](Project.md)
    * CTool - does *not* inherit from Tool
    * Documentation
    * 
    * SharedBundle
        * Bundle
        * [ResourceSet](ResourceSet.md)
        * [Application](Application.md)
        * GSWebApplication
        * [Library](Library.md)
            * CLibrary
        * Palette
        * Service
        * Subproject
        * [Tool](Tool.md)
    * SharedHeaders
        * Bundle
        * Framework
        * GSWebBundle - deprecated
        * [Library](Library.md)
            * CLibrary
        * Subproject
    * SharedStrings
        * Bundle
        * Framework
        * [Application](Application.md)
        * GSWebApplication
        * GSWebBundle - deprecated
        * [Library](Library.md)
            * CLibrary
        * ObjCTool
        * Palette
        * Service
        * Subproject
        * [Tool](Tool.md)
    * SharedJava
        * JavaTool
        * JavaPackage
    * SharedPkgConfig
        * Framework
        * [Library](Library.md)
            * CLibrary

The make library has some Shared* things that are *not* in the inheritance hierarcy, even though they are included by some project types -- they do not define any project type variables.

## Licensing

This file is part of the GNUstep Makefile Package.

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.
  
You should have received a copy of the GNU General Public
License along with this library; see the file COPYING.
If not, write to the Free Software Foundation,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
    