
aciTree - A javascript treeview control with jQuery

Features:

- the tree can be initialized with data from a JavaScript variable or used with
  the built-in AJAX loading capability, the entire tree can be initialized in
  one go or the tree branches can be loaded as requested (when a inner node
  is opened, for example);

- supports multiple item selection, checkbox and radio-button items,
  keyboard navigation with ARIA support, in-place item editing, item state
  (open/selected) persistance using local storage, URL fragment support for
  item states (open/selected), item search & filter, enabled/disabled states,
  drag & drop for sorting items, each item can have its own icon image,
  support for using image sprites;

- supports multiple data sources, each node can load his children from a
  different source, the entire tree structure can be serialized (including the
  added custom item properties);

- supports displaying multiple columns without using tables, with RTL support;

- the tree can be styled with CSS, the items can contain custom HTML, there
  are a great number of CSS classes applied by default to the tree structure;

- aciTree provides an easy to use API for handling the tree structure, the
  items can by added, updated, removed etc. on the fly, all operations trigger
  events you can listen for and respond to;

Simple usage:

$(function(){

    $('#tree').aciTree({
        ajax: {
            url: 'path/script?nodeId='
        }
    });

});

aciTree jQuery Plugin v4.5.0-rc.7
http://acoderinsights.ro

Copyright (c) 2014 Dragos Ursu
Dual licensed under the MIT or GPL Version 2 licenses.

Require jQuery Library >= v1.9.0 http://jquery.com
+ aciPlugin >= v1.5.1 https://github.com/dragosu/jquery-aciPlugin
