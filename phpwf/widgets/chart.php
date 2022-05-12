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

/**
 * @deprecated Neue Chart-Klasse stattdessen verwenden
 * @see \Xentral\Widgets\Chart\Chart
 */
class Chart {
  public static $instancescount = 0;
  var $app;

  var $rows;
  var $dataset;
  var $align;
  var $headings;
  var $lheadings;
  var $type;
  var $animate;
  var $colors;
  var $labels;
  var $background;
  var $colorcol;

  function __construct(&$app, $type = 'line', $targetjquery = 'JQUERYREADY') 
  {
    $this->app = &$app;
    $this->background = false;
    $this->labels = false;
    $this->animate = true;
    $this->type = $type;
    $this->sql ="";
    $this->limit ="";
    $this->stacked = false;
    $this->duration = 1000;
    $this->datasets = null;
    $this->headings = null;
    $this->lheadings = null;
    $this->yAxes = null;
    $this->colorcol = false;
    
    						
    $this->colors = array( 
            '45b9d3','a2c55a','0e8394','f69e06','f2c289','8dd4de','cbe0a4','c5e3e9',
            '0b8092', 'a9ca45', 'FF6600','f69e06', 'be3978', 'cb5a20', '41b3ce', '85cddb',
            '000000', '993300', '333300', '000080', '333399', '333333', '800000', 
            '808000', '008000', '008080', '0000FF', '666699', '808080', 'FF0000', 'FF9900',
            '99CC00', '339966', '33CCCC', '3366FF', '800080', '999999', 'FF00FF', 'FFCC00',
            'FFFF00', '00FF00', '00FFFF', '00CCFF', '993366', 'C0C0C0', 'FF99CC', 'FFCC99',
            'FFFF99', 'CCFFFF', '99CCFF', '808cc8', 'FFFFFF');
    foreach($this->colors as $k => $v)$this->colors[$k] = '#'.$v;
    self::$instancescount++;
    if(false && self::$instancescount == 1)
    {
      $this->app->Tpl->Add($targetjquery,'
      
      		Chart.pluginService.register({
			beforeRender: function (chart) {
				if (chart.config.options.showAllTooltips) {
					chart.pluginTooltips = [];
					chart.config.data.datasets.forEach(function (dataset, i) {
						chart.getDatasetMeta(i).data.forEach(function (sector, j) {
							chart.pluginTooltips.push(new Chart.Tooltip({
								_chart: chart.chart,
								_chartInstance: chart,
								_data: chart.data,
								_options: chart.options.tooltips,
								_active: [sector]
							}, chart));
						});
					});

					chart.options.tooltips.enabled = false;
				}
			},
			afterDraw: function (chart, easing) {
				if (chart.config.options.showAllTooltips) {
					if (!chart.allTooltipsOnce) {
						if (easing !== 1)
							return;
						chart.allTooltipsOnce = true;
					}

					chart.options.tooltips.enabled = true;
					Chart.helpers.each(chart.pluginTooltips, function (tooltip) {
						tooltip.initialize();
						tooltip.update();
						tooltip.pivot();
						tooltip.transition(easing).draw();
					});
					chart.options.tooltips.enabled = false;
				}
			}
		});
      ');
      
      
    }
  }


  function AddRow($rows)
  {
    $this->datasets[] = $rows;
  }

  function Query($sql,$labels=false,$fuelleluecken = false,$limit="")
  { 
    $this->labels = $labels;
    $this->sql = $sql; 
    $this->limit= $limit; 
    $this->headings=null;
    $xcol = 0;
    if($limit!=0){
      $page = $this->app->Secure->GetGET("page");
      if(!is_numeric($page)) $page = 1;

      $this->page = $page;
      $this->start= ($page-1) * $this->limit; 

      if(!is_array($sql))$sql.= " LIMIT {$this->start},{$this->limit}";
    }
    $this->searchrow="";
    if(is_array($sql))
    {
      $daten = $sql;
    }else{
      $daten = $this->app->DB->SelectArr($sql);
    }
    if(count($daten)>0){
      foreach($daten[0] as $colkey=>$value)
      {
        $this->headings[]=ucfirst($colkey);
        $this->lheadings[] = $colkey;
        $empty[$colkey] = 0;
      }
    }
    $first = $daten[0][$this->lheadings[$xcol]];
    $mysqlformat = true;
    if(strpos($first,'.') !== false)$mysqlformat = false;
    $last = $daten[count($fuelleluecken) - 1][$this->lheadings[$xcol]];
    if($fuelleluecken && is_array($fuelleluecken) &&  isset($fuelleluecken['typ']))
    {
      $von = false;
      $bis = false;
      $zeitraum = false;
      if(isset($fuelleluecken['von']))
      {
        $von = $fuelleluecken['von'];
      }
      if(isset($fuelleluecken['bis']))
      {
        $bis = $fuelleluecken['bis'];
      }
      if(isset($fuelleluecken['zeitraum']))
      {
        $zeitraum = $fuelleluecken['zeitraum'];
      }
      if($von || $bis || $zeitraum)
      {
        switch($fuelleluecken['typ'])
        {
          case 'tag':
          case 'tage':
            if(!$von && !$bis)
            {
              $bis = date('Y-m-d');
              $date=new DateTime(date('Y-m-d'));
              $date->sub(new DateInterval('P'.$zeitraum.'D'));
              $von = $date->format('Y-m-d');
              
            }elseif(!$von)
            {
              if(strpos($bis, '.') !== false)$bis = $this->app->String->Convert($bis, "%1.%2.%3","%3-%2-%1");
              $date=new DateTime($bis);
              $date->sub(new DateInterval('P'.$zeitraum.'D'));
              $von = $date->format('Y-m-d');
            }elseif(!$bis)
            {
              if(strpos($von, '.') !== false)$von = $this->app->String->Convert($von, "%1.%2.%3","%3-%2-%1");
              $date=new DateTime($von);
              $date->add(new DateInterval('P'.$zeitraum.'D'));
              $bis = $date->format('Y-m-d');
            }
            if($von && $bis)
            {
              $datetime1 = new DateTime($bis);
              $datetime2 = new DateTime($von);
              $interval = $datetime1->diff($datetime2);
              $zeitraum = $interval->format('%a');
              foreach($daten as $key => $data)
              {
                $aktdat = $data[$this->lheadings[$xcol]];
                if(strpos($aktdat,'.') !== false)$aktdat = $this->app->String->Convert($aktdat ,"%1.%2.%3","%3-%2-%1");
                $datumzuid[$aktdat] = $key;
              }
              $aktdat = $datetime2;
              for($i = 0; $i <= $zeitraum; $i++)
              {
                if($i > 0)$aktdat->add(new DateInterval('P1D'));
                $aktdatstr = $aktdat->format('Y-m-d');
                if(isset($datumzuid[$aktdatstr]))
                {
                  $this->datasets[] = $daten[$datumzuid[$aktdatstr]];
                }else{
                  if($mysqlformat)
                  {
                    $empty[$this->lheadings[$xcol]] = $aktdatstr;
                  }else{
                    $empty[$this->lheadings[$xcol]] = $this->app->String->Convert($aktdatstr,"%3-%2-%1","%1.%2.%3");
                  }
                  $this->datasets[] = $empty;
                }
              }
            }
          break;
          case 'monat':
          case 'monate':
            if(!$von && !$bis)
            {
              $bis = date('m/Y');
              $date=new DateTime(date("Y-m-d"));
              $date->sub(new DateInterval('P'.($zeitraum-1).'M'));
              $von = $date->format('m/Y');
              
            }elseif(!$von)
            {
              if(strpos($bis, '.') !== false)$bis = $this->app->String->Convert($bis, "%1.%2.%3","%2\/%3");
              $date=new DateTime($bis);
              $date->sub(new DateInterval('P'.($zeitraum-1).'M'));
              $von = $date->format('m/Y');
            }elseif(!$bis)
            {
              if(strpos($von, '.') !== false)$von = $this->app->String->Convert($von, "%1.%2.%3","%2\/%3");
              $date=new DateTime($von);
              $date->add(new DateInterval('P'.($zeitraum-1).'M'));
              $bis = $date->format('m/Y');
            }
            if($von && $bis)
            {
              
              $von = $this->app->String->Convert($von,"%2\/%3","%3-%2-01");
              $bis = $this->app->String->Convert($bis,"%2\/%3","%3-%2-01");
              $datetime1 = new DateTime($bis);
              $datetime2 = new DateTime($von);
              $interval = $datetime1->diff($datetime2);

              $zeitraum = ($interval->format('%d') > 27?1:0)+$interval->format('%m')+12*$interval->format('%y');

              $_zeitbis = new DateTime($bis);
              $_zeitbis->add(new DateInterval('P'.$zeitraum.'M'));
              if($datetime1->format('m/Y') != $_zeitbis->format('m/Y'))$zeitraum++;

              foreach($daten as $key => $data)
              {
                $aktdat = $data[$this->lheadings[$xcol]];
                if(strpos($aktdat,'.') !== false)$aktdat = $this->app->String->Convert($aktdat ,"%1.%2.%3","%2\/%3");
                $datumzuid[$aktdat] = $key;
              }
              $aktdat = $datetime2;
              for($i = 0; $i < $zeitraum; $i++)
              {
                if($i > 0)$aktdat->add(new DateInterval('P1M'));
                $aktdatstr = $aktdat->format('m/Y');
                if(isset($datumzuid[$aktdatstr]))
                {
                  $this->datasets[] = $daten[$datumzuid[$aktdatstr]];
                }else{
                  if($mysqlformat)
                  {
                    $empty[$this->lheadings[$xcol]] = $aktdatstr;
                  }else{
                    $empty[$this->lheadings[$xcol]] = $this->app->String->Convert($aktdatstr,"%1.%2.%3","%2\/%3");
                  }
                  $this->datasets[] = $empty;
                }
              }
            }
          
          break;
        }
      }else{
        $this->datasets = $daten;
      }
    }
    else
    {
      $this->datasets = $daten;
    }
  }
  
  function kommulieren()
  {
    if($this->datasets)
    {
      
      foreach($this->datasets as $k => $row)
      {
        $first = true;
        foreach($row as $key => $v)
        {
          if($first)
          {
            $first = false;
          }else{
            if(!isset($sum[$key]))$sum[$key] = 0;
            $sum[$key] += $v;
            $this->datasets[$k][$key] = $sum[$key];
          }
        }
      }
    }
  }
  
  function replaceLabelsDate($label)
  {
    if(preg_match_all('/^[1-2]{1}[0-9]{3}\-[0-1]{1}[0-9]{1}\-[0-3]{1}[0-9]{1}$/', $label, $erg))$label = $this->app->String->Convert($label, '%1-%2-%3', '%3.%2.%1');
    return $label;
  }
  
  // Fügt $value in $array ein, an der Stelle $index
  function array_insert($array, $index, $value)
  {
    return array_merge(array_slice($array, 0, $index), $value, array_slice($array, $index));
  }  

  function DisplayWithBox($parsetarget,$id,$name,$width = "300",$height = "250",$style="")
  {
    $this->calcbcolors();
    $_width = $width;
    $this->app->Tpl->Add($parsetarget, '<div class="dashbox dashbox_grey" id="db'.$id.'" style="width:98%; display:block;">
    '.($name?'<div class="dashbox_header" style="display:block;">'.$name.'</div>':'').'
    <div class="dashbox_content" width="100%" style="display:block;">');
    switch($this->type)
    {
      case 'polarArea':
      case 'doughnut':
        if(count($this->datasets) > 0 && $this->labels)
        {
          $this->app->Tpl->Add($parsetarget,'<ul class="chart_legendbox" style="display:block;position:relative;padding-top:2px;padding-right:2px;margin:0;min-height:50px;float:right;list-style-type:none;font-size:65%;">');
          $j = 0;
          $i = 1;
          $sum = 0;
          foreach($this->datasets as $row)
          {
            $el = (float)$row[$this->lheadings[$i]];
            $sum += $el;
          }
          
          foreach($this->datasets as $row)
          {
            $_label = $row[$this->lheadings[0]];
            $wert = $row[$this->lheadings[$i]];
            $proz = 100*($sum > 0?($wert / $sum):0);
            if($this->colors && count($this->colors) > 0);
            
            $farbe = $this->colors[$j % count($this->colors)];
            $this->app->Tpl->Add($parsetarget,'<li><span style="background-color:'.$farbe.';width:10px;left:20px;position:absolute;height:10px;margin:0;display:inline-block;"></span><span style="text-align:left;width:100%;display:inline-block;">'.$_label.' '.number_format($wert,2,',','.').' ('.round($proz,1).'%)</span></li>');
            $j++;
          } 
          $this->app->Tpl->Add($parsetarget,'</ul>');
        }
      break;
    }
    $this->Display($parsetarget, $id, $width,(int)$height-50, $style);
    $this->app->Tpl->Add($parsetarget,'</div></div><div style="clear:both;"></div>');
  }

  function calcbcolors($alpha = 0.1)
  {
    if($this->colorcol)
    {
      if(isset($this->lheadings[$this->colorcol-1]))
      {
        foreach($this->datasets as $k => $row)
        {
          if(isset($row[$this->lheadings[$this->colorcol-1]]) && $row[$this->lheadings[$this->colorcol-1]] && strtolower(str_replace('#','',$row[$this->lheadings[$this->colorcol-1]])) != 'ffffff')
          {
            
            $this->colors[$k] = str_replace('#','',$row[$this->lheadings[$this->colorcol-1]]);
          }
        }
      }
    }
    foreach($this->colors as $key => $v)
    {
      $v = strtoupper(str_replace('#','',$v));
      if(strlen($v) == 3) $v = $v[0].$v[0].$v[1].$v[1].$v[2].$v[2];
      $this->colors[$key] = '#'.$v;
      $r = (int)hexdec($v[0].$v[1]);
      $g = (int)hexdec($v[2].$v[3]);
      $b = (int)hexdec($v[4].$v[5]);
      $this->bcolors[$key] = 'rgba('.$r.','.$g.','.$b.','.$alpha.')';
    }
  }
  
  function Display($parsetarget,$id,$width = "300",$height = "200",$style="")
  {
    $this->calcbcolors();
    $this->app->Tpl->Add($parsetarget, '<canvas id="'.$id.'" width="'.$width.'" height="'.$height.'"></canvas>
      <script>
      var myChart'.$id.' = null;
      
      var ctx'.$id.' = document.getElementById("'.$id.'");
      
      setTimeout(function(){
      myChart'.$id.' = new Chart(ctx'.$id.', {
          type: \''.$this->type.'\',
      animation:{
              animateScale:'.($this->animate?'true':'false').',
              duration:'.$this->duration.'
          },

data: {
      ');
      
    switch($this->type)
    {
      case 'line':
        
        if($this->labels)
        {
          $this->app->Tpl->Add($parsetarget,'labels: ["');
          if(count($this->datasets)>0){
            foreach($this->datasets as $row){
              foreach($row as $col)
              {
                $tmplabel[] = $this->replaceLabelsDate($col);
                break;
              }
            }
            $this->app->Tpl->Add($parsetarget, implode('","',$tmplabel));
          }
          
          $this->app->Tpl->Add($parsetarget,'"],');
        }
        $this->app->Tpl->Add($parsetarget,'datasets: [');
        for($i = 0; $i < count($this->headings); $i++)
        {
          if($i > 0 || !$this->labels)
          {
            $this->app->Tpl->Add($parsetarget, '{
            label: "'.$this->headings[$i].'",
            
                        '.(!is_null($this->yAxes)?'
                yAxisID: "y-axis-'.$this->yAxes[$i-($this->labels?1:0)].'",
            ':'').'
            
            fill: '.($this->background?'true':'false').',
            lineTension: 0.1,
            
            borderColor:"'.$this->colors[$i-($this->labels?1:0)].'",
            
            borderCapStyle: "butt",
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: "miter",
            backgroundColor: "'.$this->bcolors[$i-($this->labels?1:0)].'",
            pointBackgroundColor: "'.$this->colors[$i-($this->labels?1:0)].'",
            pointBorderWidth: 1,
            pointHoverRadius: 5,

            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [');
            if(count($this->datasets) > 0)
            {
              foreach($this->datasets as $row)
              {
                $els[] = $row[$this->lheadings[$i]];
              }
              
              $this->app->Tpl->Add($parsetarget,implode(',', $els));
              unset($els);
            }
            
            $this->app->Tpl->Add($parsetarget,"],}\r\n");
            if($i < count($this->headings) - 1)$this->app->Tpl->Add($parsetarget,',');
          }
          
          
        }
        $this->app->Tpl->Add($parsetarget,"]\r\n");
        $this->app->Tpl->Add($parsetarget,'},
           options: {
                  xAxes: [{
                      display: false
                  }],responsive: true                  ,scales: {
                      xAxes: [{
                          '.($this->stacked?'stacked: true,':'').'
                      }],
                      yAxes: [
                      '.(!is_null($this->yAxes)?'
                      {
                          position: "left",
                          type:"linear",
                          display:true,
                          id: "y-axis-1"
                          '.($this->stacked?',stacked: true':'').'
                      
                      },{
                          position: "right",
                          type:"linear",
                          display:true,
                      id: "y-axis-2"
                          '.($this->stacked?',stacked: true':'').'
                      
                      }
                      ':'
                        {
                          '.($this->stacked?'stacked: true':'').'
                        }
                      ').'
                      
                      ]
                  },    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
        	var dataset = data.datasets[tooltipItem.datasetIndex];
          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
            return previousValue + currentValue;
          });
          var currentValue = dataset.data[tooltipItem.index];
          var name = dataset.label;
          name = name  +\': \';
          //name = \'\';
          
          var x = parseFloat(currentValue);
          var k = (x.toFixed(2)).toString().replace(\'.\',\',\');
          var anzstellen = k.length;
          var vorzeichen = 0;           
          if(k.substring(0,1) == \'-\')
          {
            vorzeichen = 1;
          }
          if(anzstellen - 1 <= 6){
            currentValue = k;
          }else{
            var vorzeichenstring = \'\';
            if(vorzeichen)vorzeichenstring = k.substring(0, 1);
            var vorkomma = k.substring(vorzeichen, anzstellen - 3);
            var ret = vorzeichenstring;
            var modstellen = vorkomma.length % 3;
            if(modstellen > 0)ret = ret + vorkomma.substring(0, modstellen)+\'.\';
            var nachkomma = k.substring(anzstellen - 3, anzstellen);
            
            var i = 0;
            for(i = 0; i < Math.floor(vorkomma.length / 3); i++)
            {
              if(i > 0)ret = ret + \'.\';
              ret = ret + vorkomma.substring(i*3+modstellen, (i+1)*3+modstellen);
            }
            ret = ret+nachkomma;
            currentValue = ret;
          }

          return name+currentValue;
        }
      },backgroundColor: "rgba(0,0,0,0.5)"
    }
              }

          });');

      break;
      case 'bar':
        if($this->labels)
        {
          $this->app->Tpl->Add($parsetarget,'labels: ["');
          if(count($this->datasets)>0){
            foreach($this->datasets as $row){
              foreach($row as $col)
              {
                $tmplabel[] = $this->replaceLabelsDate($col);
                break;
              }
            }
            $this->app->Tpl->Add($parsetarget, implode('","',$tmplabel));
          }
          
          $this->app->Tpl->Add($parsetarget,'"],');
        }
        $this->app->Tpl->Add($parsetarget,'datasets: [');
        for($i = 0; $i < count($this->headings); $i++)
        {
          if($i > 0 || !$this->labels)
          {
            $this->app->Tpl->Add($parsetarget, '{
            label: "'.$this->headings[$i].'",
            backgroundColor:"'.$this->colors[$i-($this->labels?1:0)].'",
            
            '.(!is_null($this->yAxes)?'
                yAxisID: "y-axis-'.$this->yAxes[$i-($this->labels?1:0)].'",
            ':'').'
            data: [');
            if(count($this->datasets) > 0)
            {
              foreach($this->datasets as $row)
              {
                $els[] = $row[$this->lheadings[$i]];
              }
              
              $this->app->Tpl->Add($parsetarget,implode(',', $els));
              unset($els);
            }
            
            $this->app->Tpl->Add($parsetarget,"],}\r\n");
            if($i < count($this->headings) - 1)$this->app->Tpl->Add($parsetarget,',');
          }
          
          
        }
        $this->app->Tpl->Add($parsetarget,"]\r\n");
        $this->app->Tpl->Add($parsetarget,'},
           options: {
                xAxes: [{
                    display: false
                }]
                
                
                  ,scales: {
                      xAxes: [{
                          '.($this->stacked?'stacked: true,':'').'
                      }],
                      yAxes: [
                      '.(!is_null($this->yAxes)?'
                      {
                          position: "left",
                          type:"linear",
                          display:true,
                          id: "y-axis-1"
                          '.($this->stacked?',stacked: true':'').'
                      
                      },{
                          position: "right",
                          type:"linear",
                          display:true,
                      id: "y-axis-2"
                          '.($this->stacked?',stacked: true':'').'
                      
                      }
                      ':'
                        {
                          '.($this->stacked?'stacked: true':'').'
                        }
                      ').'
                      
                      ]
                  },    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
        	var dataset = data.datasets[tooltipItem.datasetIndex];
          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
            return previousValue + currentValue;
          });
          var currentValue = dataset.data[tooltipItem.index];
          var name = dataset.label;
          name = name  +\': \';
          //name = \'\';
          
          var x = parseFloat(currentValue);
          var k = (x.toFixed(2)).toString().replace(\'.\',\',\');
          var anzstellen = k.length;
          var vorzeichen = 0;           
          if(k.substring(0,1) == \'-\')
          {
            vorzeichen = 1;
          }
          if(anzstellen - 1 <= 6){
            currentValue = k;
          }else{
            var vorzeichenstring = \'\';
            if(vorzeichen)vorzeichenstring = k.substring(0, 1);
            var vorkomma = k.substring(vorzeichen, anzstellen - 3);
            var ret = vorzeichenstring;
            var modstellen = vorkomma.length % 3;
            if(modstellen > 0)ret = ret + vorkomma.substring(0, modstellen)+\'.\';
            var nachkomma = k.substring(anzstellen - 3, anzstellen);
            
            var i = 0;
            for(i = 0; i < Math.floor(vorkomma.length / 3); i++)
            {
              if(i > 0)ret = ret + \'.\';
              ret = ret + vorkomma.substring(i*3+modstellen, (i+1)*3+modstellen);
            }
            ret = ret+nachkomma;
            currentValue = ret;
          }

          return name+currentValue;
        }
      },backgroundColor: "rgba(0,0,0,0.5)"
    }
                ,responsive: true
            }

          });');

      break;
      case 'polarArea':
      case 'doughnut':
        $this->app->Tpl->Add($parsetarget,'labels: ["');
        if(count($this->datasets)>0){
          foreach($this->datasets as $row){
            foreach($row as $col)
            {
              $tmplabel[] = $col;
              break;
            }
          }
          $this->app->Tpl->Add($parsetarget, implode('","',$tmplabel));
        }
        
        $this->app->Tpl->Add($parsetarget,'"],');
        $this->app->Tpl->Add($parsetarget,'datasets: [');
        if(count($this->datasets) > 0)
        {
          $i = 1;
          if($i > 0 || !$this->labels)
          {
            $this->app->Tpl->Add($parsetarget, '{
            label: "'.$this->headings[$i].'",
            data: [');
            if(count($this->datasets) > 0)
            {
              foreach($this->datasets as $row)
              {
                $els[] = $row[$this->lheadings[$i]];
              }
              
              $this->app->Tpl->Add($parsetarget,implode(',', $els));
              unset($els);
            }
            
            $this->app->Tpl->Add($parsetarget,"],\r\n");

            
            $this->app->Tpl->Add($parsetarget, 'backgroundColor:[');
            if(count($this->datasets) > 0)
            {
              $j = 0;
              foreach($this->datasets as $row)
              {
                if($this->colors && count($this->colors) > 0);
                if($j > 0)$this->app->Tpl->Add($parsetarget,',');
                $this->app->Tpl->Add($parsetarget,'"'.$this->colors[$j % count($this->colors)].'"');
                $j++;
              }
            }
            $this->app->Tpl->Add($parsetarget,"]}\r\n");
            //if($i < count($this->headings) - 1)$this->app->Tpl->Add($parsetarget,',');
          }
        }
        $this->app->Tpl->Add($parsetarget,"]\r\n");
        $this->app->Tpl->Add($parsetarget,'},
          options: {showAllTooltips: true, 
            cutoutPercentage: 50');
        if(!$this->labels || true)
        {
          $this->app->Tpl->Add($parsetarget,'
          ,legend:{display:false}
          ');
        }else{
          $this->app->Tpl->Add($parsetarget,'
    ,legend: {
      position: \'top\',
    }
          ');          
        }
        $this->app->Tpl->Add($parsetarget,    '
        ,showTooltips: true,
        
    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
        	var dataset = data.datasets[tooltipItem.datasetIndex];
          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
            return previousValue + currentValue;
          });
          var currentValue = dataset.data[tooltipItem.index];
          var precentage = Math.floor(((currentValue/total) * 100)+0.5);
          var name = data.labels[tooltipItem.index];
          name = name  +\': \';
          //name = \'\';
                    
          var x = parseFloat(currentValue);
          var k = (x.toFixed(2)).toString().replace(\'.\',\',\');
          var anzstellen = k.length;
          var vorzeichen = 0;           
          if(k.substring(0,1) == \'-\')
          {
            vorzeichen = 1;
          }
          if(anzstellen - 1 <= 6){
            currentValue = k;
          }else{
            var vorzeichenstring = \'\';
            if(vorzeichen)vorzeichenstring = k.substring(0, 1);
            var vorkomma = k.substring(vorzeichen, anzstellen - 3);
            var ret = vorzeichenstring;
            var modstellen = vorkomma.length % 3;
            if(modstellen > 0)ret = ret + vorkomma.substring(0, modstellen)+\'.\';
            var nachkomma = k.substring(anzstellen - 3, anzstellen);
            
            var i = 0;
            for(i = 0; i < Math.floor(vorkomma.length / 3); i++)
            {
              if(i > 0)ret = ret + \'.\';
              ret = ret + vorkomma.substring(i*3+modstellen, (i+1)*3+modstellen);
            }
            ret = ret+nachkomma;
            currentValue = ret;
          }
          return name+currentValue+\' (\'+  precentage + "%)";
        }
      },backgroundColor: "rgba(0,0,0,0.5)"
    },responsive: true,responsiveAnimationDuration:'.$this->duration.'

        
          }

          }); ');
      break;
    }

    
    

    $this->app->Tpl->Add($parsetarget, '
    
    var aktc = 0
    $(\'.chart_legendbox\').each(function(){if(aktc < $(this).height())aktc = $(this).height();
    });
    if(aktc != 0)
    {
      $(\'.chart_legendbox\').each(function(){
        if(aktc < $(this).height())aktc = $(this).height();
          if($(this).height() < aktc)
          {
            $(this).css(\'height\',aktc+\'px\');
          }
      });
    }
    
    }
    
    ,100);</script>');
  }

}


?>
