<?php
require_once "libs/Smarty-3.1.16/libs/Smarty.class.php";
require_once "libs/easyrdf-0.8.0/lib/EasyRdf.php";

// header("Content-Type: text/json");
header("Content-Type: text/plain");

$config = require_once("config.inc.php");
// This is the official endpoint
$endpoint = $config->defaultEndpoint;

// Initialize Smarty for caching purposes
$smarty = new Smarty();
$smarty->setTemplateDir('./templates/');
$smarty->setCacheDir('./templates/cache/');
$smarty->setCompileDir('./templates/compile/');
$smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);


// Retrieve the endpoint, graph and node
$graph = $_GET["graph"];
$node = $_GET["node"];
// Values MUST be provided
if($graph == "") die();
if($node == "") die();

// Check if the output is already in cache and if not, fetch the graph from the triplestore
$cacheID = md5($endpoint . ' ' . $graph . ' ' . $node);
if(!$smarty->isCached('ajax_nodeData.tpl', $cacheID)) {

	EasyRdf_Namespace::set('mf', 'http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#');
	EasyRdf_Namespace::set('earl', 'http://www.w3.org/ns/earl#');
	EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
	EasyRdf_Namespace::set('xsd', 'http://www.w3.org/2001/XMLSchema#');
	EasyRdf_Namespace::set('sd', 'http://www.w3.org/ns/sparql-service-description#');
	EasyRdf_Namespace::set('sq', 'http://sparqlscore.net/Score#');
	EasyRdf_Namespace::set('git', 'http://www.w3.org/ns/git#');
	$sparql = new EasyRdf_Sparql_Client($endpoint);

	// Retrieve the description of the software
	$result = $sparql->query("
	SELECT * WHERE {
		GRAPH <$graph> {
			<$node> a earl:Assertion.
			<$node> earl:result ?result.
			#?result earl:date ?date.
			?result earl:info ?info.
			#?result earl:outcome ?outcome.
		}
	}
	");

	$output = new stdClass();
	$output->endpoint = $graph;
	$output->graph = $graph;
	$output->node = $node;
	// here should be only one result
	foreach ($result as $row) {
		$output->info = $row->info->getValue();
	}
}

//$smarty->assign('output', json_encode((array)$output));
$smarty->assign('output', $output->info);
$smarty->display('ajax_testResults.tpl', $cacheID);
