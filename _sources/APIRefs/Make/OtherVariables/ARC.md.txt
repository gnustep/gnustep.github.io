# Automatic Reference Counting Variables

````{make:var} GS_WITH_ARC
GS_WITH_ARC may be set to 1 to turn on ARC for the current build if using the Next Generation runtime setting. This variable may be defined as an environment variable, or on the make command line, or (usually) in the GNUmakefile. The library-combo needs to specify the next generation runtime (eg ng-gnu-gnu) for this variable to take effect. When the ng runtme is used, setting this variable causes the the flags specified in ARC_OBJCFLAGS to be used when compiling any Objective-C source files). If no value is defined for ARC_OBJCFLAGS it is assumed to be ’-fobjc-arc -fobjc-arc-exceptions’ so that code is built with ARC enabled and with support for exceptions (objects are not leaked when an exception occurs). Alternatively, to switch on ARC for individual files, you can have a makefile fragment like this: 
```make
ifeq ($(OBJC_RUNTIME_LIB), ng)
file1.m_FILE_FLAGS += -DGS_WITH_ARC=1 -fobjc-arc -fobjc-arc-exceptions
file2.m_FILE_FLAGS += -DGS_WITH_ARC=1 -fobjc-arc -fobjc-arc-exceptions
file9.m_FILE_FLAGS += -DGS_WITH_ARC=1 -fobjc-arc -fobjc-arc-exceptions
endif
```
````

```{make:var} ARC_CPPFLAGS
ARC_CPPFLAGS sets the flags to defien preprocessor values be used when building code for ARC. This variable is used only if ng runtime is used and the GS_WITH_ARC variable is set to say that ARC is used. 
```

```{make:var} ARC_OBJCFLAGS
ARC_OBJCFLAGS sets the compiler/linker flags to be used when building code for ARC. This variable is used only if ng runtime is used and the ’GS_WITH_ARC’ variable is set to say that ARC is used. The -fobjc-arc flag enables ARC, but by default ARC_OBJCFLAGS is assumed to be -fobjc-arc -fobjc-arc-exceptions, which adds support for exceptions (reducing performance, but preventing leaked memory when an exception occurs). 
```
