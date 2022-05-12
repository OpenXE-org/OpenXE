<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]


  <div class="mlmTreeContainerLeft">
<fieldset>
<legend>{|Suche|}</legend>
      <div class="mlmTreeSuche">Bezeichnung: <input id="search" type="text" value=""><hr></div>
</fieldset>
<br><br>
      <div id="mlmTree" class="aciTree"></div>
    </div>
    <div class="mlmTreeContainerRight">
   
    </div>
    <div class="mlmClear"></div>

    <script type="text/javascript" src="js/aciTree/js/jquery.aciPlugin.min.js"></script>
    <script type="text/javascript" src="js/aciTree/js/jquery.aciTree.min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/aciTree/css/aciTree.css">

    <style>
    .mlmTreeContainerLeft {
      width: 40%;
      float: left;
    }

    .aciTree {
      padding-left:50px;
    }

    .mlmTreeContainerRight {
      width: 59%;
      float: right;
      position: relative;
      min-height:600px;
    }
    .mlmClear {
      clear: both;
    }

    .mlmTreeSuche {
      padding: 10px 10px 5px 10px;
    }

    .mlmintranet_minidetail_layer {
      width: 100%;
    }

    .searched > div {
      background-color: #E5F5D2;
    }
    </style>

    <script type="text/javascript">
    function katloeschen()
    {
      if(confirm('Wirklich löschen?'))
      {
        $('#loeschen').val(1);
        $('#artikelbaumfrm').submit();
      }
    }
    
    $(document).ready(function() {

      $('#mlmTree').aciTree({
          autoInit: false,
          checkboxChain: false,
          ajax: {
              url: '[URL]'
          },
          checkbox: true,
          itemHook: function(parent, item, itemData, level) {
            //console.log(itemData);
          },
            filterHook: function(item, search, regexp) {

              if (search.length) {
                  var parent = this.parent(item);

                  if (parent.length) {
                      var label = this.getLabel(parent);
                      if (regexp.test(String(label))) {
                          this.setVisible(item);
                          return true;
                      }
                      this.setVisible(item);
                  }

                  if (regexp.test(String(this.getLabel(item)))) {
                    item.addClass('searched');
                    return true;
                  } else {
                    return false;
                  }

                  //return regexp.test(String(this.getLabel(item)));
              } else {
                  return true;
              }
          }
      });


      var api = $('#mlmTree').aciTree('api');



      $('#search').val('');
      var last = '';

      $('#search').on('keyup', function() {
          if ($(this).val() === last) {
              return;
          }

          $('.aciTreeLi').removeClass('searched');

          last = $(this).val();
          api.filter(null, {
              search: $(this).val(),
              callback: function() {

              },
              success: function(item, options) {

                  if (!options.first) {
                      //alert('No results found!');
                  }
              }
          });
      });


      $('#mlmTree').on('acitree', function(event, api, item, eventName, options){
          switch (eventName){
              case 'checked':
                      console.log('the event 1 is: ' + eventName + ' for the item ID: ' + api.getId(item));
              break;
              case 'unchecked':
                      console.log('the event 2 is: ' + eventName + ' for the item ID: ' + api.getId(item));
              break;
              case 'selected':

                var ajaxData = {
                  id: api.getId(item),
                  name: api.getLabel(item)
                }

                $.ajax({
                  url: 'index.php?module=artikelbaum&action=detail',
                  data: ajaxData,
                  success: function(data) {
                    $('.mlmTreeContainerRight').html(data);
                    checkContainerPos();
                  }
                });

                break;
              default:
                  if (api.isItem(item)){
                      //console.log('the event is: ' + eventName + ' for the item ID: ' + api.getId(item));
                  } else {
                      //console.log('the event is: ' + eventName + ' for the tree ROOT');
                  }
          }
      });

        $('#mlmTree').aciTree('init');
        $(window).on('scroll', function() {
          checkContainerPos();
        });

    });

    function checkContainerPos() {
      var newContainerPos = ($(window).scrollTop() - 113);
      if (newContainerPos <= 0) {
        newContainerPos = 0;
      }
      $('.mlmintranet_minidetail_layer').css({
        top: newContainerPos
      });
    }
    
    function addnode(id)
    {
      $.ajax({
        url: 'index.php?module=artikelbaum&action=change',
        type: 'POST',
        data: {'cmd':'addkat','id':id},
        success: function(data) {
          $('.mlmTreeContainerRight').html(data);
          checkContainerPos();
        }
      });
    }

    </script>

</div>

<!-- tab view schließen -->
</div>

