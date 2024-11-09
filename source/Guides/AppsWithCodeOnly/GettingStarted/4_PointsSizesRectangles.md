# 4 - Points, Sizes and Rectangles

The GNUstep base library defines some useful structs for dealing with two dimensional geometry: `NSPoint`, `NSSize` and `NSRect`. It is worth to quickly review them here, before beginning.

4.1 NSPoint
---------------------------------------------------------

A `NSPoint` is defined as a struct with two members, the *x* and *y* coordinate of the point:

    typedef struct _NSPoint
    {
      float x;
      float y;
    } NSPoint;

So, to access the `x` and `y` coordinates of a `NSPoint` called `myPoint`, you just do as in `myPoint.x` and `myPoint.y`. Please note that the coordinates of a `NSPoint` are floats; so, they might be negative and/or fractionary.

To create a point with given `x` and `y` coordinates, you can use the function (macro) `NSMakePoint ()`, as in the following example:

    NSPoint testPoint;

    testPoint = NSMakePoint (10, 20);
    NSLog (@"x coordinate: %f", testPoint.x); // 10
    NSLog (@"y coordinate: %f", testPoint.y); // 20

It might be worth quoting the (predefined) constant `NSZeroPoint`, which is a point with zero x coordinate and zero y coordinate.

  

------------------------------------------------------------------------




4.2 NSSize
--------------------------------------------------------

An `NSSize` is a struct describing the size of a rectangle, regardless of its position.

    typedef struct _NSSize
    {
      float width;
      float height;
    } NSSize;

Using `NSSize` is completely analogous to using `NSPoint`, as in the following code example:

    NSSize testSize;

    testSize = NSMakeSize (0.5, 51);
    NSLog (@"x coordinate: %f", testSize.width); // 0.5
    NSLog (@"y coordinate: %f", testSize.height); // 51

`NSZeroSize` is a constant equal to a size with zero width and zero height.

  

------------------------------------------------------------------------




4.3 NSRect
--------------------------------------------------------

An `NSRect` is a struct describing both the position and the size of a rectangle:

    typedef struct _NSRect
    {
      NSPoint origin;
      NSSize size;
    } NSRect;

Using `NSRect` is similar to using `NSPoint` and `NSSize`:

    NSRect testRect;

    testRect = NSMakeRect (8.1, -3, 10, 15);
    NSLog (@"x origin: %f", testRect.origin.x); // 8.1
    NSLog (@"y origin: %f", testRect.origin.y); // -3
    NSLog (@"width: %f", testRect.size.width); // 10
    NSLog (@"height: %f", testRect.size.height); // 15

Note that you first access the `origin`, and then its coordinates, and similarly for the `size`.

The constant `NSZeroRect` represents a rect with `NSZeroPoint` `origin` and `NSZeroSize` `size`.

  

------------------------------------------------------------------------




4.4 Geometry Functions
--------------------------------------------------------------------

The GNUstep base library provides functions and macros to do all the most common geometry operations on `NSPoint`s, `NSSize`s and `NSRect`s (such as determining if a point is contained in a rectangle, computing the intersection of two rectangles, etc). It would be off topic to discuss them here; please refer to the base library documentation whenever you need to do some of these common geometrical operations.

  

------------------------------------------------------------------------

