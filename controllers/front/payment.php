<?php

if(!defined('_PS_VERSION_'))
	exit;

class DwollaPaymentModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		include_once(_PS_MODULE_DIR_.'dwolla/lib/dwolla.php');
		include_once(_PS_MODULE_DIR_.'dwolla/dwolla.php');
		$this->display_column_left = false;
		
		parent::initContent();
		
		$shop_url = Tools::getShopDomainSsl(true)._MODULE_DIR_;
		$dwolla = new DwollaRestClient(Configuration::get('apiKey'), Configuration::get('apiSecret'),$this->context->link->getModuleLink('dwolla', 'complete')); //$shop_url.'dwolla/complete.php'); //$this->context->link->getPageLink('complete.php'));
		
		//setting test mode on
		if(Configuration::get('mode'))
			$dwolla->setMode('TEST');
			
		$dwolla->startGatewaySession();

		foreach ($this->context->cart->getProducts() as $product) 
		{
			$dwolla->addGatewayProduct($product["name"], $product["price"],$product["cart_quantity"]);
		}
		
		//tax computing
		$tax = $this->context->cart->getOrderTotal() - $this->context->cart->getOrderTotal(false);
		
		if ($tax < 0)
			$tax = 0;
		
		$discount =$this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
		$shipping= $this->context->cart->getTotalShippingCost();
		$callback= $shop_url.'dwolla/notifier.php';
		$id=$this->context->cart->id;
		
		$url=$dwolla->getGatewayURL(Configuration::get('destinationId'), $id, $discount, $shipping, $tax, '', $callback, $custInfo);
		 
		 	$this->context->smarty->assign(array(
				'error'=> $dwolla->getError(),
				'back_link' => $this->context->link->getPageLink('order', true, NULL),
				'dwolla_link' => $url,
				'dwollaTotal' => $this->context->cart->getOrderTotal()
			));
			$this->setTemplate('confirm.tpl');
	}
}

?>