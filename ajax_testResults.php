<?php
require_once "libs/Smarty-3.1.16/libs/Smarty.class.php";
require_once "libs/easyrdf-0.8.0/lib/EasyRdf.php";
error_reporting(E_ALL);

header("Content-Type: text/json");

// Initialize Smarty for caching purposes
$smarty = new Smarty();
$smarty->setTemplateDir('./templates/');
$smarty->setCacheDir('./templates/cache/');
$smarty->setCompileDir('./templates/compile/');
$smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);


// Retrieve the endpoint and graph
$endpoint = $_GET["endpoint"];
$graph = $_GET["graph"];
// Values MUST be provided by index.php
if($endpoint == "") die();
if($graph == "") die();


// Check if the output is already in cache and if not, fetch the graph from the triplestore
$cacheID = md5($endpoint . ' ' . $graph);
if(!$smarty->isCached('ajax_testResults.tpl', $cacheID)) {
	// This is the output structure
	$output = new stdClass();
	$output->software->serverName = "";
	$output->software->serverVersion = "";
	$output->software->testerName = "";
	$output->software->testerVersion = "";
	$output->tests = array();

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
	SELECT ?serverName ?serverVersion ?testerName ?testerVersion WHERE {
		GRAPH <$graph> {
			?service a sd:Service ;
					sd:server ?server ;
					sd:testedBy ?tester  .
			?server git:name ?serverName ;
					git:describeTag ?serverVersion ;
					git:describe ?serverVersionBuild .
			?tester  git:name ?testerName ;
					git:describeTag ?testerVersion  .
		}
	}
	");


	foreach ($result as $row) {
		$output->software->serverName = $row->serverName->getValue();
		$output->software->serverVersion = $row->serverVersion->getValue();
		$output->software->testerName = $row->testerName->getValue();
		$output->software->testerVersion = $row->testerVersion->getValue();
	}


	// Retrieve the test results
	$result = $sparql->query("
	CONSTRUCT {
		?categoryIRI rdf:label ?categoryName.
		?categoryIRI earl:test ?test.
		?categoryIRI sq:totalTest ?totalTest.
		?categoryIRI sq:scoreTest ?score.
		?test rdf:label ?testName.
		?test earl:assertion ?assertion.
		?assertion  rdf:label ?assertionName.
		?assertion  earl:outcome ?outcome.
	} WHERE {
		GRAPH <$graph> {
			?assertion a earl:Assertion.
			?assertion earl:test ?test.
			?assertion rdf:label ?assertionName.
			?assertion earl:result ?result.
			?result earl:date ?date.
			OPTIONAL {?result earl:duration ?duration.}
			?result earl:outcome ?outcome.
			#?categoryIRI sq:totalTest ?totalTest ;
			#			 sq:scoreTest ?score.
						 
		}
		GRAPH <http://dev.grid-observatory.org/sparql11-test-suite/> {
			?categoryIRI rdfs:label ?categoryName ;
							 mf:conformanceRequirement ?list.
					?list rdf:rest*/rdf:first ?ttlTests .
					?ttlTests mf:entries ?entries .
					?entries rdf:rest*/rdf:first ?test.
					?test mf:name ?testName.
		}
	}
	");

	$w3cTests = new stdClass();
	$w3cTests->id = "w3cTests";
	$w3cTests->name = "W3C Tests";
	$w3cTests->column = "left";
	$w3cTests->items = array();

	// List test categories
	$categories = $result->resourcesMatching("earl:test");

	foreach($categories as $category) {
		$currentCategory = new stdClass();
		$currentCategory->id = "category" . sizeof($w3cTests->items);
		$currentCategory->name = $category->getLiteral("rdf:label")->getValue();
		$currentCategory->items = array();
		$w3cTests->items[] = $currentCategory;

		// List tests inside the category
		$tests = $category->all("earl:test");

		foreach($tests as $test) {
			$testItem = new stdClass();
			$testItem->id = "testItem" . sizeof($currentCategory->items);
			$testItem->name = $test->getLiteral("rdf:label")->getValue();
			$testItem->items = array();

			$assertions = $test->all("earl:assertion");
			foreach($assertions as $assertion) {
				$testAssertion = new stdClass();
				$testAssertion->id = $testItem->id . "_" . sizeof($testItem->items);
				$testAssertion->nodeId = $assertion->getUri();
				$testAssertion->name = $assertion->get("rdf:label")->getValue();
				switch($assertion->get("earl:outcome")) {
					case "http://www.w3.org/ns/earl#passed":
					case "earl:pass":
						$testAssertion->result = "PASS";
						break;
					case "http://www.w3.org/ns/earl#failed":
					case "earl:fail":
						$testAssertion->result = "FAILURE";
						break;
					case "http://www.w3.org/ns/earl#error":
					case "earl:error":
						$testAssertion->result = "ERROR";
						break;
					case "http://www.w3.org/ns/earl#untested":
					case "earl:untested":
						$testAssertion->result = "SKIPPED";
						break;
					default:
						die("bug: " . $assertion->get("earl:outcome"));
						$testAssertion->result = "UNKNOWN";
						break;
				}

				$testItem->items[]= $testAssertion;
			}

			$currentCategory->items[] = $testItem;
		}
	}
	$output->tests[] = $w3cTests;
	$smarty->assign('output', json_encode((array)$output));
}


$smarty->display('ajax_testResults.tpl', $cacheID);
