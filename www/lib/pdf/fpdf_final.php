<?php
/*******************************************************************************
* Software: FPDF_EPS
* Version:  1.3
* Date:     2006-07-28
* Author:   Valentin Schmidt
*
* Last Changes:
* - handle binary bytes in front of PS code (which before caused troubles for ereg)
* - fixed positioning and BoundingBox handling code (was totally screwed)
* - fix for BoundingBox detection without space after : (now Corel-compatible)
* - added some dirty code to handle compound paths
* - support for custom colors (x operator)
*******************************************************************************/

require('rotation.php');

class PDF_EPS extends PDF{

function __construct($orientation='P',$unit='mm',$format='A4'){
	FPDFWAWISION::__construct($orientation,$unit,$format);
}

function ImageEps ($file, $x, $y, $w=0, $h=0, $link='', $useBoundingBox=true){
	$data = file_get_contents($file);
	if ($data===false) $this->Error('EPS file not found: '.$file);

	# strip binary bytes in front of PS-header
	$start = strpos($data, '%!PS-Adobe');
	if ($start>0) $data = substr($data, $start);

	# find BoundingBox params
	preg_match ("/%%BoundingBox:([^\r\n]+)/", $data, $regs);
	if (count($regs)>1){
		list($x1,$y1,$x2,$y2) = explode(' ', trim($regs[1]));
	}
	else $this->Error('No BoundingBox found in EPS file: '.$file);

	$start = strpos($data, '%%EndSetup');
	if ($start===false) $start = strpos($data, '%%EndProlog');
	if ($start===false) $start = strpos($data, '%%BoundingBox');

	$data = substr($data, $start);

	$end = strpos($data, '%%PageTrailer');
	if ($end===false) $end = strpos($data, 'showpage');
	if ($end) $data = substr($data, 0, $end);

	# save the current graphic state
	$this->_out('q');

	$k = $this->k;

	if ($useBoundingBox){
		$dx = $x*$k-$x1;
		$dy = $y*$k-$y1;
	}else{
		$dx = $x*$k;
		$dy = $y*$k;
	}
	
	# translate
	$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', 1,0,0,1,$dx,$dy+($this->hPt - 2*$y*$k - ($y2-$y1))));
	
	if ($w>0){
		$scale_x = $w/(($x2-$x1)/$k);
		if ($h>0){
			$scale_y = $h/(($y2-$y1)/$k);
		}else{
			$scale_y = $scale_x;
			$h = ($y2-$y1)/$k * $scale_y;
		}
	}else{
		if ($h>0){
			$scale_y = $h/(($y2-$y1)/$k);
			$scale_x = $scale_y;
			$w = ($x2-$x1)/$k * $scale_x;
		}else{
			$w = ($x2-$x1)/$k;
			$h = ($y2-$y1)/$k;
		}
	}
	
	# scale	
	if (isset($scale_x))
		$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $scale_x,0,0,$scale_y, $x1*(1-$scale_x), $y2*(1-$scale_y)));
	
	# handle pc/unix/mac line endings
	//$lines = split ("\r\n|[\r\n]", $data);
	$lines = explode("\n",str_replace(["\r\n","\r"],"\n", $data));
	$u=0;
	$cnt = count($lines);
	for ($i=0;$i<$cnt;$i++){
		$line = $lines[$i];
		if ($line=='' || $line[0]=='%') continue;
		$len = strlen($line);
		if ($len>2 && $line[$len-2]!=' ') continue;
		$cmd = $line[$len-1];

		switch ($cmd){
			case 'm':
			case 'l':
			case 'v':
			case 'y':
			case 'c':

			case 'k':
			case 'K':
			case 'g':
			case 'G':

			case 's':
			case 'S':

			case 'J':
			case 'j':
			case 'w':
			case 'M':
			case 'd' :
			
			case 'n' :
			case 'v' :
				$this->_out($line);
				break;
										
			case 'x': # custom colors
				list($c,$m,$y,$k) = explode(' ', $line);
				$this->_out("$c $m $y $k k");
				break;
				
			case 'Y':
				$line[$len - 1] = 'y';
				$this->_out($line);
				break;

			case 'N':
				$line[$len - 1] = 'n';
				$this->_out($line);
				break;
		
			case 'V':
				$line[$len - 1] = 'v';
				$this->_out($line);
				break;
												
			case 'L':
				$line[$len - 1] = 'l';
				$this->_out($line);
				break;

			case 'C':
				$line[$len - 1] = 'c';
				$this->_out($line);
				break;

			case 'b':
			case 'B':
				$this->_out($cmd . '*');
				break;

			case 'f':
			case 'F':
				if ($u>0){
					$isU = false;
					$max = min($i+5,$cnt);
					for ($j=$i+1;$j<$max;$j++)
						$isU = ($isU || ($lines[$j]=='U' || $lines[$j]=='*U'));
					if ($isU) $this->_out("f*");
				}else
					$this->_out("f*");
				break;

			case 'u':
				if ($line[0] == '*') $u++;
				break;

			case 'U':
				if ($line[0] == '*') $u--;
				break;
			
			#default: echo "$cmd<br>"; #just for debugging
		}

	}

	# restore previous graphic state
	$this->_out('Q');
	if ($link)
		$this->Link($x,$y,$w,$h,$link);
}

}# END CLASS

# for backward compatibility
if (!function_exists('file_get_contents')){
	function file_get_contents($filename, $use_include_path = 0){
		$file = @fopen($filename, 'rb', $use_include_path);
		if ($file){
			if ($fsize = @filesize($filename))
				$data = fread($file, $fsize);
			else {
				$data = '';
				while (!feof($file)) $data .= fread($file, 1024);
			}
			fclose($file);
			return $data;
		}else
			return false;
	}
}

?>
