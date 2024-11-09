# 2 - Setting your application's icon

To set an icon for your app, you just need to define in your `GNUmakefile` the variable `xxx_APPLICATION_ICON`, where `xxx` is the name of your application. For example, if we put the source code for our application shown before (still to be completed though) in the file `MyApp.m`, and the application icon in `MyApp.png`, the `GNUmakefile` could be as follows:

    include $(GNUSTEP_MAKEFILES)/common.make

    APP_NAME = MyApplication
    MyApplication_APPLICATION_ICON = MyApp.png
    MyApplication_RESOURCE_FILES = MyApp.png
    MyApplication_OBJC_FILES = MyApp.m 

    include $(GNUSTEP_MAKEFILES)/application.make

Please note that you need to list the image in the resource files because you want it to be installed in the application directory (otherwise it can't be found by the app at run-time).

  

------------------------------------------------------------------------
