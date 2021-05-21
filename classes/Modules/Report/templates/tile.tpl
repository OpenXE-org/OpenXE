
<div class="tile {if $report.readonly eq 1}original-report{else}custom-report{/if}">
	<div class="tile-header">
		<div class="tile-header-icons-left">

			{if $report.is_favorite eq 1}
				<a class="tile-icon svg-favorite favorite-on" data-id="{$report.id|default:0}" data-favorite="1" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
						<path d="M0 0h24v24H0z" fill="none"/>
						<path class="svg-fill" fill="#929292" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
						<path d="M0 0h24v24H0z" fill="none"/>
					</svg>
				</a>
			{else}
				<a class="tile-icon svg-favorite" data-id="{$report.id|default:0}" data-favorite="0" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
						<path d="M0 0h24v24H0z" fill="none"/>
						<path class="svg-fill" fill="#929292" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
						<path d="M0 0h24v24H0z" fill="none"/>
					</svg>
				</a>
			{/if}

		</div>
		<div class="tile-title">{$report.category|default:''}</div>
		<div class="tile-header-icons-right">

			<a href="#" class="tooltip-inline">
				<span class="icon icon-tooltip"></span>
				<span class="tooltip" role="tooltip">
            <span class="tooltip-title">Beschreibung:</span>
            <span class="tooltip-content">{$report.description|default:''}</span>
        </span>
			</a>
			<a class="tile-icon table-button-edit" href="?module=report&action=edit&id={$report.id|default:0}">
				<img src="./themes/{$theme|default:'new'}/images/edit.svg" alt="Bearbeiten">
			</a>
			<a class="tile-icon table-button-delete" href="#" data-report-id="{$report.id|default:0}">
				<img src="./themes/{$theme|default:'new'}/images/delete.svg" alt="Löschen">
			</a>
			<a class="tile-icon table-button-copy" href="#" data-report-id="{$report.id|default:0}">
				<img src="./themes/{$theme|default:'new'}/images/copy.svg" alt="Kopieren">
			</a>
		</div>
	</div>

{*	<div class="tile-body">*}
		<a class="tile-body" href="?module=report&action=view&id={$report.id|default:0}" data-id="{$report.id|default:0}">
			<h1>{$report.name|default:''}</h1>
		</a>
{*	</div>*}

	<div class="tile-footer">

		<a class="reportbutton active" href="?module=report&action=view&id={$report.id|default:0}">
			<span>TABLE</span>
		</a>

		{if $report.allow_download eq 1 and $report.allow_csv eq 1}
		<a class="reportbutton download-csv-button {if $report.allow_download eq 1 and $report.allow_csv eq 1}active{/if}"
			 href="#" data-format="csv" data-report-id="{$report.id|default:0}">
			<span>CSV</span>
		</a>
		{/if}

		{if $report.allow_download eq 1 and $report.allow_pdf eq 1}
		<a class="reportbutton download-csv-button {if $report.allow_download eq 1 and $report.allow_pdf eq 1}active{/if}"
			 href="#" data-format="pdf" data-report-id="{$report.id|default:0}">
			<span>PDF</span>
		</a>
		{/if}

		{if $report.allow_chart eq 1}
			<a class="reportbutton chart-button {if $report.allow_chart eq 1 }active{/if}"
				 href="#" data-report-id="{$report.id|default:0}">
				<span>CHART</span>
			</a>
		{/if}

	</div>
</div>
