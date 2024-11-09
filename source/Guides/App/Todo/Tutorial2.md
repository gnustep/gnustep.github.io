Translated with Google Translate

# 2 - NSWindowController and Design Pattern MVC
GNUstep has an NSWindowController class which will facilitate the implementation of the MVC Design Pattern.
NSWindowController notably manages:

* the instantiation of the window (generally the .gorm) via the `initWithWindowNibName:` method
* closing the window `close`, `shouldCloseDocument`, `setShouldCloseDocument:`
* save window properties (position/size...) `setWindowFrameAutosaveName:`, `windowFrameAutosaveName:`


## Notes on the user interface

Automatically saving the size and position of used windows is an example of the user approach in GNUstep; we can cite the NeXT UI Guide:

> “The similarity of graphical to real objects is at a fundamental rather than a superficial level. Graphical objects don’t
need to resemble physical objects in every detail. But they do
need to behave in ways that our experience with real objects
would lead us to expect.For example, objects in the real world
stay where we put them.”

## Some classes and widgets ...

Before setting up our MVC, let's explain
first the principle of the NSView, NSCell and NSMatrix
classes of the GNUstep framework.

### NSView and NSCell

GNUstep has two classes for displaying something in a graphical interface:

* NSView is the abstract class responsible for display, event management (mouse, keyboard, etc.), and printing (postscript). All widgets available in the ApplicationKit are thus derived from this class.
* NSCell is a class allowing just to display text and images, but which is therefore lighter than NSView. In practice, GNUstep widgets mostly use NSCells for their display rather than directly overriding the drawing method of the NSView class, allowing better clipping 

### NSMatrix

NSMatrix is an object for grouping NSCell objects and managing them. It allows you to group them in rows and columns. The NSCells used are generally of the same class (as we will see in our case), but you can possibly use different classes in the same NSMatrix; the only constraint being that the NSCells must necessarily have the same size 

## Using NSWindowController

Let’s go back to our MVC, and take as an example of using NSWindowController the preferences panel of our application “Todo” started in the previous article.

We will choose here to save “on the fly” (no “apply” button) the preferences, also giving more interactivity. So we have the following components:

* NSUserDefaults (the Model) 
* Preferences.gorm (the View)
* PreferencesController (the Controller)

<figure>

![Figure 1 - Relationship between objects](Figure%201.png)
<figcaption>Figure 1 - Relationship between objects</figcaption>

</figure>

Note that for all GNUstep-based applications the Preferences panel is (should be) accessible via the Info &rarr; Preferences menu. Now let's create our Preferences panel with Gorm:

Document &rarr; New Module &rarr; New Empty

In the Palettes panel choose the window section and drag & drop a panel onto the desktop. 

<figure>

![Figure 2 - Window palette](Figure%202.jpg)
<figcaption>Figure 2 - Window palette</figcaption>

</figure>

```{note}
It is important to choose a panel rather than a window because in GNUstep (and OPENSTEP), when an application loses focus, these panels become invisible (this avoids cluttering the workspace).
```

Place two boxes there. In the first we will put a “Matrix” (an NSMatrix object) of buttons.

To do this, drag & drop a SwitchButton then select this button in the window, press the Alt key and simultaneously drag down to obtain a matrix of 3 buttons.

Call them:
* Status
* End date
* Advancement

Do the same for the status: drag & drop a RadioButton and create the matrix. Position tags using the inspector (see previous article) for each of your elements, for example:
* Percent tag: 0
* Tag Color: 1

Then add a “reset” button that will reset the preferences to their default values. You should get a panel that looks like Figure 3. 

<figure>

![Figure 3 - Our app's preferences panel](Figure%203.jpg)
<figcaption>Figure 3 - Our app's preferences panel</figcaption>

</figure>

Let's now create our subclass of NSWindowController, PreferencesController (figure 4). 

<figure>

![Figure 4 - The PreferencesController class](Figure%204.jpg)
<figcaption>Figure 4 - The PreferencesController class</figcaption>

</figure>

Add the following Outlets:

* `revertButton`
* `statusMatrix`
* `viewMatrix`

and the Shares:

* `statusAction:`
* `viewAction:`
* `revertAction:`

Then go back to “Objects”, click on NSOwner and choose PreferencesController. This indicates that the owner of the Gorm file (represented by Gorm by the NSOwner icon), i.e. the object that will load the file, will be an instance of the class
PreferencesController.

You can then directly connect the Outlets and Actions to the NSOwner. Connect the statusMatrix and viewMatrix Outlets to the two NSMatrix you put on the preferences panel, and the revertButton Outlet to the “reset” button.

Then connect the actions, from the two NSMatrix to the NSOwner (Actions `viewAction:` and `statusAction:`) and from the “defaults” button to the `revertAction:` action of the NSOwner.

Our controller is thus linked to the view (the preferences panel), the Gorm file thus containing all the relations. We still have to deal with the model.

## NSUserDefaults: Managing preferences

As we already told you, the OpenStep specifications integrate certain classes which help in the management of a user environment (what is more commonly called under Linux a desktop).
Managing user preferences is one of them.

A database of preferences is maintained in *`GNUSTEP_USER_ROOT`*`/Defaults/.GNUstepDefaults`. (which normally corresponds to the `~/GNUstep/Defaults/.GNUstepDefaults` directory).

Unlike other environments, this preference database is a simple ascii file, readable in clear text. The format used is a “property list” (“plist”) (if you are using WindowMaker, this is the same format as its configuration files).

For example, to store an array of objects, delimiters are parentheses, to store a string quotes are used, and to store a dictionary, braces are used.

Objects that can be stored as is in a property list are NSData, NSString, NSNumber, NSDate, NSArray, and NSDictionary. They can be read and written directly.

example of a defaults database:

> **Note**: if you are viewing this file in VS Code, you'll need to install the [old-style plist extension](https://marketplace.visualstudio.com/items?itemName=speedy37.ascii-plist) to view the colored version of the code below.

```plist
{
    ImageViewer = {
        "NSWindow Frame Inspector" = "382 192 260 382 0 0 960 768";
        "NSWindow Frame Preferences" = "382 233 260 303 0 0 960 768";
    };
    NSGlobalDomain = {
        GSFontAntiAlias = YES;
        NSLanguages = (
            French,
            English
        );
        "back−art−subpixel−text" = 0;
        controlBackgroundColor = {
            Alpha = 1.000000;
            B = 0.83;
            ColorSpace = NSCalibratedRGBColorSpace;
            G = 0.85;
            R = 0.86;
        };
        windowBackgroundColor = {
            Alpha = 1.000000;
            B = 0.83;
            ColorSpace = NSCalibratedRGBColorSpace;
            G = 0.85;
            R = 0.86;
        };
    };
}
```

Here two keys are present (`ImageViewer` and `NSGlobalDomain`). `NSGlobalDomain` is the key where the user's global preferences are saved (language, timezone...). You can use [SystemPreferences.app](github.com/gnustep/apps-systempreferences) to graphically configure these defaults.

`ImageViewer` is the key saving the preferences of the ImageViewer application; it contains the size and position of two windows of this application, and these preferences have been automatically managed and saved by GNUstep and the use of the class
`NSWindowController`. 

## Access to defaults using the NSUserDefaults class

Preferences are accessed via the class method

```objc
+ (NSUserDefaults*)standardUserDefaults
```

of NSUserDefaults, which returns an instance of NSUserDefaults. NSUserDefaults is in fact a singleton, i.e. there is only one and the same instance of NSUserDefaults per program (the standardUserDefaults method creates an instance if
it does not already exist, and otherwise returns the existing instance
aunt). This Design Pattern makes it possible to avoid possible problems related to maintaining consistency.

You can then access the preferences via the methods:

```objc
- (NSArray*)           arrayForKey: (NSString*) defaultName
- (BOOL)                boolForKey: (NSString*) defaultName
- (NSData*)             dataForKey: (NSString*) defaultName
- (NSDictionary*) dictionaryForKey: (NSString*) defaultName
- (float)              floatForKey: (NSString*) defaultName
- (int)              integerForKey: (NSString*) defaultName
-  (id)                 objectForKey: (NSString*) defaultName
- (NSArray*)     stringArrayForKey: (NSString∗) defaultName
- (NSString*)         stringForKey: (NSString*) defaultName
```

Modify them with the methods:

```objc
- (void)    setBool: (BOOL)value  forKey: (NSString*) defaultName
- (void)   setFloat: (float)value forKey: (NSString*) defaultName
- (void) setInteger: (int)value   forKey: (NSString*) defaultName
- (void)  setObject:  (id) value    forKey: (NSString*) defaultName
```

or delete them with:

```objc
- (void) removeObjectForKey: (NSString*) defaultName
```

Writing (on disk) is done by the method

```objc
- (BOOL) synchronize
```

The preferences accessible in this way are those of the current user and of the application that accesses them.

The preferences, in addition to being manipulated from within the programs, can also be modified with a command line tool: defaults.
```bash
defaults write NSGlobalDomain test "Hello World!"
```
will add the value test key "Hello World" in the `NSGlobalDomain` domain:
```objc
test="Hello World";
```
```bash
defaults delete NSGlobalDomain test
```
will remove the `test` key from the `NSGlobalDomain` domain .

## Implementation for Todo.app

We decided to change the preferences “on the fly” (i.e. their effect is immediate) to avoid the painful Apply/OK/Cancel actions for the user.

Here is the implementation of the viewAction and statusAction methods of the `PreferencesController` class. This code is to be added to the `PreferencesController.m` file that you will have generated with Gorm (Class &rarr; Create Class Files).
```objc
static NSString* STATUS = @"STATUS";
static NSString* PERCENT = @"PERCENT";
static NSString* COLOR = @"COLOR";
```
and in the implementation of the PreferencesController class:

```objc
− (void) awakeFromNib {
    defaults = [NSUserDefaults
        standardUserDefaults];

// Remember user preferences
// When launching Preferences
    revertDict = [NSDictionary
        dictionaryWithDictionary:	
        [defaults dictionaryRepresentation]];
    [revertDict retain];

// Display the preferences
    [self displayPrefsWithDict: 
        revertDict];
}
```

```objc
− (IBAction) statusAction:  (id)  sender {
    if ( sender != statusMatrix ) {
        return;
    }
    {
    // We get the tag on the selection
        int tag = [[sender selectedCell] tag];

    // We make changes in our preferences instance
        if ( tag == 0 ) {
            [defaults setObject:PERCENT forKey: STATUS];
        } else if ( tag == 1 ) {
            [defaults setObject:COLOR forKey: STATUS];
        }

    // We synchronize to save the defaults
        [defaults synchronize];
        [revertButton setEnabled: YES];
    }
}
```

```objc
− (IBAction) statusAction:  (id)  sender {
    if ( sender != statusMatrix )
        return;

// We display the preferences with the default values
    [self displayPrefsWithDict: revertDict];
    [revertButton setEnabled: NO]
}
```

We would however like the changes made in the preferences to reflect on our application <!-- (for example ....) -->. A first solution would be to pass the messages directly to our main window.

Let's be more stylish :-)

It is possible that preferences affect other parts of our application. For this we will use another great classic of Design Patterns: The Observer.

## The Observer Design Pattern

This Design Pattern (figure 5) makes it easier to maintain consistency when working with objects that depend on the state of another object.

The “observer” objects then register with the object they want to monitor. This one then sends, to all the observers, a message during a change of state.

<figure>

![Figure 5 - The Observer design pattern](Figure%205.png)
<figcaption>Figure 5 - The Observer design pattern</figcaption>

</figure>

This pattern also makes it possible to send messages to objects that we do not necessarily know, or even whose number evolves dynamically while the application is running.

## Notifications

<figure>

![Figure 6 - GNUstep Notifications](Figure%206.png)
<figcaption>Figure 6 - GNUstep Notifications</figcaption>

</figure>

GNUstep actually has a practical implementation of this Design Pattern, through notifications. A notification is a message that any object can send to an `NSNotificationCenter` object, which will centralize the notifications received.

If one or more other objects are interested in a given type of message, or coming from a given object, they register as receivers with the NSNotificationCenter. The latter thus serves as a switcher (see figure 6 on the previous page). This organization makes it very simple to send distributed notifications (to remote objects, from another application, on the same machine or simply across the network), by simply changing object “pointer” . All you have to do is replace the `NSNotificationCenter` object with an NSDistributedNotificationCenter object.

Commonly used `NSNotificationCenter` methods are:
```objc
- (void) postNotificationName: (NSString*) notificationName
                       object: (id) anObject
                     userInfo: (NSDictionary*) userInfo
```
to post a notification,
```objc
- (void) addObserver: (id) anObserver 
            selector: (SEL) aSelector
                name: (NSString*) notificationName
              object: (id) anObject
```
to add an observer, and
```objc
- (void) removeObserver: (id) anObserver
                   name: (NSString*) notificationName
                 object: (id) anObject
```
to delete an observer.

## Using notifications

NSUserDefaults already posts a notification when preferences have been synced. This notification is called `NSUserDefaultsDidChangeNotification`. So we just need to add an Observer in the classes that want to be aware of the change, here in `TodoController`.

In the `-init` method of `TodoController` we therefore add:

```objc
[[NSNotificationCenter defaultCenter]
    addObserver: self
       selector: @selector(prefsChanged:)
           name: NSUserDefaultsDidChangeNotification
         object: nil];
```

This addition tells the `NSNotificationCenter` that we are adding the TodoController (self) object back as an observer for the    `NSUserDefaultsDidChangeNotification` notification.

When the observer receives the notification, the prefsChanged: method will be called with the notification parameter (an `NSNotification` object).

The object of the notification will be the instance of `NSUserDefaults`. We then just need to apply the changes to our interface.

```objc
− (void) prefsChanged: (NSNotification*) aNotification {
    // Retrieve the UserDefaults preference
    NSUserDefaults ∗defaults = [aNotification object];
    // Call (private) methods that will process the different changes 
    [self _reloadWithTableColumns:
        [defaults objectForKey: VIEW]];
    [self _changeStatusDisplay:
        [defaults objectForKey: DISPLAYSTATUS]];
}
```

## Conclusion

We have thus seen in this article an important particularity of GNUstep, the use of a database accessible in plain text, containing the faults of all the applications and of the environment.

This fault management allows the programmer to easily and in a standard way save the state of his application and the preferences of the user.

The use of notifications is another example of a Design Pattern directly available in the GNUstep framework, allowing moreover inter-application communication (with notifications
distributed).

The `#gnustep` irc channel on the LibreraChat network, and the `gnu.gnustep.discuss` mailing list is a good place to get help if needed. Also, don't forget the many sites on GNUstep, Cocoa or OpenStep, like the ones referenced at the end of this article!

Do not hesitate to contact us if you have any comments or suggestions for us.

### Contact the authors
Nicolas Roard, <nicolas@roard.com>  
Fabien Vallon, <fabien@tuxfamily.org>  

### More reading
* [Apple NSUserDefaults documentation](https://developer.apple.com/documentation/foundation/nsuserdefaults?language=objc)
* [GNUstep NSUserDefaults documentation](http://gnustep.org/resources/documentation/Developer/Base/Reference/NSUserDefaults.html#class$NSUserDefaults)
* [Notification Programming Topics](https://developer.apple.com/library/archive/documentation/Cocoa/Conceptual/Notifications/Introduction/introNotifications.html#//apple_ref/doc/uid/10000043i)
* [Preferences and Settings Programming Guide](https://developer.apple.com/library/archive/documentation/Cocoa/Conceptual/UserDefaults/Introduction/Introduction.html#//apple_ref/doc/uid/10000059i)