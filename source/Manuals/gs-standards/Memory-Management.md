



## 4.1 Memory Management

We encourage the use of the following macros to ease retain and release and as a convenience for managing code which should work in both a conventional retain counting environment and one with automatic reference counting (ARC)

- ASSIGN(object,value) to assign an object variable, performing the appropriate retain/release as necessary.
- ASSIGNCOPY(object,value) to copy the value and assign it to the object.
- DESTROY(object) to release an object variable and set it to nil.
- ENTER\_POOL and LEAVE\_POOL to bracket statements which should be performed inside their own auutorlease context.
