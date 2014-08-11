<?php
require_once "libs/Smarty-3.1.16/libs/Smarty.class.php";
require_once "libs/easyrdf-0.8.0/lib/EasyRdf.php";

header("Content-Type: text/json");

$config = require_once("config.inc.php");
// This is the official endpoint
$endpoint = $config->defaultEndpoint;

// Initialize Smarty for caching purposes
$smarty = new Smarty();
$smarty->setTemplateDir('./templates/');
$smarty->setCacheDir('./templates/cache/');
$smarty->setCompileDir('./templates/compile/');
//$smarty->setCaching(Smarty::CACHING_OFF);
$smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);


$endpoint = $config->defaultEndpoint;
// Retrieve the graph
$graph = @$_GET["graph"];
// Values MUST be provided by index.php
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
	
	/*
	PREFIX mf: <http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#>
	PREFIX earl: <http://www.w3.org/ns/earl#>
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
	PREFIX sd: <http://www.w3.org/ns/sparql-service-description#>
	PREFIX sq: <http://sparqlscore.net/Score#>
	PREFIX git: <http://www.w3.org/ns/git#>
	*/
	EasyRdf_Namespace::set('mf', 'http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#');
	EasyRdf_Namespace::set('earl', 'http://www.w3.org/ns/earl#');
	EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
	EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
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
					sd:testedBy ?tester .
			?server git:name ?serverName ;
					git:describeTag ?serverVersion ;
					git:describe ?serverVersionBuild .
			?tester  git:name ?testerName ;
					git:describeTag ?testerVersion .
		}
	}
	");

	foreach ($result as $row) {
		$output->software->serverName = $row->serverName->getValue();
		$output->software->serverVersion = $row->serverVersion->getValue();
		$output->software->testerName = $row->testerName->getValue();
		$output->software->testerVersion = $row->testerVersion->getValue();
	}

	// Construct the subquery for the test suites
	$tsSubqueries = array();
	foreach($config->defaultTestSuites as $ts) {
		$tsSubqueries[] = "
		{
			GRAPH <$ts> {
				# Find the name for this test suite
				?manifestall a mf:Manifest ;
					rdfs:label ?label ;
					mf:include ?includes. # Only the global manifest has a mf:include predicate
				# Find the categories, their names and the associated tests
				?categoryIRI rdfs:label ?categoryName ;
					mf:conformanceRequirement ?list.
				?list rdf:rest*/rdf:first ?ttlTests .
				?ttlTests mf:entries ?entries .
				?entries rdf:rest*/rdf:first ?test.
				?test mf:name ?testName.
			}
			GRAPH <$graph> {
				# Find all the tests and assertions
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
		}\n";
	}
	// Concatenate all the subqueries
	$tsSubquery = join("\t\tUNION \n", $tsSubqueries);


	// Construct the query
	$queryStr = "
	CONSTRUCT {
		?manifestall a earl:testSuite ; # We've just invented this one
		             rdfs:label ?label ;
					 earl:testCategory ?categoryIRI. # This one too
		?categoryIRI rdf:label ?categoryName ;
		             earl:test ?test ;
		             sq:totalTest ?totalTest ;
		             sq:scoreTest ?score.
		?test rdf:label ?testName ;
		      earl:assertion ?assertion.
		?assertion rdf:label ?assertionName ;
		           earl:outcome ?outcome.
	} WHERE {
		 $tsSubquery
	} 
	";
        //echo $queryStr;
	//exit();
	
	// Run the query and retrieve the test results
	$result = $sparql->query($queryStr);

	// List all test suites
	$testSuites = $result->resourcesMatching("earl:testCategory");
	foreach($testSuites as $testSuite) {
		$tsCount = sizeof($output->tests);
		# Create an entry for this suite
		$tsNode = new stdClass();
		$tsNode->id = "testsuite" . $tsCount;
		$tsNode->name = $testSuite->getLiteral("rdfs:label")->getValue();
		$tsNode->column = ($tsCount % 2 == 0 ? "left" : "right");
		$tsNode->items = array();

		// List test categories inside this suite
		$categories = $testSuite->all("earl:testCategory");

		foreach($categories as $category) {
			$currentCategory = new stdClass();
			$currentCategory->id = $tsNode->id . "_category" . sizeof($tsNode->items);
			$currentCategory->name = $category->getLiteral("rdf:label")->getValue();
			$currentCategory->items = array();
			$tsNode->items[] = $currentCategory;

			// List tests inside the category
			$tests = $category->all("earl:test");

			foreach($tests as $test) {
				$testItem = new stdClass();
				$testItem->id = $currentCategory->id . "_testItem" . sizeof($currentCategory->items);
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
		$output->tests[] = $tsNode;
		$smarty->assign('output', json_encode((array)$output));
	}
}


$smarty->display('ajax_testResults.tpl', $cacheID);
