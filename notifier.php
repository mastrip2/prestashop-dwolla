<?php

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(_PS_MODULE_DIR_.'dwolla/dwolla.php');

$dwolla = new Dwolla();
$dwolla->validate(file_get_contents('php://input'));
?>