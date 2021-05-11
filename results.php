<?php
require_once "libs/Smarty-3.1.16/libs/Smarty.class.php";
$config = require_once("config.inc.php");

// Default endpoint and graph
$graph = $_GET["graph"];
if($graph == "") $graph = $config->defaultGraph;
$endpoint = $_GET["endpoint"];
if($endpoint == "") $endpoint = $config->defaultEndpoint;


$smarty = new Smarty();
// $smarty->caching = FALSE;
$smarty->setTemplateDir('./templates/');
$smarty->setCacheDir('./templates/cache/');
$smarty->setCompileDir('./templates/compile/');

$smarty->assign('graph', $graph);
$smarty->assign('endpoint', $endpoint);
$smarty->display('results.tpl');

?>
