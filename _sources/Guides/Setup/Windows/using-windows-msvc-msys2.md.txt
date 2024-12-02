# Using the GNUstep MSVC toolchain from MSYS2

The GNUstep MSVC toolchain is a native Windows toolchain, but nothing prevents
us from using it from a POSIX shell.  This guide is mainly for developers who
are used to working in a POSIX environment, and want to use the toolchain from a
POSIX shell.

One of the advantages of using the toolchain from a POSIX shell, is that we can
use the GNUstep environment variables, and the `gnustep-config` tool to get the
flags needed to compile and link with the GNUstep libraries.

## Requirements

First we need to install LLVM, to get the clang frontend, and lld. We will be
using [Chocolatey](https://chocolatey.org/) to install it, but you can also
install it manually using the official installer from the [GitHub
releases](https://github.com/llvm/llvm-project/releases).

We also need to install MSYS2, if not already installed.

In a Administrator Shell (either CMD or Powershell), run the following command:
```sh
choco install llvm msys2
```

## Setting the PATH in bash

Before we can use the toolchain, we need to add the LLVM and the GNUstep binaries to the PATH.

I recommend adding the modifications to your `~/.bashrc` or `~/.bash_profile`.
Open a MSYS2 shell (Windows + R -> msys2.exe), and run the following commands:
```sh
# Add LLVM and GNUstep binaries to PATH
# The paths may be different depending on your installation
export PATH=$PATH:/c/Program\ Files/LLVM/bin:/c/GNUstep/x64/Debug/bin

# Source the GNUstep environment
source /c/GNUstep/x64/Debug/share/GNUstep/Makefiles/GNUstep.sh
```

## 1 Hello World (From the command line)

Open a MSYS2 shell (Windows + R -> msys2.exe), and run the following commands:
```sh
# Create a directory for our project
mkdir hello && cd hello

touch main.m
```

You can now edit `main.m` with your favourite text editor, and add the following code:
```objc
#import <Foundation/Foundation.h>

int main(int argc, const char * argv[]) {
	@autoreleasepool {
		NSLog(@"Hello, World!");
	}
	return 0;
}
```

Now we can compile the program:
```sh
clang -o hello `gnustep-config --objc-flags` `gnustep-config --base-libs` main.m
```

Or alternatively in two stages:
```sh
# --objc-flags: output the flags needed to compile Objective-C code
clang -c `gnustep-config --objc-flags` main.m
# --base-libs: output the flags needed to link with the base library
clang -o hello main.o `gnustep-config --base-libs`
```

For more information you can run `gnustep-config --help`.
If everything went well, you can now run the program:
```sh
./hello
# 2023-10-22 00:33:36.049 hello[5916:12508] Hello, World!
```

# 2 Using the toolchain without MSYS2

Using a POSIX shell is great for developers accustomed to UNIX-based systems, but not natural in the Windows world.
If you want to integrate GNUstep into a Visual Studio, or other build system, the best way is to specify the flags manually.

For more information on how to use the toolchain in Visual Studio, see [Using the toolchain in Visual Studio](https://github.com/gnustep/tools-windows-msvc#using-the-toolchain-in-visual-studio) in the projects GitHub readme.
