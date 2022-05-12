<?php
/*

$printer = new LabelPrinter();
$printer->Configure(203,50,18,3,0,6);
$printer->Printpath("/dev/lp0");
$printer->Printamount(1);

// mit Barcode (links), darunter 2 Zeilen:
//$printer->Barcode(5,0,10,12345678);
//$printer->Line(0,12,3,"Lager");
//$printer->Line(15,110,2,"1,2,3,4,5,6,7,8,9,10,11,12,");
//$printer->Qrcode(40,0,3,"123456");

*/

$xmlconfig = '
<printer>
<label width="50" height="80" />
<setting dpi="50" offsetx="0" offsety="0" />
</printer>
';

$xml ='
<label>
<line x="5" y="0" size="3" rotation="1">Blabla</line>
</label>
';

$printer = new LabelPrinter();
$printer->ConfigureXML($xmlconfig);
//$_POST['label'] = trim($_POST['label']);
$printer->LoadXML($_POST['label']);
//$printer->LoadXML($xml);
$printer->Output($_POST['amount']);

class LabelPrinter
{
	private $ppath;				// Print path
	private $pamount;			// Print amount
	private $width;				// Label width
	private $height;			// Label height
	private $height_between; 	// Height between the labels
	private $variable;			// "Variable" of the printer
	private $lines;				// a line for the printer variable
	private $contents;			// the content that should be printed

	/*
		203 dpi oder 300 dpi
		GK420t hat 203 dpi
		dpi / 25.4 mm

		8 punkte pro millimeter bei 203
	*/	

	function __construct($simulate=false)
	{
		$this->simulate=$simulate;
		$this->ppath="/dev/lp0";
		$this->pamount=1;
		$this->max_line_chars = 99; // only for internal variables!
	}


	function ConfigureXML($xml)
	{
		$this->Configure(203,50,18,3,0,6);
	}


	function LoadXML($xml)
	{
		$xml_data = simplexml_load_string("<xml>".$xml."</xml>");

		foreach($xml_data->children() as $label)
		{ 
  		foreach($label->children() as $commands)
  		{
	unset($parameter);
        foreach($commands->attributes() as $attribute => $value)
                $parameter[$attribute] = $value;

        $content = $commands;

        switch($commands->getName())
        {
                case "line":
									$this->Line($parameter['x'],$parameter['y'],$parameter['rotation'],$parameter['size'],$content);
                break;
                case "barcode":
									$this->Barcode($parameter['x'],$parameter['y'],$parameter['rotation'],$parameter['size'],$content,$parameter['type']);
                break;
                case "qrcode":
									$this->Qrcode($parameter['x'],$parameter['y'],$parameter['type'],$content);
                break;
                default: // unkown
        }
  		}
		}
	}

	// Laenge und Hoehe des Labels werden im Konstruktor initialisiert in mm
	function Configure($dpi,$qwidth, $qheight, $qheight_between,$offset_x=0,$offset_y=0) 
	{
		$this->scale = ($dpi/25.4);	

		// offset for real label in the real world
		$this->width = $this->ScaleValue($qwidth)+$offset_x;
		$this->height = $this->ScaleValue($qheight)+$offset_y;
		$this->height_between = $this->ScaleValue($qheight_between);
	}

	function ScaleValue($value)
	{
	  $value = round($value*$this->scale);
	  return $value;
	}
	
	
	
	// Label definieren:
	
	// erstellt einen Barcode
	// B<X-Wert_in_Px>,<Y-Wert_in_Px>,<Rotation>,<Bar Code Selection>,<duenner_Balken_Breite_in_Px>,<dicker_Balken_Breite_in_Px>,<Hoehe_in_Px>,<Lesbarer Code (B=yes, N=no)>,"Daten_als_String"
	function Barcode($pos_x, $pos_y, $rotation, $height,$text,$type="1")
	{
		$pos_x = $this->ScaleValue($pos_x);
		$pos_y = $this->ScaleValue($pos_y);

		$height = $this->ScaleValue($height);

		$rotation = $rotation + 0;
		if(!is_numeric($rotation)) $rotation=0;



		// 0 = rot (3.), 1 = typ (4.), 2.=blackwidth (5.), 8 whitewidth (6.)
		switch($type)
		{
			case 1: $barcode = 'B'.$pos_x.','.$pos_y.','.$rotation.',1,2,8,'.$height.',N,'; break;
			case 2: $barcode = 'B'.$pos_x.','.$pos_y.','.$rotation.',1,3,8,'.$height.',N,'; break;
			default: $barcode = 'B'.$pos_x.','.$pos_y.','.$rotation.',1,2,8,'.$height.',N,'; break;
		}
		$this->AddLabel($barcode, $text);
		
	}
	
	// erstellt einen 2D Bar Code
	// b<X-Wert_in_Px>,<Y-Wert_in_Px>,Q (fuer QR Code),s<1-99> (Groesse des QR-Codes (s = scale)),[optional p4,p5,p6,p7,p8,p9],"Daten_als_String"
	
	function Qrcode($pos_x, $pos_y, $type, $text)
	{
		$pos_x = $this->ScaleValue($pos_x);
		$pos_y = $this->ScaleValue($pos_y);

		$qrcode = 'b'.$pos_x.','.$pos_y.',Q,s'.$type.',';
		$this->AddLabel($qrcode, $text);
		
		//$this->errprint($errcode);
	}
	
	// erstellt eine Zeile mit Inhalt (ohne Angabe der Schriftgroesse, diese wird automatisch auf "2" gesetzt)
	function Line($pos_x, $pos_y, $rotation, $size,$text)
	{
		$pos_x = $this->ScaleValue($pos_x);
		$pos_y = $this->ScaleValue($pos_y);

		$rotation = $rotation + 0;
		if(!is_numeric($rotation)) $rotation=0;

		$size = $size + 0;
		if(!is_numeric($size)) $size=0;

		if(!is_numeric($size))
		{
			$size = 2;
		}
		else if($size <= 1)
		{
			$size = 1;
		}
		else if($size >= 5)
		{
			$size = 5;
			// alle schriftgroessen >= 5 sind immer grossbuchstaben
			$text = strtoupper($text);
		}
		
		$line = 'A'.$pos_x.','.$pos_y.','.$rotation.','.$size.',1,1,N,';
		$this->AddLabel($line, $text);
	}
	
	
	// setzt die Labelvariablen fuer den Drucker
	/* 	es wird zuerst die zu erstellende Druckervariable in definiert ($this->variable[]), anschliessend die Variable selbst ($this->lines[])
		in $this->contents[] wird der Text abgespeichert, der spaeter in den Output-Funktionen hinzugefuegt wird		
	*/
	function AddLabel($vartext, $linetext)
	{
		$varcounter = 0;
		$varcounter = count($this->variable);

		$varnumber = "V".str_pad($varcounter, 2, "0", STR_PAD_LEFT);

		$this->variable[] = $varnumber.','.$this->max_line_chars.',N,"Var'.$varnumber.'"';
		$this->lines[] = $vartext.''.$varnumber;
		$this->contents[] = $linetext;
	}
		
	
	// gibt das zusammengebaute Label zurueck
	function OutputLabel()
	{
		$label = 'FK"Label1"'."\r\n"
				.'FK"Label1"'."\r\n"
				.'FS"Label1"'."\r\n";
		
		for($i=0; $i<count($this->variable); $i++)
		{
			$label .= $this->variable[$i]."\r\n";
		}

		
		// label breite und hoehe + abstand zwischen labels
		$label .= 'q'.$this->width."\r\n"
				.'Q'.$this->height.','.$this->height_between."\r\n";
		
		for($i=0; $i<count($this->lines); $i++)
		{
			$label .= $this->lines[$i]."\r\n";
		}
	
		// label form end	
		$label .= "FE";
			
		return $label;
	}
			
	
	// gibt den zu druckenden Inhalt zur�ck
	function OutputContent()
	{
		$inhalt = 'FR"Label1"'."\r\n"
					.'?'."\r\n";
			
		for($i=0; $i<count($this->lines); $i++)
		{
			$inhalt .= $this->contents[$i]."\r\n";
		}
			
		$inhalt .="P1";
			
		return $inhalt;
	}
	
	function Output($amount)
	{
		if(!is_numeric($amount)) $amount=1;
		$this->pamount = $amount;
		$label = $this->OutputLabel();
		$inhalt = $this->OutputContent();

		if(!$this->simulate)
		{
			system("echo '$label' > $this->ppath");

		for($i=0;$i<$this->pamount;$i++)
			system("echo '$inhalt' > $this->ppath");
		} else {
			echo "\r\n-->$label<--\r\n";
			echo "\r\n-->$inhalt<--\r\n";
		}

	}

	// Error ausgeben
	function errPrint($text)
	{
		//echo "<p>$text</p>";
	}
	
	// Error des Array ausgeben
	function errprintarr($text)
	{
		//echo "<pre>$text</pre>";
	}
	
	
	// Predruckvariablen definieren:
	
	// Druckerpfad definieren:
	function Printpath($path)
	{
		$this->ppath = $path;
	}
	
	// Druckanzahl definieren:
	function Printamount($amount)
	{
		$this->pamount = $amount;
	}
	

}


