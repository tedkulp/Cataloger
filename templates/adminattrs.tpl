{$innernav}
<br />
<h3>{$category}</h3>
{if $message != ''}<h4>{$message}</h4>{/if}
{$startform}
{$tab_headers}
{$start_item_tab}
<div class="pageoverflow">
	<p>{$title_item_attributes_help}</p>
	<table>
		<tr><th>{$title_item_attributes}</th><th>{$title_is_textfield}</th><th>{$title_delete}</th></tr>
	   {foreach from=$attribute_inputs item=entry}
			{if $entry->type == 1}
            <tr><td>{$entry->input}</td><td>{$entry->istext}</td><td>{$entry->delete}{$entry->hidden}</td></tr>
            {/if}
	   {/foreach}
	</table>
</div>
{$end_tab}
{$start_category_tab}
<div class="pageoverflow">
	<p>{$title_category_attributes_help}</p>
	<table>
		<tr><th>{$title_category_attributes}</th><th>{$title_is_textfield}</th><th>{$title_delete}</th></tr>
	   {foreach from=$attribute_inputs item=entry}
			{if $entry->type == 2}
	        <tr><td>{$entry->input}</td><td>{$entry->istext}</td><td>{$entry->delete}{$entry->hidden}</td></tr>
	        {/if}
	   {/foreach}
	</table>
</div>
{$end_tab}
{$start_catalog_tab}
<div class="pageoverflow">
	<p>{$title_catalog_attributes_help}</p>
	<table>
		<tr><th>{$title_catalog_attributes}</th><th>{$title_is_textfield}</th><th>{$title_delete}</th></tr>
	   {foreach from=$attribute_inputs item=entry}
			{if $entry->type == 3}
	        <tr><td>{$entry->input}</td><td>{$entry->istext}</td><td>{$entry->delete}{$entry->hidden}</td></tr>
	        {/if}
	   {/foreach}
	</table>
</div>
{$end_tab}
{$tab_footers}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
