# `Info.plist`

Many projects need an `Info.plist`. GNUstep Make will automatically convert your `xxxInfo.plist` (where `xxx` is the target name) in the same directory as the `GNUmakefile` into the proper `Info.plist`.

You can have a single `xxxInfo.plist` for both GNUstep and Apple.
Often enough, you can just put in it all fields required by both
GNUstep and Apple; if there is a conflict, you can provide
a `xxxInfo.`**`cplist`** (please note the suffix!) - that file is
automatically run through the C preprocessor to generate a
`xxxInfo.plist` file from it.  The preprocessor will define `GNUSTEP`
when using gnustep-base, `APPLE` when using Apple's Foundation, `NEXT`
when using NeXTstep or OpenStep FoundationKit, and `UNKNOWN` when using
something else, so you can use

```c
#ifdef GNUSTEP
  // ... some plist code for GNUstep ...
#else
  // ... some plist code for Apple ...
#endif
```
to have your `.cplist` use different code for each.