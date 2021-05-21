<script type="text/javascript">

$(document).ready ( function () {

	$("form").submit (
		form_unchange
	);

	$(":input").keydown (
		form_changed
	);

	var form_change_noticed = false;

	function form_changed () {
		form_change_noticed = true;
	}

	function form_unchange () {
		form_change_noticed = false;
	}

	window.onbeforeunload= function(){		
		if ( form_change_noticed ) {
			return "Es gibt noch ungespeicherte Änderungen!";
		} else	
			return;
		};
});

</script>
