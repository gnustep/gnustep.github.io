



# Appendix E GNUstep Compliance to Standards

 <span id="index-standards-compliance" class="index-entry-id"></span> <span id="index-OpenStep-compliance" class="index-entry-id"></span> <span id="index-OS-X-compatibility" class="index-entry-id"></span>

GNUstep is generally compatible with the OpenStep specification and with recent developments of the MacOS-X (Cocoa) API. Where MacOS deviates from the OpenStep API, GNUstep generally attempts to support both versions. In some cases the newer MacOS APIs are incompatible with OpenStep, and GNUstep usually supports the richer version.

In order to deal with compatiblity issues, GNUstep uses two mechanisms - it provides conditionally compiled sections of the library header files, so that software can be built that will conform strictly to a particular API, and it provides user default settings to control the behavior of the library at runtime.




## E.1 Conditional Compilation



Adding an option to a makefile to define one of the following preprocessor constants will modify the API visible to software being compiled -

<span class="category-def">preprocessor: </span>**NO\_GNUSTEP**  
GNUstep specific extensions to the OpenStep and MacOS cocoa APIs are excluded from the headers if this is defined to a non-zero value.

<!-- -->

<span class="category-def">preprocessor: </span>**STRICT\_MACOS\_X**  
Only methods and classes that are part of the MacOS cocoa API are made available in the headers if this is defined.

<!-- -->

<span class="category-def">preprocessor: </span>**STRICT\_OPENSTEP**  
Only methods and classes that are part of the OpenStep specification are made available in the headers if this is defined.

Note, these preprocessor constants are used in developer code (ie the code that users of GNUstep write) rather than by the GNUstep software itself. They permit a developer to ensure that he/she does not write code which depends upon API not present on other implementations (in practice, MacOS-X or some old OPENSTEP systems). The actual GNUstep libraries are always built with the full GNUstep API in place, so that the feature set is as consistent as possible.

## E.2 User Defaults



User defaults may be specified ...

<span class="category-def">defaults: </span>**GNU-Debug**  
An array of strings that lists debug levels to be used within the program. These debug levels are merged with any which were set on the command line or added programmatically to the set given by the \[NSProcessInfo-debugSet\] method.

<!-- -->

<span class="category-def">defaults: </span>**GSLogSyslog**  
Setting the user default GSLogSyslog to YES will cause log/debug output to be sent to the syslog facility (on systems which support it), rather than to the standard error stream. This is useful in environments where stderr has been re-used strangely for some reason.

<!-- -->

<span class="category-def">defaults: </span>**GSMacOSXCompatible**  
Setting the user default GSMacOSXCompatible to YES will cause MacOS compatible behavior to be the default at runtime. This default may however be overridden to provide more fine grained control of system behavior.

<!-- -->

<span class="category-def">defaults: </span>**GSOldStyleGeometry**  
Specifies whether the functions for producing strings describing geometric structures (NSStringFromPoint(), NSStringFromSize(), and NSStringFromRect()) should produce strings conforming to the OpenStep specification or to MacOS-X behavior. The functions for parsing those strings should cope with both cases anyway.

<!-- -->

<span class="category-def">defaults: </span>**GSSOCKS**  
May be used to specify a default SOCKS5 server (and optionally a port separated from the server by a colon) to which tcp/ip connections made using the NSFileHandle extension methods should be directed.

This default overrides the SOCKS5\_SERVER and SOCKS\_SERVER environment variables.

<!-- -->

<span class="category-def">defaults: </span>**Local Time Zone**  
Used to specify the name of the timezone to be used by the NSTimeZone class.

<!-- -->

<span class="category-def">defaults: </span>**NSWriteOldStylePropertyLists**  
Specifies whether text property-list output should be in the default MacOS-X format (XML), or in the more human readable (but less powerful) original OpenStep format.

Reading of property lists is supported in either format, but only if GNUstep is built with the libxml library (which is needed to handle XML parsing).

NB. MacOS-X generates illegal XML for some strings - those which contain characters not legal in XML. GNUstep always generates legal XML, at the cost of a certain degree of compatibility. GNUstep XML property lists use a backslash to escape illegal chatracters, and consequently any string containing either a backslash or an illegal character will be written differently to the same string on MacOS-X.

<!-- -->

<span class="category-def">defaults: </span>**NSLanguages**  
An array of strings that lists the users prefered languages, in order or preference. If not found the default is just English.




