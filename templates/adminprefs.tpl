{$innernav}
<br />
<h3>{$section}</h3>
{if $message!=''}<h4>{$message}</h4>{/if}
{$startform}
{$tab_headers}
{$start_item_image_tab}
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_count}</p>
        <p class="pageinput">{$input_item_image_count}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_size_hero}</p>
        <p class="pageinput">{$input_item_image_size_hero}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_size_thumbnail}</p>
        <p class="pageinput">{$input_item_image_size_thumbnail}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_size_section}</p>
        <p class="pageinput">{$input_item_image_size_section}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_size_catalog}</p>
        <p class="pageinput">{$input_item_image_size_catalog}</p>
	</div>
{$end_tab}
{$start_section_image_tab}
	<div class="pageoverflow">
        <p class="pagetext">{$title_section_image_count}</p>
        <p class="pageinput">{$input_section_image_count}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_section_image_size_hero}</p>
        <p class="pageinput">{$input_section_image_size_hero}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_section_image_size_thumbnail}</p>
        <p class="pageinput">{$input_section_image_size_thumbnail}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_section_recurse}</p>
        <p class="pageinput">{$input_section_recurse}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_items_per_page}</p>
        <p class="pageinput">{$input_items_per_page}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_sort_order}</p>
        <p class="pageinput">{$input_item_sort_order}</p>
	</div>
{$end_tab}
{$start_aspect_tab}
	<div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput" style="width:450px">{$title_aspect_ratio_help}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_force_aspect_ratio}</p>
        <p class="pageinput">{$input_force_aspect_ratio}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_image_aspect_ratio}</p>
        <p class="pageinput">{$input_image_aspect_ratio}</p>
	</div>
{$end_tab}
{$tab_footers}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}</p>
	</div>
{$endform}
