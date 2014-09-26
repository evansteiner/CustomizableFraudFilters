<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
put shit here
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 