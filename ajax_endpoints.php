<?php
header("Content-Type: text/json");
require_once "libs/easyrdf-0.8.0/lib/EasyRdf.php";

$config = require_once("config.inc.php");
// This is the official endpoint
$endpoint = $config->defaultEndpoint;
/*
prefix sq: <http://sparqlscore.net/Score#> 
prefix sd: <http://www.w3.org/ns/sparql-service-description#> 
prefix git: <http://www.w3.org/ns/git#> 
prefix rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
prefix xsd:  <http://www.w3.org/2001/XMLSchema#>
*/
EasyRdf_Namespace::set('sq', 'http://sparqlscore.net/Score#');
EasyRdf_Namespace::set('sd', 'http://www.w3.org/ns/sparql-service-description#');
EasyRdf_Namespace::set('git', 'http://www.w3.org/ns/git#');
EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('xsd', 'http://www.w3.org/2001/XMLSchema#');
$sparql = new EasyRdf_Sparql_Client($endpoint);

// Retrieve the graphs containing a test suite
$result = $sparql->query("
SELECT ?graph ?serverName ?serverVersion ?score ?total ?lastDate ?testerName ?testerVersion
WHERE {
	GRAPH ?graph {
		?service a sd:Service ;
		sd:server ?server ;
		sd:testedBy ?tester ;
		sd:testedDate ?lastDate.
		?server git:name ?serverName ;
		git:describeTag ?serverVersion ;
		git:describe ?serverVersionBuild .
		?tester  git:name ?testerName ;
		git:describeTag ?testerVersion  .
		?service sq:scoreTest ?score .
		?service sq:totalTest ?total .
	}
	{
		SELECT ?serverName ?serverVersion (Max(?date) AS ?lastDate)
		WHERE {
			GRAPH ?graph {
				?service a sd:Service ;
				sd:server ?server ;
				sd:testedDate ?date .
				?server git:name ?serverName ;
				git:describeTag ?serverVersion .
			}
		} GROUP BY ?serverName ?serverVersion
	}
} 
ORDER BY DESC(?score) ?date ?serverName
");

$prevEntry = null;
$graphs = array();
foreach ($result as $row) {
	$graph = new stdClass();
	$graph->graphName = $row->graph->getUri();
	$graph->serverName = $row->serverName->getValue();
	$graph->serverVersion = $row->serverVersion->getValue();
	$graph->testerName = $row->testerName->getValue();
	$graph->testerVersion = $row->testerVersion->getValue();
	$graph->score = $row->score->getValue();
	$graph->total = $row->total->getValue();
	$graph->testDate = $row->lastDate->getValue();
	// Only add the new entry if it is different from the previous one, because multiple tests can test the same database
	// ORDER BY ensure the latest test arrives first
	if($prevEntry == null || $prevEntry->serverName != $graph->serverName || $prevEntry->serverVersion != $graph->serverVersion
	 || $prevEntry->testerName != $graph->testerName || $prevEntry->testerVersion != $graph->testerVersion) {
		$graphs[]= $graph;
	}
	$prevEntry = $graph;
}

print json_encode($graphs);


