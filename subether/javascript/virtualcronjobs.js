
/*******************************************************************************
*   SubEther, The Decentralized Network.                                       *
*   Copyright (C) 2012 Friend Studios AS                                       *
*                                                                              *
*   This program is free software: you can redistribute it and/or modify       *
*   it under the terms of the GNU Affero General Public License as             *
*   published by the Free Software Foundation, either version 3 of the         *
*   License, or (at your option) any later version.                            *
*                                                                              *
*   This program is distributed in the hope that it will be useful,            *
*   but WITHOUT ANY WARRANTY; without even the implied warranty of             *
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              *
*   GNU Affero General Public License for more details.                        *
*                                                                              *
*   You should have received a copy of the GNU Affero General Public License   *
*   along with this program.  If not, see <https://www.gnu.org/licenses/>.     *
*******************************************************************************/

function VirtualCronJobs()
{
	// Doesn't work for worker has to be native funcs
	/*var timeoutCronJobs = false;
	var timeoutCronJobtime = 30000;
	
	var j = new bajax ();
	j.openUrl ( '?function=virtualcronjobs&fastlane=1', 'get', true );
	j.onload = function ()
	{
		console.log( this.getResponseText () );
		
		clearTimeout( timeoutCronJobs );
		timeoutCronJobs = setTimeout( function()
		{
			VirtualCronJobs();
		}, timeoutCronJobtime );
	}
	j.send ();*/
	
	/*var data = 'data:text/javascript;charset=US-ASCII,' +
	encodeURIComponent('onmessage = function (e) {' +
		'postMessage({' +
			'"evaluated": eval(e.data.code)' +
		'});' +
	'}');*/
	
	//var worker = new Worker("data:text/javascript;charset=US-ASCII,onmessage%20%3D%20function%20%28oEvent%29%20%7B%0A%09postMessage%28%7B%0A%09%09%22id%22%3A%20oEvent.data.id%2C%0A%09%09%22evaluated%22%3A%20eval%28oEvent.data.code%29%0A%09%7D%29%3B%0A%7D");
	var worker = new Worker("subether/javascript/jsworker.js");
	//var worker = new Worker(data);
	
	//var worker = new SharedWorker("subether/javascript/jsworker.js");
	
	/*// URL.createObjectURL
	window.URL = window.URL || window.webkitURL;
	
	// "Server response", used in all examples
	//var response = "self.onmessage=function(e){postMessage('Worker: '+e.data);}";
	
	var response = 'self.onmessage = function (e) {' +
		'postMessage({' +
			'"evaluated": eval(e.data.code)' +
		'});' +
	'}';
	
	var blob;
	try
	{
		blob = new Blob([response], {type: 'application/javascript'});
	}
	catch (e)
	{
		// Backwards-compatibility
		window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder;
		blob = new BlobBuilder();
		blob.append(response);
		blob = blob.getBlob();
	}
	var worker = new Worker(URL.createObjectURL(blob));*/
	
	/*worker.onmessage = function(e)
	{
		//alert('Response: ' + e.data);
	};*/
	
	var domain = location.protocol + '//' + location.hostname;
	var base_url = '';
	
	var bases = document.getElementsByTagName('base');
	
	if ( bases.length > 0 )
	{
		base_url = bases[0].href;
	}
	
	/*var sCode = '(function ()' +
	'{' +
		'var timeoutCronJobs = false;' +
		'var timeoutCronJobtime = 30000;' +
		'function VirtualCronJobs()' +
		'{' +
			'var oReq = new XMLHttpRequest();' +
			'oReq.open("get", "' + domain + '?function=virtualcronjobs&fastlane=1", false);' +
			//'oReq.setRequestHeader("Access-Control-Allow-Origin", "*");' +
			'oReq.onreadystatechange = function ( oEvent )' +
			'{' +
				'if ( oReq.readyState === 4 )' +
				'{' +
					'if ( oReq.status === 200 )' +
					'{' +
						'clearTimeout( timeoutCronJobs );' +
						'timeoutCronJobs = setTimeout( function()' +
						'{' +
							'VirtualCronJobs();' +
						'}, timeoutCronJobtime );' +
					'}' +
				'}' +
			'};' +
			'oReq.send(null);' +
		'}' +
		'timeoutCronJobs = setTimeout( function()' + 
		'{' + 
			'VirtualCronJobs();' + 
		'}, timeoutCronJobtime );' + 
	'})()';*/
	
	var sCode = '(function ()' +
	'{' +
		'var url = "' + base_url + 'subether/include/virtualcronjobs.php";' + 
		'var timeoutCronJobs = false;' +
		'var timeoutCronJobtime = 30000;' +
		'function VirtualCronJobs()' +
		'{' +
			'var oReq = new XMLHttpRequest();' +
			'oReq.open("get", url);' +
			/*'oReq.setRequestHeader("Access-Control-Allow-Origin", "*");' + */
			'oReq.onreadystatechange = function ( oEvent )' +
			'{' +
				'if ( oReq.readyState === 4 )' +
				'{' +
					'if ( oReq.status === 200 )' +
					'{' +
						'clearTimeout( timeoutCronJobs );' +
						'timeoutCronJobs = setTimeout( function()' +
						'{' +
							'VirtualCronJobs();' +
						'}, timeoutCronJobtime );' +
					'}' +
				'}' +
			'};' +
			'oReq.send(null);' +
		'}' +
		'timeoutCronJobs = setTimeout( function()' + 
		'{' + 
			'VirtualCronJobs();' + 
		'}, timeoutCronJobtime );' + 
	'})()';
	
	/*var sCode = '(function ()' +
	'{' +
		'var timeoutCronJobs = false;' +
		'var timeoutCronJobtime = 30000;' +
		'function VirtualCronJobs()' +
		'{' +
			'request = new XDomainRequest();' +
			'request.open("get", "' + domain + '?function=virtualcronjobs&fastlane=1");' +
			'request.onload = function()' +
			'{' +
				'clearTimeout( timeoutCronJobs );' +
				'timeoutCronJobs = setTimeout( function()' +
				'{' +
					'VirtualCronJobs();' +
				'}, timeoutCronJobtime );' +
			'};' +
			'request.send(null);' +
		'}' +
		'timeoutCronJobs = setTimeout( function()' + 
		'{' + 
			'VirtualCronJobs();' + 
		'}, timeoutCronJobtime );' + 
	'})()';*/
	
	//worker.postMessage(
	//{
	//	"id": null,
	//	"code": sCode
	//});
	
	worker.postMessage(
	{
		"code": sCode
	});
	
	/*worker.port.start();
	
	worker.port.onmessage = function(e)
	{
		alert('Response: ' + e.data.evaluated);
	};
	
	worker.port.postMessage(
	{
		"code": sCode
	});
	console.log('Message posted to worker');*/
	
	//worker.postMessage('Test');
}

// Can only run with FF at the moment chrome and safari are a pain
VirtualCronJobs();

/*function SocketTest()
{
	//var exampleSocket = new WebSocket("ws://pal.ideverket.no/?function=virtualcronjobs&fastlane=1", "protocolOne");
	//var exampleSocket = new WebSocket("ws://pal.ideverket.no", "protocolOne");
	var exampleSocket = new WebSocket("ws://pal.ideverket.no");
	
	exampleSocket.onopen = function (event)
	{
		exampleSocket.send("Here's some text that the server is urgently awaiting!"); 
	};
	
	exampleSocket.onmessage = function (event)
	{
		console.log(event.data);
		exampleSocket.close();
	}
}*/

