<h1>{$title}</h1>
{$notes}
<table style="border: solid 1px black;"><tr>
{assign var="cols" value="3"}
    {section name=numloop loop=$items}
        <td style="width: 200px;"><a href="{$items[numloop].link}"><img src="/uploads/images/catalog/{$items[numloop].image}" alt="{$items[numloop].title}"/></a><br /><a href="{$items[numloop].link}">{$items[numloop].title}</a></td>
        {if not ($smarty.section.numloop.rownum mod $cols)}
                {if not $smarty.section.numloop.last}
                        </tr><tr>
                {/if}
        {/if}
        {if $smarty.section.numloop.last}
                {math equation = "n - a % n" n=$cols a=$items|@count assign="cells"}
                {if $cells ne $cols}
                {section name=pad loop=$cells}
                        <td style="width: 200px;">&nbsp;</td>
                {/section}
                {/if}
                </tr>
        {/if}
    {/section}
</table>
