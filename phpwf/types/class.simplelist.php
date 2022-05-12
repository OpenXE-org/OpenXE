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


class SimpleList
{
  var $actual = 0;
  var $items = 0; 

  var $List = array();

  function __construct(){}

  function Add($data)
  {
    $this->List[] = $data;
    $this->actual = $this->items;
    $this->items++;
    return TRUE; 
  }


  function &getFirst() 
  {
    $this->actual = 0;
    return $this->getActual();
  }


  function getLast() 
  {
    $last = count($this->List);
    $this->actual = $last;
    return $this->getActual();
  }


  function &getNext() 
  {
    $this->actual++;
    return $this->getActual();
  } 

  function &getActual()
  {
    if($this->actual >=0 && $this->actual < $this->items) 
      return $this->List[$this->actual];

    return FALSE;
  }

  
  function getPrev() 
  {
    $this->actual++;
    return $this->getActual();
  } 

}

