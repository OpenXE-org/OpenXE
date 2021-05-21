{namespace key='customreport'}
{block name='paramtable'}
    {foreach $parameters as $item}
			<div class="parameter-view" data-id="{$item.id|default:0}" data-key="{$item.varname|default:''}"
			data-value="{$item.default_value|default:''}">
				{$item.varname|default:''}={$item.default_value|default:''}
			</div>
    {/foreach}
{/block}
