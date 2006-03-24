{$innernav}
<br />
<h3>{$category}</h3>
{if $message!=''}<h4>{$message}</h4>{/if}
{$startform}
	<div class="pageoverflow">
        <p class="pagetext">{$title_category_recurse}</p>
        <p class="pageinput">{$input_category_recurse}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_items_per_page}</p>
        <p class="pageinput">{$input_items_per_page}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_sort_order}</p>
        <p class="pageinput">{$input_item_sort_order}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
