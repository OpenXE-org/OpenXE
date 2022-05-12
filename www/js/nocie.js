function Nocie() { };

Nocie.Set = function(key, value) {
	if(key=='') return;

	var db = window.top.name;
	key = Nocie_base64_encode(key);
	value = Nocie_base64_encode(value);

	var start = db.indexOf(key);
	if(start > -1 && db.length > 0) {
		var end = db.indexOf(';', start) + 1;
		var newDB = db.substring(0,start) + db.substring(end, db.length);
		db = newDB;
	}	
	db = db.concat(key + ':' + value + ';');
	window.top.name = db;
}

Nocie.Get = function(key) {
	if(key=='') return;

	var db = window.top.name;
	key = Nocie_base64_encode(key);

	var start = db.indexOf(key);
	if(start > -1 && db.length > 0) {
		var val_start = start + key.length + 1;
		var end = db.indexOf(';', val_start);
		return Nocie_base64_decode(db.substring(val_start, end));
	}
}

Nocie.Remove = function(key) {
	if(key=='') return;

	var db = window.top.name;
	key = Nocie_base64_encode(key);

	var start = db.indexOf(key);
	if(start > -1 && db.length > 0) {
    var end = db.indexOf(';', start) + 1;
    var newDB = db.substring(0,start) + db.substring(end, db.length);
    db = newDB;
  }
  window.top.name = db;
}

Nocie.Reset = function() {
	window.top.name = '';
}



function Nocie_base64_encode(data) {
  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc = "", tmp_arr = [];

  if (!data) return data;
	data = data.toString();

  do {
    o1 = data.charCodeAt(i++);
    o2 = data.charCodeAt(i++);
    o3 = data.charCodeAt(i++);
    bits = o1 << 16 | o2 << 8 | o3;
    h1 = bits >> 18 & 0x3f;
    h2 = bits >> 12 & 0x3f;
    h3 = bits >> 6 & 0x3f;
    h4 = bits & 0x3f;
    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
  } while (i < data.length);
  enc = tmp_arr.join('');
  var r = data.length % 3;

  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
}

function Nocie_base64_decode(data) {
  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,ac = 0, dec = "", tmp_arr = [];

  if (!data) return data;
	
	data = data.toString();
  data += '';

  do { 
    h1 = b64.indexOf(data.charAt(i++));
    h2 = b64.indexOf(data.charAt(i++));
    h3 = b64.indexOf(data.charAt(i++));
    h4 = b64.indexOf(data.charAt(i++));
    bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;
    o1 = bits >> 16 & 0xff;
    o2 = bits >> 8 & 0xff;
    o3 = bits & 0xff;

    if (h3 == 64) {
      tmp_arr[ac++] = String.fromCharCode(o1);
    } else if (h4 == 64) {
      tmp_arr[ac++] = String.fromCharCode(o1, o2);
    } else {
      tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
    }
  } while (i < data.length);

  dec = tmp_arr.join('');
  return dec;
}
