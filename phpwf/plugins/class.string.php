<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php

class WawiString 
{


  function __construct()
  {
  }

  function Convert($value,$input,$output)
  {
    if($input=="")
      return $value;
   

    /*if (strpos($a, '\\') !== false)
      $input = str_replace('/','\/',$input);*/

    $array = $this->FindPercentValues($input);
    $regexp = $this->BuildRegExp($array);

    $elements =
      preg_split($regexp,$value,-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    // input und elements stimmmen ueberein

    $newout = $output;
    $i = 0;
    foreach($array as $key=>$value)
    {
      $newout = str_replace($key,isset($elements[$i])?$elements[$i]:'',$newout);
      $i++;
    }
    return $newout;
  }

  /**
   * @param string $string
   *
   * @return string
   */
  public function removeUtf8Bom($string) {
    if(!is_string($string) || strlen($string)< 3) {
      return $string;
    }
    if(ord($string[0]) === 239 && ord($string[1]) === 187 && ord($string[2]) === 191) {
      return substr($string,3);
    }
    return $string;
  }

  function BuildRegExp($array)
  {

    $regexp = '/^';
    foreach($array as $value)
    {
      $value = str_replace('.','\.',$value);
      $value = str_replace('+','\+',$value);
      $value = str_replace('*','\*',$value);
      $value = str_replace('?','\?',$value);
      $regexp .= '(\S+)'.$value;
    }
    $regexp .= '/';

    return $regexp;
  }

  function FindPercentValues($pattern)
  {
    preg_match_all('/(?:(%[0-9]+)|.)/i', $pattern, $matches);
    $hash = '';
    $collect = '';
    $start = true;
    foreach($matches[1] as $key=>$value)
    {
      if($value=="")
	      $collecting = true;
      else
      {
        $collecting = false;
        $oldhash = isset($hash)?$hash:null;
        $hash = $value;
      }

      if(!$collecting)
      {
        if(!$start)
          $replace[$oldhash] = $collect;
        $collect="";
      }
      else
	      $collect .=$matches[0][$key];
      $start = false;
    }
    $replace[$hash] = $collect;
    return $replace;
  }

  function encodeText($string)
  {
    $string = str_replace("\\r\\n","#BR#",$string);
    $string = str_replace("\n","#BR#",$string);
    $encoded = htmlspecialchars(stripslashes($string), ENT_QUOTES); 

   
    return $encoded;
  }

 function decodeText($_str, $_form=true) 
 {
   if ($_form) {
     $_str      = str_replace("#BR#", "\r\n", $_str);
   }
   else {
     $_str      = str_replace("#BR#", "<br>", $_str);
   }
   return($_str);
 }

	function valid_utf8( $string )
	{
		return !((bool)preg_match('~\xF5\xF6\xF7\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF\xC0\xC1~ms',$string));
	}

  /**
   * @param mixed $text
   *
   * @return string
   */
  public function fixeUmlaute($text) {
    if(!is_string($text)) {
      return $text;
    }
    $umlaute = $this->getUmlauteArray();

    return str_replace(array_keys($umlaute),array_values($umlaute), $text);
  }

  /**
   * @return array
   */
  public function getUmlauteArray() {
    return array( 'Ã¼'=>'ü', 'Ã¤'=>'ä', 'Ã¶'=>'ö', 'Ã–'=>'Ö', 'Ã?'=>'ß','ÃŸ'=>'ß', 'Ã '=>'à', 'Ã¡'=>'á', 'Ã¢'=>'â', 'Ã£'=>'ã', 'Ã¹'=>'ù', 'Ãº'=>'ú', 'Ã»'=>'û', 'Ã™'=>'Ù', 'Ãš'=>'Ú', 'Ã›'=>'Û', 'Ãœ'=>'Ü', 'Ã²'=>'ò', 'Ã³'=>'ó', 'Ã´'=>'ô', 'Ã¨'=>'è', 'Ã©'=>'é', 'Ãª'=>'ê', 'Ã«'=>'ë', 'Ã€'=>'À', 'Ã<81>'=>'Á', 'Ã‚'=>'Â', 'Ãƒ'=>'Ã', 'Ã„'=>'Ä', 'Ã…'=>'Å', 'Ã‡'=>'Ç', 'Ãˆ'=>'È', 'Ã‰'=>'É', 'ÃŠ'=>'Ê', 'Ã‹'=>'Ë', 'ÃŒ'=>'Ì', 'Ã<8d>'=>'Í', 'ÃŽ'=>'Î', 'Ã<8f>'=>'Ï', 'Ã‘'=>'Ñ', 'Ã’'=>'Ò', 'Ã“'=>'Ó', 'Ã”'=>'Ô', 'Ã•'=>'Õ', 'Ã˜'=>'Ø', 'Ã¥'=>'å', 'Ã¦'=>'æ', 'Ã§'=>'ç', 'Ã¬'=>'ì', 'Ã­'=>'í', 'Ã®'=>'î', 'Ã¯'=>'ï', 'Ã°'=>'ð', 'Ã±'=>'ñ', 'Ãµ'=>'õ', 'Ã¸'=>'ø', 'Ã½'=>'ý', 'Ã¿'=>'ÿ', 'â‚¬'=>'€' );
  }


  function unicode_decode($content) {
    $ISO10646XHTMLTrans = array(
      "&"."#34;" => "&quot;",
      "&"."#38;" => "&amp;",
      "&"."#39;" => "&apos;",
      "&"."#60;" => "&lt;",
      "&"."#62;" => "&gt;",
      "&"."#128;" => "&euro;",
      "&"."#160;" => "",
      "&"."#161;" => "&iexcl;",
      "&"."#162;" => "&cent;",
      "&"."#163;" => "&pound;",
      "&"."#164;" => "&curren;",
      "&"."#165;" => "&yen;",
      "&"."#166;" => "&brvbar;",
      "&"."#167;" => "&sect;",
      "&"."#168;" => "&uml;",
      "&"."#169;" => "&copy;",
      "&"."#170;" => "&ordf;",
      "&"."#171;" => "&laquo;",
      "&"."#172;" => "&not;",
      "&"."#173;" => "­",
      "&"."#174;" => "&reg;",
      "&"."#175;" => "&macr;",
      "&"."#176;" => "&deg;",
      "&"."#177;" => "&plusmn;",
      "&"."#178;" => "&sup2;",
      "&"."#179;" => "&sup3;",
      "&"."#180;" => "&acute;",
      "&"."#181;" => "&micro;",
      "&"."#182;" => "&para;",
      "&"."#183;" => "&middot;",
      "&"."#184;" => "&cedil;",
      "&"."#185;" => "&sup1;",
      "&"."#186;" => "&ordm;",
      "&"."#187;" => "&raquo;",
      "&"."#188;" => "&frac14;",
      "&"."#189;" => "&frac12;",
      "&"."#190;" => "&frac34;",
      "&"."#191;" => "&iquest;",
      "&"."#192;" => "&Agrave;",
      "&"."#193;" => "&Aacute;",
      "&"."#194;" => "&Acirc;",
      "&"."#195;" => "&Atilde;",
      "&"."#196;" => "&Auml;",
      "&"."#197;" => "&Aring;",
      "&"."#198;" => "&AElig;",
      "&"."#199;" => "&Ccedil;",
      "&"."#200;" => "&Egrave;",
      "&"."#201;" => "&Eacute;",
      "&"."#202;" => "&Ecirc;",
      "&"."#203;" => "&Euml;",
      "&"."#204;" => "&Igrave;",
      "&"."#205;" => "&Iacute;",
      "&"."#206;" => "&Icirc;",
      "&"."#207;" => "&Iuml;",
      "&"."#208;" => "&ETH;",
      "&"."#209;" => "&Ntilde;",
      "&"."#210;" => "&Ograve;",
      "&"."#211;" => "&Oacute;",
      "&"."#212;" => "&Ocirc;",
      "&"."#213;" => "&Otilde;",
      "&"."#214;" => "&Ouml;",
      "&"."#215;" => "&times;",
      "&"."#216;" => "&Oslash;",
      "&"."#217;" => "&Ugrave;",
      "&"."#218;" => "&Uacute;",
      "&"."#219;" => "&Ucirc;",
      "&"."#220;" => "&Uuml;",
      "&"."#221;" => "&Yacute;",
      "&"."#222;" => "&THORN;",
      "&"."#223;" => "&szlig;",
      "&"."#224;" => "&agrave;",
      "&"."#225;" => "&aacute;",
      "&"."#226;" => "&acirc;",
      "&"."#227;" => "&atilde;",
      "&"."#228;" => "&auml;",
      "&"."#229;" => "&aring;",
      "&"."#230;" => "&aelig;",
      "&"."#231;" => "&ccedil;",
      "&"."#232;" => "&egrave;",
      "&"."#233;" => "&eacute;",
      "&"."#234;" => "&ecirc;",
      "&"."#235;" => "&euml;",
      "&"."#236;" => "&igrave;",
      "&"."#237;" => "&iacute;",
      "&"."#238;" => "&icirc;",
      "&"."#239;" => "&iuml;",
      "&"."#240;" => "&eth;",
      "&"."#241;" => "&ntilde;",
      "&"."#242;" => "&ograve;",
      "&"."#243;" => "&oacute;",
      "&"."#244;" => "&ocirc;",
      "&"."#245;" => "&otilde;",
      "&"."#246;" => "&ouml;",
      "&"."#247;" => "&divide;",
      "&"."#248;" => "&oslash;",
      "&"."#249;" => "&ugrave;",
      "&"."#250;" => "&uacute;",
      "&"."#251;" => "&ucirc;",
      "&"."#252;" => "&uuml;",
      "&"."#253;" => "&yacute;",
      "&"."#254;" => "&thorn;",
      "&"."#255;" => "&yuml;",
      "&"."#338;" => "&OElig;",
      "&"."#339;" => "&oelig;",
      "&"."#352;" => "&Scaron;",
      "&"."#353;" => "&scaron;",
      "&"."#376;" => "&Yuml;",
      "&"."#402;" => "&fnof;",
      "&"."#710;" => "&circ;",
      "&"."#732;" => "&tilde;",
      "&"."#913;" => "&Alpha;",
      "&"."#914;" => "&Beta;",
      "&"."#915;" => "&Gamma;",
      "&"."#916;" => "&Delta;",
      "&"."#917;" => "&Epsilon;",
      "&"."#918;" => "&Zeta;",
      "&"."#919;" => "&Eta;",
      "&"."#920;" => "&Theta;",
      "&"."#921;" => "&Iota;",
      "&"."#922;" => "&Kappa;",
      "&"."#923;" => "&Lambda;",
      "&"."#924;" => "&Mu;",
      "&"."#925;" => "&Nu;",
      "&"."#926;" => "&Xi;",
      "&"."#927;" => "&Omicron;",
      "&"."#928;" => "&Pi;",
      "&"."#929;" => "&Rho;",
      "&"."#931;" => "&Sigma;",
      "&"."#932;" => "&Tau;",
      "&"."#933;" => "&Upsilon;",
      "&"."#934;" => "&Phi;",
      "&"."#935;" => "&Chi;",
      "&"."#936;" => "&Psi;",
      "&"."#937;" => "&Omega;",
      "&"."#945;" => "&alpha;",
      "&"."#946;" => "&beta;",
      "&"."#947;" => "&gamma;",
      "&"."#948;" => "&delta;",
      "&"."#949;" => "&epsilon;",
      "&"."#950;" => "&zeta;",
      "&"."#951;" => "&eta;",
      "&"."#952;" => "&theta;",
      "&"."#953;" => "&iota;",
      "&"."#954;" => "&kappa;",
      "&"."#955;" => "&lambda;",
      "&"."#956;" => "&mu;",
      "&"."#957;" => "&nu;",
      "&"."#958;" => "&xi;",
      "&"."#959;" => "&omicron;",
      "&"."#960;" => "&pi;",
      "&"."#961;" => "&rho;",
      "&"."#962;" => "&sigmaf;",
      "&"."#963;" => "&sigma;",
      "&"."#964;" => "&tau;",
      "&"."#965;" => "&upsilon;",
      "&"."#966;" => "&phi;",
      "&"."#967;" => "&chi;",
      "&"."#968;" => "&psi;",
      "&"."#969;" => "&omega;",
      "&"."#977;" => "&thetasym;",
      "&"."#978;" => "&upsih;",
      "&"."#982;" => "&piv;",
      "&"."#8194;" => "&ensp;",
      "&"."#8195;" => "&emsp;",
      "&"."#8201;" => "&thinsp;",
      "&"."#8204;" => "&zwnj;",
      "&"."#8205;" => "&zwj;",
      "&"."#8206;" => "&lrm;",
      "&"."#8207;" => "&rlm;",
      "&"."#8211;" => "&ndash;",
      "&"."#8212;" => "&mdash;",
      "&"."#8216;" => "&lsquo;",
      "&"."#8217;" => "&rsquo;",
      "&"."#8218;" => "&sbquo;",
      "&"."#8220;" => "&ldquo;",
      "&"."#8221;" => "&rdquo;",
      "&"."#8222;" => "&bdquo;",
      "&"."#8224;" => "&dagger;",
      "&"."#8225;" => "&Dagger;",
      "&"."#8226;" => "&bull;",
      "&"."#8230;" => "&hellip;",
      "&"."#8240;" => "&permil;",
      "&"."#8242;" => "&prime;",
      "&"."#8243;" => "&Prime;",
      "&"."#8249;" => "&lsaquo;",
      "&"."#8250;" => "&rsaquo;",
      "&"."#8254;" => "&oline;",
      "&"."#8260;" => "&frasl;",
      "&"."#8364;" => "&euro;",
      "&"."#8465;" => "&image;",
      "&"."#8472;" => "&weierp;",
      "&"."#8476;" => "&real;",
      "&"."#8482;" => "&trade;",
      "&"."#8501;" => "&alefsym;",
      "&"."#8592;" => "&larr;",
      "&"."#8593;" => "&uarr;",
      "&"."#8594;" => "&rarr;",
      "&"."#8595;" => "&darr;",
      "&"."#8596;" => "&harr;",
      "&"."#8629;" => "&crarr;",
      "&"."#8656;" => "&lArr;",
      "&"."#8657;" => "&uArr;",
      "&"."#8658;" => "&rArr;",
      "&"."#8659;" => "&dArr;",
      "&"."#8660;" => "&hArr;",
      "&"."#8704;" => "&forall;",
      "&"."#8706;" => "&part;",
      "&"."#8707;" => "&exist;",
      "&"."#8709;" => "&empty;",
      "&"."#8711;" => "&nabla;",
      "&"."#8712;" => "&isin;",
      "&"."#8713;" => "&notin;",
      "&"."#8715;" => "&ni;",
      "&"."#8719;" => "&prod;",
      "&"."#8721;" => "&sum;",
      "&"."#8722;" => "&minus;",
      "&"."#8727;" => "&lowast;",
      "&"."#8730;" => "&radic;",
      "&"."#8733;" => "&prop;",
      "&"."#8734;" => "&infin;",
      "&"."#8736;" => "&ang;",
      "&"."#8743;" => "&and;",
      "&"."#8744;" => "&or;",
      "&"."#8745;" => "&cap;",
      "&"."#8746;" => "&cup;",
      "&"."#8747;" => "&int;",
      "&"."#8756;" => "&there4;",
      "&"."#8764;" => "&sim;",
      "&"."#8773;" => "&cong;",
      "&"."#8776;" => "&asymp;",
      "&"."#8800;" => "&ne;",
      "&"."#8801;" => "&equiv;",
      "&"."#8804;" => "&le;",
      "&"."#8805;" => "&ge;",
      "&"."#8834;" => "&sub;",
      "&"."#8835;" => "&sup;",
      "&"."#8836;" => "&nsub;",
      "&"."#8838;" => "&sube;",
      "&"."#8839;" => "&supe;",
      "&"."#8853;" => "&oplus;",
      "&"."#8855;" => "&otimes;",
      "&"."#8869;" => "&perp;",
      "&"."#8901;" => "&sdot;",
      "&"."#8968;" => "&lceil;",
      "&"."#8969;" => "&rceil;",
      "&"."#8970;" => "&lfloor;",
      "&"."#8971;" => "&rfloor;",
      "&"."#9001;" => "&lang;",
      "&"."#9002;" => "&rang;",
      "&"."#9674;" => "&loz;",
      "&"."#9824;" => "&spades;",
      "&"."#9827;" => "&clubs;",
      "&"."#9829;" => "&hearts;",
      "&"."#9830;" => "&diams;"
    );

    return str_replace(array_keys($ISO10646XHTMLTrans), array_values($ISO10646XHTMLTrans), $content);
  }

  /**
   * @param string $string
   *
   * @return string
   */
  public function ReadyForPDF($string='')
  {
    return trim(
      html_entity_decode(
        str_replace(
          ['“','„','–',"&rsquo;","&apos;","NONBLOCKINGZERO"],
          ['"','','-',"'","'",''],
          $string
        ),
        ENT_QUOTES,
        'UTF-8'
      )
    );
  }
}
