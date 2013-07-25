<?php

require _PS_MODULE_DIR_.'dwolla/lib/dwolla.php';
require _PS_MODULE_DIR_.'dwolla/dwolla.php';

$Dwolla = new DwollaRestClient(Configuration::get('apiKey'), Configuration::get('apiSecret'));

if(!$Dwolla->verifyWebhookSignature()) {
  die("Invalid signature!");
}

$parsedBody = json_decode(file_get_contents('php://input'), TRUE);

//Configuration::get('dwolla_OS_AUTHORIZATION')


?>