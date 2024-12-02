# IDE setup

## 3 Code completion, navigation, and linting

### 3.1 Using Visual Studio Code

We use a combination of the Microsoft C/C++ extension, and the clangd extension. Make sure to disable IntelliSense when prompted, as IntelliSense does not support Objective-C and Objective-C++ code.

Here is the list of extensions:
- [C/C++](https://marketplace.visualstudio.com/items?itemName=ms-vscode.cpptools) - IntelliSense, debugging, and code browsing for C and C++
- [clangd](https://marketplace.visualstudio.com/items?itemName=llvm-vs-code-extensions.vscode-clangd) - The official clang language server for Visual Studio Code

You need to explicitly set the clangd executable path in the clangd extension settings if you want to use the system version of LLVM. You can find the clangd executable in the LLVM installation directory (e.g. `C:\Program Files\LLVM\bin\clangd.exe`).

You can now open a folder in Visual Studio Code, and start coding The clangd extension will search for the file `compile_commands.json` to get the compiler flags. 

Behaviour with different build systems:
- Meson: Generates `compile_commands.json` by default
- CMake: Generates `compile_commands.json` with the `-DCMAKE_EXPORT_COMPILE_COMMANDS=1` flag
- Make: Intercept and generate `compile_commands.json` with the [bear](https://github.com/rizsotto/Bear) tool
