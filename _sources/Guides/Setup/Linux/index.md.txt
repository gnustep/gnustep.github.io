# Linux

```{toctree}
:maxdepth: 2
:caption: Contents
:titlesonly: true

building-linux
```

## Installation (simple)

Ensure you have `bash`, `curl`, and `sudo` installed and working. Then run the following command in your terminal:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/gnustep/tools-scripts/master/gnustep-web-install)"
```

This will install GNUstep with most modern features enabled, including a recent version of Clang, support for ARC and other modern Objective-C features, typed selectors, ICU support in NSString, NSLocale, and similar classes, and Grand Central Dispatch. It installs from the `master` or `main` branch of most repositories.

Avoid your distribution packages -- it is often outdated and sometimes is compiled using GCC, which disables ARC and modern Objective-C features.

## Code completion, navigation, and linting

### VSCode or VSCodium

We use the clangd extension, which provides support for code completion and navigation, along with Clang-style warnings and error messages, based on the Clang compiler's code. If you have the Microsoft C/C++ extension, make sure to disable IntelliSense when prompted, as IntelliSense does not support Objective-C and Objective-C++ code.

Here is the list of extensions:
- [clangd](https://marketplace.visualstudio.com/items?itemName=llvm-vs-code-extensions.vscode-clangd) - The official clang language server for Visual Studio Code

You need to explicitly set the clangd executable path in the clangd extension settings if you want to use the system version of LLVM. You can find the clangd executable in the LLVM installation directory (e.g. `C:\Program Files\LLVM\bin\clangd.exe`).

You can now open a folder in Visual Studio Code, and start coding. The clangd extension will search for the file `compile_commands.json` to get the compiler flags. 

To build your project, simply use the integrated terminal.

Behaviour with different build systems:
- Meson: Generates `compile_commands.json` by default
- CMake: Generates `compile_commands.json` with the `-DCMAKE_EXPORT_COMPILE_COMMANDS=1` flag
- Make: Intercept and generate `compile_commands.json` with the [bear](https://github.com/rizsotto/Bear) tool
  - Run Make using `bear -- make` in order to generate a `compile_commands.json` file
