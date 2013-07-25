{capture name=path}{l s='Order confirmation' mod='dwolla'}{/capture}
	{include file="$tpl_dir./breadcrumb.tpl"}

	<h1>{l s='Order confirmation' mod='dwolla'}</h1>

	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	<br />

	{if $order}
	<p>{l s='Total of the transaction (taxes incl.) :' mod='dwolla'} <span class="bold">{$price}</span></p>
	<p>{l s='Your order ID is :' mod='dwolla'} <span class="bold">{$id_order}</span></p>
	<p>{l s='Your Dwolla transaction ID is :' mod='dwolla'} <span class="bold">{$dwolla_transaction}</span></p>
	{if $clearingDate}
	<p>{l s='Payment clearing date :' mod='dwolla'} <span class="bold">{$clearingDate}</span></p>
	{/if}
	{/if}
	<br />

	{if $is_guest}
		<a href="{$link->getPageLink('guest-tracking.php', true)}?id_order={$order_reference}" title="{l s='Follow my order' mod='dwolla'}" data-ajax="false"><img src="{$img_dir}icon/order.gif" alt="{l s='Follow my order'}" class="icon" /></a>
		<a href="{$link->getPageLink('guest-tracking.php', true)}?id_order={$order_reference}" title="{l s='Follow my order' mod='dwolla'}" data-ajax="false">{l s='Follow my order' mod='dwolla'}</a>
	{else}
		<a href="{$link->getPageLink('history.php', true)}" title="{l s='Back to orders' mod='dwolla'}" data-ajax="false"><img src="{$img_dir}icon/order.gif" alt="{l s='Back to orders'}" class="icon" /></a>
		<a href="{$link->getPageLink('history.php', true)}" title="{l s='Back to orders' mod='dwolla'}" data-ajax="false">{l s='Back to orders' mod='dwolla'}</a>
	{/if}