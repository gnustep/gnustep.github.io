



# 6 Exception Handling, Assertions, Logging, and Sanitization

 <span id="index-logging-facilities" class="index-entry-id"></span> <span id="index-assertion-facilities" class="index-entry-id"></span> <span id="index-memory-sanitisation-facilities" class="index-entry-id"></span>

No matter how well a program is designed, if it has to interact with a user or other aspect of the outside world in any way, the code is bound to occasionally meet with cases that are either invalid or just plain unexpected. A very simple example is when a program asks the user to enter a filename, and the user enters the name of a file that does not exist, or does not enter a name at all. Perhaps a valid filename *is* entered, but, due to a previous disk write error the contents are garbled. Any number of things can go wrong. In addition, programmer error inevitably occurs and needs to be taken account of. Internal functions may be called with invalid arguments, either due to unexpected paths being taken through the code, or silly things like typos using the wrong variable for something. When these problems happen (and they *will* happen), it is better to handle them gracefully than for the program to crash, or worse, to continue processing but in an erroneous way.

To allow for this, many computer languages provide two types of facilities. The first is referred to as *exception handling* or sometimes *error trapping*. The second is referred to as *assertion checking*. Exceptions allow the program to catch errors when they occur and react to them explicitly. Assertions allow a programmer to establish that certain conditions hold before attempting to execute a particular operation. GNUstep provides both of these facilities, and we will cover each in turn. The assertion facility is tied in with the GNUstep *logging* facilities, so we describe those as well.

To use any of the facilities described in this chapter requires that you include `Foundation/NSException.h`.






## 6.1 Exceptions

 <span id="index-NSException-class" class="index-entry-id"></span> <span id="index-NS_005fDURING-macro" class="index-entry-id"></span> <span id="index-NS_005fHANDLER-macro" class="index-entry-id"></span> <span id="index-NS_005fENDHANDLER-macro" class="index-entry-id"></span> <span id="index-NSUncaughtExceptionHandler" class="index-entry-id"></span>

GNUstep exception handling provides for two things:

1.  When an error condition is detected during execution, control is passed to a special error-handling routine, which is given information on the error that occurred.
2.  This routine may itself, if it chooses, pass this information up the function call stack to the next higher level of control. Often higher level code is more aware of the context in which the error is occurring, and can therefore make a better decision as to how to react.







### 6.1.1 Catching and Handling Exceptions

GNUstep exception handling is implemented through the macros `NS_DURING`, `NS_HANDLER`, and `NS_ENDHANDLER` in conjunction with the `NSException` class. The following illustrates the pattern:

    NS_DURING
      {
        // do something risky ...
      }
    NS_HANDLER
      {
        // a problem occurred; inform user or take another tack ...
      }
    NS_ENDHANDLER
      // back to normal code...

For instance:

    - (DataTree *) readDataFile: (String *)filename
    {
      ParseTree *parse = nil;
      NS_DURING
        {
          FileHandle *handle = [self getFileHandle: filename];
          parse = [parser parseFile: handle];
          if (parse == nil)
            {
              NS_VALUERETURN(nil);
            }
        }
      NS_HANDLER
        {
          if ([[localException name] isEqualToString: MyFileNotFoundException])
            {
              return [self readDataFile: fallbackFilename];
            }
          else if ([[localException name] isEqualToString: NSParseErrorException])
            {
              return [self readDataFileInOldFormat: filename];
            }
          else
            {
              [localException raise];
            }
        }
      NS_ENDHANDLER
      return [[DataTree alloc] initFromParseTree: parse];
    }

Here, a file is parsed, with the possibility of at least two different errors: not finding the file and the file being misformatted. If a problem does occur, the code in the `NS_HANDLER` block is jumped to. Information on the error is passed to this code in the `localException` variable, which is an instance of `NSException`. The handler code examines the name of the exception to determine if it can implement a work-around. In the first two cases, an alternative approach is available, and so an alternative value is returned.

If the file is found but the parse simply produces a nil parse tree, the `NS_VALUERETURN` macro is used to return nil to the `readDataFile:` caller. Note that it is *not* allowed to simply write "`return nil;`" inside the NS\_DURING block, owing to the nature of the behind-the-scenes C constructs implementing the mechanism (the `setjmp()` and `longjmp()` functions). If you are in a void function not returning a value, you may use simply “`NS_VOIDRETURN`” instead.

Finally, notice that in the third case above the handler does not recognize the exception type, so it passes it one level up to the caller by calling `-raise` on the exception object.

### 6.1.2 Passing Exceptions Up the Call Stack

If the caller of `-readDataFile:` has enclosed the call inside its own `NS_DURING` … `NS_HANDLER` … `NS_ENDHANDLER` block, it will be able to catch this exception and react to it in the same way as we saw here. Being at a higher level of execution, it may be able to take actions more appropriate than the `-readDataFile:` method could have.

If, on the other hand, the caller had *not* enclosed the call, it would not get a chance to react, but the exception would be passed up to the caller of *this* code. This is repeated until the top control level is reached, and then as a last resort `NSUncaughtExceptionHandler` is called. This is a built-in function that will print an error message to the console and exit the program immediately. If you don't want this to happen it is possible to override this function by calling `NSSetUncaughtExceptionHandler(fn_ptr)`. Here, `fn_ptr` should be the name of a function with this signature (defined in `NSException.h`):

    void NSUncaughtExceptionHandler(NSException *exception);

One possibility would be to use this to save files or any other unsaved state before an application exits because of an unexpected error.

### 6.1.3 Where do Exceptions Originate?

You may be wondering at this point where exceptions come from in the first place. There are two main possibilities. The first is from the Base library; many of its classes raise exceptions when they run into error conditions. The second is that application code itself raises them, as described in the next section. Exceptions do *not* arise automatically from C-style error conditions generated by C libraries. Thus, if you for example call the `strtod()` function to convert a C string to a double value, you still need to check `errno` yourself in standard C fashion.

Another case that exceptions are *not* raised in is in the course of messaging. If a message is sent to `nil`, it is silently ignored without error. If a message is sent to an object that does not implement it, the `forwardInvocation` method is called instead, as discussed in [Advanced Messaging](Advanced-Messaging).

### 6.1.4 Creating Exceptions

If you want to explicitly create an exception for passing a particular error condition upwards to calling code, you may simply create an `NSException` object and `raise` it:

    NSException myException = [[NSException alloc]
                                  initWithName: @"My Exception"
                                        reason: @"[Description of the cause...]"
                                      userInfo: nil];
    [myException raise];
     // code in block after here is unreachable..

The `userInfo` argument here is a `NSDictionary` of key-value pairs containing application-specific additional information about the error. You may use this to pass arbitrary arguments within your application. (Because this is a convenience for developers, it should have been called `developerInfo`..)

Alternatively, you can create the exception and raise it in one call with `+raise`:

    [NSException raise: @"My Exception"
                format: @"Parse error occurred at line %d.",lineNumber];

Here, the `format` argument takes a printf-like format analogous to `[NSString -stringWithFormat:]` discussed [Strings in GNUstep](Objective_002dC). In general, you should not use arbitrary names for exceptions as shown here but constants that will be recognized throughout your application. In fact, GNUstep defines some standard constants for this purpose in `NSException.h`:

`NSCharacterConversionException`  
An exception when character set conversion fails.

`NSGenericException`  
A generic exception for general purpose usage.

`NSInternalInconsistencyException`  
An exception for cases where unexpected state is detected within an object.

`NSInvalidArgumentException`  
An exception used when an invalid argument is passed to a method or function.

`NSMallocException`  
An exception used when the system fails to allocate required memory.

`NSParseErrorException`  
An exception used when some form of parsing fails.

`NSRangeException`  
An exception used when an out-of-range value is encountered.

Also, some Foundation classes define their own more specialized exceptions:

`NSFileHandleOperationException (NSFileHandle.h)`  
An exception used when a file error occurs.

`NSInvalidArchiveOperationException (NSKeyedArchiver.h)`  
An archiving error has occurred.

`NSInvalidUnarchiveOperationException (NSKeyedUnarchiver.h)`  
An unarchiving error has occurred.

`NSPortTimeoutException (NSPort.h)`  
Exception raised if a timeout occurs during a port send or receive operation.

`NSUnknownKeyException (NSKeyValueCoding.h)`  
An exception for an unknown key.

### 6.1.5 When to Use Exceptions

As might be evident from the `-readDataFile:` example above, if a certain exception can be anticipated, it can also be checked for, so you don't necessarily need the exception mechanism. You may want to use exceptions anyway if it simplifies the code paths. It is also good practice to catch exceptions when it can be seen that an unexpected problem might arise, as any time file, network, or database operations are undertaken, for instance.

Another important case where exceptions are useful is when you need to pass detailed information up to the calling method so that it can react appropriately. Without the ability to raise an exception, you are limited to the standard C mechanism of returning a value that will hopefully be recognized as invalid, and perhaps using an `errno`-like strategy where the caller knows to examine the value of a certain global variable. This is inelegant, difficult to enforce, and leads to the need, with void methods, to document that "the caller should check `errno` to see if any problems arose".

## 6.2 Logging

 <span id="index-NSLog-function" class="index-entry-id"></span> <span id="index-NSDebugLog-function" class="index-entry-id"></span> <span id="index-NSWarnLog-function" class="index-entry-id"></span> <span id="index-profiling-facilities" class="index-entry-id"></span>

GNUstep provides several distinct logging facilities best suited for different purposes.






### 6.2.1 NSLog

The simplest of these is the `NSLog(NSString *format, ...)` function. For example:

    NSLog(@"Error occurred reading file at line %d.", lineNumber);

This would produce, on the console (stderr) of the application calling it, something like:

    2004-05-08 22:46:14.294 SomeApp[15495] Error occurred reading file at line 20.

The behavior of this function may be controlled in two ways. First, the user default `GSLogSyslog` can be set to "`YES`", which will send these messages to the syslog on systems that support that (Unix variants). Second, the function GNUstep uses to write the log messages can be overridden, or the file descriptor the existing function writes to can be overridden:

      // these changes must be enclosed within a lock for thread safety
    NSLock *logLock = GSLogLock();
    [logLock lock];

      // to change the file descriptor:
    _NSLogDescriptor = <fileDescriptor>;
      // to change the function itself:
    _NSLog_printf_handler = <functionName>;

    [logLock unlock];

Due to locking mechanisms used by the logging facility, you should protect these changes using the lock provided by `GSLogLock()` (see [Threads and Run Control](Base-Library) on locking).

The `NSLog` function was defined in OpenStep and is also available in Mac OS X Cocoa, although the overrides described above may not be. The next set of logging facilities to be described are only available under GNUstep.

### 6.2.2 NSDebugLog, NSWarnLog

The facilities provided by the `NSDebugLog` and `NSWarnLog` families of functions support source code method name and line-number reporting and allow compile- and run-time control over logging level.

The `NSDebugLog` functions are enabled at compile time by default. To turn them off, set `'diagnose = no'` in your makefile, or undefine `GSDIAGNOSE` in your code before including `NSDebug.h`. To turn them off at runtime, call `[[NSProcessInfo processInfo] setDebugLoggingEnabled: NO]`. (An `NSProcessInfo` instance is automatically instantiated in a running GNUstep application and may be obtained by invoking `[NSProcessInfo processInfo]`.)

At runtime, whether or not logging is enabled, a debug log method is called like this:

    NSDebugLLog(@"ParseError", @"Error parsing file at line %d.", lineNumber);

Here, the first argument to `NSDebugLog`, "`ParseError`", is a string *key* that specifies the category of message. The message will only actually be logged (through a call to `NSLog()`) if this key is in the set of active debug categories maintained by the `NSProcessInfo` object for the application. Normally, this list is empty. There are three ways for string keys to make it onto this list:

- Provide one or more startup arguments of the form `--GNU-Debug=<key>` to the program. These are processed by GNUstep and removed from the argument list before any user code sees them.
- Call `[NSProcessInfo debugSet]` at runtime, which returns an `NSMutableSet`. You can add (or remove) strings to this set directly.
- The `GNU-Debug` user default nay contain a comma-separated list of keys. However, note that `[NSUserDefaults standardUserDefaults]` must first be called before this will take effect (to read in the defaults initially).

While any string can be used as a debug key, conventionally three types of keys are commonly used. The first type expresses a "level of importance" for the message, for example, “Debug”, “Info”, “Warn”, or “Error”. The second type of key that is used is class name. The GNUstep Base classes used this approach. For example if you want to activate debug messages for the `NSBundle`” class, simply add '`NSBundle`’ to the list of keys. The third category of key is the default key, ’`dflt`’. This key can be used whenever the specificity of the other key types is not required. Note that it still needs to be turned on like any other logging key before messages will actually be logged.

There is a family of `NSDebugLog` functions with slightly differing behaviors:

`NSDebugLLog(key, format, args,...)`  
Basic debug log function already discussed.

`NSDebugLog(format, args,...)`  
Equivalent to `NSDebugLLog` with key "dflt" (for default).

`NSDebugMLLog(key, format, args,...)`  
Equivalent to `NSDebugLLog` but includes information on which method the logging call was made from in the message.

`NSDebugMLog(format, args,...)`  
Same, but use 'dflt’ log key.

`NSDebugFLLog(key, format, args,...)`  
As `NSDebugMLLog` but includes information on a function rather than a method.

`NSDebugFLog(format, args,...)`  
As previous but using 'dflt’ log key.

The implementations of the `NSDebugLog` functions are optimized so that they consume little time when logging is turned off. In particular, if debug logging is deactivated at compile time, there is NO performance cost, and if it is completely deactivated at runtime, each call entails only a boolean test. Thus, they can be left in production code.

There is also a family of `NSWarn` functions. They are similar to the `NSDebug` functions except that they do not take a key. Instead, warning messages are shown by default unless they are disabled at compile time by setting `'warn = no'` or undefining `GSWARN`, or at runtime by *adding* "`NoWarn`" to `[NSProcessInfo debugSet]`. (Command-line argument `--GNU-Debug=NoWarn` and adding “NoWarn” to the `GNU-Debug` user default will also work.) `NSWarnLog()`, `NSWarnLLog()`, `NSWarnMLLog`, `NSWarnMLog`, `NSWarnFLLog`, and `NSWarnFLog` are all similar to their `NSDebugLog` counterparts.

### 6.2.3 Last Resorts: GSPrintf and fprintf

Both the `NSDebugLog` and the simpler `NSLog` facilities utilize a fair amount of machinery - they provide locking and timestamping for example. Sometimes this is not appropriate, or might be too heavyweight in a case where you are logging an error which might involve the application being in some semi-undefined state with corrupted memory or worse. You can use the `GSPrintf()` function, which simply converts a format string to UTF-8 and writes it to a given file:

    GSPrintf(stderr, "Error at line %d.", n);

If even this might be too much (it uses the `NSString` and `NSData` classes), you can always use the C function `fprintf()`:

    fprintf(stderr, "Error at line %d.", n);

Except under extreme circumstances, the preferred logging approach is either `NSDebugLog`/`NSWarnLog`, due the the compile- and run-time configurability they offer, or `NSLog`.

### 6.2.4 Profiling Facilities

GNUstep supports optional programmatic access to object allocation statistics. To initiate collection of statistics, call the function `GSDebugAllocationActive(BOOL active)` with an argument of "`YES`". To turn it off, call it with “`NO`”. The overhead of statistics collection is only incurred when it is active. To access the statistics, use the set of `GSDebugAllocation...()` functions defined in `NSDebug.h`.

In addition to basic statistics (but at higher performance cose), the `GSDebugAllocation...()` functions provide detailed records of when and where objects are allocated/deallocated. This can be useful when debugging for memory leaks.

Finally, for pinpoint accuracy, the -trackOwnership method can be called on an individual object to turn on tracking of the lifetime of that object. In this case a stack trace is printed logging every ownership event (retain, release, or dealloc) and a log is printed at process exit if the object has not been deallocated. The same method may be called on a class to track every object of that class. This method is declared in `NSObject+GNUstepBase.h`. Tracking the life of an individual object is particularly useful if a leak checker (eg when your program was built using `(make asan=yes)` or run under valgrind) has reported a leak and the cause of the leak is hard to find: the leak checker will have told you the stack trace where the leaked memory was allocated, so you can change your code to start tracking immediately after that and see exactly what happened to the object ownership after its creation.

## 6.3 Assertions

 <span id="index-NSAssert-macro" class="index-entry-id"></span> <span id="index-NSAssertionHandler-class" class="index-entry-id"></span>

Assertions provide a way for the developer to state that certain conditions must hold at a certain point in source code execution. If the conditions do not hold, an exception is automatically raised (and succeeding code in the block is not executed). This avoids an operation from taking place with illegal inputs that may lead to worse problems later.

The use of assertions is generally accepted to be an efficient means of improving code quality, for, like unit testing, they can help rapidly uncover a developer's implicit or mistaken assumptions about program behavior. However this is only true to the extent that you carefully design the nature and placement of your assertions. There is an excellent discussion of this issue bundled in the documentation with Sun’s Java distribution.





### 6.3.1 Assertions and their Handling

Assertions allow the developer to establish that certain conditions hold before undertaking an operation. In GNUstep, the standard means to make an assertion is to use the `NSAssert` or `NSCAssert` macros. The general form of these macros is:

    NSAssert(<boolean test>, <formatString>, <argumentsToFormat>);

For instance:

    NSAssert(x == 10, "X should have been 10, but it was %d.", x);

If the test '`x == 10`’ evaluates to `true`, `NSLog()` is called with information on the method and line number of the failure, together with the format string and argument. The resulting console message will look like this:

    Foo.m:126  Assertion failed in Foo(instance), method Bar.  X should have been
    10, but it was 5.

After this is logged, an exception is raised of type '`NSInternalInconsistencyException`’, with this string as its description.

If you need to make an assertion inside a regular C function (not an Objective-C method), use the equivalent macro `NSCAssert()`, etc..

***Note***, you can completely disable assertions (saving the time for the boolean test and avoiding the exception if fails) by putting `#define NS_BLOCK_ASSERTIONS` before you include `NSException.h`.

### 6.3.2 Custom Assertion Handling

The aforementioned behavior of logging an assertion failure and raising an exception can be overridden if desired. You need to create a subclass of `NSAssertionHandler` and register an instance in each thread in which you wish the handler to be used. This is done by calling:

    [[[NSThread currentThread] threadDictionary]
        setObject:myAssertionHandlerInstance forKey:N̈SAssertionHandler"];

See [Threads and Run Control](Base-Library) for more information on what this is doing.

### 6.3.3 Comparison with Java

 <span id="index-logging_002c-compared-with-Java" class="index-entry-id"></span> <span id="index-assertion-handling_002c-compared-with-Java" class="index-entry-id"></span>

GNUstep's exception handling facilities are, modulo syntax, equivalent to those in Java in all but three respects:

- There is no provision for a "finally" block executed after either the main code or the exception handler code.
- You cannot declare the exception types that could be raised by a method in its signature. In Java this is possible and the compiler uses this to enforce that a caller should catch exceptions if they might be generated by a method.
- Correspondingly, there is no support in the [documentation system](GSDoc) for documenting exceptions potentially raised by a method. (This will hopefully be rectified soon.)

The logging facilities provided by `NSDebugLog` and company are similar to but a bit more flexible than those provided in the Java/JDK 1.4 logging APIs, which were based on the IBM/Apache Log4J project.

The assertion facilities are similar to but a bit more flexible than those in Java/JDK 1.4 since you can override the assertion handler.

## 6.4 Address sanitization

 <span id="index-memory-access" class="index-entry-id"></span>

One of the powers of the C family of languages is the existence of pointer to memory locations containing information, indeed every Objective-C object variable is a pointer to a memory location containing that object.

Aside from object specific problems, we have the standard problems of C in that a pointer to memory can result in access violations where we attempt to read from a memory location that we shouldn't or write to one that we shouldn’t. These operations can cause our program to crash or to misbehave (if we read from a memory location that doesn’t contain the information we are expecting).

Modern compilers try to catch such errors and will warn about the more dangerous looking code.

Operating systems catch some errors, and kill programs which access some memory locations.

The combination of these mechanisms still doesn't deal with everything.






### 6.4.1 Building with ASAN

The gnustep-make package provides an option (`asan=yes`) to build software with address sanitisation turned on, to catch many more errors when the program runs.

This causes the compiler to add extra code to check things at runtime (as well as curing the code to link with different libraries to catch errors in the parameters passed to many standard functions).

This is dangerous for production code, because the program will terminate as soon as such an error (even if it is harmless) is detected, but is a great feature for a software developer or QA tester to have turned on.

In addition to building individual files or projects using `asan=yes` gnustep-make understands the environment variable setting `GNUSTEP_WITH_ASAN=1` as turning on building with ASAN.

### 6.4.2 Typical address issues

The typical issues detected are buffer overflow (or overrun) where we write to a location just beyond the start or end of a section of memory allocated by the malloc() function, and buffer overread where we read beyond the meaningful data.

    for (char *ptr = buffer; *ptr != '\0'; ptr++)
      {
        if (memcmp(ptr, key, keylen) == 0)
          {
            return 1; // found the key
          }
      }
    return 0;   // key not found

The code to search for a key in a buffer containing a nul terminated C-string may (depending on exactly how memcmp() is implemented) read data beyond the terminating nul, and possibly outside the memory allocated for the buffer. This will be detected at runtime by the address sanitizer and cause the program to stop immediately and print a message to STDERR describing the location and nature of the problem:

    =================================================================
    ==NNNNNN==ERROR: AddressSanitizer: heap-buffer-overflow on address ...
    READ of size N at ...
        #0 ... in MemcmpInterceptorCommon...
        #1 ... in memcmp (/home/username/a+...)...
        #2 ... in main /home/username/a.m:120:40
        ...

    ... is located N bytes after N-byte region ... allocated by thread ... here:
        #0 ... in malloc ...
        #1 ... in main /home/username/a.m:55:22
        ...

    SUMMARY: AddressSanitizer: heap-buffer-overflow ... 
    Shadow bytes around the buggy address: ...

The report says what happened, then gives a stack trace of where it happened, then a stack trace of where the buffer was allocated, then a summary, and finally some incomprehensible (unless you want to get deep into the details of how the address sanitizer works) 'shadow byte’ details.

Generally a look at the source code corresponding to the first reported stack trace, in conjunction with the knowledge of the type of error detected, is enough to spot the problem so that you can fix it.

### 6.4.3 Leak sanitization



Leak sanitization is a close cousin to address sanitization. Rather than attempting to detect general addressing errors of writing to incorrect locations, it concentrates on the issue of memory allocations which are not matched by deallocation when the memory is no longer needed. Memory leaks typically result in crashes because the system runs out of memory.

Like address sanitization, leak sanitization is turned on by the asan=yes option in gnustep-make. Unlike address sanitization, it does not necessarily have to be turned on while compiling every file, only when compiling an linking the main executable.

Memory leaks are normally checked at the point when a process is shut down, but can optionally be checked for and reported by a running process.

Leak sanitization reports are somewhat similar to address sanitization reports:

    =================================================================
    ==NNNNNN==ERROR: LeakSanitizer: detected memory leaks

    Direct leak of N byte(s) in N object(s) allocated from:
        #0 ... in malloc (/home/username/a.out+...)...
        #1 ... in main /home/username/a.m:7:19
        #2 ... in __libc_start_call_main ...
        #3 ... in __libc_start_main csu/...
        #4 ... in _start (/home/username/a.out+...)

    SUMMARY: AddressSanitizer: N byte(s) leaked in N allocation(s).

The above example is the approximate format for a program named a.out built by compiling the source file a.m and shows that the leaked memory was allocated by a call to malloc() at line 7 column 19 in a.m

For more information see <a href="Objects.html" class="pxref">Working With Objects</a>

### 6.4.4 Drawbacks of ASAN

While address sanitization has great points: helping prevent crashes (writing to bad locations), program logic errors (reading and making decisions on bad data), and attacks (specially crafted data deliberately changing program logic), it also has drawbacks.

- It needs to be turned on when compiling each source file of any library or program.
- If turned on for a library, that library can only be used with programs for which it is also turned on.
- There is no error recovery possible; any detected error causes the program to stop immediately.
- The extra code for monitoring memory accesses makes your program run at (approximately) half the normal speed.
- It allocates a huge amount of virtual memory (terabytes) making making it impossible to monitor memory usage by your process using most tools.
- It uses a lot more real memory to record information about what your process does, possibly causing your system to run out of memory.




