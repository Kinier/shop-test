{include file="header.tpl"}
{*//> index tpl *}
{foreach $rsProducts as $item name=products}
    <div style="float: left; padding: 0px 30px 40px 0px;">
        <a href="/product/{$item['id']}/">
            <img src="/images/products/{$item['image']}" width="100">
        </a> <br>
        <div style="max-width: 100px;">
            <a href="/product/{$item['id']}/">{$item['name']}</a>
        </div>
    </div>
    {if $smarty.foreach.products.iteration mod 5 == 0}
        <div style="clear :both;"></div>
    {/if}
{/foreach}

<div style="clear :both;"></div>

<div class="pagination">


    {if $paginator['currentPage'] !=1}
        <span><a href="{$paginator['link']}{$paginator['currentPage']-1}">{$paginator['currentPage']-1}&nbsp;</a> </span>
    {/if}

    <strong><span>{$paginator['currentPage']}</span></strong>

    {if $paginator['currentPage'] < $paginator['pageCnt']}
        <span><a href="{$paginator['link']}{$paginator['currentPage']+1}">&nbsp;{$paginator['currentPage']+1}</a></span>
    {/if}

</div>

{*//< index tpl*}

{include file="footer.tpl"}
{*/ в видео все файлы подключаются в индекс контроллере, в том числе и индекс.tpl , хотя он пустой
    поэтому я сделал подключение в индекс контроллере через один файл индекс.tpl, а этот файд подсосет остальные*}







