<?php
/**
 * Copyright (c) 2014 Michael Billington <michael.billington@gmail.com>,
 *   with additions by Warren Doyle (wdoyle)
 *   modified by Benedikt Sauter changed all fwrite to this->str 24.01.2016
 *   modified by Benedikt Sauter add qrCode,wrapperSend2dCodeData,intLowHigh from https://github.com/mike42/escpos-php/blob/master/src/Mike42/Escpos/Printer.php 05.03.2016
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * If you wish to expand this library you can use the below link:
 * http://content.epson.de/fileadmin/content/files/RSD/downloads/escpos.pdf
 */
define("DEBUG_MODE", 0);

if(DEBUG_MODE == 1)
{
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
}

class phpprint {
  /* ASCII codes */
  const NUL = "\x00";
  const LF = "\x0a";
  const ESC = "\x1b";
  const GS = "\x1d";

  /* Print mode constants */
  const MODE_FONT_A = 0;
  const MODE_FONT_B = 1;
  const MODE_EMPHASIZED = 8;
  const MODE_DOUBLE_HEIGHT = 16;
  const MODE_DOUBLE_WIDTH = 32;
  const MODE_UNDERLINE = 128;

  /* Fonts */
  const FONT_A = 0;
  const FONT_B = 1;
  const FONT_C = 2;

  /* Justifications */
  const JUSTIFY_LEFT = 0;
  const JUSTIFY_CENTER = 1;
  const JUSTIFY_RIGHT = 2;

  /* Cut types */
  const CUT_FULL = 65;
  const CUT_PARTIAL = 66;

  /* Barcode types */
  const BARCODE_UPCA = 0;
  const BARCODE_UPCE = 1;
  const BARCODE_JAN13 = 2;
  const BARCODE_JAN8 = 3;
  const BARCODE_CODE39 = 4;
  const BARCODE_ITF = 5;
  const BARCODE_CODABAR = 6;
  private $fp;

  /* QR code error correction levels */
  const QR_ECLEVEL_L = 0;
  const QR_ECLEVEL_M = 1;
  const QR_ECLEVEL_Q = 2;
  const QR_ECLEVEL_H = 3;
  
  /* QR code models */
  const QR_MODEL_1 = 1;
  const QR_MODEL_2 = 2;
  const QR_MICRO = 3;

  /** @var string $txt */
  public $txt;

  /** @var string $str */
  public $str;

  protected $pdfCommands = [];
  
  function __construct($head = null) {
    //THIS IS THE SERIAL PORT YOU ARE OPENING
    //$fp = fopen($head,"w");
    //$this -> fp = $fp;
    $this -> initialize();
  }

  public function getPdfCommands(): array
  {
    return $this->pdfCommands;
  }

  // Funktion am 12.02.2016 durch Benedikt Sauter hinzugefuegt
  function uml($text) 
  {
    $rw=$text;
    $rw=str_replace("Ä",chr(142),$rw);
    $rw=str_replace("Ö",chr(153),$rw);
    $rw=str_replace("Ü",chr(154),$rw);
    $rw=str_replace("ä",chr(132),$rw);
    $rw=str_replace("ö",chr(148),$rw);
    $rw=str_replace("ü",chr(129),$rw);
    $rw=str_replace("ß",chr(225),$rw);
    return $rw;
  }
  /**
   * Add text to the buffer
   *
   * @param string $str Text to print
   */
  function text($str = "") {
    if(DEBUG_MODE == 1)
      echo $str;

    //fwrite($this -> fp, $str);
    $this->str .= $this->uml($str);
    $this->txt .= $str;
    $this->pdfCommands[] = ['type' => 'text', 'value' => $str];
  }
  /**
   * Print and feed line / Print and feed n lines
   *
   * @param int $lines Number of lines to feed
   */
  function feed($lines = 1) {
    if($lines <= 1) {
      //fwrite($this -> fp, self::LF);
      $this->str .= self::LF;
      $this->txt .= "\r\n";
      $this->pdfCommands[] = ['type' => 'text', 'value' => "\r\n"];
    } else {
      //fwrite($this -> fp, self::ESC . "d" . chr($lines));
      $this->str .= self::ESC . "d" . chr($lines);
    }
  }
  /**
   * Select print mode(s).
   *
   * Arguments should be OR'd together MODE_* constants:
   * MODE_FONT_A
   * MODE_FONT_B
   * MODE_EMPHASIZED
   * MODE_DOUBLE_HEIGHT
   * MODE_DOUBLE_WIDTH
   * MODE_UNDERLINE
   *
   * @param int $mode
   */
  function select_print_mode($mode = self::NUL) {
    //fwrite($this -> fp, self::ESC . "!" . chr($mode));
    $this->str .= self::ESC . "!" . chr($mode);
    $this->pdfCommands[] = ['type' => 'print_mode', 'value' => $mode];
  }
  function reverse_mode($rev = 0) {
    //fwrite($this -> fp, self::GS . "B" . chr($rev));
    $this->str .= self::GS . "B" . chr($rev);
  }
  /**
   * Turn underline mode on/off
   *
   * @param int $underline 0 for no underline, 1 for underline, 2 for heavy underline
   */
  function set_underline($underline = 1) {
    //fwrite($this -> fp, self::ESC . "-". chr($underline));
    $this->str .= self::ESC . "-". chr($underline);
    $this->pdfCommands[] = ['type' => 'underline', 'value' => $underline];
  }
  /**
   * Initialize printer
   */
  function initialize() {
    //fwrite($this -> fp, self::ESC . "@");
    $this->str .= self::ESC . "@";
  }
  /**
   * Turn emphasized mode on/off
   *
   *  @param boolean $on true for emphasis, false for no emphasis
   */
  function set_emphasis($on = false) {
    //fwrite($this -> fp, self::ESC . "E". ($on ? chr(1) : chr(0)));
    $this->str .= self::ESC . "E". ($on ? chr(1) : chr(0));
  }
  /**
   * Turn double-strike mode on/off
   *
   * @param boolean $on true for double strike, false for no double strike
   */
  function set_double_strike($on = false) {
    //fwrite($this -> fp, self::ESC . "G". ($on ? chr(1) : chr(0)));
    $this->str .= self::ESC . "G". ($on ? chr(1) : chr(0));
  }
  /**
   * Select character font.
   * Font must be FONT_A, FONT_B, or FONT_C.
   *
   * @param int $font
   */
  function set_font($font = self::FONT_A) {
    //fwrite($this -> fp, self::ESC . "M" . chr($font));
    $this->str .= self::ESC . "M" . chr($font);
    $this->pdfCommands[] = ['type' => 'font', 'value' => $font];
  }
  /**
   * Select justification
   * Justification must be JUSTIFY_LEFT, JUSTIFY_CENTER, or JUSTIFY_RIGHT.
   */
  function set_justification($justification = self::JUSTIFY_LEFT) {
    //fwrite($this -> fp, self::ESC . "a" . chr($justification));
    $this->str .= self::ESC . "a" . chr($justification);
    $this->pdfCommands[] = ['type' => 'justification', 'value' => $justification];
  }
  /**
   * Print and reverse feed n lines
   *
   * @param int $lines number of lines to feed
   */
  function feed_reverse($lines = 1) {
    //fwrite($this -> fp, self::ESC . "e" . chr($lines));
    $this->str .= self::ESC . "e" . chr($lines);
  }
  /**
   * Cut the paper
   *
   * @param int $mode Cut mode, either CUT_FULL or CUT_PARTIAL
   * @param int $lines Number of lines to feed
   */
  function cut($mode = self::CUT_FULL, $lines = 3) {
    //fwrite($this -> fp, self::GS . "V" . chr($mode) . chr($lines));
    $this->str .= self::GS . "V" . chr($mode) . chr($lines);
  }


  function qrCode($content, $ec = self::QR_ECLEVEL_L, $size = 3, $model = self::QR_MODEL_2) {
    if($content == "") {
      return;
    }
    $cn = '1'; // Code type for QR code
    // Select model: 1, 2 or micro.
    $this -> wrapperSend2dCodeData(chr(65), $cn, chr(48 + $model) . chr(0));
    // Set dot size.
    $this -> wrapperSend2dCodeData(chr(67), $cn, chr($size));
    // Set error correction level: L, M, Q, or H
    $this -> wrapperSend2dCodeData(chr(69), $cn, chr(48 + $ec));
    // Send content & print
    $this -> wrapperSend2dCodeData(chr(80), $cn, $content, '0');
    $this -> wrapperSend2dCodeData(chr(81), $cn, '', '0');
    $this->pdfCommands[] = ['type' => 'qr_code', 'value' => $content, 'ec' => $ec, 'size' => $size, 'model' => $model];
  }

/**
   * Wrapper for GS ( k, to calculate and send correct data length.
   * 
   * @param string $fn Function to use
   * @param string $cn Output code type. Affects available data
   * @param string $data Data to send.
   * @param string $m Modifier/variant for function. Often '0' where used.
   * @throws InvalidArgumentException Where the input lengths are bad.
   */
  private function wrapperSend2dCodeData($fn, $cn, $data = '', $m = '') {
    $header = $this -> intLowHigh(strlen($data) + strlen($m) + 2, 2);
    $this->str .= self::GS . "(k" . $header . $cn . $fn . $m . $data;
  }


  /**
   * Generate two characters for a number: In lower and higher parts, or more parts as needed.
   * @param int $int Input number
   * @param int $length The number of bytes to output (1 - 4).
   */
  private static function intLowHigh($input, $length) {
    $maxInput = (256 << ($length * 8) - 1);
    $outp = "";
    for($i = 0; $i < $length; $i++) {
      $outp .= chr($input % 256);
      $input = (int)($input / 256);
    }
    return $outp;
  }



  /**
   * Set barcode height
   *
   * @param int $height Height in dots
   */
  function set_barcode_height($height = 8) {
    //fwrite($this -> fp, self::GS . "h" . chr($height));
    $this->str .= self::GS . "h" . chr($height);
  }
  /**
   * Print a barcode
   *
   * @param string $content
   * @param int $type
   */
  function barcode($content, $type = self::BARCODE_CODE39) {
    //fwrite($this -> fp, self::GS . "k" . chr($type) . $content . self::NUL);
    $this->str .= self::GS . "k" . chr($type) . $content . self::NUL;
  }
  /**
   * This will generate a Barcode and print it directly to the Printer
   *
   * @param string $content
   * @param int $type
   * @param int $height
   *
   */
  function generateBarcode($content, $type, $height)
  {
    $this->set_barcode_height($height);
    $this->barcode($content, $type);
    $this->feed();
  }
  /*
   *
   *  This will increase the font used on the page whilst it is true
   * 	@param bool $on
   *
   */
  function enlargePrint($on = false)
  {
    if($on == true)
      $this->select_print_mode(self::MODE_DOUBLE_HEIGHT);
    else
      $this->select_print_mode();

  }

  /*
   *  This will print an empty line
   */
  function newline()
  {
    $this->text("\n");
  }

  /**
   * Generate a pulse, for opening a cash drawer if one is connected.
   * The default settings should open an Epson drawer.
   *
   * @param int $pin 0 or 1, for pin 2 or pin 5 kick-out connector respectively.
   * @param int $on_ms pulse ON time, in milliseconds.
   * @param int $off_ms pulse OFF time, in milliseconds.
   */
  function pulse($pin = 0, $on_ms = 120, $off_ms = 240) {
    //fwrite($this -> fp, self::ESC . "p" . chr($m + 48) . chr($t1 / 2) . chr($t2 / 2));
    $this->str .= chr(27). chr(112). chr(0). chr(100). chr(250);
    //self::ESC . "p" . chr($m + 48) . chr($t1 / 2) . chr($t2 / 2);
  }
}
