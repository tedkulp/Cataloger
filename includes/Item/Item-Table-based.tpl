<div class="item"><p>{$title}</p><table><tr><td><img id="item_image" src="{$image_1_url}" /></td><td>{section name=ind loop=$image_url_array}<a href="javascript:repl('{$image_url_array[ind]}')"><img src="{$image_thumb_url_array[ind]}" /></a>{/section}</td></tr></table>{section name=at loop=$attrlist}<p><strong>{$attrlist[at].name}</strong>: {eval var=$attrlist[at].key}</p>{/section}{literal}<script type="text/javascript">function repl(img)   {   document.item_image.src=img;   }</script>{/literal}</div>