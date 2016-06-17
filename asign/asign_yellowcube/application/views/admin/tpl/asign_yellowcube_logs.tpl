[{include file="headitem.tpl" title="GENERAL_YELLOWCUBE_HEADING"}]
[{assign var="stylepath" value=$oViewConf->getModuleUrl("asign/asign_yellowcube", "out/css/ycubestyles.css")}]
<link rel="stylesheet" type="text/css" href="[{$stylepath}]">
<div class="export">[{oxmultilang ident="GENERAL_YELLOWCUBELOGS_HEADING"}]</div><br />
    [{if $_isReadable eq false}]
        <div class="messagebox"><p style="color:red;font-weight: bold;">[{oxmultilang ident="ASIGN_LOGFILE_UNREADABLE"}]</p></div><br />
    [{/if}]
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        [{block name="asign_message_logs_block"}]
            <tr>
                <td>
                    <div class="errLogBox">
                        <code>
                            [{if $yclogs|@count > 0}]
                                [{foreach from=$yclogs item="elog"}][{$elog}]<hr>[{/foreach}]
                            [{/if}]
                        </code>
                    </div><br />
                </td>
            </tr>
        [{/block}]
    </table>
</body>
</html>
