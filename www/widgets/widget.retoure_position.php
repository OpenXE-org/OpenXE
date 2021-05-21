<?php
include ("_gen/widget.gen.retoure_position.php");

class WidgetRetoure_position extends WidgetGenRetoure_position 
{
  private $app;
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $this->app->erp->AnzeigeFreifelderPositionen($this->form);

    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("zolltarifnummer","zolltarifnummer",1);

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->app->YUI->DatePicker("lieferdatum");

    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("menge",$this,"ReplaceMenge");

    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("beschreibung","belege");
      $this->app->YUI->CkEditor("bemerkung","basic");
    }

    $this->app->erp->ArtikelFreifeldBezeichnungen();

    $field = new HTMLInput("nummer","text","",50);
    $field->readonly="readonly";

    $this->form->NewField($field);
    $id = $this->app->Secure->GetGET('id');
    $position = $this->app->DB->SelectRow(
      sprintf(
        'SELECT grund, retoure FROM retoure_position WHERE id = %d',
        $id
      )
    );
    $grund = !empty($position['grund'])?$position['grund']:'';
    $retoure = !empty($position['retoure'])?$position['retoure']:0;
    $projekt = $this->app->DB->Select(sprintf('SELECT projekt FROM retoure WHERE id =%d', $retoure));
    $rmavorlagen = $this->app->DB->SelectFirstCols(
      sprintf(
        'SELECT bezeichnung 
          FROM rma_vorlagen_grund 
          WHERE ausblenden = 0 AND (projekt = 0 OR projekt = %d) 
          ORDER BY bezeichnung',
        $projekt
      )
    );
    $options = [];

    if(!in_array($grund, $rmavorlagen)) {
      $options = [$grund=>$grund];
    }
    foreach($rmavorlagen as $vorlage) {
      $options[$vorlage] = $vorlage;
    }
    $field = new HTMLSelect('grund',0,'grund');
    $field->AddOptionsSimpleArray($options);
    $this->form->NewField($field);

  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }
  
  function ReplaceMenge($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMenge($db,$value,$fromform);
  }  


  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT retoure, id FROM retoure_position");
    $table->Display($this->parsetarget);
  }



  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
