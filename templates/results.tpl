{extends file="base.tpl"}
{block name=contents}
	<div id='score'></div>
	<div id='results'></div>
{/block}

{block name=scripts}
		<script>
		<!--
			// Download results of a previous test
			downloadResults("{$endpoint}", "{$graph}");

			// Wait for its results and call onTestsOK when they are received
			function waitForResults(cb) {
				var callback = cb;
				
				function wait() {
					if (t == null) 
						window.setTimeout(wait, 100)
					else 
						callback();
				}
				
				wait();
			}
			
			waitForResults(okTestsOK);

			function okTestsOK() {
				// update the maximum value
				t.maximum = t.getTotalPoints();
				t.score = t.getScore();

				/* Update total score */
				var container = document.getElementById('score');
				// Update the display of the score
				container.innerHTML = tim(
					"<div class='pointsPanel'>" +
					{literal}"<h2><span>" + 'This software scores' + "</span> <strong>{{score}}</strong> <span>out of {{maximum}} points</span></h2>" +{/literal}
					"</div>" +
					"<div class='tripleStorePanel'>" +
					{literal}"<p>Triplestore tested: {{serverName}} {{serverVersion}}</p>" +{/literal}
					{literal}"<p>Testing software used: {{testerName}} {{testerVersion}}</p>" +{/literal}
					"<a href=\"index.php\">Back to the list of triplestores</a>" +
					"</div>",
				t);

				/* Show detailed report of scores */
				var container = document.getElementById('results');
				var div = document.createElement('div');
				div.className = 'resultsTable detailsTable';
				container.appendChild(div);

				var table = new ResultsTable({
					parent:			div,
					tests:			t.tests,
					endpoint:		t.endpoint,
					graph:			t.graph,
					header:			false,
					links:			true,
					explainations: 	true,
					grading:		true,
					bonus:			true,
					distribute:		true,
					columns:		1
				});
			
				table.updateColumn(0, t);

				new Index({
					tests:			t.tests,
					index:			document.getElementById('index'),
					wrapper:		document.getElementById('contentwrapper')
				});

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