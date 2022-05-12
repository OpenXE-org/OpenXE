lieferscheinnummer;auftrag;tracking;name;strasse;plz;ort;land;
{foreach from=$trackingliste key=keyrow item=tracking}{* Belege *}
	{strip}
		{$tracking->lieferscheinnummer|quoteCsv};
		{$tracking->auftrag|quoteCsv};
		{$tracking->tracking|quoteCsv};
		{$tracking->name|quoteCsv};
		{$tracking->strasse|quoteCsv};
		{$tracking->plz|quoteCsv};
		{$tracking->ort|quoteCsv};
		{$tracking->land|quoteCsv};
	{/strip}
{/foreach}
