$(document).ready(function() {

//muell // warten //import

$("#spaeter").change(function(){
  $("input").each(function(){
    if($(this).val()=="warten") $(this).prop("checked", true);
  });
});


$("#muell").change(function(){
  $("input").each(function(){
    if($(this).val()=="muell") $(this).prop("checked", true);
  });
});


$("#import").change(function(){
  $("input").each(function(){
    if($(this).val()=="import") $(this).prop("checked", true);
  });
});

});
