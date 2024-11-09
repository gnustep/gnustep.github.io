# Setting File Compiler Commands


````{make:var} xxx_FILE_FILTER_OUT_FLAGS
xxx_FILE_FILTER_OUT_FLAGS (where xxx is the file name, such as mframe.m) is a filter-out [GNU make pattern](https://www.gnu.org/software/make/manual/html_node/Pattern-Rules.html) of flags to be filtered out from the compilation flags when compiling xxx. In exceptional conditions, you might need to want to use different compiler flags for a file (for example, if a file doesn’t compile with optimization turned on, you might want to compile that single file with optimizations turned off). 

```make
file.m_FILE_FILTER_OUT_FLAGS = -O% -fomit-frame-pointer
```

This says that when compiling file.m we should disable optimization flags, and also remove frame pointer information.
````

````{make:var} xxx_FILE_FLAGS
xxx_FILE_FLAGS (where xxx is the file name, such as main.m) add special compilation flags to be used when compiling xxx. In exceptional conditions, you might need to want to use different compiler flags for a file (for example, if ou want to turn on automated reference counting for that file) 

```make
file.m_FILE_FLAGS = -fobjc-arc
```

This says that when compiling file.m we should turn on ARC. 
````

```make
#
# In exceptional conditions, you might need to want to use different compiler
# flags for a file (for example, if a file doesn't compile with optimization
# turned on, you might want to compile that single file with optimizations
# turned off).  gnustep-make allows you to do this - you can specify special 
# flags to be used when compiling a *specific* file in two ways - 
#
# xxx_FILE_FLAGS (where xxx is the file name, such as main.m) 
#                are special compilation flags to be used when compiling xxx
#
# xxx_FILE_FILTER_OUT_FLAGS (where xxx is the file name, such as mframe.m)
#                is a filter-out make pattern of flags to be filtered out 
#                from the compilation flags when compiling xxx.
#
# Typical examples:
#
# Disable optimization flags for the file NSInvocation.m:
# NSInvocation.m_FILE_FILTER_OUT_FLAGS = -O%
#
# Disable optimization flags for the same file, and also remove 
# -fomit-frame-pointer:
# NSInvocation.m_FILE_FILTER_OUT_FLAGS = -O% -fomit-frame-pointer
#
# Force the compiler to warn for #import if used in file file.m:
# file.m_FILE_FLAGS = -Wimport
# file.m_FILE_FILTER_OUT_FLAGS = -Wno-import
#
```