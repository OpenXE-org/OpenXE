nummer;mhddatum;lagerzahl;lagerplatz;
{foreach from=$artikelliste key=keyrow item=artikel}{* Artikel *}
{if $artikel->mindesthaltbarkeitsdatum}
{strip}
{$artikel->nummer|quoteCsv};
{$artikel->mhddatum|quoteCsv};
{$artikel->menge|quoteCsv};
{$artikel->kurzbezeichnung|quoteCsv};
{/strip}
{/if}
{/foreach}
