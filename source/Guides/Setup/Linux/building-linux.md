# Building on Linux

We first need to install some prequisits including
clang for Objective-C 2.0 support, and set some
enviroment variables.

Debian-based:
```sh
sudo apt install clang lld git cmake make pkg-config
```

Environment:
```sh
export CC=/usr/bin/clang
export CXX=/usr/bin/clang++
export PATH=/usr/local/bin:$PATH
export LD_LIBRARY_PATH=/usr/local/lib:$LD_LIBRARY_PATH
export LD=/usr/bin/ld.lld
export LDFLAGS=-fuse-ld=/usr/bin/ld.lld
```

## 1.1 libobjc2

We can now build and install the Objective-C runtime libobjc2.
```sh
git clone --recurse-submodules --branch <REPLACE_WITH_LATEST_TAG> "https://github.com/gnustep/libobjc2"
cd libobjc2
mkdir build && cd build
cmake .. -DCMAKE_BUILD_TYPE=RelWithDebInfo
sudo make install
```

## 1.2 libdispatch

Grand Central Dispatch (GCD) is a library that provides a
low-level and efficient API for managing work across threads.

```sh
git clone --branch <REPLACE_WITH_LATEST_TAG> https://github.com/apple/swift-corelibs-libdispatch
cd swift-corelibs-libdispatch/
mkdir build && cd build
cmake .. -DCMAKE_BUILD_TYPE=RelWithDebInfo -DINSTALL_PRIVATE_HEADERS=YES
sudo make install
```

## 1.3 GNUstep Make

GNUstep make is a collection of makefiles and scripts to
build GNUstep frameworks, libraries and applications.

```sh
git clone --branch <REPLACE_WITH_LATEST_TAG> https://github.com/gnustep/tools-make
cd tools-make
./configure \
           --with-layout=fhs \
           --enable-native-objc-exceptions \
           --enable-objc-arc \
           --enable-install-ld-so-conf \
           --enable-debug-by-default
sudo make install
```

## 1.4 GNUstep Base

GNUstep Base features the Foundation framework, a collection of non-graphical Objective-C objects.
Different from the Foundation framework on macOS, GNUstep Base is not based on CoreFoundation.
You can still use CoreFoundation (a.k.a CoreBase) on GNUstep, but it is not required.

### 1.4.1 Dependencies
You will need to install the following dependencies before building GNUstep Base:
- GNUstep Make (see above)
- gnutls or openssl
- libffi
- libxml2
- libxslt
- zlib
- libavahi
- libicu
- tzdata
- libcurl

Debian-based:
```sh
sudo apt install gnutls-bin, libffi-dev, libxml2-dev, libxslt1-dev, libgnutls28-dev, zlib1g-dev, libavahi-client-dev, libicu-dev, tzdata, libcurl4-openssl-dev
```

### 1.4.2 Building

```sh
# Source the GNUstep environment
source /usr/local/share/GNUstep/Makefiles/GNUstep.sh

# Clone the repository
git clone --branch <REPLACE_WITH_LATEST_TAG> https://github.com/gnustep/libs-base

cd libs-base
./configure
```

## 1.5 GNUstep CoreBase
If you are interested in using the C-based CoreFoundation
API you can build and install GNUstep CoreBase.

Please note that CoreBase is not that mature yet and you can run into
issues when using it. For more information about the current state
of CoreBase please see [TODO]().

### 1.5.1 Dependencies
- GNUstep Make (see above)
- GNUstep Base (see above)
- libicu

Debian-based:
```sh
sudo apt install libicu-dev
```

### 1.5.2 Building

```sh
# Source the GNUstep environment
source /usr/local/share/GNUstep/Makefiles/GNUstep.sh

# Clone the repository
git clone --branch <REPLACE_WITH_LATEST_TAG> https://github.com/gnustep/libs-corebase

cd libs-corebase
./configure
```

If you are only interested in using non-graphical frameworks you can stop here.