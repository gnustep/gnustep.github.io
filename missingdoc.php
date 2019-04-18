<?
  if (strlen($URL)>0)
    $TestURL=$URL;
  else
    $TestURL=$REQUEST_URI;

  $NewURL="";
  if ($TestURL=="/resources/documentation/base/Base.html")
    $NewURL="http://www.gnustep.org/resources/documentation/Developer/Base/Reference/Base.html";
  else if ($TestURL=="/resources/documentation/manual_toc.html")
    $NewURL="http://www.gnustep.org/resources/documentation/Developer/Base/ProgrammingManual/manual_toc.html";

  if (strlen($NewURL)>0)
  {
    header ("HTTP/1.0 301 OK");
    header ("Location: $NewURL");
  }
  else
  {
    print "<html><body>Sorry, the document you requested does not exist or has been moved. Try navigating from the <a href=\"http://www.gnustep.org\">home page</a> to find what you want.</body></html>\n";
  };

?>

