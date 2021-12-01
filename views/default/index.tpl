{include file="header.tpl"} 

{*//> index tpl *}
{foreach $rsProducts as $item name=products}
    <div style="float: left; padding: 0px 30px 40px 0px;">  
        <a href="/product/{$item['id']}/">
            <img src="/images/products/{$item['image']}" width="100">
        </a> <br>
        <a href="/product/{$item['id']}/">{$item['name']}</a>
    </div>
    {if $smarty.foreach.products.iteration mod 3 == 0}
        <div style="clear :both;"></div>
    {/if}
{/foreach}

{*//< index tpl*}

{include file="footer.tpl"}
{*/ в видео все файлы подключаются в индекс контроллере, в том числе и индекс.tpl , хотя он пустой
    поэтому я сделал подключение в индекс контроллере через один файл индекс.tpl, а этот файд подсосет остальные*}







