# 1 - The shared application object

1.1 Creating the shared NSApplication instance
--------------------------------------------------------------------------------------------

The class `NSApplication`, provided by the GNUstep GUI library, represents a gui application. Each gui application has one (and only one) instance of this class, which is shared by all the code; it keeps tracks of all the application windows and panels, and manages the application's run loop. You access this shared instance by calling the `+sharedApplication` method of `NSApplication`, which creates the instance of `NSApplication` representing your app the first time it is invoked, and returns the previously created instance when called again. Creating the shared application is very important, because when it is first created the gui library initializes the gnustep backend; in other words, you need to create the shared application object before doing anything at all with the gui or backend library. So, we will start our first gui application with the code:

    NSApplication *myApplication;

    myApplication = [NSApplication sharedApplication];

An interesting thing to know is that, *after* you have created an `NSApplication` shared instance for your app, you can access it simply through the global variable `NSApp`. So, many people simply discard the result of `+sharedApplication`, and start their apps as follows:

    /* The following line creates the shared application instance */
    [NSApplication sharedApplication];

    /* Then, use NSApp to access NSApplication's shared instance  */

------------------------------------------------------------------------






1.2 Setting a delegate for the shared NSApplication instance
----------------------------------------------------------------------------------------------------------

Once you have learnt how to create your application's shared instance, the fastest way to develop an application is to set a *delegate* for your shared application object. This is done by using the method `-setDelegate:`, as follows:

    id myObject;

    // <missing: create myObject>
    [NSApp setDelegate: myObject];

A delegate is an object of your choice which can customize the behaviour of your application by implementing some (predefined) methods. For example, if you want your application to display a menu (all apps should), you just need to implement in the delegate a method called

    - (void) applicationWillFinishLaunching: (NSNotification *)not;

(ignore the argument for now) and put the code to create the menu inside this method. Your shared application will check if the delegate of your choice has implemented this method, and if so it will run the method just before entering the main run loop. The documentation of `NSApplication` lists all the other methods the delegate can implement to customize your application's behaviour; we'll learn about some of them in other tutorials.

After you set the delegate of your application object, you need to run your application; to do this, just invoke the function `NSApplicationMain ()`, which does all for you.

To sum up, here is the code we are going to use:

    #include <Foundation/Foundation.h>
    #include <AppKit/AppKit.h>

    @interface MyDelegate : NSObject
    - (void) applicationWillFinishLaunching: (NSNotification *)not;
    @end

    @implementation MyDelegate
    - (void) applicationWillFinishLaunching: (NSNotification *)not
    {
      // TODO - Create the menu here.
    }
    @end

    int main (int argc, const char **argv)
    { 
      [NSApplication sharedApplication];
      [NSApp setDelegate: [MyDelegate new]];

      return NSApplicationMain (argc, argv);
    }

------------------------------------------------------------------------






1.3 The run loop of your application
----------------------------------------------------------------------------------

When you call `NSApplicationMain ()`, the GUI library \`\`runs'' your application. When the application is running, the GUI library simply enters a loop waiting for events from the user (such as the user clicking on a window, on a menu, or on the application icon). It creates an autorelease pool at the beginning of each loop, and empties it at the end; this means you don't have to worry about manually creating and emptying autorelease pools in your gui application once it is running.

Of course, if you run an application without having created any windows or menus, the application will wait indefinitely since the user has no means of communicating with it.

  

------------------------------------------------------------------------




1.4 Terminating your application
------------------------------------------------------------------------------

Whenever you want to terminate your application, you can do:

    [NSApp terminate: nil];

This is usually done when the user selects the `Quit` entry in the main menu. When you terminate your application, the gui library quits the run loop and exits the program.

Btw, the argument of `terminate:` is of no importance, so we can pass any argument (`nil` in this case); the only reason why this method takes an object as argument is that it can then be set as the `action` of a gui control (discussed below).

  

------------------------------------------------------------------------

