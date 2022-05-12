<?xml version="1.0" encoding="utf-8"?>
<salesreport_list>
{foreach from=$salesreportlist key=keyrow item=auftrag}{* Belege *}
  <auftrag>
    <belegnr>{$auftrag->auftrag|escapeXml}</belegnr>
    <artikelnummer>{$auftrag->nummer|escapeXml}</artikelnummer>
    <ean>{$auftrag->ean|escapeXml}</ean>
    <name_de>{$auftrag->name_de|escapeXml}</name_de>
    <menge>{$auftrag->menge|escapeXml}</menge>
    <verkaufspreis>{$auftrag->verkaufspreis|escapeXml}</verkaufspreis>
    <rabatt>{$auftrag->rabatt|escapeXml}</rabatt>
    <einkaufspreis>{$auftrag->einkaufspreis|escapeXml}</einkaufspreis>
  </auftrag>
{/foreach}
</salesreport_list>
