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
		<p class="pageinput">{$input_template}<br />
		<div id="available"><strong>{$title_avail_attrs}</strong>:<br />{$avail_attrs}
		<br /><strong>{$title_avail_imattrs}:</strong><br />{$avail_imattrs}
		</div></p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}</p>
	</div>
{$endform}
