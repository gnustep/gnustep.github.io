# Installing the GNUstep MSVC toolchain on Windows

We call the native GNUstep toolchain for Windows the MSVC (Microsoft Visual C++) toolchain, as it uses the MSVC compiler for some projects,
and uses native Windows APIs, instead of a POSIX compatibility layer like MinGW. It also produces native Windows binaries.

Clang is used for compiling Objective-C code.

## 1 Prerequisites

First we need to install LLVM, to get the clang frontend, and lld. We will be using [Chocolatey](https://chocolatey.org/) to install it, but you can also install it manually using the official installer.

In a Administrator Shell (either CMD or Powershell), run the following command:
```sh
choco install llvm
```

## 2 Installing from a prebuilt release

We provide prebuilt releases of the toolchain, which you can download from the [releases page](https://github.com/gnustep/tools-windows-msvc/releases) on GitHub. Select the latest release, and download `GNUstep-Windows-MSVC-x64.zip` artefact
if you are on a x86_64 system.

Extract the zip file to a directory of your choice. We will use `C:\GNUstep` in this guide, as it is also the default installation directory of the build script.

You can now use the toolchain, and start building your own projects.

## 3 Structure of the toolchain directory

If you are familiar with the standard filesystem structure on UNIX-based system, you will find some similarities here.
`C:\GNUstep\x64` contains two directories: Debug and Release. The debug build contains debug symbols (.pdb files for dlls).

```sh
$ ls GNUstep/x64/Debug
bin  etc  include  lib  share
```

The `bin` directory contains binaries, and dlls (objc.dll, gnustep-base-1_28.dll, etc).
```sh
$ ls GNUstep/x64/Debug/bin
HTMLLinker.exe  defaults.exe  gnustep-base-1_28.dll   gnustep-corebase-0.pdb  icuin72.dll  libffi-8.dll  make_strings.exe  opentool     plget.exe    plutil.exe
autogsdoc.exe   dispatch.dll  gnustep-base-1_28.pdb   gnustep-tests           icuio72.dll  libffi-8.pdb  objc.dll          pl.exe       plmerge.exe  sfparse.exe
cvtenc.exe      dispatch.pdb  gnustep-config          gspath.exe              icutu72.dll  libiconv.dll  objc.pdb          pl2link.exe  plparse.exe  xmlparse.exe
debugapp        gdnc.exe      gnustep-corebase-0.dll  icudt72.dll             icuuc72.dll  libiconv.pdb  openapp           pldes.exe    plser.exe
```

The `lib` directory contains static libraries (.lib files).
```
$ ls GNUstep/x64/Debug/lib
GNUstep  dispatch.lib  exslt.lib  ffi.lib  gnustep-base.lib  gnustep-corebase.lib  iconv.lib  icudt.lib  icuin.lib  icuio.lib  icutest.lib  icutu.lib  icuuc.lib  objc.lib  pkgconfig  xml2.lib  xslt.lib
```

The `include` directory contains the headers for the libraries (you will recognise the `Foundation` subdirectory containing the header files). The `share` directory contains the GNUstep configuration files.

