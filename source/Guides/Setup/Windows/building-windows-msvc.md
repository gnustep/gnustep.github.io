# Building on Windows

As building the GNUstep toolchain on Windows is a bit more involved than on other platforms, we have a dedicated build script for it.

The toolchain is used commercially as well, and can be used to integrate Objective-C into your existing Windows projects.

## 1 Prerequisites

Before we can start building, we need to install some dependencies. We will be using [Chocolatey](https://chocolatey.org/) to install them.

Follow the guide on installing Visual Studio, and the Windows SDK from the [Windows Development Workflow](./windows-dev-workflow.md) guide.

### 1.1 Installing other dependencies

Now install the following dependencies using chocolatey:

```sh
choco install llvm git msys2 nasm
choco install cmake --installargs 'ADD_CMAKE_TO_PATH=System'
```

We use LLVM and the clang frontend to compile Objective-C code, and lld as a linker.

MSYS2 is a software distribution and a development platform for Windows, providing a
Unix-like environment, a package management system (based on pacman), and tools
for bridging Windows idiosyncrasies with the Unix world.

### 1.2 Installing msys2 packages

Now we need to install some packages using pacman, the package manager of MSYS2.
Open a MSYS2 shell (Windows + R -> msys2.exe), and run the following commands:

```sh
pacman -S --needed make autoconf automake libtool pkg-config
```

### 2 Building

We can start building the toolchain. Open a MSYS2 shell, and clone the tools-windows-msvc repository:

```sh
# Export the path to the LLVM and Git binaries
export PATH=$PATH:/c/Program\ Files/Git/cmd:/c/Program\ Files/LLVM/bin

# Clone the repository (you can change the installation directory)
cd /c
git clone https://github.com/gnustep/tools-windows-msvc
```

Open a x64 Native Tools Command Prompt (search for it in the start menu) and run the build.bat script:

```sh
cd C:\tools-windows-msvc

# You can omit the type to build debug, and release, or change it to Debug
# We only build non-gui libraries (libobjc2, libdispatch, base, corebase)
# If you want to build the gui libraries, you can remove the --no-gui flag
build.bat --type Release --no-gui
```

If no error occured during the build, you can now find the toolchain in `C:\GNUstep\x64\{Debug, Release}`.
To learn more about the toolchain directory structure and how to use it, see [Using the MSVC toolchain](./using-windows-msvc.md).