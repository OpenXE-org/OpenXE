<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php

class WFMonitor
{


  function __construct(&$app)
  {
    $this->app = &$app;
  }


  function Error($msg)
  {
    $this->ErrorBox($msg);
  }



  function ErrorBox($content)
  {
    $box .="
      <table border=\"1\" width=\"100%\" bgcolor=\"#ffB6C1\">
	<tr><td>phpWebFrame Error: $content</td></tr>
      </table>";

    echo $box;
  }
}
?>
