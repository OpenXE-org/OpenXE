var escpressed = false;
var esctimeout = null;
$(document).ready(function() {
  var navtabsel = new Array();
  $('#navtabs').find('a').each(function(){
    navtabsel.push(this);
  });
  
  
  $(document).on('keydown', function(e){
    var key = e.which;
    if(key == 27)
    {
      if(!escpressed)$('#tpllogofirma').parent('a').focus();
      escpressed = true;
      esctimeout = setTimeout(function(){escpressed = false},1000);
    }
  });
  
  $('#navhauptmenu').find('a.navdirekt').parent().on('click',function(e){
    $(this).children('ul').first().each(function(){
      $(this).css('visibility','visible');
    });
  });

  $('#navhauptmenu').find('a.navdirekt').on('click',function(e)
  {
    $(this).parent().children('ul').first().each(function(){
      $(this).css('visibility','visible');
    });
    
  });
  
  $('#navhauptmenu').find('a.navdirekt').on('focus',function(e)
  {
    $(this).parent().children('ul').first().each(function(){
      $(this).css('visibility','visible');
    });
    
  });
  
  $('#navhauptmenu').find('a.navdirekt').on('focusout',function(e)
  {
    hideuntermenu();
  });
  
  function hideuntermenu()
  {
    setTimeout(function(){
      $('#navhauptmenu').find('a.navdirekt').each(function()
      {
        $(this).parent().children('ul').first().each(function(){
          if ($(this).parent().find(":focus").length == 0)
          {
            if(!$(this).parent().hasClass('aktiv2'))
            {
              $(this).css('visibility','hidden');
              //window.console.log('hidden');
            }
          }
        });
      });
    },200);    
  }
  
  $('#navtabs').find('a').on('keydown',function(e){
    var key = parseInt(e.which);
    switch(key)
    {
      case 37:
        e.preventDefault();
        //links
        var found = -1;
        var first = -1;
        var last = -1;
        var dolast = false;
        for(var key in navtabsel)
        {
          if(first == -1)first = key;
          if(navtabsel[key] === this && found == -1)
          {
            found = key;
            if(last != -1)
            {
              $(navtabsel[last]).focus(); 
              return;
            }else{
              dolast = true;
            }
          }
          last = key;
        }
        if(dolast)
        {
          $(navtabsel[last]).focus();
          return;
        }
      break;
      case 39:
        e.preventDefault();
        //rechts
        var found = -1;
        var first = -1;
        var last = -1;
        var dofirst = true;
        for(var key in navtabsel)
        {
          if(found != -1)
          {
            $(navtabsel[key]).focus();
            dofirst = false;
            return;
          }
          if(first == -1)first = key;
          if(navtabsel[key] === this && found == -1)
          {
            found = key;
          }
          last = key;
        }
        if(dofirst && first != -1)
        {
          $(navtabsel[first]).focus();
          return;
        }
      break;
    }
  });
  
  $('#navhauptmenu').find('li.secnav > a').on('keydown',function(e){
    var key = parseInt(e.which);
    hideuntermenu();
    
    switch(key)
    {
      case 37:
        e.preventDefault();
        //links
        
        $(this).parent().parent().parent().children('a').first().each(function(){
          var el = this;
          var fertig = false;
          $(this).parent().prev().children('a.navdirekt').first().each(function(){
            $(el).parent().toggleClass('aktiv2',false);
            fertig = true;
            $(this).parent().toggleClass('aktiv2',true);
            $(this).focus();
            hideuntermenu();
          });
          if(!fertig)
          {
            $(this).parent().parent().children('li').last().children('a.navdirekt').first().each(function(){
              fertig = true;
              $(el).parent().toggleClass('aktiv2',false);
              $(this).parent().toggleClass('aktiv2',true);
              $(this).focus();
              hideuntermenu();
            });
          }
        });
      break;
      case 39:
        e.preventDefault();
        //rechts
        $(this).parent().parent().parent().children('a').first().each(function(){
          var el = this;
          var fertig = false;
          $(this).parent().next().children('a.navdirekt').first().each(function(){
            fertig = true;
            $(el).parent().toggleClass('aktiv2',false);
            $(this).parent().toggleClass('aktiv2',true);
            $(this).focus();
            hideuntermenu();
          });
          if(!fertig)
          {
            $(this).parent().parent().children('li').first().next().children('a.navdirekt').first().each(function(){
              fertig = true;
              $(el).parent().toggleClass('aktiv2',false);
              $(this).parent().toggleClass('aktiv2',true);
              $(this).focus();
              hideuntermenu();
            });
          }
          if(!fertig)
          {
            $(this).parent().parent().children('li').first().next().next().children('a.navdirekt').first().each(function(){
              fertig = true;
              $(el).parent().toggleClass('aktiv2',false);
              $(this).parent().toggleClass('aktiv2',true);
              $(this).focus();
              hideuntermenu();
            });
          }
        });
      break;
      case 40:
        e.preventDefault();
        //runter
        var fertig = false;
        $(this).parent().next('li').children('a').first().each(function(){
          $(this).focus();
          fertig = true;
        });
        if(!fertig)
        {
          $(this).parent().parent().children('li').first().children('a').first().each(function(){
            $(this).focus();
          });
        }
      break;
      case 38:
        e.preventDefault();
        //hoch
        var fertig = false;
        $(this).parent().prev('li').children('a').first().each(function(){
          $(this).focus();
          fertig = true;
        });
        if(!fertig)
        {
          $(this).parent().parent().children('li').last().children('a').first().each(function(){
            $(this).focus();
          });
        }
      break;
      case 13:
        e.preventDefault();
        window.location.href = $(this).prop('href');
      break;
    }
  });
  
  $('#navhauptmenu').find('a.navdirekt').on('keydown',function(e){
    var key = parseInt(e.which);
    var el = this;
    hideuntermenu();
    switch(key)
    {
      case 37:
        e.preventDefault();
        //links
        
        var fertig = false;
        $(this).parent().prev().children('a.navdirekt').first().each(function(){
          fertig = true;
          $(el).parent().toggleClass('aktiv2',false);
          $(this).parent().toggleClass('aktiv2',true);
          hideuntermenu();
          $(this).focus();
        });
        if(!fertig)
        {
          $(this).parent().parent().children('li').last().children('a.navdirekt').first().each(function(){
            fertig = true;
            $(el).parent().toggleClass('aktiv2',false);
            $(this).parent().toggleClass('aktiv2',true);
            hideuntermenu();
            $(this).focus();
          });
        }
      break;
      case 39:
        //rechts
        e.preventDefault();
        
        var fertig = false;
        $(this).parent().next().children('a.navdirekt').first().each(function(){
          fertig = true;
          $(el).parent().toggleClass('aktiv2',false);
          $(this).parent().toggleClass('aktiv2',true);
          hideuntermenu();
          $(this).focus();
        });
        if(!fertig)
        {
          $(this).parent().parent().children('li').first().next().children('a.navdirekt').first().each(function(){
            fertig = true;
            $(el).parent().toggleClass('aktiv2',false);
            $(this).parent().toggleClass('aktiv2',true);
            hideuntermenu();
            $(this).focus();
          });
        }
        if(!fertig)
        {        
          $(this).parent().parent().children('li').first().next().next().children('a.navdirekt').first().each(function(){
            fertig = true;
            $(el).parent().toggleClass('aktiv2',false);
            $(this).parent().toggleClass('aktiv2',true);
            hideuntermenu();
            $(this).focus();
          });
        }
      break;
      case 40:
        //runter
        e.preventDefault();
        
        $(this).parent().children('ul').first().each(function(){
          $(this).css('visibility','visible');
          $(this).children('li.secnav').children('a').first().focus();
        });
      break;
    }
    
    
  });
  
  
  $('#tpllogofirma').parent('a').on('keydown', function(e){
    var key = e.which;
    if( key ){
        
    }
    if(key >= 48 && key <= 57 || key == 27)
    {
      var nr = key - 48;
      if(key == 27)nr = 27;
      switch(nr)
      {
        case 27:

          var gefunden = false;
          if(typeof firstfocuselement != 'undefined')
          {
            $(firstfocuselement).first().each(function(){
              gefunden = true;
              $(this).focus();
            });
          }
          if(!gefunden)
          {
            $('#page_container .ui-tabs-panel:visible').find('input:focusable , select:focusable, a:focusable').first().each(function(){
              $(this).focus();
              gefunden = true;
            });
          }
          if(!gefunden)
          {
            $('#page_container').find('input:focusable , select:focusable, a:focusable').first().each(function(){
              $(this).focus();
              gefunden = true;
            });
          }
        break;
        case 1:
          $('#navhauptmenu').find('a.navdirekt').first().each(function(){
            $(this).focus(); 
          });
        break;
        case 2:
          $('#navtabs').find('a').first().each(function(){
            $(this).focus();
          });
        break;
        case 3:
          $('#tabs .ui-tabs-nav a:first').each(function(){
            $(this).focus();
          });
        break;
        case 4:
          $('#page_container').find('input , select, focusable, a').first().each(function(){
            $(this).focus();
          });        
        break;
        case 5:
          $('#navtoolbarleft').find('a').first().each(function(){
            $(this).focus();
          });
        break;
        
        case 8:
          $('#tabellenachrichtenboxen').find('a').first().each(function(){
            $(this).focus();
          }); 
        break;
        
        case 9:
          $('#footer').find('a').first().each(function(){
            $(this).focus();
          });   
        break;
      }
    }
  });
});