###To find under Apps -> Master data -> Kupferzuschlag

The modul adds extra positions to business documents which contain articles with copper.
The purchase prices of these surcharge articles can be edited quickly with the module 'Tagespreise' (index.php?module=tagespreise)
Theses purchase prices represent the DEL-values (DEL stands for Deutsches Elektrolytkupfer für Leitzwecke)

###Preparation:

The following values get mangaged in the module:

- copper surcharge article (Kupferzuschlagsartikel)
an article which gets addes as surcharge position in all business documents

- Add position (Position einfügen)
There are three posibilities how surcharge articles can be added as positions:
		- a position gets added for every article which is a copper article
		- only one position gets added for all copper articles
		- for every group a surcharge position gets added

- Copper surcharge - offer to order (Kupferzuschlag - Angebot zu Auftrag)
two posibilities:
		- DEL from offer date: if an offer exists the date from it will be used to evaluate the DEL. If there is no offer the present day is used
		- DEL from order date: the date from the order is used

- coppersurcharge - create invoice (Kupferzuschlag Rechnung erstellen)
four posibilities:
		- DEL from order date: date from order, if not found present day
		- DEL from order delivery date: delivery date, if not found present day
		- DEL from invoice date: date from invoice
		- DEL from offer date: offer date, if not found present day

- Where should the values get specified (Wie sollen die Daten gespeichert werden?)
There are two posibilities to save the data needed to specify a copper article:
By the app raw materials (Rohstoffe) or with additional fields in articles (managed in (Grundeinstellungen -> Freifelder))

- article specific copper number (kg/km) (Artikelspezifische Kupferzahl (kg/km))
this field only appears when 'with additional article fields is seleted'. Only fields which are already set in the 'Grundeinstellungen' appear

- delivery costs (in percent) (Bezugskosten (in Prozent))
The current calculation includes always a delivery cost addition. Normally this is 1% for all articles

- standard copper base (Standard Kupferbasis (in EUR pro 100kg))
The current calculation also includes this value, default is 150

- article specific copper base (Artikelspezifische Kupferbasis (in EUR pro 100kg))
if the value from the field before differs in an article another additional field in the article can be assigned here

####Create copper surcharge article:

New surcharge positions need an article as base. Therefore an new one must be created.
This article must be marked as daily price article (the checkbox 'Daily prices'(Tagespreise))
In Artikelbeschreibung (DE) several placeholders can be used to get replaced in the position:
{ARTIKELNUMMER}, {ARTIKELNAME}, {NETPRICE} the net price for the surcharge, {COPPERBASIS} the copper base (see calculation), {COPPERNUMBER} the copper weight (kg/km) (see calculation), {DELVALUE} the DEL value

####Mark an Article as copper base:

Dependinfg on how the module should work (raw materials or additional article fields) the article must be marked as 'Raw material list' (Rohstoffliste) or the additional field(s) must be filled
If raw materials are used, switch to teh slider 'raw materials' (Rohstoffe) and create a new entry with the surcharge article as article and amount as copper weight (kg/km)

####Daily Prices:

in the module daily prices (Tagespreise - index.php?module=tagespreise ), in the slider configuration select the surcharge article in one of the seven possible rows and add a name
In the slider 'overview' (Übersicht) you now can add the newest DEL values every day. DEL values can be found here: http://www.del-notiz.org/ - neu ausgegeben
If a new daily price is added an an older one is existing the old will be changed to invalid and the new one gets added

####Price calculation:

Copper surcharge EUR/km = (copper weight (kg/km) * (DEL + 1% delivery costs)) - copper base / 100

Example:

copper weight/km: 13,00 kg
copper base: 150,00 EUR/100 kg
DEL: 550,00 EUR/100 kg

13 * ((550,00 + (550,00 * 0.01)) - 150,00) / 100 = 52,72 EUR/km

###How the code works:

The logic starts its work by calling the hooks 'ANABREGSNeuberechnen_1' (adding positions)
and 'ANABREGSNeuberechnenEnde' (updating some unneccesary values which are filled between the hooks).

Because there is no relation between different positions of on business document, I always delete all
surcharge positions and create new ones. Time will show if this is not too much and if it wouldn't be better
to add a relation table for positions and their dependencies.

After deleting these positions the code starts collecting all necessary copper positions.
It differentiates between regular articles and part list articles.
The main difference between these types of articles is, that part list elements with copper get always grouped.

After that the prices get calculated and depending on the settings the positions get added.



