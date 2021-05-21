/**
 * clears a select form element
 * 
 * @param targetSel - dom reference to the select element
 */
function clearSelect(targetSel) {
	for (var i=targetSel.length; i>=0; i--) {
		targetSel.options[i] = null;
	}
}
/**
 * sends a request under usage of a loading graphic - targettable has to be positioned relative
 *
 * @param srcref - reference to the select element with chosen data
 * @param target - form element name of target element
 */
function sendRequestO(domref, target) {
	// skip if no target specified
	if(!target) return false;
 
	// save reference to next target
	if(domref) domref.followup = target;
 
	var req;
	try {
		req = window.XMLHttpRequest ? new XMLHttpRequest():
			new ActiveXObject("Microsoft.XMLHTTP");
	} catch (e) {
		// no AJAX Support
	}
	req.onreadystatechange = function() {
		if ((req.readyState == 4) && (req.status == 200)) {
			// merge empty line with response
			var data = eval('(' + req.responseText + ')');
			var targetRef = document.getElementById(target);
			var targetSel = targetRef.getElementsByTagName('select')[0];
 
			// make it visible
			targetRef.style.display = 'block';
 
			// clear old data
			clearSelect(targetSel);
 
			// fill with data from json response
			for(var x in data) {
				targetSel.appendChild(new Option(
					data[x].text, 
					data[x].id
				));
			}
 
			// clear all followups
			while(targetSel.followup) {
				targetRef = document.getElementById(targetSel.followup);
 
				// make it hidden
				targetRef.style.display = 'none';
 
				// mark next select
				targetSel = targetRef.getElementsByTagName('select')[0];
 
				// clear old data
				clearSelect(targetSel);
			}
		}
	}
 
	req.open('post', 'ajax/select.php');
	req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
 
	// send empty post with initial load
	req.send(domref !== null ? 'id='+domref.value+'&name='+domref.name : '');
 
	return false; // return false to avoid reload/recentering of the page
}
