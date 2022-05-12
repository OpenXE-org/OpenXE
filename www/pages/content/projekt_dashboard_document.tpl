
[MESSAGE]

<div style="padding:10px;">

	<div class="filter-box filter-usersave">

		<div class="filter-block filter-reveal">
			<div class="filter-title">{|Filter|}<span class="filter-icon"></span></div>
			<ul class="filter-list">
				<li class="filter-item"><input type="checkbox" id="filterbelegebestellung" /><label for="filterbelegebestellung">{|Bestellungen|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegeangebot" /><label for="filterbelegeangebot">{|Angebot|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegeauftrag" /><label for="filterbelegeauftrag">{|Auftrag|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegepreisanfrage" /><label for="filterbelegepreisanfrage">{|Preisanfrage|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegerechnung" /><label for="filterbelegerechnung">{|Rechnung|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegegutschrift" /><label for="filterbelegegutschrift">{|Gutschrift|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegelieferschein" /><label for="filterbelegelieferschein">{|Lieferschein|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegeproduktion" /><label for="filterbelegeproduktion">{|Produktion|}</label></li>
				<li class="filter-item"><input type="checkbox" id="filterbelegereisekosten" /><label for="filterbelegereisekosten">{|Reisekosten|}</label></li>
				[VORPROFORMARECHNUNGFILTER]
			</ul>
		</div>

		<div class="filter-block filter-inline">
			<div class="filter-title">{|Status|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="filterbelegeangelegt" class="switch">
						<input type="checkbox" id="filterbelegeangelegt" />
						<span class="slider round"></span>
					</label>
					<label for="filterbelegeangelegt">{|angelegt|}</label>
				</li>
				<li class="filter-item">
					<label for="filterbelegefreigegeben" class="switch">
						<input type="checkbox" id="filterbelegefreigegeben" />
						<span class="slider round"></span>
					</label>
					<label for="filterbelegefreigegeben">{|freigegeben|}</label>
				</li>
				<li class="filter-item">
					<label for="filterbelegeabgeschlossen" class="switch">
						<input type="checkbox" id="filterbelegeabgeschlossen" />
						<span class="slider round"></span>
					</label>
					<label for="filterbelegeabgeschlossen">{|abgeschlossen|}</label>
				</li>
				<li class="filter-item">
					<label for="filterbelegeversendet" class="switch">
						<input type="checkbox" id="filterbelegeversendet" />
						<span class="slider round"></span>
					</label>
					<label for="filterbelegeversendet">{|versendet|}</label>
				</li>
				<li class="filter-item">
					<label for="filterbelegestorniert" class="switch">
						<input type="checkbox" id="filterbelegestorniert" />
						<span class="slider round"></span>
					</label>
					<label for="filterbelegestorniert">{|storniert|}</label>
				</li>
			</ul>
		</div>
	</div>


	[DOCUMENT_TABLE]

</div>
<script type="application/javascript">
    function clickdashboardbelegeedit(el)
    {
        var span = $(el).parent().find('span').first();
        var ida = $(span).html().split('-');
        var eins = 1;
        var nu = 0;
        var beleg = ida[nu];
        var belegid = parseInt(ida[eins]);
        switch(beleg)
        {
            case '1':
                beleg = 'auftrag';
                break;
            case '2':
                beleg = 'rechnung';
                break;
            case '3':
                beleg = 'gutschrift';
                break;
            case '4':
                beleg = 'angebot';
                break;
            case '5':
                beleg = 'bestellung';
                break;
            case '6':
                beleg = 'produktion';
                break;
            case '7':
                beleg = 'lieferschein';
                break;
            case '8':
                beleg = 'preisanfrage';
                break;
            case '9':
                beleg = 'proformarechnung';
                break;
            case '10':
                beleg = 'reisekosten';
                break;
            default:
                beleg = '';
                break;
        }
        if(belegid > 0 && beleg != '')
        {
            window.open('index.php?module='+beleg+'&action=edit&id='+belegid,'_blank');
        }
    }
</script>
