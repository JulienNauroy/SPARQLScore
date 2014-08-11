/* Polyfills */

if (!Function.prototype.bind) {
  Function.prototype.bind = function (oThis) {
	if (typeof this !== "function") {
	  // closest thing possible to the ECMAScript 5 internal IsCallable function
	  throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
	}

	var aArgs = Array.prototype.slice.call(arguments, 1),
		fToBind = this,
		fNOP = function () {},
		fBound = function () {
		  return fToBind.apply(this instanceof fNOP && oThis
								 ? this
								 : oThis,
							   aArgs.concat(Array.prototype.slice.call(arguments)));
		};

	fNOP.prototype = this.prototype;
	fBound.prototype = new fNOP();

	return fBound;
  };
}

(function(win, doc){
	if(win.addEventListener)return;		//No need to polyfill

	function docHijack(p){var old = doc[p];doc[p] = function(v){return addListen(old(v))}}
	function addEvent(on, fn, self){
		return (self = this).attachEvent('on' + on, function(e){
			var e = e || win.event;
			e.preventDefault  = e.preventDefault  || function(){e.returnValue = false}
			e.stopPropagation = e.stopPropagation || function(){e.cancelBubble = true}
			fn.call(self, e);
		});
	}
	function addListen(obj, i){
		if(!obj) return obj;
		if(i = obj.length)while(i--)obj[i].addEventListener = addEvent;
		else obj.addEventListener = addEvent;
		return obj;
	}

	addListen([doc, win]);
	if('Element' in win)win.Element.prototype.addEventListener = addEvent;			//IE8
	else{																			//IE < 8
		doc.attachEvent('onreadystatechange', function(){addListen(doc.all)});		//Make sure we also init at domReady
		docHijack('getElementsByTagName');
		docHijack('getElementById');
		docHijack('createElement');
		addListen(doc.all);
	}
})(window, document);




/* Utility functions */

var tim = (function(){
	var starts  = "\\{\\{",
		ends    = "\\}\\}",
		path    = "[a-z0-9_][\\.a-z0-9_]*", // e.g. config.person.name
		pattern = new RegExp(starts + "("+ path +")" + ends, "gim"),
		undef;

	return function(template, data, notFound){
		// Merge the data into the template string
		return template.replace(pattern, function(tag, ref){
			var path = ref.split("."),
				len = path.length,
				lookup = data,
				i = 0;

			for (; i < len; i++){
				lookup = lookup[path[i]];

				// Error handling for when the property is not found
				if (lookup === undef){
					// If specified, substitute with the "not found" arg
					if (notFound !== undef){
						return notFound;
					}
					// Throw error
					throw "Tim: '" + path[i] + "' not found in " + tag;
				}

				// Success! Return the required value
				if (i === len - 1){
					return lookup;
				}
			}
		});
	};
}());



/* Base UI functions */

var Index = function() { this.initialize.apply(this, arguments) };
Index.prototype = {
	initialize: function(options) {
		var that = this;
		this.options = options;

		var menu = document.createElement('div');
		menu.id = 'indexmenu';
		options.index.appendChild(menu);
		
		var categories = document.createElement('ul');
		menu.appendChild(categories);

		for (var i = 0; i < options.tests.length; i++) {
			var category = document.createElement('li');
			category.className = 'category ' + options.tests[i].id;
			categories.appendChild(category);
			
			var link = document.createElement('a');
			link.href = '#' + options.tests[i].id;
			link.onclick = function () { that.closeIndex(); };
			link.innerHTML = options.tests[i].name;
			category.appendChild(link);

			if (options.tests[i].items.length) {
				var items = document.createElement('ul');
				category.appendChild(items);
			
				for (var j = 0; j < options.tests[i].items.length; j++) {
					var item = document.createElement('li');
					items.appendChild(item);

					var link = document.createElement('a');
					link.href = '#table-' + options.tests[i].items[j].id;
					link.onclick = function () { that.closeIndex(); };
					link.innerHTML = options.tests[i].items[j].name;
					item.appendChild(link);
				}
			}
		}
					
		var button = document.createElement('button');
		button.innerHTML = '';
		button.id = 'indexbutton';
		button.onclick = this.toggleIndex;
		options.index.appendChild(button);
		
		options.wrapper.onclick = this.closeIndex;
	},

	toggleIndex: function() {
		if (document.body.className.indexOf(' indexVisible') == -1) {
			document.body.className = document.body.className.replace(' indexVisible', '') + ' indexVisible';
		} else {
			document.body.className = document.body.className.replace(' indexVisible', '');
		}
	},

	closeIndex: function() {
		document.body.className = document.body.className.replace(' indexVisible', '');
	}
}


var NO = 0,
	YES = 1,
	OLD = 2,
	BUGGY = 4,
	PREFIX = 8,
	BLOCKED = 16;

var ResultsTable = function() { this.initialize.apply(this, arguments) };
ResultsTable.prototype = {

	initialize: function(options) {

	  
	  
		this.parent = options.parent;
		this.endpoint = options.endpoint;
		this.graph = options.graph;
		this.tests = options.tests;
		this.options = {
			title:			options.title || '',
			columns:		options.columns || 2,
			distribute:		options.distribute || false,
			header:			options.header || false,
			links:			options.links || false,
			grading:		options.grading || false,
			features:		options.features || false,
			explainations:	options.explainations || false,

			onChange:		options.onChange || false
		}
		
		//SORTS
		this.tests.sort(function (a, b) {
		    if (a.name > b.name)
		      return 1;
		    if (a.name < b.name)
		      return -1;
		    // a doit être égale à b
		    return 0;
		});
		
		for(var key1 in this.tests){
		  this.tests[key1].items.sort(function (a, b) {
			if (a.name > b.name)
			  return 1;
			if (a.name < b.name)
			  return -1;
			// a doit être égale à b
			return 0;
		    });
		  for(var key2 in this.tests[key1].items){
		    this.tests[key1].items[key2].items.sort(function (a, b) {
			  if (a.name > b.name)
			    return 1;
			  if (a.name < b.name)
			    return -1;
			  // a doit être égale à b
			  return 0;
		      });
		    for(var key3 in  this.tests[key1].items[key2].items){
			this.tests[key1].items[key2].items[key3].items.sort(function (a, b) {
			      if (a.name > b.name)
				return 1;
			      if (a.name < b.name)
				return -1;
			      // a doit être égale à b
			      return 0;
			  });
		    }
		  }

		}

		this.panel = null;

		var that = this;

		function close(e) {
			if (that.panel) {
				var cell = that.panel.parentNode;
				var node = e.target;

				while (node.parentNode) {
					if (node == that.panel) return;
					node = node.parentNode;
				}

				that.panel.parentNode.removeChild(that.panel);
				that.panel = null;

				var node = e.target;
				while (node.parentNode) {
					if (node == cell) return e.stopPropagation();
					node = node.parentNode;
				}
			}
		}

		document.addEventListener('click', close, true)
		document.addEventListener('touchstart', close, true)

		this.data = [ null ];

		this.createCategories(this.parent, this.tests);
	},

	// Calculate the awarded points for a given test suite
	// also works for asserts
	// TODO still needed ?
	computePoints: function(testSuite) {
		var total = 0;
		for (var i = 0; i < testSuite.items.length; i++) {
			var testCase = testSuite.items[i];
			if(typeof testCase.items != 'undefined') {
				var subpoints = this.computePoints(testCase);
				if(subpoints == testCase.items.length)
					total += 1;
			} else {
				total += testCase.result == 'PASS' ? 1 : 0;
			}
		}
		return total;
	},

	// Calculate the total points for a given test suite
	computeMaximum: function(testSuite) {
		return testSuite.items.length;
	},

	updateColumn: function(column, tests) {
		this.data[column] = tests;
		// let's call the hierarchy category/testSuite/testCase[/assert]
		// Iterate through categories and testSuites
		for (var c = 0; c < this.tests.length; c++)
		for (var i = 0; i < this.tests[c].items.length; i++) {
			var testSuite = this.tests[c].items[i];
			if (typeof testSuite != 'string') {
				if (typeof testSuite != 'undefined') {
					var points = this.computePoints(testSuite);
					var maximum = this.computeMaximum(testSuite);

					var row = document.getElementById('head-' + testSuite.id);
					var cell = row.childNodes[0].firstChild.nextSibling;

					var content = "<div class='grade'>";

					if (this.options.grading) {
						var grade = '';
						var percent = parseInt(points / maximum * 100, 10);
						switch (true) {
							case percent == 0: 	grade = 'none'; break;
							case percent <= 30: grade = 'badly'; break;
							case percent <= 60: grade = 'reasonable'; break;
							case percent <= 95: grade = 'good'; break;
							default:			grade = 'great'; break;
						}

						if (points == maximum)
							content += "<span class='" + grade + "'>" + points + "</span>";
						else
							content += "<span class='" + grade + "'>" + points + "/" + maximum + "</span>";
					} else {
						content += "<span>" + points + "</span>";
					}

					content += "</div>";

					cell.innerHTML = content;
					this.updateItems(column, testSuite);
				}
			}
		}
	},

	// parentTest is needed only to identify the proper html element by id
	updateItems: function(column, testSuite, parentTest) {
		var testCases = testSuite.items;
		// count[x,y]: x is the amount of tests; y the amount of successes
		var count = [ 0, 0 ];

		for (var i = 0; i < testCases.length; i++) {
			if (typeof testCases[i] != 'string') {
				var row = document.getElementById('row-' + (parentTest == null ? '' : parentTest.id + '-') + testSuite.id + '-' + testCases[i].id);
				var cell = row.childNodes[column + 1];

				// If there are subtests, evaluate them
				if (typeof testCases[i].items != 'undefined') {
					var results = this.updateItems(column, testCases[i], testSuite);
					// The results can be all OK, at least one failure, or partial pass
					if (results[0] == results[1])
						cell.innerHTML = '<div>' + 'Pass' + ' <span class="check">✔</span></div>';
					else if (results[1] == 0)
						cell.innerHTML = '<div>' + 'Fail' + ' <span class="ballot">✘</span></div>';
					else
						cell.innerHTML = '<div><span class="partially">' + 'Partial' + '</span> <span class="partial">○</span></div>';
				} else {
					switch(testCases[i].result) {
						case 'PASS': cell.innerHTML = '<div>' + 'Pass' + ' <span class="check">✔</span></div>'; count[1]++; break;
						case 'FAILURE': cell.innerHTML = '<div>' + 'Fail' + ' <span class="ballot">✘</span></div>'; break;
						case 'SKIPPED': cell.innerHTML = '<div>' + 'Skipped' + ' <span class="partial">?</span></div>'; break;
						case 'ERROR': cell.innerHTML = '<div>' + 'Error' + ' <span class="buggy">!</span></div>'; break;
						default: cell.innerHTML = '<div><span class="partially">' + 'Unknown' + '</span> <span class="partial">?</span></div>'; break;
					}
				}
				count[0]++;
			}
		}

		return count;
	},

	createCategories: function(parent, tests) {
		var left, right;

		left = document.createElement('div');
		left.className = 'left';
		left.innerHTML = '<div></div>';
		parent.appendChild(left);

		right = document.createElement('div');
		right.className = 'right';
		right.innerHTML = '<div></div>';
		parent.appendChild(right);


		for (var i = 0; i < tests.length; i++) {
			var container = parent;
			if (tests[i].column == 'left') container = left.firstChild;
			if (tests[i].column == 'right') container = right.firstChild;

			var div = document.createElement('div');
			div.className = 'category ' + tests[i].id;
			div.id = 'category-' + tests[i].id;
			container.appendChild(div);

			var h2 = document.createElement('h2');
			h2.innerHTML = tests[i].name;
			div.appendChild(h2);

			this.createSections(div, tests[i].items);
		}
	},

	createSections: function(parent, tests) {
		for (var i = 0; i < tests.length; i++) {
			var table = document.createElement('table');
			table.cellSpacing = 0;
			table.id = 'table-' + tests[i].id;
			parent.appendChild(table);

			var thead = document.createElement('thead');
			table.appendChild(thead);

			var tr = document.createElement('tr');
			tr.id = 'head-' + tests[i].id;
			thead.appendChild(tr);

			var th = document.createElement('th');
			th.innerHTML = tests[i].name + "<div></div>";
			th.colSpan = this.options.columns + 1;
			tr.appendChild(th);

			if (typeof tests[i].items != 'undefined') {
				var tbody = document.createElement('tbody');
				table.appendChild(tbody);

				var status = typeof tests[i].status != 'undefined' ? tests[i].status : '';
				this.createItems(tbody, 0, tests[i].items, {
					id:		tests[i].id,
					nodeId:	tests[i].nodeId
				});
			}
		}
	},

	createItems: function(parent, level, tests, data) {
		var ids = [];

		for (var i = 0; i < tests.length; i++) {
			var tr = document.createElement('tr');
			parent.appendChild(tr);

			if (typeof tests[i] == 'string') {
				if (this.options.explainations || tests[i].substr(0, 4) != '<em>') {
					var th = document.createElement('th');
					th.colSpan = this.options.columns + 1;
					th.className = 'details';
					tr.appendChild(th);

					th.innerHTML = tests[i];
				}
			} else {
				var th = document.createElement('th');
				th.innerHTML = "<div><span>" + tests[i].name + "</span></div>";
				tr.appendChild(th);

				for (var c = 0; c < this.options.columns; c++) {
					var td = document.createElement('td');
					tr.appendChild(td);
				}

				tr.id = 'row-' + data.id + '-' + tests[i].id;

				if (level > 0) {
					tr.className = 'isChild';
				}

				if (typeof tests[i].items != 'undefined') {

					tr.className += 'hasChild';

					var children = this.createItems(parent, level + 1, tests[i].items, {
						id: 	data.id + '-' + tests[i].id,
						nodeId:	tests[i].nodeId
					});

					this.hideChildren(tr, children);

					(function(that, tr, th, children) {
						th.onclick = function() {
							that.toggleChildren(tr, children);
						};
					})(this, tr, th, children);
				} else {
					var showResult = false;

					th.className = 'hasLink';
					(function(that, th, data) {
						th.onclick = function() {
							that.showResult(th, data);
						};
					})(this, th, {
						// id:		data.id + '-' + tests[i].id,
						test:	tests[i]
					});
				}

				ids.push(tr.id);
			}
		}

		return ids;
	},

	showResult: function(parent, data) {
		if (this.panel) {
			this.panel.parentNode.removeChild(this.panel);
			this.panel = null;
		}

		var content = "";
		// Keep the possibility to have icons at some point in time as they were in HTML5TEST
		/*
		content += "<div class='info'>";
		content += "<div class='column left status " + data.status + "'><span>" + status + "</span></div>";
		content += "<div class='column middle" + (data.value ? '' : ' none') + "'><em>" + ( data.value || '✘' ) + "</em> <span>" + (data.value != 1 ? 'Points' : 'Point') + "</span></div>";
		content += "<div class='column right'><a href='/compare/feature/" + data.id +".html' class='compare'><span>" + 'Compare' + "</span></a></div>";
		content += "</div>";
		*/
		content += "<divclass='links'>";
		content += '<a href="' + data.test.nodeId + '" target="_blank">View the original test suite</a>';
		if(data.test.result == "FAILURE")
			//content += "<br /><br />Results:<br /><span id='currentOpenLink'>Loading. Please wait...</span>";
			content += "<br /><br />Results:<br /><a href='ajax_testResultNode.php?graph="+encodeURIComponent(this.graph)+"&node="+encodeURIComponent(data.test.nodeId)+"' target='_blank'>View the errors</a>";
		content += "</div>";

		
		// Retrieve the node's error info with an AJAX query
		//if(data.test.result == "FAILURE")
		//	this.downloadTestResultNodeInfo(data.test.nodeId);

		this.panel = document.createElement('div');
		this.panel.className = 'linksPanel popupPanel pointsLeft';
		this.panel.innerHTML = content;
		parent.appendChild(this.panel);
	},
	
	downloadTestResultNodeInfo: function(nodeId) {
		//send request
		var URL = "ajax_testResultNode.php?graph="+encodeURIComponent(this.graph)+"&node="+encodeURIComponent(nodeId);

		var req = new XMLHttpRequest();
		req.open("GET", URL, true); 
		req.onreadystatechange = function (aEvt) {
			if (req.readyState == 4) {
				if(req.status == 200 || req.status == 0) {
					if (req.responseText == "") { 
						messageWaiting = "Error while retrieving data";
						return;
					}
					console.log(req.responseText);
					// Retrieve the results and update the popup
					result = JSON.parse(req.responseText);
					document.getElementById('currentOpenLink').innerHTML = result.info;

				} else
					messageWaiting = "Error loading page";
			}
		}; 
		req.send(null); 
	},

	toggleChildren: function(element, ids) {
		if (element.className.indexOf(' hidden') == -1) {
			this.hideChildren(element, ids);
		} else {
			this.showChildren(element, ids);
		}
	},

	showChildren: function(element, ids) {
		element.className = element.className.replace(' hidden', '');

		for (var i = 0; i < ids.length; i++) {
			var e = document.getElementById(ids[i]);
			e.style.display = 'table-row';
		}
	},

	hideChildren: function(element, ids) {
		element.className = element.className.replace(' hidden', '');
		element.className += ' hidden';

		for (var i = 0; i < ids.length; i++) {
			var e = document.getElementById(ids[i]);
			e.style.display = 'none';
		}
	}
}
