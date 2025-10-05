



## 4.5 Object Persistence

The standard method of saving and restoring object information in GNUstep is through the use of the -encodeWithCoder: and -initWithCoder: methods. Any object which requires persistence implements these methods. They are used, for instance by Gorm, to save GUI interface elements. It is important that all changes to these methods be backward compatible with previously stored archives (for instance, those created by Gorm). The easiest way to do this is to use class version numbers to indicate which archive configuration should be read. Modern implementations are expected to suppoort keyed archiving and should use the same keys that are used in OSX.
