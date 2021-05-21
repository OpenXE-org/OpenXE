{namespace key='customreport'}
{block name='columntable'}

		{foreach $columns as $item}
{*			<div class="column-view" data-id="{$item.id|default:0}">*}
{*				{$item.title|default:''}*}
{*			</div>*}
				<tr role="row" class="column-view" data-id="{$item.id|default:0}">
					<td data-varname="key_name">{$item.key_name|default:''}</td>
					<td data-varname="title">{$item.title|default:''}</td>
					<td data-varname="width">{$item.width|default:''}</td>
					<td data-varname="alignment">
						{if $item.alignment eq 'right'}Rechts{/if}
						{if $item.alignment eq 'left'}Links{/if}
						{if $item.alignment eq 'center'}Mitte{/if}
					</td>
					<td data-varname="sum">
						{if $item.sum === 1}Ja{else}Nein{/if}
					</td>
					<td width="1%">
						<a href="#" class="column-edit-button">
							<img src="themes/[THEME]/images/edit.svg" border="0" alt="edit column">
						</a>
					</td>
				</tr>
		{/foreach}

{/block}
