



# 4 ObjectiveC

Since GNUstep is primarily written in ObjectiveC the C language coding standards largely apply with modifications as specified in the previous section.

Most code is expect to be written in traditional ObjectiveC, but classes implementing newer APIs designed by Apple will sometimes need to be written using ObjectiveC-2.0, though compatibility with old compilers should be maintained wherever possible, and preprocessor macros must be used to at least conditionally build new code without breaking old code.

In particular, blocks are completely non-portable and must never be used internally (though methods with block arguments are provided for compatibilty with the Apple APIs). As well as being similar to the 'goto’ operation in making code hard to maintain, bllocks have a number of issues which mean they are never likely to become standard across compilers (eg https://thephd.dev/lambdas-nested-functions-block-expressions-oh-my).

Another ObjectiveC-2.0 feature (the dot ('.’) operator) is also forbidden. One problem is that, while apparently simple, the actual operation of this feature in unusual cases is actually undefined and varies between compiler versions. The more serious problem is that the feature is simply very bad style because it looks like a simple structure field access and yet the code is really doing something very different and much more expensive, so use of the feature tends to lead to performance problems, bugs, and less explicit/readable code.






