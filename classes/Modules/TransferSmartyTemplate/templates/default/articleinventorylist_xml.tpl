<?xml version="1.0" encoding="utf-8"?>
<artikelliste>
{foreach from=$artikelliste key=keyrow item=artikel}{* Artikel *}
  {if !$artikel->mindesthaltbarkeitsdatum}<artikel>
    <nummer>{$artikel->nummer|escapeXml}</nummer>
    <lagerzahl>{$artikel->menge|escapeXml}</lagerzahl>
    <lagerplatz>{$artikel->kurzbezeichnung|escapeXml}</lagerplatz>
  </artikel>
{/if}
{/foreach}
</artikelliste>
