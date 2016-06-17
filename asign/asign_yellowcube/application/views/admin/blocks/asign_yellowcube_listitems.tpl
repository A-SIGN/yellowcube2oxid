[{ if $listitem->oxorder__oxstorno->value == 1 }]
    [{assign var="listclass" value=listitem3 }]
[{else}]
    [{ if $listitem->blacklist == 1}]
        [{assign var="listclass" value=listitem3 }]
    [{ else}]
        [{assign var="listclass" value=listitem$blWhite }]
    [{ /if}]
[{/if}]

[{ if $listitem->getId() == $oxid }]
    [{assign var="listclass" value=listitem4 }]
[{ /if}]

<td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">    
    <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxorderdate|oxformdate:'datetime':true }]</a>
    [{if $listitem->oxorder__oxpaymenttype->value ne "oxidpayadvance"}]
        <img src="[{$oViewConf->getModuleUrl('asign/asign_yellowcube','out/img/parcel.png')}]" alt="" title="Order Shipped by SPS" style="vertical-align:middle;height:20px;margin-left:10px;" />
    [{/if}]
</div></td>
<td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxpaid|oxformdate }]</a></div></td>
<td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxordernr->value }]</a></div></td>
<td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxbillfname->value }]</a></div></td>
<td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->oxorder__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->oxorder__oxbilllname->value }]</a></div></td>
<td class="[{ $listclass}]">
    [{if !$readonly}]
        <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->oxorder__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
        <a href="Javascript:StornoThisArticle('[{ $listitem->oxorder__oxid->value }]');" class="pause" id="pau.[{$_cnt}]" [{include file="help.tpl" helpid=item_storno}]></a>
    [{/if}]</td>
</td>