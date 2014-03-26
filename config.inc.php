<?php
return (object) array(
	// This is the endpoint where the data is fetched.
	"defaultEndpoint" => "http://dev.grid-observatory.org:3030/tests/query",
	// This is the list of test suites
	"defaultTestSuites" => array(
		// "http://dev.grid-observatory.org/sparql11-test-suite/",
		"http://bordercloud.github.io/TFT-tests/sparql11-test-suite/",
		"http://bordercloud.github.io/TFT-tests/GO3/",
	),
);