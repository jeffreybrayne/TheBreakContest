{if is_array($cells)}
<tr class="nodrag nodrop">
{foreach from=$cells item="_cell"}
    {if isset($_cell.class)}
        <th class="page_table_footer {$_cell.class}" style="width:{$_cell.width}">{$_cell.title}</th>
    {else}
        <th class="page_table_footer" style="width:{$_cell.width}">{$_cell.title}</th>
    {/if}
{/foreach}
</tr>
{/if}

</table>
</td>
</tr>    
