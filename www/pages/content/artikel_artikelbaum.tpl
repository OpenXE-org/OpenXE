  <div class="mlmTreeContainerLeft">
    <div id="mlmTree" class="aciTree"></div>
    </div>
    <div class="mlmClear"></div>

    <script type="text/javascript" src="js/aciTree/js/jquery.aciPlugin.min.js"></script>
    <script type="text/javascript" src="js/aciTree/js/jquery.aciTree.min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/aciTree/css/aciTree.css">

    <style>
    .mlmTreeContainerLeft {
      width: 100%;
      float: left;
    }

    .aciTree {
      padding-left:50px;
    }
    .mlmTreeSuche.mlmNoPadding .aciTree {
        padding-left: 0;
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

    
    $(document).ready(function() {

      $('#mlmTree').aciTree({
          autoInit: false,
          checkboxChain: false,
          ajax: {
              url: 'index.php?module=artikelbaum&action=baumajax&cmd=suche'
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
                  url: 'index.php?module=artikel&action=profisuche&cmd=filterbaum&id=[ID]&fmodul=[FMODULE]',
                  data: ajaxData,
                  type: 'POST',
                  dataType: 'json',
                  success: function(data) {
                    if($('#kundeartikelpreise').length)
                    {
                      var oTable = $('#kundeartikelpreise').DataTable( );
                      oTable.ajax.reload();
                      checkContainerPos();
                    }else{
                      if($('#artikeltabellebilder').length)
                      {
                        var oTable = $('#artikeltabellebilder').DataTable( );
                        oTable.ajax.reload();
                      }
                      if($('#artikeltabelle').length)
                      {
                        var oTable = $('#artikeltabelle').DataTable( );
                        oTable.ajax.reload();
                      }
                    }
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
    

    </script>