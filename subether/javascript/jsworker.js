
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

onmessage = function (oEvent)
{
	postMessage(
	{
		"evaluated": eval(oEvent.data.code)
	});
}

/*onconnect = function(e)
{
    var port = e.ports[0];

    port.addEventListener('message', function(e)
	{
		//var workerResult = 'Result: ' + (e.data[0] * e.data[1]);
		//var workerResult = 'testings fra server elns';
		//port.postMessage(workerResult);
		var domain = location.protocol + '//' + location.hostname;
		var sCode = (function ()
		{
			var timeoutCronJobs = false;
			var timeoutCronJobtime = 30000;
			function VirtualCronJobs()
			{
				var oReq = new XMLHttpRequest();
				oReq.open("get", domain + "?function=virtualcronjobs&fastlane=1", false);
				oReq.onreadystatechange = function ( oEvent )
				{
					if ( oReq.readyState === 4 )
					{
						if ( oReq.status === 200 )
						{
							clearTimeout( timeoutCronJobs );
							timeoutCronJobs = setTimeout( function()
							{
								VirtualCronJobs();
							}, timeoutCronJobtime );
						}
					}
				};
				oReq.send(null);
			}
			timeoutCronJobs = setTimeout( function()
			{
				VirtualCronJobs();
			}, timeoutCronJobtime );
		})();
		sCode();
		port.postMessage(
		{
			"evaluated": sCode
		});
    });
	
    port.start(); // Required when using addEventListener. Otherwise called implicitly by onmessage setter.
}*/
