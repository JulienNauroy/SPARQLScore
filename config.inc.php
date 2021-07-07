<?php
return (object) array(
	// This is the endpoint where the data is fetched.
	//"defaultEndpoint" => "http://io.sparqlscore.com/sparqlscore/query",
	"defaultEndpoint" => "http://134.158.74.239/test/query",
	// This is the list of test suites
	"defaultTestSuites" => array(
		// "http://dev.grid-observatory.org/sparql11-test-suite/",
		"https://bordercloud.github.io/rdf-tests/sparql11/data-sparql11/",
		"https://bordercloud.github.io/TFT-tests/geosparql/",
		"https://bordercloud.github.io/TFT-tests/GO3/",
	),
);
