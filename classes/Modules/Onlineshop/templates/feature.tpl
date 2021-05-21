{namespace key='onlineshops'}
{block name="featuretable"}

	{foreach from=$features key=arrayname item=data}
			{if $arrayname eq "shop"}
				<div id="tabs-1">
			{elseif $arrayname eq "market"}
				<div id="tabs-2">
			{elseif $arrayname eq "misc"}
				<div id="tabs-3">
			{/if}
  	{foreach from=$data key=keytab item=tab}

		  	<table class="onlineshopsfeatures">
			  		{foreach from=$tab key=keyrow item=row}
										{if $keyrow eq "tabheader"}
												{foreach from=$row key=keycol item=col}
													<th>{$col}</th>
												{/foreach}
										{else}
												<tr>
												{foreach from=$row key=keycol item=col}
													{if $keycol eq "tabdescription"}
															<td>{translate key='{keycol}'}{$col|default:$col}{/translate}</td>
													{else}
																<td>
																		{if $col['export'] eq "1" && $col['import'] eq "1"}
																			<img src="themes/new/images/unavailable.png" border="0"
																					 alt="{translate key='not available'}{'nicht verfügbar'|default:'nicht verfügbar'}{/translate}"
																					 title="{translate key='not available'}{'nicht verfügbar'|default:'nicht verfügbar'}{/translate}" />

																		{elseif $col['export'] eq "5"}
																			<img src="themes/new/images/not_in_shop.png" border="0"
																					 alt="{translate key='not available in shop'}{'wird vom Shop nicht unterstützt'|default:'wird vom Shop nicht unterstützt'}{/translate}"
																					 title="{translate key='not available in shop'}{'wird vom Shop nicht unterstützt'|default:'wird vom Shop nicht unterstützt'}{/translate}" />

                                    {elseif ($col['export'] eq "2" || $col['export'] eq "3") && ($col['import'] eq "2" || $col['import'] eq "3")}
																			<img src="themes/new/images/matrix_ok.png" border="0"
																					 alt="{translate key='available'}{'verfügbar'|default:'verfügbar'}{/translate}"
																					 title="{translate key='available'}{'verfügbar'|default:'verfügbar'}{/translate}" />

                                    {elseif ($col['export'] eq "2" || $col['export'] eq "3") && $col['import'] ne "2" && $col['import'] ne "3"}
																			<img src="themes/new/images/exportable.png" border="0"
																					 alt="{translate key='can be exported'}{'kann exportiert werden'|default:'kann exportiert werden'}{/translate}"
																					 title="{translate key='can be exported'}{'kann exportiert werden'|default:'kann exportiert werden'}{/translate}" />

																		{elseif $col['import'] eq "2" || $col['import'] eq "3"}
																			<img src="themes/new/images/importable.png" border="0"
																					 alt="{translate key='can be imported'}{'kann importiert werden'|default:'kann importiert werden'}{/translate}"
																					 title="{translate key='can be imported'}{'kann importiert werden'|default:'kann importiert werden'}{/translate}" />

                                    {elseif $col['export'] eq "4" && $col['import'] eq "4"}
																			<img src="themes/new/images/unknown.png" border="0"
																					 alt="{translate key='unknown'}{'unbekannt'|default:'unbekannt'}{/translate}"
																					 title="{translate key='unknown'}{'unbekannt'|default:'unbekannt'}{/translate}" />
																		{else}
																			<img/>
																		{/if}

                                    {if $col['export'] eq "2" ||  $col['import'] eq "2"}
																			<img src="themes/new/images/editable.png" border="0"
																					 alt="{translate key='edtitable'}{'editierbar'|default:'editierbar'}{/translate}"
																					 title="{translate key='edtitable'}{'editierbar'|default:'editierbar'}{/translate}" />
																		{else}
																			<img/>
																		{/if}
																</td>
														{/if}
												{/foreach}
										{/if}
								</tr>
						{/foreach}
	     	</table>
	  {/foreach}
					<fieldset>
						<legend>{translate key='legend'}{'Legende'|default:'Legende'}{/translate}</legend>
						<table class="onlineshopsfeatureslegend">
							<tr>
								<td>
									<img src="themes/new/images/matrix_ok.png" border="0"
											 alt="{translate key='available'}{'Import und Export verfügbar'|default:'Import und Export verfügbar'}{/translate}"
											 title="{translate key='available'}{'Import und Export verfügbar'|default:'Import und Export verfügbar'}{/translate}" />
                    {translate key='available'}{'Import und Export verfügbar'|default:'Import und Export verfügbar'}{/translate}
								</td>
								<td>
									<img src="themes/new/images/unavailable.png" border="0"
											 alt="{translate key='not available'}{'Nicht verfügbar'|default:'Nicht verfügbar'}{/translate}"
											 title="{translate key='not available'}{'Nicht verfügbar'|default:'Nicht verfügbar'}{/translate}" />
                    {translate key='not available'}{'Nicht verfügbar'|default:'Nicht verfügbar'}{/translate}
								</td>
								<td>
									<img src="themes/new/images/exportable.png" border="0"
											 alt="{translate key='export available'}{'Export aus Xentral verfügbar'|default:'Export aus Xentral verfügbar'}{/translate}"
											 title="{translate key='export available'}{'Export aus Xentral verfügbar'|default:'Export aus Xentral verfügbar'}{/translate}" />
                    {translate key='export available'}{'Export aus Xentral verfügbar'|default:'Export aus Xentral verfügbar'}{/translate}
								</td>
								<td></td>
							</tr>
							<tr>
								<td>
									<img src="themes/new/images/editable.png" border="0"
											 alt="{translate key='toggleable'}{'Ein-/Ausschaltbar'|default:'Ein-/Ausschaltbar'}{/translate}"
											 title="{translate key='toggleable'}{'Ein-/Ausschaltbar'|default:'Ein-/Ausschaltbar'}{/translate}" />
                    {translate key='toggleable'}{'Ein-/Ausschaltbar'|default:'Ein-/Ausschaltbar'}{/translate}
								</td>
								<td>
									<img src="themes/new/images/not_in_shop.png" border="0"
											 alt="{translate key='not available in shop'}{'Im Shop nicht verfügbar'|default:'Im Shop nicht verfügbar'}{/translate}"
											 title="{translate key='not available in shop'}{'Im Shop nicht verfügbar'|default:'Im Shop nicht verfügbar'}{/translate}" />
                    {translate key='not available in shop'}{'Im Shop nicht verfügbar'|default:'Im Shop nicht verfügbar'}{/translate}
								</td>
								<td>
									<img src="themes/new/images/importable.png" border="0"
											 alt="{translate key='import available'}{'Import in Xentral verfügbar'|default:'Import in Xentral verfügbar'}{/translate}"
											 title="{translate key='import available'}{'Import in Xentral verfügbar'|default:'Import in Xentral verfügbar'}{/translate}" />
                    {translate key='import available'}{'Import in Xentral verfügbar'|default:'Import in Xentral verfügbar'}{/translate}
								</td>
								<td>
									<img src="themes/new/images/unknown.png" border="0"
											 alt="{translate key='unknown'}{'Unbekannt'|default:'Unbekannt'}{/translate}"
											 title="{translate key='unknown'}{'Unbekannt'|default:'Unbekannt'}{/translate}" />
                    {translate key='unknown'}{'Unbekannt'|default:'Unbekannt'}{/translate}
								</td>
							</tr>
						</table>
					</fieldset>
					</div>
				{/foreach}
{/block}
