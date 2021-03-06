{extends file="base.tpl"}
{block name=contents}
<div class="paper">
	<div>
		<h2>List of tested triplestores</h2>
		<div id="testGraphs">
			Loading...
		</div>
	</div>
</div>
{/block}
{block name=scripts}
	<script>
	<!--
		// Download the list of test graphs
		downloadTestGraphs("{$endpoint}", "{$graph}");

		// Wait for its results and call onGraphsOK when they are received
		function waitForResults(cb) {
			var callback = cb;
			
			function wait() {
				if (graphs == null) 
					window.setTimeout(wait, 100)
				else 
					callback();
			}

			wait();
		}
		
		waitForResults(onGraphsOK);

		function onGraphsOK() {
			// Interpret the results
			var testGraphs = document.getElementById('testGraphs');
			var newContents = "<table><thead><tr><th>Server name</th><th>version</th><th>test tool</th><th>score</th><th>test date</th></tr></thead><tbody>";
			graphs.forEach(function(graph) {
				newContents += "<tr><td><a href=\"results.php?graph="+graph.graphName+"\">"+graph.serverName+"</a></td><td>"+graph.serverVersion+"</td><td>"+graph.testerName+" "+graph.testerVersion+"</td><td>"+graph.score+"/"+graph.total+"</td><td>"+graph.testDate.date+"</td></tr>";
			});
			newContents += "</tbody></table>";
			testGraphs.innerHTML = newContents;


			// Update the display to remove the load bar and display the contents
			window.setTimeout(function() {
				var contents = document.getElementById('contents');
				contents.style.visibility = 'visible';

				var loading = document.getElementById('loading');
				loading.style.display = 'none';
			}, 100);
		}
	//-->
	</script>
{/block}