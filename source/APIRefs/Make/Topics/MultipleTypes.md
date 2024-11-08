Building more than one thing in the same GNUmakefile
====================================================

Up to now, we discussed how to use a GNUmakefile to build a single thing. For example, a tool or a library. In this section we will discuss how to build more than one thing; for example, a number of tools, or a tool and a library. It is very common in practice.

Building many things of the same type
-------------------------------------

If you want to build more than one tool in the same GNUmakefile, you simply need to list all the tools you want to build in the TOOL\_NAME variable. For example:

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = Client Server
Client_OBJC_FILES = ClientAPI.m FrontEnd.m
Server_OBJC_FILES = ServerAPI.m BackEnd.m

include $(GNUSTEP_MAKEFILES)/tool.make
```

In this case, two tools, Client and Server, will be built. The tool Client will be built by compiling and linking the files ClientAPI.m and FrontEnd.m, while the Server will be built compiling and linking the files ServerAPI.m and BackEnd.m.

The same applies to applications, or libraries, or any other type of projects. For example, for applications you would list all the applications you want to build in the APP\_NAME variable, and similarly for libraries.

Please note that when you specify multiple tools, apps or libraries to be built in the same GNUmakefile, they may be built in any order; starting with gnustep-make 2.4.0, they will even be built and linked in parallel if you are doing a parallel build (please check the forthcoming ’Parallel Building’ Tutorial for more info).

Building things of different type
---------------------------------

If you want to build a tool and an application at the same time, you include both tool.make and application.make at the same time. For example:

```makefile
include $(GNUSTEP_MAKEFILES)/common.make

TOOL_NAME = CommandLineClient
CommandLineClient_OBJC_FILES = ClientAPI.m CommandLineFrontEnd.m

APP_NAME = GUIClient
GUIClient_OBJC_FILES = ClientAPI.m GUIClientFrontEnd.m

include $(GNUSTEP_MAKEFILES)/tool.make
include $(GNUSTEP_MAKEFILES)/application.make
```

Note that in this case the order in which you include tool.make and application.make is important: the tool and the application are built in the specified order. In the example above tool.make is included before application.make and hence the tool will be built before the application.