nummer;name_de;name_en;anabregs_text;
{foreach from=$artikelliste key=keyrow item=artikel}{* Artikel *}
{strip}
{$artikel->nummer|quoteCsv};
{$artikel->name_de|quoteCsv};
{$artikel->name_en|quoteCsv};
{$artikel->anabregs_text|quoteCsv};
{/strip}
{/foreach}
