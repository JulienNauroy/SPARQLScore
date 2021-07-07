<?php

require_once "libs/Smarty-3.1.16/libs/Smarty.class.php";
$config = require_once("config.inc.php");

// Endpoint and graph used for fetching data
$graph = @$_GET["graph"];
$endpoint = $config->defaultEndpoint;

// Initialize Smarty
$smarty = new Smarty();
$smarty->setTemplateDir('./templates/');
$smarty->setCacheDir('./templates/cache/');
$smarty->setCompileDir('./templates/compile/');

// All the logic is in the template file
$smarty->assign('graph', $graph);
$smarty->assign('endpoint', $endpoint);
$smarty->display('index.tpl');

?>
