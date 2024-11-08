# Overridable Flags

These flags are passed by the user when invoking Make. They should not be modified by the GNUmakefile.

````{make:var} OBJCFLAGS
OBJCFLAGS are flags that are passed to the compiler when compiling Objective-C files. The user can override this variable when running make and specify different flags as the following command illustrates: 
```
make OBJCFLAGS="-Wno-implicit -Wno-protocol"
```
````

````{make:var} CFLAGS
CFLAGS are flags that are passed to the compiler when compiling C files. The user can override this variable when running make and specify different flags as the following command illustrates: 
```
make CFLAGS="-Wall"
```
````

````{make:var} OPTFLAG
OPTFLAG is the flag used to indicate the optimization level that the compiler should perform when compiling Objective-C and C files; this flag is set to ‘-O2’ by default, but the user can override this setting when running make as the following command illustrates: 
```
make OPTFLAG=
```
This command sets the optimization flag to be empty so that no optimization will be performed by the compiler.

````

````{make:var} GNUSTEP_INSTALLATION_DOMAIN
GNUSTEP_INSTALLATION_DOMAIN is the domain where the package will install its files; overriding this variable when running make will change all of the variables within the Makefile Package that depend upon it; the following command illustrates the use of this:

```
make GNUSTEP_INSTALLATION_DOMAIN=SYSTEM
```

This command states that the SYSTEM domain should be used as the installation root directory; in particular applications in the package will be installed in the $GNUSTEP_SYSTEM_APPS directory, libraries in the package will be installed under the $GNUSTEP_SYSTEM_LIBRARIES directory, command line tools will be installed under the $GNUSTEP_SYSTEM_TOOLS directory, etc. Depending on the filesystem layout, the various directories may be located anywhere, which is why it’s important to also refer to them by using variables such as GNUSTEP_APPS, GNUSTEP_LIBRARIES and GNUSTEP_TOOLS, which automatically point to the right directory on disk for this filesystem layout and installation domain.

By default the Makefile Package sets GNUSTEP_INSTALLATION_DOMAIN to LOCAL.
````

````{make:var} messages
messages can be set to ‘yes’ in order to increase the verbosity and see all the commands the make is executing. 
```
make messages=yes
```
````

````{make:var} documentation
documentation controls whether the documentation targets in a project will be executed. If you don’t desire building the documentation (which might require a working LaTeX installation, etc.) you can set this to ‘no’. Otherwise the documentation will be built. 
```
make documentation=no
```
````

````{make:var} shared
By default the Makefile Package will generate a shared library if it is building a library project type, and it will link with shared libraries if it is building an application or command line tool project type. The following command illustrates how to tell the Makefile Package not to build using shared libraries but using static libraries instead.
```
make shared=no
```
This default is only applicable on systems that support shared libraries; systems that do not support shared libraries will always build using static libraries. Some systems support dynamic link libraries (DLL) which are a form of shared libraries; on these systems, DLLs will be built by default unless the Makefile Package is told to build using static libraries instead, as in the above command. 
````

````{make:var} profile
By default the Makefile Package does not tell the compiler to generate profiling information when compiling Objective-C and C files. The following command illustrates how to tell the Makefile Package to pass the appropriate flags to the compiler so that profiling information is put into the binary files.
```
make profile=yes
```
````

````{make:var} debug
By default the Makefile Package tells the compiler to generate debugging information when compiling Objective-C and C files. The following command illustrates how to tell the Makefile Package to pass the appropriate flags to the compiler so that debugging information is not put into the binary files.
```
make debug=no
```
````
