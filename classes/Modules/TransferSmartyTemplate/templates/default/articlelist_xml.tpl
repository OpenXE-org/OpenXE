<?xml version="1.0" encoding="utf-8" ?>
<artikelliste>
{foreach from=$artikelliste key=keyrow item=artikel}{* Artikel *}
  <artikel>
    <nummer>{$artikel->nummer|escapeXml}</nummer>
    <name_de>{$artikel->name_de|escapeXml}</name_de>
    <name_en>{$artikel->name_en|escapeXml}</name_en>
    <anabregs_text>{cdata}{$artikel->anabregs_text}{/cdata}</anabregs_text>
  </artikel>
{/foreach}
</artikelliste>
