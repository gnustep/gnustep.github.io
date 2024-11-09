# 6 - Adding a Button in the Window

We are now going to add a button inside our window. But first, we need to prepare the ground with a very short introduction to the wonderful `NSView` class.

6.1 A Quick Introduction to Views
-------------------------------------------------------------------------------

`NSView` is a class whose objects represent a rectangular area in a window. Each `NSView` has its own internal coordinate system; `NSView` provides a complete framework for managing the rectangle represented by the view, its coordinate system, and for drawing and getting user events inside the view's rectangle.

Any object which can be displayed (and/or can accept user events) inside a window (such as a button, a text field, a scroll view, a slider, etc) is actually an instance of a subclass of `NSView`. The specific subclass (for example, `NSButton`) implements a specific way of drawing and managing user events inside the view's rectangle. An unsubclassed `NSView` by default draws nothing and reacts to no user events, even if it contains all the machinery (ready for subclasses to use) to do both.

When you create a window, the library automatically creates an `NSView` covering the whole content area of the window. This view is called the window's *content view*. You can replace the default content view (which is transparent and reacts to no user events) with an `NSView` of your choice (which can be an instance of a subclass of `NSView`, such as a `NSButton` or a `NSScrollView`), but you should not change the rectangle it represents, which must always cover the whole window.

If you want to have a view which covers only a part of a window, you need to add it as a *subview* of the content view. We will explore subviews in a forth-coming tutorial; in our first example, we will go for the simplest possible solution avoiding use of subviews: we will create a single `NSButton`, make a window to fit exactly our button, and then replace the default window's content view with our button. In this way we'll get a window containing exactly our button. In the next tutorial, we'll learn how to enclose views one over the other in a subview tree, so that we can put more things in a single window.

------------------------------------------------------------------------






6.2 Creating a Button
-------------------------------------------------------------------

Creating a button is quite easy:

    NSButton *myButton;

    myButton = AUTORELEASE ([NSButton new]);
    [myButton setTitle: @"Print Hello!"];
    [myButton sizeToFit];

`setTitle:` sets the title (string) to be displayed on the button; `sizeToFit` resizes the button so that it fits the title it is displaying.

Next, we need to set target and action of the button:

    [myButton setTarget: self];
    [myButton setAction: @selector (printHello:)];

(`self` will refer to our custom object)

  

------------------------------------------------------------------------




6.3 Putting the button in the window
----------------------------------------------------------------------------------

We now need to modify our code to create the window so that it creates a window exactly of the right size to contain our button. First, we need to get the size of the button:

    NSSize buttonSize;

    buttonSize = [myButton frame].size;

`frame` is a method inherited by `NSView` which returns the rectangle enclosing the button (or, more generally, the rectangle enclosing/represented by an `NSView`). The `origin` of this rectangle is meaningless at this point; it is the `size` (which was set by `sizeToFit`) what we want.

Then, we choose for the window a content rect with the `size` of the button, and with an `origin` of our choice; (100, 100) in this example:

    NSRect rect;

    rect = NSMakeRect (100, 100, 
                       buttonSize.width, 
                       buttonSize.height);

At this point, we just create the window as before, but using this rectangle (full code below). The last thing to do is then replacing the default window content view with our button:

    [myWindow setContentView: myButton];

  

------------------------------------------------------------------------




6.4 Source Code
-------------------------------------------------------------

We are ready to show the whole source code (the `GNUmakefile` is the usual one):

    #include <Foundation/Foundation.h>
    #include <AppKit/AppKit.h>

    @interface MyDelegate : NSObject
    {
      NSWindow *myWindow;
    }
    - (void) printHello: (id)sender;
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

    - (void) printHello: (id)sender
    {
      printf ("Hello!\n");
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
      NSRect rect;
      unsigned int styleMask = NSTitledWindowMask 
                               | NSMiniaturizableWindowMask;
      NSButton *myButton;
      NSSize buttonSize;

      myButton = AUTORELEASE ([NSButton new]);
      [myButton setTitle: @"Print Hello!"];
      [myButton sizeToFit];
      [myButton setTarget: self];
      [myButton setAction: @selector (printHello:)];

      buttonSize = [myButton frame].size;
      rect = NSMakeRect (100, 100, 
                         buttonSize.width, 
                         buttonSize.height);

      myWindow = [NSWindow alloc];
      myWindow = [myWindow initWithContentRect: rect
                           styleMask: styleMask
                           backing: NSBackingStoreBuffered
                           defer: NO];
      [myWindow setTitle: @"This is a test window"];
      [myWindow setContentView: myButton];
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

  

------------------------------------------------------------------------
