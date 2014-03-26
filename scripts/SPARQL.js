// This will hold the Test instance and the results
var t = null;
// This will hold the graphs
var graphs = null;

// Download the list of test results and their graph names given an endpoint
function downloadTestGraphs(endpoint) {
	endpoint = typeof endpoint !== 'undefined' ? endpoint : "";

	//send request
	var URL = "ajax_endpoints.php?endpoint="+endpoint;
	var req = new XMLHttpRequest();
	req.open("GET", URL, true); 
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200 || req.status == 0) {
				if (req.responseText == "") { 
					messageWaiting = "Error : The domain in the query is different than the current web site...";
					return;
				}
				// Retrieve the results
				graphs = JSON.parse(req.responseText);

			} else
				messageWaiting = "Error loading page";
		}
	}; 
	req.send(null); 
}

// Retrieve the results of a test given graph containing it
function downloadResults(graph) {
	graph = typeof graph !== 'undefined' ? graph : "";
	
	//send request
	var URL = "ajax_testResults.php?graph="+graph;
	var req = new XMLHttpRequest();
	req.open("GET", URL, true); 
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200 || req.status == 0) {
				if (req.responseText == "") { 
					messageWaiting = "Error : The domain in the query is different than the current web site...";
					return;
				}

				var output = JSON.parse(req.responseText);
				// Instantiate the tests and set the results
				t = new Tests();
				t.graph = graph;
				t.tests = output.tests;
				t.serverName = output.software.serverName;
				t.serverVersion = output.software.serverVersion;
				t.testerName = output.software.testerName;
				t.testerVersion = output.software.testerVersion;
			} else
				messageWaiting = "Error loading page";
		}
	}; 
	req.send(null); 
}


Tests = (function() {
	// total score for this test
	this.score = 0;
	// maximum score for this test
	this.maximum = 0;

	// Information about the software
	this.softwareName = "";
	this.softwareTag = "";
	
	// Inforamtion about the endpoint
	this.endpoint = "";
	this.graph = "";

	this.getTotalPoints = function() {
		var total = 0;
		for (var i = 0; i < this.tests.length; i++) {
			var t1 = this.tests[i];
			for (var j = 0; j < t1.items.length; j++) {
				var t2 = t1.items[j];
				for (var k = 0; k < t2.items.length; k++) {
					var t = t2.items[k];
					total += 1;
				}
			}
		}
		return total;
	}

	this.getScore = function() {
		var total = 0;
		for (var i = 0; i < this.tests.length; i++) {
			var t1 = this.tests[i];
			for (var j = 0; j < t1.items.length; j++) {
				var t2 = t1.items[j];
				for (var k = 0; k < t2.items.length; k++) {
					var t = t2.items[k];
					if (typeof t.items != 'undefined') {
						var allOK = true;
						for (var l = 0; l < t.items.length; l++) {
							var a = t.items[l];
							if(a.result != 'PASS') allOK = false;
						}
						if(allOK) total += 1;
					} else {
						total += t.result == 'PASS' ? 1 : 0;
					}
				}
			}
		}
		return total;
	}

	this.tests = [];
});
