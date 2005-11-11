{$innernav}
<br />
<h3>{$category}</h3>
{if $message != ''}<h4>{$message}</h4>{/if}
{$startform}
{$tab_headers}
{$start_item_tab}
<p>{$title_item_attributes_help}</p>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_attributes}</p>
        <p class="pageinput">
	   {foreach from=$attribute_inputs item=entry}
			{if $entry->type == 1}
            {$entry->input}<br /><br />
            {/if}
	   {/foreach}
        </p>
	</div>
{$end_tab}
{$start_category_tab}
<p>{$title_category_attributes_help}</p>
	<div class="pageoverflow">
        <p class="pagetext">{$title_category_attributes}</p>
        <p class="pageinput">
	   {foreach from=$attribute_inputs item=entry}
			{if $entry->type == 2}
            {$entry->input}<br /><br />
            {/if}
	   {/foreach}
        </p>
	</div>
{$end_tab}
{$start_catalog_tab}
<p>{$title_catalog_attributes_help}</p>
	<div class="pageoverflow">
        <p class="pagetext">{$title_catalog_attributes}</p>
        <p class="pageinput">
	   {foreach from=$attribute_inputs item=entry}
			{if $entry->type == 3}
            {$entry->input}<br /><br />
            {/if}
	   {/foreach}
        </p>
	</div>
{$end_tab}
{$tab_footers}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}</p>
	</div>
{$endform}
