{$innernav}
<br />
<h3>{$section}</h3>
{if $message!=''}<p>{$message}</p>{/if}
{if $itemcount > 0}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>{$title_template}</th>
			<th>{$title_template_type}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->title}</td>
			<td>{$entry->type}</td>
			<td>{$entry->editlink}</td>
			<td>{$entry->deletelink}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{else}
<p>{$notemplates}</p>
{/if}
<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
</div>
