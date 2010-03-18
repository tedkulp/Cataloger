{$innernav}
<br />
<h3>{$category}</h3>
{if $message!=''}<p class="pagemessage">{$message}</p>{/if}
{$startform}
{$tab_headers}
{$start_item_image_tab}
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_count}</p>
        <p class="pageinput">{$input_item_image_count}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_show_only_existing_images}</p>
        <p class="pageinput">{$input_show_only_existing_images}</p>
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
        <p class="pagetext">{$title_item_image_size_category}</p>
        <p class="pageinput">{$input_item_image_size_category}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_image_size_catalog}</p>
        <p class="pageinput">{$input_item_image_size_catalog}</p>
	</div>
{$end_tab}
{$start_file_tab}
	<div class="pageoverflow">
	    <p class="pagetext">{$title_item_file_count}</p>
	    <p class="pageinput">{$input_item_file_count}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_item_file_types}</p>
        <p class="pageinput">{$input_item_file_types}</p>
	</div>
{$end_tab}
{$start_category_image_tab}
	<div class="pageoverflow">
        <p class="pagetext">{$title_category_image_count}</p>
        <p class="pageinput">{$input_category_image_count}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_category_image_size_hero}</p>
        <p class="pageinput">{$input_category_image_size_hero}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_category_image_size_thumbnail}</p>
        <p class="pageinput">{$input_category_image_size_thumbnail}</p>
	</div>
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
        <p class="pagetext">{$title_flush_cats}</p>
        <p class="pageinput">{$input_flush_cats}</p>
	</div>
{$end_tab}
{$start_printable_tab}
	<div class="pageoverflow">
        <p class="pagetext">{$title_printable_sort_order}</p>
        <p class="pageinput">{$input_printable_sort_order}</p>
	</div>
{$end_tab}
{$start_image_tab}
	<div class="pageoverflow">
        <p class="pagetext">{$title_show_missing_images}</p>
        <p class="pageinput">{$input_show_missing_images}</p>
	</div>
{$end_tab}
{$start_path_tab}
	<div class="pageoverflow">
		{$path_help}
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_image_upload_path}</p>
        <p class="pageinput">{$input_image_upload_path}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_image_proc_path}</p>
        <p class="pageinput">{$input_image_proc_path}</p>
	</div>
	<div class="pageoverflow">
        <p class="pagetext">{$title_file_upload_path}</p>
        <p class="pageinput">{$input_file_upload_path}</p>
	</div>

{$end_tab}
{$tab_footers}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
