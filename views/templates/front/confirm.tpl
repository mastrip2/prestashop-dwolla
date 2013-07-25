{capture name=path}<a href="{$back_link}">{l s='Your shopping cart' mod='Dwolla'}</a><span class="navigation-pipe">
{$navigationPipe}</span>{l s='Dwolla' mod='Dwolla'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='Dwolla'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<p>
	<img src="{$modules_dir}./dwolla/dwolla.png" alt="{l s='Dwolla' mod='Dwolla'}" style="margin-bottom: 5px; width:100px; height: 100px" />
	<br />{l s='You have chosen to pay with Dwolla.' mod='Dwolla'}
	<br/><br />
	{l s='Here is a short summary of your order:' mod='Dwolla'}
</p>
<p style="margin-top:20px;">
	- {l s='The total amount of your order is' mod='Dwolla'}
		<span class="price">{convertPriceWithCurrency price=$dwollaTotal currency=$currency}</span> {if $use_taxes == 1}{l s='(tax incl.)' mod='Dwolla'}{/if}
</p>
<p>
	- {l s='We accept the following currency to be sent by Dwolla Checkout:' mod='Dwolla'}&nbsp;<b>{$currency->name}</b>
</p>
<p>
	<b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='Dwolla'}.</b>
</p>
<p class="cart_navigation">
	<a href="{$back_link}&step=3" class="button_large">{l s='Other payment methods' mod='Dwolla'}</a>
	<a href="{$dwolla_link}" class="exclusive_large">{l s='I confirm my order' mod='Dwolla'}</a>
</p>