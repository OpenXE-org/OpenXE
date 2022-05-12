<?xml version="1.0" encoding="utf-8"?>
<lieferschein_liste>
{foreach from=$trackingliste key=keyrow item=tracking}{* Belege *}
  <lieferschein>
    <lieferscheinnummer>{$tracking->lieferscheinnummer|escapeXml}</lieferscheinnummer>
    <auftrag>{$tracking->auftrag|escapeXml}</auftrag>
    <tracking>{$tracking->tracking|escapeXml}</tracking>
    <name>{$tracking->name|escapeXml}</name>
    <strasse>{$tracking->strasse|escapeXml}</strasse>
    <plz>{$tracking->plz|escapeXml}</plz>
    <ort>{$tracking->ort|escapeXml}</ort>
    <land>{$tracking->land|escapeXml}</land>
  </lieferschein>
{/foreach}
</lieferschein_liste>
