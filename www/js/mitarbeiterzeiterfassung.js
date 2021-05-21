jQuery.expr[':'].Contains = function(a, i, m) { 
  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
};
jQuery.expr[':'].contains = function(a, i, m) { 
  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
};
var mainterval;
var reloaded = false;
$(document).ready(function() {

	// Mitarbeiterliste

	mitarbeiterListePosHeight();
	$(window).scroll(function() {
		mitarbeiterListePosHeight();
	});
	
	$('.mitarbeiterzeiterfassung').css({
		'min-height': ($(window).height() - 240)
	});


	$('.rOpen-Dialog').click(function(event) {
		event.preventDefault();

		var adresse = $(this).attr('data-adresse');
		var date = $(this).attr('data-date');
		
		jQuery('#dialog')
			.load('index.php?module=mitarbeiterzeiterfassung&action=tag&adresse=' + adresse + '&date=' + date,function( response, status, xhr ) {
        
        mainterval =  window.setInterval(function(
        ) {
          $('#dialog').find('iframe').contents().find('#dialogreload').each(function(){
            window.clearInterval(mainterval); 
            if(!reloaded)location.reload();
            reloaded = true;
          });
          
          
        },100);
        
          if ( status == "error" ) {
            var msg = "Sorry but there was an error: ";
            $( "#dialog" ).html( msg + xhr.status + " " + xhr.statusText );
          }
        })
			.dialog({
				title: date,
				width: 600
			}); 
	});
	$('.yearSelect').change(function() {
		$('.yearSelect').find('form').submit();
	});

	var wTimeout = null;
	$('.sollstundenChangeJs').keyup(function() {
		var inputField = $(this);
		clearTimeout(wTimeout);
		setTimeout(function() {
			saveSollstundenTag(inputField);
		}, 750);
	});

	$('.iststundenChangeJs').keyup(function() {
		var inputField = $(this);
		clearTimeout(wTimeout);
		setTimeout(function() {
			saveIststundenTag(inputField);
		}, 750);
	});
  
	$('.unbezahltstundenChangeJs').keyup(function() {
		var inputField = $(this);
		clearTimeout(wTimeout);
		setTimeout(function() {
			saveunbezahltminutenTag(inputField);
		}, 750);
	});
  
	$('.urlaubstundenChangeJs').keyup(function() {
		var inputField = $(this);
		clearTimeout(wTimeout);
		setTimeout(function() {
			saveurlaubminutenTag(inputField);
		}, 750);
	});
  
	$('.krankstundenChangeJs').keyup(function() {
		var inputField = $(this);
		clearTimeout(wTimeout);
		setTimeout(function() {
			savekrankminutenTag(inputField);
		}, 750);
	});
  
  
	$('.filterMitarbeiterJs').keyup(function() {
		$('.mitarbeiterListe li').hide();
		$('.mitarbeiterListe li:contains("' + $(this).val() + '")').show();
		/*
		$('.mitarbeiterListe li').filter(function() { 
			return $.text([this]) === 'blabla'; 
		}).show();
		*/
	});

});

function mitarbeiterListePosHeight() {

	var scrollTop = $(window).scrollTop();
	scrollTop = scrollTop-90;
	if (scrollTop <= 0) {
		scrollTop = 0;
	}
	var windowHeight = $(window).height();
	var newHeight = windowHeight-240;

	$('.mitarbeiterzeiterfassungMitarbeiter').css({
		height: newHeight,
		top: scrollTop
	})

}

function saveSollstundenTag(inputField) {

	$.ajax({
		url: 'index.php?module=mitarbeiterzeiterfassung&action=savesollstundentag',
		method: 'GET',
		data: {
			datum: inputField.attr('data-date'),
			adresse: inputField.attr('data-adresse'),
			stunden: inputField.val()
		},
		beforeSend: function() {
			App.loading.open();
			//inputField.attr('disabled', 'disabled');
		},
		success: function() {
			App.loading.close();
			//inputField.removeAttr('disabled');
/*
			if (inputField.attr('data-callback').length != 0) {
				eval(inputField.attr('data-callback'));
			}*/

		}
	});

}

function saveIststundenTag(inputField)
{
	$.ajax({
		url: 'index.php?module=mitarbeiterzeiterfassung&action=saveiststundentag',
		method: 'GET',
		data: {
			datum: inputField.attr('data-date'),
			adresse: inputField.attr('data-adresse'),
			stunden: inputField.val()
		},
		beforeSend: function() {
			App.loading.open();
			//inputField.attr('disabled', 'disabled');
		},
		success: function() {
			App.loading.close();
			//inputField.removeAttr('disabled');

      /*
			if (inputField.attr('data-callback').length != 0) {
				eval(inputField.attr('data-callback'));
			}*/

		}
	});
}

function saveunbezahltminutenTag(inputField)
{
	$.ajax({
		url: 'index.php?module=mitarbeiterzeiterfassung&action=saveunbezahltminutentag',
		method: 'GET',
		data: {
			datum: inputField.attr('data-date'),
			adresse: inputField.attr('data-adresse'),
			stunden: inputField.val()
		},
		beforeSend: function() {
			App.loading.open();
			//inputField.attr('disabled', 'disabled');
		},
		success: function() {
			App.loading.close();
			//inputField.removeAttr('disabled');
/*
			if (inputField.attr('data-callback').length != 0) {
				eval(inputField.attr('data-callback'));
			}*/

		}
	});
}

function saveurlaubminutenTag(inputField)
{
	$.ajax({
		url: 'index.php?module=mitarbeiterzeiterfassung&action=saveurlaubminutentag',
		method: 'GET',
		data: {
			datum: inputField.attr('data-date'),
			adresse: inputField.attr('data-adresse'),
			stunden: inputField.val()
		},
		beforeSend: function() {
			App.loading.open();
			//inputField.attr('disabled', 'disabled');
		},
		success: function() {
			App.loading.close();
			//inputField.removeAttr('disabled');
/*
			if (inputField.attr('data-callback').length != 0) {
				eval(inputField.attr('data-callback'));
			}*/

		}
	});
}

function savekrankminutenTag(inputField)
{
	$.ajax({
		url: 'index.php?module=mitarbeiterzeiterfassung&action=savekrankminutentag',
		method: 'GET',
		data: {
			datum: inputField.attr('data-date'),
			adresse: inputField.attr('data-adresse'),
			stunden: inputField.val()
		},
		beforeSend: function() {
			App.loading.open();
			//inputField.attr('disabled', 'disabled');
		},
		success: function() {
			App.loading.close();
			//inputField.removeAttr('disabled');
/*
			if (inputField.attr('data-callback').length != 0) {
				eval(inputField.attr('data-callback'));
			}*/

		}
	});
}

function update_wochenstunden(kalenderwoche) {
	
	var stunden = 0;
	var tage = $('tr[data-kalenderwoche="'+kalenderwoche+'"]').find('input');
	if (tage) {
		$.each(tage, function() {
			stunden += parseFloat($(this).val());
		});
	}

	$('tr[data-kalenderwoche="'+kalenderwoche+'"]').find('span.wochenstunden').text(stunden);

}
