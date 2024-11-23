# 5 - Adding a Window to your Application

5.1 Window Attributes
-------------------------------------------------------------------

Windows are represented in GNUstep by `NSWindow` objects. We are mainly interested in two attributes of a `NSWindow` object:

1.  its *content rect*, which is a `NSRect` describing the rectangle covered by the internal area of the window; i.e., without all the decorations added by the window manager (title bar, border, etc);

2.  its *style mask*, which is an `unsigned int` describing the decorations of the window. A zero style mask is also known as a `NSBorderlessWindowMask`, and means that the window has no decorations at all. If the window needs to have decorations, you need to set its style mask to a combination (`OR`) of one or more of the following constants:

    -   `NSTitledWindowMask` = the window has a title bar;
    -   `NSClosableWindowMask` = the window can be closed by the user (it has a close button);
    -   `NSMiniaturizableWindowMask` = the window can be miniaturized by the user (it has a miniaturize button);
    -   `NSResizableWindowMask` = the window can be resized by the user.

    For example, a window which has a title bar, is closable and miniaturizable will have a style mask of:

        unsigned int windowMask = NSTitledWindowMask 
                                  | NSClosableWindowMask 
                                  | NSMiniaturizableWindowMask;

    where `|` is the C operator for the logical OR.

While you might freely change the content rect of your window after the window has been created, you can only set the style mask when the window is first created. You can not change the style mask of the window after creation.

------------------------------------------------------------------------






5.2 Creating a Window
-------------------------------------------------------------------

We are now ready to show an example of creating a window:

    NSWindow *myWindow;
    NSRect rect = NSMakeRect (100, 100, 200, 200);
    unsigned int styleMask = NSTitledWindowMask 
                             | NSMiniaturizableWindowMask;

    myWindow = [NSWindow alloc];
    myWindow = [myWindow initWithContentRect: rect
                         styleMask: styleMask
                         backing: NSBackingStoreBuffered
                         defer: NO];

(Please ignore the `backing:` and `defer:` arguments for now. The values given in the example are appropriate in most cases). This window has an initial content rect which has its origin (its lower left corner) positioned in the point of coordinates (100, 100) in the screen coordinates. In GNUstep, all coordinates system are by default Cartesian and the origin is always in the lower left corner (this is only the default, because inside a GNUstep window you can then flip, rotate and (more generally) transform coordinates at your wish). The window has a title bar, and can be miniaturized by the user, but not resized or closed.

  

------------------------------------------------------------------------




5.3 Setting the title of a window
-------------------------------------------------------------------------------

To set the title of the window `myWindow`, you simply do something like:

    [myWindow setTitle: @"This is a test window"];

  

------------------------------------------------------------------------




5.4 Ordering Front a window
-------------------------------------------------------------------------

Creating a window does not make it visible. The window is ready to be used, but it is not visible till you invoke the `orderFront:` method, as in:

    [myWindow orderFront: nil];

Note that `orderFront:` takes an object argument so that it can be invoked as an *action* from a button or a menu item, but the argument is usually ignored, which is why we simply use `nil` for it.

If you want your window to be ordered front and to get the keyboard focus, you do:

    [myWindow makeKeyAndOrderFront: nil];

  

------------------------------------------------------------------------




5.5 Integrating the window with your application
----------------------------------------------------------------------------------------------

We now want to show a very simple example of an application creating a single window. Our application will be useless if its only window is not visible; for this reason, we do not add a close button to the window (following the *NEXTSTEP* tradition, the user needs to select *Quit* from the main menu to quit the application. To get the main menu, the user typically clicks the right button of his mouse on one of the windows).

We can create the window in the same method

    - (void) applicationWillFinishLaunching: (NSNotification *)not;

where we created the menu. This method contains initialization code to be run *before* the application is launched.

But, we can't order front (ie, make visible) windows before `NSApp` has become active (which happens when the application is \`launched'), so that ordering front our window in `applicationWillFinishLaunching:` would not work, because this method is called *before* the application finished launching.

To get around this problem, we implement in our application delegate also another method, called:

    - (void) applicationDidFinishLaunching: (NSNotification *)not;

This method (if implemented by the application's delegate) is invoked by the `NSApp` just after it finished launching. We order front our window in this method.

  

------------------------------------------------------------------------

<span id="CHILD_LINKS">**Subsections**</span>

-   <a href="node12.html" id="tex2html145">5.5.1 The organization of start-up code</a>

------------------------------------------------------------------------






### 5.5.1 The organization of start-up code

The way we have organized the start-up of our application is as follows:

    - (void) applicationWillFinishLaunching: (NSNotification *)not;

contains code for the initialization process of the application. This usually means creating the application's main objects; in our case, the menu and the only application's window. You can't (and shouldn't) display any window in this method.

Instead, the method

    - (void) applicationDidFinishLaunching: (NSNotification *)not;

contains a sequence of user-visible actions to be taken just after the application became active: typically, popping up a window or a panel to the user.

It should be mentioned that this is not the only possible way of organizing the start-up of an application.

  

------------------------------------------------------------------------




5.6 Source Code
-------------------------------------------------------------

Here is the source code of our new application:

    #include <Foundation/Foundation.h>
    #include <AppKit/AppKit.h>

    @interface MyDelegate : NSObject
    {
      NSWindow *myWindow;
    }
    - (void) createMenu;
    - (void) createWindow;
    - (void) applicationWillFinishLaunching: (NSNotification *)not;
    - (void) applicationDidFinishLaunching: (NSNotification *)not;
    @end

    @implementation MyDelegate : NSObject 
    - (void) dealloc
    {
      RELEASE (myWindow);
    }

    - (void) createMenu
    {
      NSMenu *menu;

      menu = AUTORELEASE ([NSMenu new]);

      [menu addItemWithTitle: @"Quit"  
            action: @selector (terminate:)  
            keyEquivalent: @"q"];

      [NSApp setMainMenu: menu];
    }

    - (void) createWindow
    {
      NSRect rect = NSMakeRect (100, 100, 200, 200);
      unsigned int styleMask = NSTitledWindowMask 
                               | NSMiniaturizableWindowMask;


      myWindow = [NSWindow alloc];
      myWindow = [myWindow initWithContentRect: rect
                           styleMask: styleMask
                           backing: NSBackingStoreBuffered
                           defer: NO];
      [myWindow setTitle: @"This is a test window"];
    }

    - (void) applicationWillFinishLaunching: (NSNotification *)not
    {
      [self createMenu];
      [self createWindow];
    }

    - (void) applicationDidFinishLaunching: (NSNotification *)not;
    {
      [myWindow makeKeyAndOrderFront: nil];
    }
    @end

    int main (int argc, const char **argv)
    { 
      [NSApplication sharedApplication];
      [NSApp setDelegate: [MyDelegate new]];

      return NSApplicationMain (argc, argv);
    }

To make the code easier to read and to understand, we have moved all the code creating the menu into a `createMenu` method, and the code creating the window into a `createWindow` method.

We have also implemented the `dealloc` method, which is quite useless in this case, because we create only one `MyDelegate` object, which is only released when the application quits (there is few interest in releasing memory when the application is quitting, since all memory is going to be released anyway). But it is good programming practice to always implement `dealloc`; this method should release all the resources that the object was using. In this case, we only need to release the window object. Please refer to the Memory Management tutorial \[yet to be written \*grin\*\] for more information on the `dealloc` method.

The `GNUmakefile` is the usual one:

    include $(GNUSTEP_MAKEFILES)/common.make

    APP_NAME = WindowApp
    WindowApp_OBJC_FILES = MyApp.m 

    include $(GNUSTEP_MAKEFILES)/application.make

  

------------------------------------------------------------------------
