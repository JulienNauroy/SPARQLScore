<!DOCTYPE html>

<html>
	<head>
		<title>SPARQLScore - The soon-to-be reference in triplestore benckmarking</title>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=EDGE">
		<link rel="stylesheet" href="css/main.css" type="text/css">
		<link rel="stylesheet" href="css/bordercloud.css" type="text/css">
		<script src='scripts/base.js'></script>
		<script src='scripts/SPARQL.js'></script>
		<meta name="application-name" content="SPARQLScore"/>
		<meta property="og:title" content="The SPARQLScore test - How well does your endpoint support SPARQL?" />
		<meta property="og:description" content="The SPARQLScore test score is an indication of how well your endpoint supports the existing W3D standards and related specifications." />
		<meta property="og:type" content="website" />
	</head>


<!--
	Copyright (c) 2010-2013 Niels Leenheer

	Permission is hereby granted, free of charge, to any person obtaining
	a copy of this software and associated documentation files (the
	"Software"), to deal in the Software without restriction, including
	without limitation the rights to use, copy, modify, merge, publish,
	distribute, sublicense, and/or sell copies of the Software, and to
	permit persons to whom the Software is furnished to do so, subject to
	the following conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
	LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
	OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
	WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->


	<body>
	    <!--div class="work-in-progress">
	      <image src="images/work-in-progress.gif" width="70%"> 
	      <span style="color:red"> <br/><br/><br/><br/>Sorry the service is down. <br/>The new version will be available at the end april.</span>
	    </div-->
	    <div class="github-fork-ribbon-wrapper right">
		<div class="github-fork-ribbon">
		    <a href="https://github.com/BorderCloud/TFT">Fork me on GitHub</a>
		</div>
	    </div>
		<div id="fb-root"></div>
		<div id='contentwrapper'>
			<div class='header'>
			<h1>SPARQLScore (BETA) - <em>The soon-to-be reference in triplestore benckmarking</em>
			
			<a href="https://twitter.com/share" class="twitter-share-button" 
				data-url="http://sparqlscore.com" 
				data-counturl="http://sparqlscore.com" 
				data-related="sparqlscore" 
				data-text="@SPARQLScore (version BETA) evaluates everyday the conformance of triplestores to the #SPARQL 1.1 Recommendations." 
				data-lang="en" >Tweet</a>
			<div class="g-plusone" data-size="medium" data-width="50" data-href="http://sparqlscore.com/"></div>
			</h1>
			</div>

			<div class='page'>
				<noscript><h2>This site extensively requires Javascript!</h2></noscript>

				<div id='loading'><div></div></div>

				<div id='warning'></div>

				<div id='contents' class='column' style='visibility: hidden;'>

					{block name=contents}{/block}
					
					<div class='paper'>
						<div>
<p>
This benchmark uses Travis CI, Docker and GitHub. You can find here the list of test scripts for each database :<br/>
<a href="https://github.com/BorderCloud/tft-blazegraph">Blazegraph</a> <br/>
<a href="https://github.com/BorderCloud/tft-jena-fuseki">Jena-Fuseki</a><br/>
<a href="https://github.com/BorderCloud/tft-stardog">Stardog</a> <br/>
<a href="https://github.com/BorderCloud/tft-virtuoso7-stable">Virtuoso version stable/7</a>
</p>
						</div>
					</div>
					<div class='paper'>
						<div>
							<h2>Add your RDF database system</h2>

							<div class='text'>
								<p>
You can easily add your database to this list by sending an email to <a href="mailto:karima.rafes@gmail.com">karima.rafes@gmail.com</a>. If your database is open source, we will ask to write a docker file in the aim to test your database (you can look scripts of other databases). 
</p>
							</div>
						</div>
					</div>
					
					<div class='paper'>
						<div>
							<h2>About SPARQLScore</h2>

							<div class='text'>
								<p>
									SPARQLScore is an attempt to evaluate the conformance of triplestores to the W3C standards.
									The results displayed here correspond to the <a href="http://www.w3.org/2009/sparql/docs/tests/">tests published by the W3C</a>.
									Two test suites were removed from the list, namely "SPARQL 1.1 Service Description" and "SPARQL 1.1 Protocol", because they had no test to run and would have been counted as missing points.
									We hope that this website will help triplestores better support current norms by trying to improve their score.
								</p>

								<p>
									Here is our methodology: once in a while, we scan for newer versions of triplestores, install them, and run our test suite automatically using the <a href="http://jenkins-ci.org/">Jenkins</a> continuous integration tool, hosted at <a href="http://www.inria.fr/">Inria</a>.
									The results are stored in our internal triplestore, from which the data is pulled to be displayed on this website.
									The <a href="https://github.com/BorderCloud/TFT">test tool</a> has been built by <a href="https://twitter.com/karima_rafes">Karima Rafes</a> from <a href="http://www.bordercloud.com/">Bordercloud</a>.
									The tool will soon be published and any help developing it will be welcome. Should you find a problem in a test, feel free to report it to us and we will gladly investigate the problem.
									At the moment, supporting more triplestores will be made on a per-request basis.
								</p>

								<p>
									In the months to come, we hope to be able to automate the process and add more triplestores.
									We are also willing to add user-submitted test cases to test specific ontologies and provide these users with results tailored to their needs.
								</p>
								<p>
								</p>
							</div>
						</div>
					</div>

					<div class='footer'>
						<div>
							<div class='copyright'>
								<p>
									2014 - SparqlScore version 1.0. Created by Cécile Germain, Julien Nauroy, Karima Rafes.<br/>
									Web interface based on the <a href="https://github.com/NielsLeenheer/html5test">GitHub repository</a> of <a href="http://html5test.com/">HTML5TEST</a>.<br />
									This website is itself available <a href="https://github.com/JulienNauroy/SPARQLScore">through GitHub</a>.<br />
									2014 - <a href="https://github.com/BorderCloud/TFT">TFT (Tests for Triplestores)</a> and <a href="https://github.com/BorderCloud/SPARQL">Lib SPARQL 1.1 with PHP</a> Created by <a href="http://www.bordercloud.com">Bordercloud</a>.<br/>
									Please note that the SPARQLScore test is not affiliated with the W3C working group.<br />
									Sponsors: <br />
									<a href="http://www.inria.fr" style="margin-right:30px"><img src="images/inriauk.png" alt="Inria" height="50" /></a>
									<a href="http://www.grid-observatory.org" style="margin-right:30px"><img src="images/gridobs.png" alt="The Grid Observatory" height="50" /></a>
									<a href="http://www.campus-paris-saclay.fr/en/Idex-Paris-Saclay/Les-Lidex/Paris-Saclay-Center-for-Data-Science"
									 style="margin-right:30px"><img src="images/cds.png" alt="Center for Data Science, University Paris Saclay" height="50" /></a>
									<a href="https://www.lri.fr/"
									 style="margin-right:30px"><img src="images/logo_lri.jpg" alt="LRI" height="50" /></a>
									<a href="http://www.systematic-paris-region.org/fr/projets/timco"
									 style="margin-right:30px"><img src="images/timco.png" alt="Center for Data Science, University Paris Saclay" height="50" /></a>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id='index'></div>

		{block name=scripts}{/block}
	</body>
</html>
