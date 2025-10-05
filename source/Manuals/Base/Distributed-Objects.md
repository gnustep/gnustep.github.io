



# 7 Distributed Objects



Until now we have been concentrating on using the Objective-C language to create programs that execute in a single process. But what if you want your program to interact with objects in other processes, perhaps running on different machines?

As a simple example, we may have a client process that needs to access a telephone directory stored on a remote server. The client process could send a message to the server that contained a person's name, and the server could respond by returning that person’s number.

The GNUstep base library provides a powerful set of classes that make this type of remote messaging not only possible, but easy to program. So what do these classes do and how can we use them? To answer that we must first look at the way code interacts with objects in a single process, and then look at how we can achieve the same interaction with objects that exist in different processes.







## 7.1 Object Interaction



To continue with the example above, if the telephone directory existed in the same process as the code that was accessing it, then a simple message would return the wanted telephone number.

      NSString *wantedNumber = [telephoneDirectory teleNumber: personName];

Now object and method names just hold pointers to memory addresses. The code executed at run time in response to the `teleNumber` message is located at an address held by the name of the responding method (a variable), while data in the telephone directory is located at an address held by the `telephoneDirectory` variable.

In a single process these addresses can be accessed by the client code at run time, but if the telephone directory is located on a remote server, then the address of the remote object is not known in the client process (the `telephoneDirectory` object and its responding method are said to exist in a separate 'address space’).

The Objective-C run-time library was not designed for this inter-process communication or 'remote messaging’.

## 7.2 The GNUstep Solution

 <span id="index-remote-objects" class="index-entry-id"></span> <span id="index-client_002fserver-processes" class="index-entry-id"></span> <span id="index-NSConnection-class" class="index-entry-id"></span> <span id="index-NSProxy-class" class="index-entry-id"></span> <span id="index-NSRunLoop-class" class="index-entry-id"></span>

GNUstep overcomes these limitations by providing you with classes that form what is known as a 'distributed objects’ architecture that extends the capabilities of the run-time system.

With the addition of a few lines of code in the client and server programs, these extensions allow you to send a message to a remote process by constructing a simple Objective-C statement. In the telephone directory example, the statement to retrieve the telephone number would now look something like this:

      NSString *wantedNumber = [proxyForDirectory teleNumber: personName];

Compare this to the original statement:

      NSString *wantedNumber = [telephoneDirectory teleNumber: personName];

Notice that the only difference between the two statements is the name of the object receiving the message, i.e. `proxyForDirectory` rather than `telephoneDirectory`. GNUstep makes it as simple as this to communicate with an object in another process.

The variable `proxyForDirectory` is known as a 'proxy’ for the remote `telephoneDirectory` object. A proxy is simply a substitute for the remote object, with an address in the ’address space’ of the local client process, that receives messages and forwards them on to the remote server process in a suitably coded form.

Let us now take a look at the additional lines of code required to make this 'remote messaging’ possible.








### 7.2.1 Code at the Server



In order to respond to client messages, the responding server object must be set as the 'root object’ of an instance of the `NSConnection` class, and this `NSConnection` must be registered with the network by name. Making an object available to client processes in this way is known as ’vending’ the object. The registered name for the `NSConnection` is used by the client when obtaining a proxy for the responding server object over the network.

The only other code you need to consider is the code that listens for incoming messages. This 'runloop’, as it is known, is started by sending a `run` message to an instance of the `NSRunLoop` class. Since an `NSRunLoop` object is created automatically for each process, there is no need to create one yourself. Simply get the default runloop, which is returned by the `+currentRunLoop` class method.

When the runloop detects an incoming message, the message is passed to the root object of the `NSConnection`, which performs a method in response to the message and returns a variable of the appropriate type. The `NSConnection` manages all inter-process communication, decoding incoming messages and encoding any returned values.

The code to vend the `telephoneDirectory` object and start the runloop would look something like this:

    /*
     * The main() function: Set up the program
     * as a 'Distributed Objects Server'.
     */
    int main(void)
    {
      /*
       * Remember, create an instance of the
       * NSAutoreleasePool class.
       */
      CREATE_AUTORELEASE_POOL(pool);

      /*
       * Get the default NSConnection object
       * (a new one is automatically created if none exists).
       */
      NSConnection *connXion = [NSConnection defaultConnection];

      /*
       * Set the responding server object as
       * the root object for this connection.
       */
      [connXion setRootObject: telephoneDirectory];

      /*
       * Try to register a name for the NSConnection,
       * and report an error if this is not possible.
       */
      if ([connXion registerName: @"DirectoryServer"] == NO)
      {
        NSLog(@"Unable to register as 'DirectoryServer'");
        NSLog(@"Perhaps another copy of this program is running?");
        exit(1);
      }

      /* Start the current runloop. */
      [[NSRunLoop currentRunLoop] run];

      /* Release the pool */
      RELEASE(pool);
      return 0;
    }

These additional lines of code turn a program into a distributed objects server, ready to respond to incoming client messages.

### 7.2.2 Code at the Client



At the client, all you need do is obtain a proxy for the responding server object, using the name that was registered for the `NSConnection` at the server.

      /* Create an instance of the NSAutoreleasePool class */
      CREATE_AUTORELEASE_POOL(pool);

      /* Get the proxy */
      id proxy = [NSConnection
        rootProxyForConnectionWithRegisteredName: registeredServerName];
        
      /* The rest of your program code goes here */

      /* Release the pool */
      RELEASE(pool);

The code that obtains the proxy automatically creates an NSConnection object for managing the inter-process communication, so there is no need to create one yourself.

The above example serves to establish a secure connection between processes which are run by the same person and are both on the same host.

If you want your connections to work between different host or between programs being run by different people, you do this slightly differently, telling the system that you want to use 'socket’ ports, which make TCP/IP connections over the network.

    int main(void)
    {
      CREATE_AUTORELEASE_POOL(pool);

      /*
       * Create a new socket port for your connection.
       */
      NSSocketPort *port = [NSSocketPort port];

      /*
       * Create a connection using the socket port.
       */
      NSConnection *connXion = [NSConnection connectionWithReceivePort: port
                                  sendPort: port];

      /*
       * Set the responding server object as
       * the root object for this connection.
       */
      [connXion setRootObject: telephoneDirectory];

      /*
       * Try to register a name for the NSConnection,
       * and report an error if this is not possible.
       */
      if ([connXion registerName: @"DirectoryServer"
        withNameServer: [NSSocketPortNameServer sharedInstance]] == NO)
      {
        NSLog(@"Unable to register as 'DirectoryServer'");
        NSLog(@"Perhaps another copy of this program is running?");
        exit(1);
      }

      [[NSRunLoop currentRunLoop] run];

      RELEASE(pool);
      return 0;
    }

In the above example, we specify that the socket port name server is used to register the name for the connection ... this makes the connection name visible to processes running on other machines.

The client side code is as follows

      /* Create an instance of the NSAutoreleasePool class */
      CREATE_AUTORELEASE_POOL(pool);

      /* Get the proxy */
      id proxy = [NSConnection
        rootProxyForConnectionWithRegisteredName: registeredServerName
        host: hostName
        usingNameServer: [NSSocketPortNameServer sharedInstance]];
        
      /* The rest of your program code goes here */

      /* Release the pool */
      RELEASE(pool);

If the *hostName* in this statement is 'nil’ or an empty string, then only the local host will be searched to find the *registeredServerName*. If *hostName* is "\*", then all hosts on the local network will be searched.

In the telephone directory example, the code to obtain the proxy from any host on the network would be:

      id proxyForDirectory = [NSConnection
        rootProxyForConnectionWithRegisteredName: @"DirectoryServer"
        host: @"*"
        usingNameServer: [NSSocketPortNameServer sharedInstance]];

With this additional line of code in the client program, you can now construct a simple Objective-C statement to communicate with the remote object.

      NSString *wantedNumber = [proxyForDirectory teleNumber: personName];

### 7.2.3 Using a Protocol

 <span id="index-distributed-objects_002c-using-a-protocol" class="index-entry-id"></span>

A client process does not need to know the class of a remote server object to avoid run-time errors, it only needs to know the messages to which the remote object responds. This can be determined by the client at run-time, by asking the server if it responds to a particular message before the message is sent.

If the methods implemented at the server are stated in a formal protocol, then the client can ask the server if it conforms to the protocol, reducing the network traffic required for the individual message/response requests.

A further advantage is gained at compile time, when the compiler will issue a warning if the server fails to implement any method declared in the protocol, or if the client contains any message to which the server cannot respond.

The protocol is saved to a header file and then included in both client and server programs with the usual compiler `#include` directive. Only the server program needs to implement the methods declared in the protocol. To enable compiler checking in the client program, extend the type declaration for the proxy to this protocol, and cast the returned proxy object to the same extended type.

In the telephone directory example, if the declared protocol was `TelephoneDirectory`, declared in header file `protocolHeader.h`, then the client code would now look like this:

      #include "protocolHeader.h";

      /* Extend the type declaration */
      id<TelephoneDirectory> proxyForDirectory;

      /* Cast the returned proxy object to the extended type */
      proxyForDirectory = (id<TelephoneDirectory>) [NSConnection
        rootProxyForConnectionWithRegisteredName: @"DirectoryServer"
        usingNameServer: [NSSocketPortNameServer sharedInstance]];

Since class names and protocol names do not share the same 'address space’ in a process, the declared protocol and the class of the responding server object can share the same name, making code easier to understand.

For example, `proxyForDirectory` at the client could be a proxy for an instance of the `TelephoneDirectory` class at the server, and this class could implement the `TelephoneDirectory` protocol.

### 7.2.4 Complete Code for Telephone Directory Application

Here we provide the rest of the code needed for client and server to actually run the above example.

**Code At Server**

    #include <Foundation/Foundation.h>

    /* Include the TelephoneDirectory protocol header file */
    #include "TelephoneDirectory.h"

    /*
     * Declare the TelephoneDirectory class that
     * implements the 'teleNumber' instance method.
     */
    @interface TelephoneDirectory : NSObject <TelephoneDirectory>
    @end

    /*
     * Define the TelephoneDirectory class
     * and the instance method (teleNumber).
     */
    @implementation TelephoneDirectory : NSObject
    - (char *) teleNumber: (char *) personName
    {
      if (strcmp(personName, "Jack") == 0) return " 0123 456";
      else if (strcmp(personName, "Jill") == 0) return " 0456 789";
      else return " Number not found";
    }
    @end

     /* main() function: Set up the program  as a 'Distibuted Objects Server'. */
     /*   [use code from server example above ...] */

**Code at Client**

    #include <Foundation/Foundation.h>

    /* Include the TelephoneDirectory protocol header file */
    #include "TelephoneDirectory.h"

    /*
     * The main() function: Get the telephone number for
     * 'personName' from the server registered as 'DirectoryServer'.
     */
    int main(int argc, char *argv[])
    {
      char *personName = argv[1];
      char *returnedNumber;
      id<TelephoneDirectory> proxyForDirectory;
      CREATE_AUTORELEASE_POOL(pool);

      /* Acquire the remote reference. */
      proxyForDirectory = (id<TelephoneDirectory>) [NSConnection
        rootProxyForConnectionWithRegisteredName: @"DirectoryServer"
        host: @"*"
        usingNameServer: [NSSocketPortNameServer sharedInstance]];

      if (proxyForDirectory == nil)
        printf("\n** WARNING: NO CONNECTION TO SERVER **\n");
      else printf("\n** Connected to server **\n");

      if (argc == 2) // Command line name entered
      {
        returnedNumber = (char *)[proxyForDirectory teleNumber: personName];
        printf("\n%s%s%s%s%s\n", "** (In client) The telephone number for ",
               personName, " is:",
               returnedNumber, "  **");
      }
      else printf("\n** No name entered **\n");
      printf("\n%s\n\n", "** End of client program **");
      RELEASE(pool);
      return 0;
    }

To get this running, all you need do is create two directories, one for the client and one for the server. Each directory will hold a makefile, the client or server source code, and a copy of the protocol header file. When the files compile, first run the server and then the client. You can try this on the same machine, or on two different machines (with GNUstep installed) on the same LAN. What happens when you run the client without the server? How would you display a "No Server Connection" warning at the client?

### 7.2.5 GNUstep Distributed Objects Name Server

 <span id="index-Distributed-Objects-Name-Server_002c-GNUstep" class="index-entry-id"></span>

You might wonder how the client finds the server, or, rather, how it finds the directory the server lists itself in.

For the default connection type (a connection only usable on the local host between processes run by the same person), a private file (or the registry on ms-windows) is used to hold the name registration information.

For connections using socket ports to communicate between hosts, an auxiliary process will automatically be started on each machine, if it isn't running already, that handles this, allowing the server to register and the client to send a query behind the scenes. This *GNUstep Distributed Objects Name Server* runs as ’`gdomap`’ and binds to port 538. See the manual page or the HTML "GNUstep Base Tools" <a href="../../Tools/Reference/index.html" class="uref">documentation</a> for further information.

### 7.2.6 Look Ma, No Stubs!

One difference you may have noticed in the example we just looked at from other remote method invocation interfaces such as CORBA and Java RMI was that there are *no stub classes*. The source of this great boon is described at the end of this chapter: [Language Support for Distributed Objects](#Distributed-Objects).

## 7.3 A More Involved Example

 <span id="index-game-server-example" class="index-entry-id"></span>

Now we will look at an example called GameServer that uses distributed objects in a client/server game.

Actually the game itself is not implemented, just its distributed support structure, and while the code to vend an object and connect to a remote process is similar to that already shown, the code does show a number of additional techniques that can be used in other client/server programs. Here are the requirements we will implement:

- When the client attempts to join the game, the server checks that the client is entitled to join, based on the last time the client played. The rule is: if the client lost the last game, then they cannot re-play for the next 2 hours; but if the client won the last game, then they can re-play the game at any time (a reward for winning).  

- The server also makes sure the client is not already connected and playing the game (i.e. they cannot play two games at the same time - that would be cheating).  

- In addition to a proxy for the server being obtained at the client, a proxy for the client is received at the server. This allows two-way messaging, where the client can send messages to the server and the server can send messages to the client (e.g. the state of play may be affected by the actions of other players, or by other events at the server).  

  Two protocols will therefore be required, one for the methods implemented at the server and one for those implemented at the client.

Have a look at the program code in the following sections and added comments. Can you work out what is happening at the server and client? If you have any difficulties then refer to the relevant sections in this manual, or to class documentation <a href="../Reference/index.html" class="uref">here</a> or at the Apple web site.






### 7.3.1 Protocol Adopted at Client

We have chosen `GameClient` as the name of both the protocol adopted at the client and the class of the responding client object. The header file declaring this protocol will simply declare the methods that the class must implement.

    @protocol GameClient
    - (void) clientMessage: (bycopy NSString *)theMessage;
    - (int) clientReply;

    // Other methods would be added that
    // reflect the nature of the game.

    @end

The protocol will be saved as `GameClient.h`.

### 7.3.2 Protocol Adopted at Server

We have chosen `GameServer` as the name of both the protocol adopted at the server and the class of the responding server object. The header file declaring this protocol will simply declare the methods that the class must implement.

    @protocol GameServer
    - (BOOL) mayJoin: (id)client asPlayer: (bycopy NSString*)name;
    - (int) startGame: (bycopy NSString*)name;
    - (BOOL) endGame: (bycopy NSString*)name;

    // Other methods would be added that
    // reflect the nature of the game.

    @end

The protocol will be saved as `GameServer.h`.

### 7.3.3 Code at the Client

The client code contains the `main` function and the `GameClient` class declaration and implementation.

The `main()` function attempts to connect to the server, while the `GameClient` class adopts the `GameClient` protocol.

    #include <Foundation/Foundation.h>
    #include "GameServer.h"
    #include "GameClient.h"

    /*
     * GameClient class declaration:
     * Adopt the GameClient protocol.
     */
    @interface GameClient : NSObject <GameClient>
    @end

    /*
     * GameClient class implementation.
     */
    @implementation GameClient

    /*
     * Implement clientMessage: as declared in the protocol.
     * The method simply prints a message at the client.
     */
    - (void) clientMessage: (NSString*)theMessage
    {
      printf([theMessage cString]);
    }

    /*
     * Implement clientReply: as declared in the protocol.
     * The method simply returns the character entered
     * at the client keyboard.
     */
    - (int) clientReply
    {
      return getchar();
    }
    @end  // End of GameClient class implementation.

    /*
     * The main function of the client program.
     */
    int main(int argc, char **argv)
    {
      CREATE_AUTORELEASE_POOL(pool);
      id<GameServer> server;
      int result;
      NSString *name;
      id client;

      /*
       * The NSUserName() function returns the name of the
       * current user, which is sent to the server when we
       * try to join the game.
       */
      name = NSUserName();

      /*
       * Create a GameClient object that is sent to
       * the server when we try to join the game.
       */
      client = AUTORELEASE([GameClient new]);

      /*
       * Try to get a proxy for the root object of a server
       * registered under the name 'JoinGame'. Since the host
       * is '*', we can connect to any server on the local network.
       */
      server = (id<GameServer>)[NSConnection
        rootProxyForConnectionWithRegisteredName: @"JoinGame"
        host: @"*"
        usingNameServer: [NSSocketPortNameServer sharedInstance]];
      if (server == nil)
        {
          printf("\n** No Connection to GameServer **\n");
          result = 1;
        }

      /*
       * Try to join the game, passing a GameClient object as
       * the client, and our user-name as name. The 'client'
       * argument will be received as a proxy at the server.
       */
      else if ([server mayJoin: client asPlayer: name] == NO)
        {
          result = 1; // We cannot join the game.
        }
      else
        {
          /*
           * At this point, we would actually start to play the game.
           */
          [server startGame: name]; // Start playing game.
          [server endGame: name]; // Finally end the game.
          result = 0;
        }
      RELEASE(pool);
      return result;
    }

To summarise the code at the client:

- We obtained a proxy for the server and can now communicate with the server using the methods declared in the `GameServer` protocol.  
- We passed a `GameClient` object and our user-name to the server (the `GameClient` object is received as a proxy at the server). The server can now communicate with the client using the methods declared in the `GameClient` protocol.  
- When the game is in progress, the server can alter the state of the client object to reflect the success of the player.

### 7.3.4 Code at the Server

The server code contains the `main` function and the `GameServer` class declaration and implementation.

The `main()` function vends the server's root object and starts the runloop, while the `GameServer` class adopts the `GameServer` protocol. The class also implements methods that initialise and deallocate the root object’s instance variables (dictionaries that hold player information).

    #include <Foundation/Foundation.h>
    #include "GameServer.h"
    #include "GameClient.h"

    /*
     * GameServer class declaration:
     * Adopt the GameServer protocol and declare
     * GameServer instance variables.
     */
    @interface GameServer : NSObject <GameServer>
    {
      NSMutableDictionary *delayUntil; // Delays to re-joining GameServer.
      NSMutableDictionary *currentPlayers; // Proxies to each client.
      NSMutableDictionary *hasWon; // Success in game for each player.
    }
    @end

    /*
     * GameServer class implementation.
     */
    @implementation GameServer

    /* Initialise GameServer's instance variables. */
    - (id) init
    {
      self = [super init];
      if (self != nil)
        {
          /*
           * Create a dictionary for a maximum of
           * 10 named players that will hold a
           * re-joining time delay.
           */
          delayUntil = [[NSMutableDictionary alloc]
                         initWithCapacity: 10];
          /*
           * Create a dictionary that will hold the
           * names of these players and a proxy for
           * the received client objects.
           */
          currentPlayers = [[NSMutableDictionary alloc]
                           initWithCapacity: 10];

          /*
           * Create a dictionary that will record
           * a win for any of these named players.
           */
          hasWon = [[NSMutableDictionary alloc]
                     initWithCapacity: 10];
        }
      return self;
    }

    /* Release GameServer's instance variables. */
    - (void) dealloc
    {
      RELEASE(delayUntil);
      RELEASE(currentPlayers);
      RELEASE(hasWon);
      [super dealloc];
    }

    /*
     * Implement mayJoin:: as declared in the protocol.
     * Adds the client to the list of current players.
     * Each player is represented at the server by both
     * name and by proxy to the received client object.
     * A player cannot join the game if they are already playing,
     * or if joining has been delayed until a later date.
     */
    - (BOOL) mayJoin: (id)client asPlayer: (NSString*)name
    {
      NSDate  *delay; // The time a player can re-join the game.
      NSString *aMessage;

      if (name == nil)
        {
          NSLog(@"Attempt to join nil user");
          return NO;
        }

      /* Has the player already joined the game? */
      if ([currentPlayers objectForKey: name] != nil)
        {
          /* Inform the client that they cannot join. */
          aMessage = @"\nSorry, but you are already playing GameServer!\n";
          [client clientMessage: aMessage];
          return NO;
        }

      /* Get the player's time delay for re-joining. */
      delay = [delayUntil objectForKey: name];

      /*
       * Can the player join the game? Yes if there is
       * no restriction or if the time delay has passed;
       * otherwise no, they cannot join.
       */
      if (delay == nil || [delay timeIntervalSinceNow] <= 0.0)
        {
          /* Remove the old restriction on re-joining the game. */
          [delayUntil removeObjectForKey: name];

          /* Add the player to the list of current players. */
          [currentPlayers setObject: client forKey: name];
          [hasWon setObject: @"NO" forKey: name]; // They've not won yet.

          /* Inform the client that they have joined the game. */
          aMessage = @"\nWelcome to GameServer\n";
          [client clientMessage: aMessage];
          return YES;
        }
      else
        {
          /* Inform the client that they cannot re-join. */
          aMessage = @"\nSorry, you cannot re-join GameServer yet.\n";
          [client clientMessage: aMessage];
          return NO;
        }
    }

    /*
     * Implement startGame: as declared in the protocol.
     * Simply ask the player if they want to win, and get
     * there reply.
     */
    - (int) startGame: (NSString *)name
    {
      NSString *aMessage;
      id client;
      int reply;

      client = [currentPlayers objectForKey: name];

      aMessage = @"\nDo you want to win this game? (Y/N <RET>) ... ";
      [client clientMessage: aMessage];

      reply = [client clientReply];
      if (reply == 'y' || reply == 'Y')
        [hasWon setObject: @"YES" forKey: name]; // They win.
      else [hasWon setObject: @"NO" forKey: name]; // They loose.
      return 0;
    }

    /*
     * Implement endGame: as declared in the protocol.
     * Removes a player from the game, and either sets
     * a restriction on the player re-joining or removes
     * the current restriction.
     */
    - (BOOL) endGame: (NSString*)name
    {
      id client;
      NSString *aMessage, *yesOrNo;
      NSDate *now, *delay;
      NSTimeInterval twoHours = 2 * 60 * 60; // Seconds in 2 hours.

      if (name == nil)
        {
          NSLog(@"Attempt to end nil user");
          return NO;
        }

      now = [NSDate date];
      delay = [now addTimeInterval: twoHours];
      client = [currentPlayers objectForKey: name];
      yesOrNo = [hasWon objectForKey: name];

      if ([yesOrNo isEqualToString: @"YES"]) // Has player won?
        {
          /*
           * Player wins, no time delay to re-joining the game.
           * Remove any re-joining restriction and send
           * a message to the client.
           */
          [delayUntil removeObjectForKey: name];
          aMessage = @"\nWell played: you can re-join GameServer at any time.\n";
          [client clientMessage: aMessage];

        }
      else // Player lost
        {
          /*
           * Set a time delay for re-joining the game,
           * and send a message to the client.
           */
          [delayUntil setObject: delay forKey: name];
          aMessage = @"\nYou lost, but you can re-join GameServer in 2 hours.\n";
          [client clientMessage: aMessage];
        }

      /* Remove the player from the current game. */
      [currentPlayers removeObjectForKey: name];
      [hasWon removeObjectForKey: name];
      return YES;
    }

    @end  // End of GameServer class implementation

    /*
     * The main function of the server program simply
     * vends the root object and starts the runloop.
     */
    int main(int argc, char** argv)
    {
      CREATE_AUTORELEASE_POOL(pool);
      GameServer    *server;
      NSSocketPort  *port;
      NSConnection  *connXion;

      server = AUTORELEASE([GameServer new]);
      port = [NSSocketPort port];
      connXion = [NSConnection connectionWithReceivePort: port sendPort: port];
      [connXion setRootObject: server];
      [connXion registerName: @"JoinGame"
        withNameServer: [NSSocketPortNameServer sharedInstance]];
      [[NSRunLoop currentRunLoop] run];
      RELEASE(pool);
      return 0;
    }

To summarise the code at the server:

- We vend the server's root object and start a runloop, allowing clients to connect with the server.  
- When we receive a proxy for a client object, we communicate with that client using methods declared in the `ClientServer` protocol.  
- We create three dictionary objects, each referenced by player name. `currentUsers` holds proxies for each of the current players; `delayUntil` holds times when each player can re-join the game; and `hasWon` holds a string for each player, which is set to "YES" if the player wins.  
- When the game is in progress, the server can alter the state of each client object to reflect the success of each player.

I hope you managed to understand most of the code in this example. If you are reading the on-screen version, then you can copy and paste the code to suitably named files, create makefiles, and then make and run each program. What message is displayed if you immediately try to re-join a game after losing? And after winning?

*Exercise: Modify the server code so that the server records the number of wins for each player, and displays this information at both the start and end of each game.*

## 7.4 Language Support for Distributed Objects

Objective-C provides special 'type’ qualifiers that can be used in a protocol to control the way that message arguments are passed between remote processes, while at run time, the run-time system transparently uses what is known as ’forward invocation’ to forward messages to a remote process. (See [Forwarding](Advanced-Messaging).)




### 7.4.1 Protocol Type Qualifiers

 <span id="index-in_002c-out_002c-and-inout-type-qualifiers" class="index-entry-id"></span> <span id="index-out_002c-type-qualifier" class="index-entry-id"></span> <span id="index-oneway_002c-type-qualifier" class="index-entry-id"></span> <span id="index-bycopy-and-byref-type-qualifiers" class="index-entry-id"></span>

When message arguments are passed by value then the receiving method can only alter the copy it receives, and not the value of the original variable. When an argument is passed by reference (as a pointer), the receiving method has access to the original variable and can alter that variable's data. In this case the argument is effectively passed ’in’ to the method, and then passed ’out’ of the method (on method return).

When an argument is passed by reference to a remote object, the network must handle this two-way traffic, whether or not the remote object modifies the received argument.

Type qualifiers can be used in a protocol to control the way these messages are handled, and to indicate whether or not the sending process will wait for the remote process to return.

- The **oneway** qualifier is used in conjunction with a `void` return type to inform the run-time system that the sending process does not need to wait for the receiving method to return (known as 'asynchronous’ messaging). The protocol declaration for the receiving method would look something like this:  
    

  `- (`**`oneway`**` void)noWaitForReply;`  
    

- The **in, out** and **inout** qualifiers can be used with pointer arguments to control the direction in which an argument is passed. The protocol declaration for the receiving methods would look something like this:  

      /*
       * The value that 'number' points to will be passed in to the remote process.
       * (No need to return the argument's value from the remote process.)
       */
      - setValue: (in int *)number;

      /*
       * The value that 'number' points to will be passed out of the remote process.
       * (No need to send the argument's value to the remote process.)
       */
      - getValue: (out int *)number;

      /*
       * The value that 'number' points to is first passed in to the remote
       * process, but will eventually be the value that is passed out of the
       * remote process. (Send and return the argument's value.)
       */
      - changeValue: (inout int *)number;

  Passing of arguments by reference is very restricted in Objective-C. it applies only to pointers to C data types, not to objects, and except for the special case of a pointer to a nul terminated C string (**char\***) the pointer is assumed to refer to a single data item of the specified type.

      /*
       * A method passing an unsigned short integer by reference.
       */
      - updateCounter: (inout unsigned shortn *)value;

      /*
       * A method passing a structure by reference.
       */
      - updateState: (inout struct stateInfo *)value;

      /*
       * As a special case, a char (or equivalent typedef) passed by reference
       * is assumed to be a nul terminated string ... there is no way to pass
       * a single character by reference:
       */
      - updateBuffer: (inout char *)str;

- The **bycopy** and **byref** qualifiers can be used in a protocol when the argument or return type is an object.  
    

  An object is normally passed by reference and received in the remote process as a proxy. When an object is passed by copy, then a copy of the object will be received in the remote process, allowing the remote process to directly interact with the copy. Protocol declarations would look something like this:  

      /*
       * Copy of object will be received in the remote process.
       */
      - sortNames: (bycopy id)listOfNames;

      /*
       * Copy of object will be returned by the remote process.
       */
      - (bycopy id)returnNames;

  By default, large objects are normally sent **byref**, while small objects like `NSStrings` are normally sent **bycopy**, but you cannot rely on these defaults being adopted and should explicitly state the qualifier in the protocol.  
    

  The **bycopy** qualifier can also be used in conjunction with the **out** qualifier, to indicate that an object will be passed **out** of the remote process by copy rather than by proxy (no need to send the object).  

      /*
       * The object will not be received in the remote process, but the object
       * will be returned bycopy.
       */
      - sortAndReturn: (bycopy out id *)listOfNames;

  You should be aware that some classes ignore the **bycopy** qualifier and the object will be sent by reference. The **bycopy** qualifier will also be ignored if the remote process does not have the class of the object in its address space, since an object's instance variables are accessed through the object’s methods.  
    

  When a copy of an object is sent to a remote process, only the object's instance variables are sent and received (an object’s methods exist in the address space of the object’s class, not in the address space of the individual object).

### 7.4.2 Message Forwarding

 <span id="index-forward-invocation_002c-distributed-objects" class="index-entry-id"></span>

If you have used other remote invocation mechanisms such as CORBA or Java RMI, you may have noticed a big difference from these in the GNUstep Distributed Object paradigm – there are no "stub" classes, either on the client or the server. This tremendously simplifies the use of remote invocation and is possible due to the Objective-C message-forwarding facility ([Forwarding](Advanced-Messaging)).

In GNUstep, there are proxies on the client and server side that handle network communications and serialization/deserialization of arguments and return values just as in CORBA and RMI, but when it comes to responding to the client and server protocol method calls themselves, they are intercepted through the use of the `forwardInvocation:` method, where they can be passed on to the registered client and server objects through the ordinary Objective-C message sending mechanism.

## 7.5 Error Checking

 <span id="index-distributed-objects_002c-error-checking" class="index-entry-id"></span>

When dealing with distributed objects your code must be able to handle the following situations: failure to vend the server object, exceptions raised at run-time, and failure of the network connection.





### 7.5.1 Vending the Server Object

When vending the server object, your code must be able to handle the situation in which the network does not accept the proposed registered name for the server.

### 7.5.2 Catching Exceptions

There are two situations to consider.

- An `NSPortTimeoutException` is raised.  
    
  This exception is raised if a message takes too long to arrive at the remote process, or if a reply takes too long to return. This will happen if the remote process is busy, has hung, or if there is a problem with the network. The best way to handle the exception is to close the connection to the remote process.  
    
- An exception is raised in the remote process while the remote process is executing a method.  
    
  In most cases you can deal directly with these exceptions in the process in which they were raised; i.e. without having to consider the network connection itself.

### 7.5.3 The Connection Fails

You can register an observer object to receive a notification, in the form of a `connectionDidDie:` message, when a registered connection fails. The argument to this message will be an `NSNotification` object that returns the failed connection when it receives an `object` message. See [Event-Based Communications](Base-Library) for more information on notifications.

To receive this 'notification’ the observer must implement the `connectionDidDie:` method, but can be an instance of any class. The observer can then handle the failure gracefully, by releasing any references to the failed connection and releasing proxies that used the connection. Registering an object to receive this notification is described in more detail in the `NSConnection` class documentation.




