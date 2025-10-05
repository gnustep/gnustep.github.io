



# 2 The Objective-C Language

In the previous chapter you were introduced to some basic object-oriented programming terms. This chapter will expand on these terms, and introduce you to some new ones, while concentrating on how they apply to the Objective-C language and the GNUstep base library. First let us look at some non OO additions that Objective-C makes to ANSI C.










## 2.1 Non OO Additions

Objective-C makes a few non OO additions to the syntax of the C programming language that include:

- A boolean data type (`BOOL`) capable of storing either of the values `YES` or `NO`.  
  A `BOOL` is a scalar value and can be used like the familiar `int` and `char` data types.  
  A `BOOL` value of `NO` is zero, while `YES` is non-zero.
- The use of a pair of slashes (`//`) to mark text up to the end of the line as a comment.
- The `#import` preprocessor directive was added; it directs the compiler to include a file only if it has not previously been included for the current compilation. This directive should only be used for Objective-C headers and not ordinary C headers, since the latter may actually rely on being included more than once in certain cases to support their functionality.

## 2.2 Objects



Object-oriented (OO) programming is based on the notion that a software system can be composed of objects that interact with each other in a manner that parallels the interaction of objects in the physical world.

This model makes it easier for the programmer to understand how software works since it makes programming more intuitive. The use of objects also makes it easier during program design: take a big problem and consider it in small pieces, the individual objects, and how they relate to each other.

Objects are like mini programs that can function on their own when requested by the program or even another object. An object can receive messages and then act on these messages to alter the state of itself (the size and position of a rectangle object in a drawing program for example).

In software an object consists of instance variables (data) that represent the state of the object, and methods (like C functions) that act on these variables in response to messages.

As a programmer creating an application or tool, all you need do is send messages to the appropriate objects rather than call functions that manipulate data as you would with a procedural program.

The syntax for sending a message to an object, as shown below, is one of the additions that Objective-C adds to ANSI C.

    [objectName message]; 

Note the use of the square \[ \] brackets surrounding the name of the object and message.

Rather than 'calling’ one of its methods, an object is said to ’perform’ one of its methods in response to a message. The format that a message can take is discussed later in this section.





### 2.2.1 Id and nil

Objective-C defines a new type to identify an object: `id`, a type that points to an object's data (its instance variables). The following code declares the variable ’`button`’ as an object (as opposed to ’`button`’ being declared an integer, character or some other data type).

    id button;

When the button object is eventually created the variable name '`button`’ will point to the object’s data, but before it is created the variable could be assigned a special value to indicate to other code that the object does not yet exist.

Objective-C defines a new keyword `nil` for this assignment, where `nil` is of type `id` with an unassigned value. In the button example, the assignment could look like this:

    id button = nil;

which assigns `nil` in the declaration of the variable.

You can then test the value of an object to determine whether the object exists, perhaps before sending the object a message. If the test fails, then the object does not exist and your code can execute an alternative statement.

    if (anObject != nil)
      ... /* send message */
    else
      ... /* do something else */

The header file `objc/objc.h` defines `id`, `nil`, and other basic types of the Objective-C language. It is automatically included in your source code when you use the compiler directive `#include <Foundation/Foundation.h>` to include the GNUstep Base class definitions.

### 2.2.2 Messages



A message in Objective-C is the mechanism by which you pass instructions to objects. You may tell the object to do something for you, tell it to change its internal state, or ask it for information.

A message usually invokes a method, causing the receiving object to respond in some way. Objects and data are manipulated by sending messages to them. Like C-functions they have return types, but function specific to the object.

Objects respond to messages that make specific requests. Message expressions are enclosed in square brackets and include the receiver or object name and the message or method name along with any arguments.

To send a message to an object, use the syntax:

`[receiver messagename];`

where `receiver` is the object.

  

The run-time system invokes object methods that are specified by messages. For example, to invoke the display method of the mySquare object the following message is used:

`[mySquare display];`

  

Messages may include arguments that are prefixed by colons, in which case the colons are part of the message name, so the following message is used to invoke the `setFrameOrigin::` method:

`[button setFrameOrigin: 10.0 : 10.0];`

  

Labels describing arguments precede colons:

`[button setWidth: 20.0 height: 122.0];`

invokes the method named `setWidth:height:`

  

Messages that take a variable number of arguments are of the form:

`[receiver makeList: list, argOne, argTwo, argThree];`

  

A message to `nil` does NOT crash the application (while in Java messages to `null` raise exceptions); the Objective-C application does nothing.

For example:

`[nil display];`

will do nothing.

If a message to `nil` is supposed to return an object, it will return `nil`. But if the method is supposed to return a primitive type such as an `int`, then the return value of that method when invoked on `nil`, is undefined. The programmer therefore needs to avoid using the return value in this instance.

### 2.2.3 Polymorphism



Polymorphism refers to the fact that two different objects may respond differently to the same message. For example when client objects receive an alike message from a server object, they may respond differently. Using Dynamic Binding, the run-time system determines which code to execute according to the object type.

## 2.3 Classes



A **class** in Objective-C is a *type of object*, much like a structure definition in C except that in addition to variables, a class has code – method implementations – associated with it. When you create an *instance* of a class, also known as an *object*, memory for each of its variables is allocated, including a pointer to the class definition itself, which tells the Objective-C runtime where to find the method code, among other things. Whenever an object is sent a message, the runtime finds this code and executes it, using the variable values that are set for this object.







### 2.3.1 Inheritance



Most of the programmer's time is spent defining classes. Inheritance helps reduce coding time by providing a convenient way of reusing code. For example, the `NSButton` class defines data (or instance variables) and methods to create button objects of a certain type, so a subclass of `NSButton` could be produced to create buttons of another type - which may perhaps have a different border colour. Equally `NSTextField` can be used to define a subclass that perhaps draws a different border, by reusing definitions and data in the superclass.

Inheritance places all classes in a logical hierarchy or tree structure that may have the `NSObject` class at its root. (The root object may be changed by the developer; in GNUstep it is `NSObject`, but in "plain" Objective-C it is a class called “`Object`” supplied with the runtime.) All classes may have subclasses, and all except the root class do have superclasses. When a class object creates a new instance, the new object holds the data for its class, superclass, and superclasses extending to the root class (typically `NSObject`). Additional data may be added to classes so as to provide specific functions and application logic.

When a new object is created, it is allocated memory space and its data in the form of its instance variables are initialised. Every object has at least one instance variable (inherited from `NSObject`) called `isa`, which is initialized to refer to the object's class. Through this reference, access is also afforded to classes in the object’s inheritance path.

In terms of source code, an Objective-C class definition has an:

- *interface* declaring instance variables, methods and the superclass name; and an
- *implementation* that defines the class in terms of operational code that implements the methods.

Typically these entities are confined to separate files with `.h` and `.m` extensions for Interface and Implementation files, respectively. However they may be merged into one file, and a single file may implement multiple classes.

### 2.3.2 Inheritance of Methods



Each new class inherits methods and instance variables from another class. This results in a class hierarchy with the root class at the core, and every class (except the root) has a superclass as its parent, and all classes may have numerous subclasses as their children. Each class therefore is a refinement of its superclass(es).

### 2.3.3 Overriding Methods



Objects may access methods defined for their class, superclass, superclass' superclass, extending to the root class. Classes may be defined with methods that overwrite their namesakes in ancestor classes. These new methods are then inherited by subclasses, but other methods in the new class can locate the overridden methods. Additionally redefined methods may include overridden methods.

### 2.3.4 Abstract Classes

 <span id="index-class_002c-abstract" class="index-entry-id"></span>

Abstract classes or abstract superclasses such as `NSObject` define methods and instance variables used by multiple subclasses. Their purpose is to reduce the development effort required to create subclasses and application structures. When we get technical, we make a distinction between a pure abstract class whose methods are defined but instance variables are not, and a semi-abstract class where instance variables are defined).

An abstract class is not expected to actually produce functional instances since crucial parts of the code are expected to be provided by subclasses. In practice, abstract classes may either stub out key methods with no-op implementations, or leave them unimplemented entirely. In the latter case, the compiler will produce a warning (but not an error).

Abstract classes reduce the development effort required to create subclasses and application structures.

### 2.3.5 Class Clusters

 <span id="index-cluster_002c-classes" class="index-entry-id"></span>

A class cluster is an abstract base class, and a group of private, concrete subclasses. It is used to hide implementation details from the programmer (who is only allowed to use the interface provided by the abstract class), so that the actual design can be modified (probably optimised) at a later date, without breaking any code that uses the cluster.

Consider a scenario where it is necessary to create a class hierarchy to define objects holding different types including **chars**, **ints**, **shorts**, **longs**, **floats** and **doubles**. Of course, different types could be defined in the same class since it is possible to **cast** or **change** them from one to the next. Their allocated storage differs, however, so it would be inefficient to bundle them in the same class and to convert them in this way.

The solution to this problem is to use a class cluster: define an abstract superclass that specifies and declares components for subclasses, but does not declare instance variables. Rather this declaration is left to its subclasses, which share the **programmatic interface** that is declared by the abstract superclass.

When you create an object using a cluster interface, you are given an object of another class - from a concrete class in the cluster.

## 2.4 NSObject: The Root Class

 <span id="index-root-class" class="index-entry-id"></span> <span id="index-class_002c-root" class="index-entry-id"></span>

In GNUstep, `NSObject` is a root class that provides a base implementation for all objects, their interactions, and their integration in the run-time system. `NSObject` defines the `isa` instance variable that connects every object with its class.

In other Objective-C environments besides GNUstep, `NSObject` will be replaced by a different class. In many cases this will be a default class provided with the Objective-C runtime. In the GNU runtime for example, the base class is called `Object`. Usually base classes define a similar set of methods to what is described here for `NSObject`, however there are variations.

The most basic functions associated with the `NSObject` class (and inherited by all subclasses) are the following:

- allocate instances
- connect instances to their classes

In addition, `NSObject` supports the following functionality:

- initialize instances
- deallocate instances
- compare self with another object
- archive self
- perform methods selected at run-time
- provide reflective information at runtime to queries about declared methods
- provide reflective information at runtime to queries about position in the inheritance hierarchy
- forward messages to other objects.

<!-- -->



### 2.4.1 The NSObject Protocol

In fact, the `NSObject` class is a bit more complicated than just described. In reality, its method declarations are split into two components: essential and ancillary. The essential methods are those that are needed by *any* root class in the GNUstep/Objective-C environment. They are declared in an "`NSObject` protocol" which should be implemented by any other root class you define (see [Protocols](Classes)). The ancillary methods are those specific to the `NSObject` class itself but need not be implemented by any other root class. It is not important to know which methods are of which type unless you actually intend to write an alternative root class, something that is rarely done.

## 2.5 Static Typing



Recall that the `id` type may be used to refer to any class of object. While this provides for great runtime flexibility (so that, for example, a generic `List` class may contain objcts of any instance), it prevents the compiler from checking whether objects implement the messages you send them. To allow type checking to take place, Objective-C therefore also allows you to use class names as variable types in code. In the following example, type checking verifies that the `myString` object is an appropriate type.

     // compiler verifies, if anObject's type is known, that it is an NSString:
    NSString *myString = anObject;
     // now, compiler verifies that NSString declares an int 'length' method:
    int len = [myString length];

Note that objects are declared as pointers, unlike when `id` is used. This is because the pointer operator is implicit for `id`. Also, when the compiler performs type checking, a subclass is always permissible where any ancestor class is expected, but not vice-versa.




### 2.5.1 Type Introspection

Static typing is not always appropriate. For example, you may wish to store objects of multiple types within a list or other container structure. In these situations, you can still perform type-checking manually if you need to send an untyped object a particular message. The `isMemberOfClass:` method defined in the `NSObject` class verifies that the receiver is of a specific class:

    if ([namedObject isMemberOfClass: specificClass] == YES)
      {
        // code here
      }

The test will return false if the object is a member of a subclass of the specific class given - an exact match is required. If you are merely interested in whether a given object *descends* from a particular class, the `isKindOfClass:` method can be used instead:

    if ([namedObject isKindOfClass: specificClass] == YES)
      {
        // code here
      }

There are other ways of determining whether an object responds to a particular method, as will be discussed in [Advanced Messaging](Advanced-Messaging).

### 2.5.2 Referring to Instance Variables



As you will see later, classes may define some or all of their instance variables to be *public* if they wish. This means that any other object or code block can access them using the standard "`->`" structure access operator from C. For this to work, the object must be statically typed (not referred to by an `id` variable).

       Bar *bar = [foo getBar];
       int c = bar->value * 2;   // 'value' is an instance variable

In general, direct instance variable access from outside of a class is not recommended programming practice, aside from in exceptional cases where performance is at a premium. Instead, you should define special methods called *accessors* that provide the ability to retrieve or set instance variables if necessary:

    - (int) value
    {
        return value;
    }

    - (void) setValue: (int) newValue
    {
        value = newValue;
    }

While it is not shown here, accessors may perform arbitrary operations before returning or setting internal variable values, and there need not even be a direct correspondence between the two. Using accessor methods consistently allows this to take place when necessary for implementation reasons without external code being aware of it. This property of *encapsulation* makes large code bases easier to maintain.

## 2.6 Working with Class Objects

Classes themselves are maintained internally as objects in their own right in Objective-C, however they do not possess the instance variables defined by the classes they represent, and they cannot be created or destroyed by user code. They do respond to class methods, as in the following:

    id result = [SomeClassName doSomething];

Classes respond to the class methods their class defines, as well as those defined by their superclasses. However, it is not allowed to override an inherited class method.

You may obtain the class object corresponding to an instance object at runtime by a method call; the class object is an instance of the "`Class`" class.

      // all of these assign the same value
    id stringClass1 = [stringObject class];
    Class stringClass2 = [stringObject class];
    id stringClass3 = [NSString class];

Classes may also define a version number (by overriding that defined in `NSObject`):

`int versionNumber = [NSString version];`

This facility allows developers to access the benefits of versioning for classes if they so choose.



### 2.6.1 Locating Classes Dynamically

Class names are about the only names with global visibility in Objective-C. If a class name is unknown at compilation but is available as a string at run time, the GNUstep library `NSClassFromString` function may be used to return the class object:

    if ([anObject isKindOf: NSClassFromString("SomeClassName")] == YES)
      {
        // do something ...
      }

The function returns `Nil` if it is passed a string holding an invalid class name. Class names, global variables and functions (but not methods) exist in the same name space, so no two of these entities may share the same name.

## 2.7 Naming Constraints and Conventions

 <span id="index-naming-conventions" class="index-entry-id"></span>

The following lists the full uniqueness constraints on names in Objective-C.

- Neither gGlobal variables nor function names may share the same name as classes, because all three entities are allocated the same (global) name space.
- A class may define methods using the same names as those held in other classes. (See [Overriding Methods](#Objective_002dC) above.)
- A class may define instance variables using the same names as those held in other classes.
- A class category may have the same name as another class category.
- An instance method and a class method may share the same name.
- A protocol may have the same name as a class, category, or any other entity.
- A method and an instance variable may share the same name.

There are also a number of conventions used in practice. These help to make code more readable and also help avoid naming conflicts. Conventions are particularly important since Objective-C does not have any namespace partitioning facilities like Java or other languages.

- Class, category and protocol names begin with an uppercase letter.
- Methods, instance variables, and variables holding instances begin with a lowercase letter.
- Second and subsequent words in a name should begin with a capital letter, as in "ThisIsALongName", not “Thisisalongname”. As can be seen, this makes long names more readable.
- Classes intended to be used as libraries (Frameworks, in NeXTstep parlance) should utilize a unique two or three letter prefix. For example, the Foundation classes all begin with 'NS’, as in "NSArray, and classes in the OmniFoundation from Omni Group (a popular library for OpenStep) began with “OF".
- Classes and methods intended to be used only be the developers maintaining them should be prefixed by an underscore, as in "\_SomePrivateClass" or “\_somePrivateMethod”. Capitalization rules should still be followed.
- Functions intended for global use should beging with a capital letter, and use prefixing conventions as for classes.

## 2.8 Strings in GNUstep

Strings in GNUstep can be handled in one of two ways. The first way is the C approach of using an array of `char`. In this case you may use the "`STR`" type defined in Objective-C in place of `char[]`.

The second approach is to rely on the `NSString` class and associated subclasses in the GNUstep Base library, and compiler support for them. Using this approach allows use of the methods in the <a href="../Reference/NSString.html" class="uref">NSString API</a>. In addition, the `NSString` class provides the means to initialize strings using printf-like formats.

The `NSString` class defines objects holding raw **Unicode** character streams or **strings**. Unicode is a 16-bit worldwide standard used to define character sets for all spoken languages. In GNUstep parlance the Unicode character is of type **unichar**.







### 2.8.1 Creating NSString Static Instances

A **static** instance is allocated at compile time. The creation of a static instance of `NSString` is achieved using the `@"..."` construct and a pointer:

    NSString *w = @"Brainstorm";

Here, `w` is a variable that refers to an `NSString` object representing the ASCII string "Brainstorm".

### 2.8.2 NSString +stringWithFormat:

The class method `stringWithFormat:` may also be used to create instances of `NSString`, and broadly echoes the `printf()` function in the C programming language. `stringWithFormat:` accepts a list of arguments whose processed result is placed in an `NSString` that becomes a return value as illustrated below:

    int       qos = 5;
    NSString    *gprsChannel;

    gprschannel = [NSString stringWithFormat: @"The GPRS channel is %d", 
                    qos];

The example will produce an `NSString` called `gprsChannel` holding the string "The GPRS channel is 5".

`stringWithFormat:` recognises the `%@` conversion specification that is used to specify an additional `NSString`:

    NSString *one;
    NSString *two;

    one = @"Brainstorm";
    two = [NSString stringWithFormat: @"Our trading name is %@", one];

The example assigns the variable `two` the string "Our trading name is Brainstorm." The `%@` specification can be used to output an object's description - as returned by the `NSObject` `-description` method), which is useful when debugging, as in:

    NSObject *obj = [anObject aMethod];

    NSLog (@"The method returned: %@", obj);

### 2.8.3 C String Conversion

When a program needs to call a C library function it is useful to convert between `NSString`s and standard ASCII C strings (not fixed at compile time). To create an `NSString` using the contents of the returned C string (from the above example), use the `NSString` class method `stringWithCString:`:

    char *function (void);

    char *result;
    NSString *string;

    result = function ();
    string = [NSString stringWithCString: result];

To convert an `NSString` to a standard C ASCII string, use the `cString` method of the `NSString` class:

    char      *result;
    NSString    *string;

    string = @"Hi!";
    result = [string cString];

### 2.8.4 NSMutableString

`NSString`s are immutable objects; meaning that once they are created, they cannot be modified. This results in optimised `NSString` code. To modify a string, use the subclass of `NSString`, called `NSMutableString`. Use a `NSMutableString` wherever a `NSString` could be used.

An `NSMutableString` responds to methods that modify the string directly - which is not possible with a generic `NSString`. To create a `NSMutableString`use `stringWithFormat:`:

    NSString *name = @"Brainstorm";
    NSMutableString *str;
    str = [NSMutableString stringWithFormat: @"Hi!, %@", name];

While `NSString`'s implementation of `stringWithFormat:` returns a `NSString`, `NSMutableString`’s implementation returns an `NSMutableString`.

**Note. Static strings created with the `@"..."` construct are always immutable.**

`NSMutableString`s are rarely used because to modify a string, you normally create a new string derived from an existing one.

A useful method of the `NSMutableString` class is `appendString:`, which takes an `NSString` argument, and appends it to the receiver:

    NSString  *name = @"Brainstorm";
    NSString    *greeting = @"Hello";
    NSMutableString *s;

    s = AUTORELEASE ([NSMutableString new]);
    [s appendString: greeting];
    [s appendString: @", "];
    [s appendString: name];

This code produces the same result as:

    NSString *name = @"Brainstorm";
    NSString *greeting = @"Hello";
    NSMutableString *s;

    s = [NSMutableString stringWithFormat: @"%@, %@", greeting, name];

### 2.8.5 Loading and Saving Strings

The the GNUstep Base library has numerous string manipulation features, and among the most notable are those relating to writing/reading strings to/from files. To write the contents of a string to a file, use the `writeToFile:atomically:` method:

    #include <Foundation/Foundation.h>

    int
    main (void)
    {
      CREATE_AUTORELEASE_POOL(pool);
      NSString *name = @"This string was created by GNUstep";

      if ([name writeToFile: @"/home/nico/testing" atomically: YES])
        {
          NSLog (@"Success");
        }
      else 
        {
          NSLog (@"Failure");
        }
      RELEASE(pool);
      return 0;
    }

`writeToFile:atomically:` returns YES for success, and NO for failure. If the atomically flag is YES, then the library first writes the string into a file with a temporary name, and, when the writing has been successfully done, renames the file to the specified filename. This prevents erasing the previous version of filename unless writing has been successful. This is a useful feature, which should be enabled.

To read the contents of a file into a string, use `stringWithContentsOfFile:`, as shown in the following example that reads `@"/home/Brainstorm/test"`:

    #include <Foundation/Foundation.h>

    int
    main (void)
    {
      CREATE_AUTORELEASE_POOL(pool);
      NSString *string;
      NSString *filename = @"/home/nico/test";

      string = [NSString stringWithContentsOfFile: filename];
      if (string == nil)
        {
          NSLog (@"Problem reading file %@", filename);
          /*
           * <missing code: do something to manage the error...>
           * <exit perhaps ?>
           */
        }

      /*
       * <missing code: do something with string...>
       */

      RELEASE(pool);
      return 0;
    }




