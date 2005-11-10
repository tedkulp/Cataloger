<div class="item">
<p>{$title}</p>
<table><tr><td><img id="item_image" src="{$image_1_url}" /></td>
<td>{section name=ind loop=$image_url_array}
<a href="javascript:repl('{$image_url_array[ind]}')"><img src="{$image_thumb_url_array[ind]}"></a>
{/section}</td></tr></table>
<p>Weight: {$weight}</p>
<p>Medium: {$mediummedia}</p>
<p>Dimensions: {$dimensions}</p>
<p>Price: {$price}</p>
<p>In Stock: {$instock}</p>

{if $notes != ""}<p>{$notes}</p>{/if}
{literal}
<script type="text/javascript">
// <![CDATA[
function repl(img)
   {ldelim}
   document.item_image.src=img;
   {rdelim}
// ]]>
</script>
{/literal}
</div>