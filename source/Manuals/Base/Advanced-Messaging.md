



# 5 Advanced Messaging

 <span id="index-messaging_002c-advanced-techniques" class="index-entry-id"></span>

Objective-C provides some additional possibilities for message routing besides the capabilities described so far (inheritance and categories). One of the most important is that it is possible for an object, upon receiving a message it has not been set up to respond to, to *forward* that message to another object. A second important capability, which forwarding relies on, is the ability to represent method implementations directly in code. This supports various reflective operations as well as optimization where messages are sent many times.






## 5.1 How Messaging Works

Sending an Objective-C message requires three types of information:

- The message **receiver** - the object which is to perform the request.
- The message **selector** - this identifies the message, and is used to locate the excecutable code of the corresponding **method** by searching the structure of the class, and if necessary its superclasses, for an implementation.
- The message **arguments** - once the implementation has been found, these are simply passed to the method on the stack as in an ordinary function call.

In the message '`[taskArray insertObject: anObj atIndex: i]`’, the receiver is "`taskArray`", the selector is “`insertObject:atIndex:`”, and the arguments are “`anObj`” and “`i`”. Notice that the selector includes the argument titles and both colons, but not the argument names. In other words, this method might have been declared as ’`- (void) insertObject: (id)anObject atIndex: (unsigned)index;`’, but the “`anObject`” and “`index`” are just used for tracking the arguments within the method implementation code and not for looking up the method itself.

The following sequence of events would occur on sending this message at runtime:

1.  The internal `isa` pointer of the **receiver** (`taskArray`) is used to look up its class.
2.  The class representation is searched for a method implementation matching the **selector** (`insertObject:atIndex:`). If it is not found, the class's superclass is searched, and recursively its superclass, until an implementation is found.
3.  The implementation is called, as if it were a C function, using the **arguments** given (`anObj` and `i`), and the result is returned to the code sending the message.

In fact, when the method implementation is actually called, it additionally receives two *implicit* arguments: the **receiver** and the **selector**. These additional hidden arguments may be referred to in the source code by the names `self` and `_cmd`.

The process of looking up the method implementation in the receiver at runtime is known as dynamic binding. This is part of what makes the language powerful and flexible, but it is inevitably (despite clever caching strategies used in the runtime library) a little slower than a simple function call in C. There are, however, ways of short-circuiting the process in cases where performance is at a premium. Before discussing this, we must first cover the concepts of selectors and implementations in greater detail.

## 5.2 Selectors

So far we have been using the following syntax to send messages to objects:

    [myArray removeObjectIdenticalTo: anObject];

The example sends the message named `removeObjectIdenticalTo:` to `myArray` with the argument `anObject`.

An alternative method of writing this is the following:

    SEL removalSelector = @selector(removeObjectIdenticalTo:);
    [myArray performSelector: removalSelector withObject: anObject];

Here, the first line obtains the desired method selector in the form of a compiled representation (not the full ASCII name), and the second line sends the message as before, but now in an explicit form. Since the message that is sent is now effectively a variable set at runtime, this makes it possible to support more flexible runtime functioning.





### 5.2.1 The Target-Action Paradigm

One conventional way of using selectors is called the *target-action* paradigm, and provides a means for, among other things, binding elements of a graphical user interface together at runtime.

The idea is that a given object may serve as a flexible signal sender if it is given a receiver (the *target*) and a selector (the *action*) at runtime. When the object is told to send the signal, it sends the selector to the receiver. In some variations, the object passes itself as an argument.

The code to implement this paradigm is simple -

    - (id) performAction
    {
      if (target == nil || action == 0)
        {
          return nil;   // Target or action not set ... do nothing
        }
      if ([target respondsToSelector: action] == NO)
        {
          return nil;   // Target cannot deal with action ... do nothing
        }
      return [target performSelector: action withObject: self];
    }

As an example, consider a graphical button widget that you wish to execute some method in your application when pressed.

      [button setTarget: bigMachine]
      [button setAction: @selector(startUp:)];

Here, `button` stores the given target and action in instance variables, then when it is pressed, it internally calls a method like `performAction` shown above, and sends the message "`[bigMachine startUp: button]`".

If you are used to programming with events and listeners in Java, the target-action paradigm provides a lighter-weight alternative for the most common case where only one object needs to be informed when an event occurs. Rather than writing or extending a special-purpose adaptor class, you just register the method you want called directly with the actuating element. If you need to send the event to multiple objects, however, you would need to write a special method to multiplex the event out. This would be approximately comparable effort to what is always required in Java, and is only needed in the minority of cases.

### 5.2.2 Obtaining Selectors

In addition to using the compile-time `@selector` operator, there are a couple of other ways of obtaining selectors.

- In a method implementation, you can always obtain the current selector from the variable `_cmd`:
      - (void) removeObjectIdenticalTo: (id)anObject
      {
        SEL  mySelector = _cmd;
          // ...
      }

- At any point, you can use the `NSSelectorFromString()` function -

        SEL  mySelector = NSSelectorFromString(@"removeObjectIdenticalTo:");

  In reality, you would never use `NSSelectorFromString` for a constant string as shown; `@selector` would do and is more efficient, since is a compile-time operator. Its chief utility lies in the case where the selector name is in a variable value (for whatever reason).

If you ever need to test the contents of a `SEL` variable for equality with another, you should use the function `sel_eq()` provided as part of the GNU Objective-C runtime library. This is necessary because, while the compiler tries to ensure that compile-time generated references to selectors for a particular message point to the same structure, selectors produced at runtime, or in different compilation units, will be different and a simple pointer equality test will not do.

### 5.2.3 Avoiding Messaging Errors when an Implementation is Not Found

Using **typed** objects as shown below, the compiler would forewarn you if the `anObject` was unable to respond to the `alert:` message, as it knows what type of object `anObject` is:

    SomeClass *anObject;      // an instance of the 'SomeClass' class

    anObject = [[SomeClass alloc] init];    // build and initialize the object
    [anObject alert: additionalObject]; // compiler warns if 'alert:' not
                                            // defined in SomeClass or a superclass

However at times the compiler will not forewarn you that a message will attempt to invoke a method that is not in the **receiver's** repertoire. For instance, consider the code below where `anObject` is not known to implement the `alert:` message:

      id      anObject;       // arbitrary object;

      anObject = [[SomeClass alloc] init];  // build and initialize object
      [anObject alert: additionalObject];   // compiler cannot check whether
                                            // 'alert' is defined

In this case, the compiler will not issue a warning, because it only knows that `anObject` is of type `id` … so it doesn't know what methods the object implements.

At runtime, if the Objective-C runtime library fails to find a **method implementation** for the `alert:` message in the `SomeClass` class or one of its superclasses, an exception is generated. This can be avoided in one of two ways.

The first way is to check in advance whether the method is implemented:

    if ([anObject respondsToSelector: @selector(alert:)] == YES)
      {
        [anObject alert: additionalObject]; // send it a message.
      }
    else
      {
        // Do something else if the object can't be alerted
      }

The second way is for the object the message was sent to to *forward* it somewhere else.

## 5.3 Forwarding



What actually happens when the GNU Objective-C runtime is unable to find a method implementation associated with an object for a given selector is that the runtime instead sends a special `forwardInvocation:` message to the object. (Other Objective-C runtimes do the same, but with a slightly different message name and structure.) The object is then able to use the information provided to handle the message in some way, a common mechanism being to forward the message to another object known as a **delegate**, so that the other object can deal with it.

    - (void) forwardInvocation: (NSInvocation*)invocation
    {
      if ([forwardee respondsToSelector: [invocation selector]])
        return [invocation invokeWithTarget: forwardee];
      else
        return [self doesNotRecognizeSelector: [invocation selector]];
    }

- **`invocation`** is an instance of the special `NSInvocation` class containing all the information about the original message sent, including its **selector** and its arguments.
- **`forwardee`** is an instance variable containing the `id` of an object which has been determined to be likely to implement methods that this object does not.
- The **`NSInvocation`** class has a convenience method that will pass the message on to a target object given as argument.
- The **`doesNotRecognizeSelector`** method is a fallback which is implemented in `NSObject`. Unless it has been overidden, its behavior is to raise a runtime exception (a `NSInvalidArgumentException` to be exact), which generates an error message and aborts.

Forwarding is a powerful method for creating software patterns. One of these is that forwarding can be used to in effect provide a form of multiple inheritance. Note, however that, unlike inheritance, a forwarded method will not show up in tests like `respondsToSelector` and `isKindOfClass:`. This is because these methods search the inheritance path, but ignore the forwarding path. (It is possible to override them though.)

Another pattern you may come across is *surrogate object*: surrogates forward messages to other objects that can be assumed to be more complex. The `forwardInvocation:` method of the surrogate object receives a message that is to be forwarded; it determines whether or not the receiver exists, and if it does not, then it will attempt to create it. A **proxy** object is a common example of a surrogate object. A proxy object is useful in a remote invocation context, as well as certain scenarios where you want one object to fulfill functions of another.

## 5.4 Implementations

Recall that when a message is sent, the runtime system searches for a method implementation associated with the recipient object for the specified selector. (Behind the scenes this is carried out by a function "`objc_msgSend()`".) This may necessitate searches across multiple superclass objects traversing upwards in the inheritance hierarchy, and takes time. Once the runtime finds an implementation for a class, it will cache the information, saving time on future calls. However, even just checking and accessing the cache has a cost associated with it. In performance-critical situations, you can avoid this by holding on to an implementation yourself. In essence, implementations are function pointers, and the compiler provides a datatype for storing them when found at runtime:

    SEL  getObjSelector = @selector(getObjectAtIndex:);
      // get the 'getObjectAtIndex' implementation for NSArray 'taskArray'
    IMP  getObjImp = [taskArray methodForSelector: getObjSelector];
      // call the implementation as a function
    id obj = (getObjImp)( taskArray, getObjSelector, i );

Here, we ask the runtime system to find the '`taskArray`’ object’s implementation of ’`getObjectAtIndex`’. The runtime system will use the same algorithm as if you were performing a method call to look up this code, and then returns a function pointer to it. In the next line, this pointer is used to call the function in the usual C fashion. Notice that the signature includes both the object and the selector – recall that these are the two implicit arguments, `self` and `_cmd`, that every method implementation receives. The actual type definition for `IMP` allows for a variable number of additional arguments, which are the explicit arguments to the method call:

    typedef id (*IMP)(id, SEL, ...);

The return type of `IMP` is `id`. However, not all methods return `id`; for these others you can still get the implementation, but you cannot use an `IMP` variable and instead must cast it yourself. For example, here is such a cast for a method taking a double and returning '`double`’:

    double (*squareFunc)( id, SEL, double );
    double result;

    squareFunc = (double (*)( id, SEL, double ))
         [mathObj methodForSelector: @selector(squareOf:)];

    result = squareFunc(mathObj, @selector(squareOf:), 4);

You need to declare such a function pointer type for any method that returns something besides `id` or `int`. It is not necessary to declare the argument list (`double`) as we did above; the first line could have been "`double (*squareFunc)( id, SEL, `**`...`**` )`" instead.

An excellent exposition of the amount of time saved in using `methodForSelector` and other details of the innards of Objective-C and the Foundation may be found here: [http://www.mulle-kybernetik.com/artikel/Optimization/opti-3.html](http://www.mulle-kybernetik.com/artikel/Optimization/opti-3.html).

You should realize that it is only worth it to acquire the `IMP` if you are going to call it a large number of times, and if the code in the method implementation itself is not large compared with the message send overhead. In addition, you need to be careful not to call it when it might be the wrong function. Even when you are sure of the class of the object you are calling it on, Objective-C is sufficiently dynamic that the correct function could change as a program runs. For example, a new category for a class could be loaded, so that the implementation of a method changes. Similarly, a class could be loaded that poses as another, or one that was posing stops doing so. In general, `IMPs` should be acquired just before they are to be used, then dropped afterwards.




