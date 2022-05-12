  <style>
  #sortable1, #sortable2, #sortable3, #sortable4 { list-style-type: none; margin: 0; float: left; margin-right: 10px; background: #eee; padding: 5px; width: 143px;}
  #sortable1 li, #sortable2 li, #sortable3 li, #sortable4 li { margin: 5px; padding: 5px; font-size: 1.2em; width: 120px; }
  .sortable_heading {  margin: 0; float: left; margin: 5px; background: #eee; padding: 5px; width: 150px; }
  .sortable_heading_h3 { margin-left:10px; }
  </style>
  <script>
  $( function() {
    $( "ul.droptrue" ).sortable({
      connectWith: "ul"
    });
 
    $( "ul.dropfalse" ).sortable({
      connectWith: "ul",
      dropOnEmpty: false
    });
 
    $( "#sortable1, #sortable2, #sortable3, #sortable4" ).disableSelection();
  } );
  </script>
</head>
<body>
<div class="sortable_heading">
<h4 class="sortable_heading_h3">Entwicklung:</h4>
<ul id="sortable1" class="droptrue">
  <li class="ui-state-default">Can be dropped..</li>
  <li class="ui-state-default">..on an empty list</li>
  <li class="ui-state-default">Item 3</li>
  <li class="ui-state-default">Item 4</li>
  <li class="ui-state-default">Item 5</li>
</ul>
</div> 

<div class="sortable_heading">
<h4 class="sortable_heading_h3">Core Team:</h4>
<ul id="sortable2" class="droptrue">
  <li class="ui-state-highlight">Cannot be dropped..</li>
  <li class="ui-state-highlight">..on an empty list</li>
  <li class="ui-state-highlight">Item 3</li>
  <li class="ui-state-highlight">Item 4</li>
  <li class="ui-state-highlight">Item 5</li>
</ul>
</div> 


<div class="sortable_heading">
<h4 class="sortable_heading_h3">Support:</h4>
<ul id="sortable3" class="droptrue">
  <li class="ui-state-highlight">Cannot be dropped..</li>
  <li class="ui-state-highlight">..on an empty list</li>
  <li class="ui-state-highlight">Item 3</li>
  <li class="ui-state-highlight">Item 4</li>
  <li class="ui-state-highlight">Item 5</li>
</ul>
</div> 


<div class="sortable_heading">
<ul id="sortable4">
<input type="button" value="Neue Liste hinzufügen">
</ul>
</div> 

 
<br style="clear:both">
