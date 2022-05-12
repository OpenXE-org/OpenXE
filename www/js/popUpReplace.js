function makeRequest( o ) {
  var sUrl = o.href;
  var request = YAHOO.util.Connect.asyncRequest( 'GET', sUrl, 
    {
      success : handleSuccess,
      failure : handleFailure 
    }
  );
}
function makeRequestURL( url ) {
  var sUrl = url;
  var request = YAHOO.util.Connect.asyncRequest( 'GET', sUrl, 
    {
      success : handleSuccess,
      failure : handleFailure 
    }
  );
}
function handleSuccess( o ) {
  if( o.responseText !== undefined ) {
    panel = new YAHOO.widget.Panel ( 
      "win", 
      {  
        effect : {
		  effect : YAHOO.widget.ContainerEffect.FADE,
		  duration : 0.5
		}, 
        constraintoviewport : true, 
        close : true, 
        visible : false, 
        draggable : false
      }
    );
    panel.render();
    var content  = o.responseText;
	content = content.replace( /<body>/, '<div id="popupbody">' );
 	content  = content.replace( /<\/body>/, '</div>' );
	var win = document.getElementById('win');
	var windowbody = win.getElementsByTagName('div')[1];
	windowbody.innerHTML=content;
    var title = win.getElementsByTagName( 'title' )[0].innerHTML;
    var body = document.getElementById( 'popupbody' ).innerHTML;
    panel.setBody( body );
    panel.setHeader( title );
    panel.show();
  }
}
function handleFailure( o ){
  if( o.responseText !== undefined ) {
    alert( 'Couldn\'t load the content: ' + o.statusText );
  }
}
