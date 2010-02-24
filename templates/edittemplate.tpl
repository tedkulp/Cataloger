{if $message!=''}<p class="pagemessage">{$message}</p>{/if}
{$innernav}
<br />
<h3>{$op}</h3>

{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$title_title}:</p>
		<p class="pageinput">{$input_title}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$title_template_type}:</p>
		<p class="pageinput">{$input_template_type}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$title_template}:</p>
		<p class="pageinput">{$input_template}</p>
	</div>
	<div id="available">{$avail_attrs}</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit} {$apply}</p>
	</div>
{$endform}
