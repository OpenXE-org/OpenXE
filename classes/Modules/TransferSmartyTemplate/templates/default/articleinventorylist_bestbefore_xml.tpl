<?xml version="1.0" encoding="utf-8"?>
<artikelliste>
{foreach from=$artikelliste key=keyrow item=artikel}{* Artikel *}
	{if $artikel->mindesthaltbarkeitsdatum}<artikel>
		<nummer>{$artikel->nummer}</nummer>
		<mhd>{$artikel->mhddatum}</mhd>
		<lagerzahl>{$artikel->menge}</lagerzahl>
		<lagerplatz>{$artikel->kurzbezeichnung}</lagerplatz>
  </artikel>
{/if}
{/foreach}
</artikelliste>
