{namespace key='customreport'}
{block name='paramtable'}

	<div class="row" id="reportTileView">
		<div class="row-height">
			<div class="col-xs-12 col-md-8 col-md-height">
				<div class="tile-container tile-columns-3">
					{foreach $reportList as $report}
							{include file='./tile.tpl'}
					{/foreach}
				</div>
			</div>
		</div>
	</div>

{/block}
