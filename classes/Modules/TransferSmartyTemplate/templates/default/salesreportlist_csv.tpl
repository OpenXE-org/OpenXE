auftrag;nummer;ean;name_de;menge;verkaufspreis;einkaufspreis;
{foreach from=$salesreportlist key=keyrow item=auftrag}
{strip}
{$auftrag->auftrag|quoteCsv};
{$auftrag->nummer|quoteCsv};
{$auftrag->ean|quoteCsv};
{$auftrag->name_de|quoteCsv};
{$auftrag->menge|quoteCsv};
{$auftrag->verkaufspreis|quoteCsv};
{$auftrag->einkaufspreis|quoteCsv};
{/strip}
{/foreach}
