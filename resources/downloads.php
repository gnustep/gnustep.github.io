<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title>GNUstep: Download</title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bs-theme-gnustep.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="SHORTCUT ICON" href="/images/gnustep-favicon.ico">
  </head>

<body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

    <nav class="navbar navbar-inverse" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	  <span class="sr-only">Toggle navigation</span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	</button>
	<div class="navbar-brand"><a href="../index.html"><img src="../images/GNUstepLogo_Square.png"></a></div>
      </div>
      
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	<ul class="nav navbar-nav">
	  <li class="dropdown">
	    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Experience<span class="caret"></span></a>
	    <ul class="dropdown-menu">
	      <li><a href="../information/aboutGNUstep.html">Introduction</a></li>
	      <li><a href="../experience/apps.html">Applications</a></li>
              <li><a href="../resources/sources.html">Download</a></li>
              <li><a href="http://wiki.gnustep.org/index.php/User_Guides">User Guides</a></li>
              <li><a href="http://wiki.gnustep.org/index.php/Get_Help">Get Help</a></li>
	      <li></li>
	    </ul>
	  </li>
	  
	  <li class="dropdown">
	    <a href="#" data-toggle="dropdown">Developers<span class="caret"></span></a>
	    <ul class="dropdown-menu">
              <li><a href="../experience/DeveloperTools.html">Developer Tools</a></li>
	      <li><a href="../developers/index.html">Developers</a></li>
	      <li><a href="http://wiki.gnustep.org/index.php/Blogs">Developer Blogs</a></li>
	    </ul>
	    
	  </li>
	  <li class="dropdown">
	    <a href="#" role="button" data-toggle="dropdown">External<span class="caret"></span></a>
	    <ul class="dropdown-menu">
	      <li><a href="http://savannah.gnu.org/projects/gnustep/">Project Page</a><br>
              <li><a href="http://wiki.gnustep.org/">Project Wiki</a></li>
	      <li><a href="../information/gethelp.html">Mailing Lists</a></li>
              <li><a href="http://wiki.gnustep.org/index.php/GNUstepWiki:Site_support">Donations</a></li>
	      <li><a href="../resources/sites.html">Related Sites...</a></li>
	    </ul>
	  </li>
	  
	</ul>

        <ul class="nav navbar-nav navbar-right">
	  <li><a href="http://wiki.gnustep.org/index.php/Report_Bugs">Report Bugs</a></li>
        </ul>

      </div>
    </nav>

<div class="container">

<!-- base href trickery doesn't work, please use PHP download() function -->


<?php

// This will need fixing if some PHP 5 options aren't set to make HTTP_GET_VARS
if (isset($HTTP_GET_VARS["site"])) {
  $site = $HTTP_GET_VARS["site"];
} else {
  $site = "http://ftpmain.gnustep.org/pub/gnustep/";
}

function download($link) {
  global $site; // Why do I have to even declare this? Stupid language.
  echo($site.$link);
}

?>


<h1 class="page-header">GNUstep Downloads</h1>

<p>We recommend that you check the software versions currently
installed on your system.  Consult the
following tables to determine which items need installing or updating.
To download the recommended version of any item of software, click on
the version number in the appropriate column.</p>

<p>For further information on any particular package, click on its name.</p>

<ul>
<li><a href="#pre">Pre-requisites</a></li>
<li><a href="#core">GNUstep Core</a></li>
<li><a href="#libs">Extra Libraries/Tools</a></li>
<li><a href="#dev">Development Tools</a></li>
<li><a href="#user">User Environment</a></li>
</ul>




<h2 class="page-header">GNUstep Core</h2>

<p>All GNUstep users should install the following items. 
<small>(see <a href="../experience/documentation.html">Installation Docs</a>
or the somewhat out-of-date <i><a href="http://gnustep.made-it.com/BuildGuide/index.html#BUILDING.GNUSTEP">Building GNUstep</a></i> in Build Guide)</small></p>


<div class="table-responsive">
<table width="100%" class="table table-condensed">


<col>
<col>
<col>
<col class="note">

<thead>
<tr>
<th>Sources</th>
<th>Required?</th>
<th>Version</th>
<th>Notes</th>
</tr>
</thead>

<tbody>


<tr>
<td>GNUstep Make</td>
<td>Required</td>
<td><a href="<?php download("core/gnustep-make-2.7.0.tar.gz")?>">2.7.0</a></td>
<td>Makefile Package</td>
</tr>

<tr>
<td>GNUstep Base</td>
<td>Required</td>
<td><a href="<?php download("core/gnustep-base-1.26.0.tar.gz")?>">1.26.0</a></td>
<td>Foundation</td>
</tr>

<tr>
<td>GNUstep GUI</td>
<td>Required</td>
<td><a href="<?php download("core/gnustep-gui-0.27.0.tar.gz")?>">0.27.0</a></td>
<td>Graphical user interface class library</td>
</tr>

<tr>
<td>GNUstep Backend</td>
<td>Required</td>
<td><a href="<?php download("core/gnustep-back-0.27.0.tar.gz")?>">0.27.0</a></td>
<td>Generic back-end</td>
</tr>

</tbody>

</table>

</div> <!-- table-responsive -->

<h2 class="page-header">Extra Libraries/Tools</h2>

<p>These libraries and tools offer additional capabilities. Some
packages and applications may require you to install these libraries
</p>


<div class="table-responsive">
<table width="100%" class="table table-condensed">

<col>
<col>
<col>
<col>
<col class="note">

<thead>
<tr>
<th>Sources</th>
<th>Required?</th>
<th>Stable</th>
<th>Unstable</th>
<th>Notes</th>
</tr>
</thead>

<tbody>

<tr>
<td>GNUstep CoreBase</td>
<td>Optional</td>
<td><a href="<?php download("libs/gnustep-corebase-0.1.tar.gz")?>">0.1</a></td>
<td><a href="https://github.com/gnustep/libs-corebase/">from git</a></td>
<td><div class="cent">from svn</div></td>
<td>Core Foundation</td>
</tr>

<tr>
<td><a href="http://www.gnustep.it/Renaissance/index.html">Renaissance</a></td>
<td>Optional</td>
<td><div class="cent">-</div></td>
<td><a href="https://github.com/gnustep/libs-renaissance/">from git</a></td>
<td>Multi-platform UI layout</td>
</tr>

<tr>
<td>GNUstep Guile</td>
<td>Unsupported</td>
<td><a href="<?php download("libs/gnustep-guile-1.1.4.tar.gz")?>">1.1.4</a></td>

<td><div class="cent">-</div></td>
<td>Requires OLD versions of GNUstep and Guile to build/run</td>
</tr>

<tr>
<td>StepTalk</td>
<td>Unsupported</td>
<td><a href="<?php download("libs/StepTalk-0.10.0.tar.gz")?>">0.10.0</a></td>
<td><div class="cent">-</div></td>
<td>Scripting language support</td>
</tr>

<tr>
<td><a href="http://www.gnustep.it/jigs/">JIGS</a></td>
<td>Optional</td>
<td><a href="http://www.gnustep.it/jigs/Download.html">1.5.5
</a></td>
<td><div class="cent">-</div></td>
<td>Java bridge</td>
</tr>

<tr>
<td><a href="http://www.gnustep.org/experience/RIGS.html">RIGS</a></td>
<td>Unsupported</td>
<td><a href="<?php download("libs/rigs-0.2.2.tar.gz")?>">0.2.2</a></td>
<td><div class="cent">-</div></td>
<td>Ruby bridge</td>
</tr>

<tr>
<td>gdl2</td>
<td>Optional</td>
<td><div class="cent">-</div></td>
<td><a href="<?php download("libs/gnustep-dl2-0.10.1.tar.gz")?>">0.10.1</a></td>
<td>Database Support Library</td>
</tr>

<tr>
<td>SQLClient</td>
<td>Optional</td>
<td><a href="<?php download("libs/SQLClient-1.8.1.tar.gz")?>">1.8.1</a></td>
<td><a href="https://github.com/gnustep/libs-sqlclient/">from git</a></td>
<td>Simple Objective-C Interface to SQL databases</td>
</tr>

<tr>
<td>WebServer</td>
<td>Optional</td>
<td><a href="<?php download("libs/WebServer-1.5.5.tar.gz")?>">1.5.5</a></td>
<td><a href="https://github.com/gnustep/libs-webserver/">from git</a></td>
<td>Simple web server for applications</td>
</tr>

<tr>
<td>WebServices</td>
<td>Optional</td>
<td><a href="<?php download("libs/WebServices-0.7.3.tar.gz")?>">0.7.3</a></td>
<td><a href="https://github.com/gnustep/libs-webservices/">from git</a></td>
<td>Classes for building web services (partial)</td>
</tr>

<tr>
<td>Performance</td>
<td>Optional</td>
<td><a href="<?php download("libs/Performance-0.5.0.tar.gz")?>">0.5.0</a></td>
<td><a href="https://github.com/gnustep/libs-performance/">from git</a></td>
<td>Performance improvement and measuring classes</td>
</tr>

<tr>
<td>PPDs</td>
<td>Optional</td>
<td><a href="<?php download("libs/gnustep-ppd-1.0.0.tar.gz")?>">1.0.0</a></td>
<td>-</td>
<td>Printer PPDs formerly in gnustep-gui</td>
</tr>

<tr>
<td>EnterpriseControlConfigurationLogging</td>
<td>Optional</td>
<td><a href="<?php download("libs/EnterpriseControlConfigurationLogging-1.1.3.tar.gz")?>">1.1.3</a></td>
<td><a href="https://github.com/gnustep/libs-ec/">from git</a></td>
<td>Classes for building and administering 24*7 server processes for large scale software systemd.</td>
</tr>

<tr>
</tbody>

</table>
</div> <!-- table-responsive -->


<h2 id="dev">GNUstep Development Tools</h2>

<p>If you intend to develop software for GNUstep, or to port software to GNUstep from another environment, you should install the development tools that are relevant to you from the following list:</p>

<div class="table-responsive">
<table width="100%" class="table table-condensed">

<col>
<col>
<col>
<col class="note">

<thead>
<tr>
<th>Sources</th>
<th>Required?</th>
<th>Version</th>
<th>Notes</th>
</tr>
</thead>

<tbody>

<tr>
<td><a href="http://www.gnustep.org/experience/Gorm.html">Gorm</a></td>
<td>Recommended</td>
<td><a href="<?php download("dev-apps/gorm-1.2.23.tar.gz")?>">1.2.23</a></td>
<td>Graphical interface builder</td>
</tr>

<tr>
<td><a href="http://www.gnustep.org/experience/ProjectCenter.html">Project Center</a></td>
<td>Recommended</td>
<td><a href="<?php download("dev-apps/ProjectCenter-0.6.2.tar.gz")?>">0.6.2
</a></td>
<td>Project developer</td>
</tr>

<tr>
<td>nib2gmodel</td>
<td>Obsolete</td>
<td><a href="<?php download("dev-apps/nib2gmodel-0.11.0.tar.gz")?>">0.11.0</a></td>
<td>Converts older (pre-10.2 nibs) </td>
</tr>

<tr>
<td>OpenStep 2GNU Converter</td>
<td>Obsolete</td>
<td><a href="<?php download("contrib/Openstep2GNUConverter-20000717.tar.gz")?>">20000717</a></td>
<td>Older PB Project converter</td>
</tr>

<tr>
<td>Examples</td>
<td>Optional</td>
<td><a href="<?php download("usr-apps/gnustep-examples-1.4.0.tar.gz")?>">1.4.0</a></td>
<td>GUI examples</td>
</tr>

</tbody>

</table>

</div> <!-- table-responsive -->

<h2 id="user">GNUstep User Environment</h2>

<p>If you intend to use GNUstep to manage files, run applications and so on, please install the items you require from the following list:
</p>

<div class="table-responsive">
<table width="100%" class="table table-condensed">

<col>
<col>
<col>
<col>

<thead>
<tr>
<th>Sources</th>
<th>Required?</th>
<th>Version</th>
<th>Notes</th>
</tr>
</thead>

<tbody>

<tr>
<td><a href="../experience/GWorkspace.html">GWorkspace</a></td>
<td>Recommended</td>
<td><a href="<?php download("usr-apps/gworkspace-0.9.4.tar.gz")?>">0.9.4</a></td>
<td>File manager for GNUstep</td>
</tr>

<tr>
<td><a href="http://www.gnustep.org/experience/apps.html">GNUstep Apps</a></td>
<td>Optional</td>
<td><a href="<?php download("usr-apps/")?>">Various</a></td>
<td>Other User Applications</td>
</tr>

</tbody>

</table>

</div> <!-- table-responsive -->

<h2 id="pre">Pre-requisites</h2>

<p>Please ensure you have at least the <i>Minimum version req'd</i> of
each of the following (where needed) on you system, before you install
GNUstep. If you need to obtain the software, we suggest that you
download the <i>Recommended version</i>.</p>

<div class="table-responsive">
<table width="100%" class="table table-condensed">

<col>
<col>
<col>
<col>
<col>

<thead>
<tr>
<th>Sources</th>
<th>Required?</th>
<th>Minimum<br>version<br>req'd</th>
<th>Recom-<br>mended<br>version</th>
<th>Notes</th>
</tr>
</thead>

<tbody>

<tr>
<td>make</td>
<td>Required</td>
<td>3.81</td>
<td><a href="ftp://ftp.gnu.org/gnu/make/"><i>current</i></a></td>
<td>GNU make</td>
</tr>

<tr>
<td>binutils</td>
<td>Depends on OS</td>
<td>2.9.6</td>
<td><a href="ftp://ftp.gnu.org/gnu/binutils/"><i>current</i></a></td>
<td>GNU binutils</td>
</tr>

<tr>
<td>iconv</td>
<td>If no glibc</td>
<td>1.7</td>
<td><a href="ftp://ftp.ilog.fr/pub/Users/haible/gnu"><i>current</i></a></td>
<td>Convert file encoding</td>
</tr>

<tr>
<td>clang</td>
<td>Recommended</td>
<td><a href="http://llvm.org/releases/download.html#3.3">3.3</a></td>
<td><a href="http://clang.llvm.org/get_started.html"><i>current</i></a></td>
<td>Objective-C Compiler supporting modern 2.0 runtime</td>
</tr>

<tr>
<td>gcc</td>
<td>Optional</td>
<td><a href="ftp://ftp.gnu.org/pub/gnu/gcc/gcc-3.2.3.tar.gz">3.2.3</a></td>
<td><a href="http://gcc.gnu.org"><i>current</i></a></td>
<td>GNU C &amp; Objective-C Compiler</td>
</tr>

<tr>
<td>libffi</td>
<td>Required</td>
<td><a href="ftp://sourceware.org/pub/libffi/libffi-3.0.9.tar.gz">3.0.9</a></td>
<td><a href="ftp://sourceware.org/pub/libffi/libffi-3.2.1.tar.gz">3.2.1</a></td>
<td>Message forwarding</td>
</tr>

<tr>
<td>ffcall</td>
<td>Optional</td>
<td>1.10</td>
<td><a href="http://www.haible.de/bruno/gnu/ffcall-1.10.tar.gz">1.10</a></td>
<td>Possible alternative to libffi</td>
</tr>

<tr>
<td>gmp</td>
<td>Optional</td>
<td>3.1.1</td>
<td><a href="ftp://ftp.gnu.org/pub/gnu/gmp"><i>current</i></a></td>
<td>Arbitrary precision arithmetic</td>
</tr>

<tr>
<td>guile</td>
<td>For guile scripting</td>
<td>1.4</td>
<td><a href="ftp://ftp.gnu.org/pub/gnu/guile"><i>current</i></a></td>
<td><i>Scheme</i> language interpreter</td>
</tr>

<tr>
<td>openssl</td>
<td>Recommended</td>
<td>0.9.6b</td>
<td><a href="http://www.openssl.org/source"><i>current</i></a></td>
<td>SSL and TSL support</td>
</tr>

<tr>
<td>libtiff</td>
<td>Required</td>
<td>3.4036</td>
<td><a href="ftp://ftp.remotesensing.org/pub/libtiff"><i>current</i></a></td>
<td>TIFF image support</td>
</tr>

<tr>
<td>libpng</td>
<td>Recommended</td>
<td>-</td>
<td>-</td>
<td>PNG image support</td>
</tr>

<tr>
<td>libjpeg</td>
<td>Recommended</td>
<td>-</td>
<td>-</td>
<td>JPEG image support</td>
</tr>

<tr>
<td>libxml</td>
<td>Required</td>
<td>2.3.0</td>
<td><a href="ftp://ftp.gnome.org/pub/GNOME/sources/libxml2"><i>current</i></a></td>
<td>For XML property lists and docs</td>
</tr>

<tr>
<td><a href="http://68k.org/~michael/audiofile/">audiofile</a></td>
<td>Recommended</td>
<td>0.2.3</td>
<td><a href="http://www.68k.org/~michael/audiofile/audiofile-0.2.6.tar.gz"><i>current</i></a></td>
<td>Program interface to digital audio formats</td>
</tr>

<tr>
<td>WindowMaker</td>
<td>Recommended</td>
<td><a href="http://windowmaker.info/pub/source/release/WindowMaker-0.92.0.tar.bz2">0.92.0</a></td>
<td><div class="cent">-</div></td>
<td>Window manager &amp; desktop</td>
</tr>

<tr>
<td>libobjc2</td>
<td>Recommended</td>
<td><a href="<?php download("libs/libobjc2-1.7.tar.bz2")?>">1.7</a></td>
<td><a href="<?php download("libs/libobjc2-1.8.1.tar.gz")?>">1.8.1</a></td>
<td>Modern ObjectiveC-2.0 support</td>
</tr>

<tr>
<td>libobjc</td>
<td>Required for MinGW/Cygwin, recommended pre gcc4.6</td>
<td><a href="<?php download("libs/gnustep-objc-1.7.2.tar.gz")?>">1.7.2</a></td>
<td><div class="cent">-</div></td>
<td>Updated libobjc for some systems.</td>
</tr>

</tbody>

</table>

</div> <!-- table-responsive -->

<p class="cont"><span class="cont">Contact: <a href="mailto:webmasters@gnustep.org">webmasters@gnustep.org</a></span></p>


</div>


</body>
</html>
