function initializeWebapp()
{
    if (window.confirm("${confirmInitialize}"))
    {
        acceptCloseGlobal();
		var controller = getController();
		    controller.execute('${labelInitialize}', this, 'uixul', 'Initialize');
    }
}

function releaseController()
{
	getController().stop();
}

function setupCompatibility(event)
{
	wCore.debug('setupCompatibility');
	
	// wForm.
	var wSearchResults = document.getElementById('wmodule_searchresults'); 
	if (wSearchResults)
	{
		var form = document.createElement('wform');
		form.setAttribute('id', 'searchResultEditForm');
		form.setAttribute('attachment', 'globalSearchResultsList/editcomponent');
		wSearchResults.appendChild(form);
	}
	
	// Controller.
	
	var controller = getController();
	
	controller.executeJSON = function (module, action, parameters, noCache) {
		return wCore.executeJSON(module, action, parameters, null, noCache);
	};
	
	controller.createRequest = function (method) {
		method = (method && method.toLowerCase() == 'post') ? 'post' : 'get';
		return new wServerRequest(this.controllerUrl, method);
	};
	
	controller.execute = function (label, senderObject, module, action, parameters, httpMethod) {
		var params = new wControllerExecuteParameters();
		params.actionLabel = label;
		params.senderObject = senderObject;
		params.module = module;
		params.action = action;
		params.requestParameters = parameters;
		params.httpMethod = httpMethod;
		return this.executeWithParameters(params);
	};

	controller.executeWithParameters = function (parameters) {
		if (!parameters.httpMethod || !parameters.httpMethod.toLowerCase || parameters.httpMethod.toLowerCase() != 'post')
		{
			parameters.httpMethod = 'get';
		}
		var request = new wServerRequest(this.controllerUrl, parameters.httpMethod);
		request.label = parameters.actionLabel;
		request.callBack = parameters.callBack;

		if (parameters.callBackParameters)
		{
		   request.callBackParameters = parameters.callBackParameters;
		}
		else
		{
		   request.callBackParameters = null;
		}

		request.addParameter('module', parameters.module);
		request.addParameter('action', parameters.action);

		for (var paramName in parameters.requestParameters)
		{
			if (typeof(parameters.requestParameters[paramName]) != "function")
			{
				request.addParameter(paramName, parameters.requestParameters[paramName]);
			}
		}

		var _this = this;
		request.senderObject = parameters.senderObject;
		request.setHandler(function(){_this.executeHandler(request);});
		try
		{
			this.enqueue(request);
			return true;
		}
		catch (e)
		{
			wCore.error("wController.executeWithParameters", [parameters], e);
			return false;
		}
	};

	controller.enqueue = function (request) {
		if (this._actionStack.length >= this.stackLength)
		{
			throw new Error("[wController] STACK_IS_FULL (stack length: "+this._actionStack.length+")");
		}
		// enqueues the action at the end of the array
		var newLength = this._actionStack.push(request);
		// executes the next action to be executed in the list
		this.run();
		return (newLength - 1);
	};

	controller.run = function () {
		if (this._actionStack.length > 0 && !this.isBusy)
		{
			// get the first action in the list
			this._currentRequest = this._actionStack[0];
			// remove it from the array
			this._actionStack.shift();
			// start the action (send request)
			this.actionStartedHandler(this._currentRequest);
			this._currentRequest.send();
		}
	};

	controller.stop = function (runNextAction) {
		if (this._currentRequest)
		{
			this._currentRequest.abort();
			this.actionAbortedHandler(this._currentRequest);
			this._currentRequest = null;
			this.release();
		}
	};

	controller.stopAll = function () {
		this.stop();
		while (true)
		{
			try
			{
				this.cancel(0);
			}
			catch (e)
			{
				wCore.error("wController.stopAll", [], e);
				break;
			}
		}
	};

	controller.getXmlObjectFromResponse = function (xmlText) {
		// strip XML header if present because E4X does not like it
		if (xmlText.indexOf('<' + '?xml') == 0) {
			xmlText = xmlText.substring(xmlText.indexOf('>')+1, xmlText.length);
		}
		xmlText = trim(xmlText);
		try
		{
			return new XML(xmlText); // create E4X object
		}
		catch (e)
		{
			wCore.error("wController.getXmlObjectFromResponse", [xmlText], e);
			this.isBusy = false;
			// XML parsing error means that the server response
			// is not correct (PHP fatal error?)
			throw new Error("[wController] UNEXPECTED_SERVER_ERROR:\n"+xmlText);

			this.getElementByAnonid('exceptionMessage').value = xmlText;
			this.getElementByAnonid('exceptionTitle').value = "Unexpected server error";
			this.getElementByAnonid('exceptionPanel').removeAttribute('hidden');
		}
	};

	controller.executeHandler = function (request) {
		var xmlHttp = request.getXmlHttpRequest();					
		if (xmlHttp.readyState == 4)
		{
			wCore.debug("wController.executeHandler : " + request.uid);
			var message = null;
			var status;
			try
			{
				status = xmlHttp.status;
			}
			catch (e)
			{
				wCore.error("wController.executeHandler", [request], e);
				// if the status cannot be retrieved, it means that
				// the request has been aborted (by the user).
				status = 0;
				
			}
			if (status == 0)
			{
				if (request.senderObject && request.senderObject.onActionAborted)
				{
					request.senderObject.onActionAborted(request);
				}
				return;
			}
			else if (status == 200)
			{
				var displayAlert = false;
				// create an E4X object from the server response
				try
				{
				    var e4x = this.getXmlObjectFromResponse(xmlHttp.responseText);
				    var module  = e4x.module.toString();
					var action  = e4x.action.toString();
					status  = e4x.status.toString().toUpperCase();
					message = e4x.message.toString();
					if (e4x.message.@alert.toString() == "true")
					{
						displayAlert = true;
					}
				}
				catch (e)
				{
					wCore.error("wController.executeHandler", [request], e);
				    for (i = 0; i < request.urlArgs.length; i++)
				    {
				         var moduleMatch = request.urlArgs[i].match(/module=(.*)/i);
				         if (moduleMatch && moduleMatch[1])
				         {
				             var module  = moduleMatch[1];
				         }
				         var actionMatch = request.urlArgs[i].match(/action=(.*)/i);
				         if (actionMatch && actionMatch[1])
				         {
				             var action  = actionMatch[1];
				         }
				    }
				    status  = 'ERROR';
					message = '';
				}

				var methodName, defaultMethodName;

				switch (status)
				{
					case 'OK' :
						if (request.callBack != null)
						{
							methodName = "on"+request.callBack+"Success";
						}
						else
						{
							methodName = "on"+action+"Success";
						}
						defaultMethodName = "onActionSuccess";
						break;

					case 'EXCEPTION' :
						this.exceptionReceivedHandler(e4x);
						break;

					case 'ERROR' :
					default:
						if (request.callBack != null)
						{
							methodName = "on"+request.callBack+"Error";
						}
						else
						{
							methodName = "on"+action+"Error";
						}
						defaultMethodName = "onActionError";
						if (displayAlert)
						{
							alert(message);
						}
				}
				if (request.senderObject)
				{
					if (methodName in request.senderObject)
					{
						try
						{
						   request.senderObject[methodName](e4x, xmlHttp, request.callBackParameters);
						}
						catch (e)
						{
							wCore.error("wController.executeHandler", [request], e);
						}
					}
					else if (defaultMethodName in request.senderObject)
					{
						try
						{
						   request.senderObject[defaultMethodName](e4x, xmlHttp, request.callBackParameters);
						}
						catch (e)
						{
							wCore.error("wController.executeHandler", [request], e);
						}
					}
				}
			}
			else
			{
				this.unexpectedServerErrorHandler("Error "+xmlHttp.status);
			}
			this.actionEndedHandler(request, status, message);
		}
	};

	controller.actionStartedHandler = function (request) {
		this.isBusy = true;
		clearTimeout(this._windowStatusTimeout);
		wToolkit.setNotificationMessage("&amp;modules.generic.backoffice.Currently-executing; " + request.label + "...", wToolkit.INFO);
	};

	controller.actionEndedHandler = function (request, status, message) {
		this.isBusy = false;
		var msg, timeout;
		if (status == 'OK')
		{
			status = wToolkit.OK;
			msg = request.label + " : &amp;modules.generic.backoffice.completed;";
			timeout = 5000;
		}
		else
		{
			status = wToolkit.INFO;
			msg = request.label + " : "+message;
			timeout = 60000;
		}
		wToolkit.setNotificationMessage(msg, status, timeout);

		// runs next request if the action stack is not empty
		this._currentRequest = null;
		this.run();
	};

	controller.cancel = function (actionIndex) {
		if (actionIndex < 0 || actionIndex >= this._actionStack.length)
		{
			throw new RangeError("[wController] CANCEL_ACTION_INDEX_OUT_OF_RANGE ("+actionIndex+")");
		}
		var cancelledRequest = this._actionStack[actionIndex];
		this._actionStack.splice(actionIndex, 1);
		this.actionCancelledHandler(cancelledRequest);
	};

	controller.actionCancelledHandler = function (request) {
		//wCore.debug("wController: actionCancelledHandler() called");
	};

	controller.actionAbortedHandler = function (request) {
		var msg = request.label + " : aborted by user.";
		wToolkit.setNotificationMessage(msg, wToolkit.ERROR, 60000);
	};

	controller.unexpectedServerErrorHandler = function (message) {
		this.openErrorPanel("Unexpected server error", message);
	};
	
	controller.exceptionReceivedHandler = function (e4x) {
		if (e4x.type.toString() == 'SessionExpiredException')
		{
			alert("&modules.uixul.bo.messages.Session-has-expired;");
			window.location.href = '{HttpHost}/admin/';
		}
		else
		{
			var trace = [];
			trace.push("Name   : " + e4x.name);
			trace.push("Message: " + e4x.message);
			trace.push("Type   : " + e4x.type);
			trace.push("Class  : " + e4x.class);
			trace.push("File   : " + e4x.file);
			trace.push("Line   : " + e4x.line);
			if (e4x.trace)
			{
				trace.push("Stack trace:");
				for (var l=0 ; l<e4x.trace.line.length() ; l++)
				{
					trace.push(" - " + e4x.trace.line[l]);
				}
			}
			this.openErrorPanel(e4x.message.toString(), trace.join("\n"));
		}
	};
}

window.addEventListener('load', setupCompatibility, true);