



## 4.4 Naming Conventions

The convention for naming items in GNUstep differs from the GNU standard as it needs to be compatible with OpenStep/MacOS-X.

Public classes, variables, functions and constants begin with the NS prefix if they are part of the OpenStep or MacOS-X APIs, and begin with GS if they are GNUstep extensions. GNUstep extensions must not use the NS prefix.

Class, public function, and global variable names have the first letter of each word in the name capitalised (underscores are not used).

    @class    NSRunLoop;
    GSSetUserName();
    NSGenericException;

Method and instance variable names are similarly capitalised, except that the first letter of the first word is usually not capitalised (there are a few exceptions to this where the first word is an acronym and all the letters in it are capitals). Underscores are not used in these names except to indicate that the method/variable is private, in which case the name begins with an underscore.

    {
      int   publicInstanceVariable;
      int   _privateInstanceVariable;
    }
    - (void) publicMethod;
    - (void) _privateMethod;

The names of accessor methods (methods used to set or get the value of an instance variable) must mirror the names of the instance variables. The name of a setter method is of the form 'setVar’ where ’Var’ is the instance variable name with any leading underscore removed and with the first letter converted to uppercase. The name of the getter method is the same as the instance variable name (with any leading underscore removed).

    {
      int   _amplitude;
      int   frequency;
    }
    - (int) amplitude;
    - (int) frequency;
    - (void) setAmplitude: (int)anAmplitude;
    - (void) setFrequencey: (int)aFrequency;




