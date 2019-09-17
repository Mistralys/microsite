var AJAX = 
{
	Send:function(method, params, completeHandler, errorHandler)
	{
		if(typeof(params) != 'object') {
			params = {};
		}
		
		if(typeof(errorHandler) != 'function') {
			errorHandler = function(message) {
				alert(message);
			};
		}
		
		$.ajax({
			'url':pageBaseURL+'&ajaxMethod='+method,
			'cache':false,
			'data':params,
			'dataType':'json'
		})
		.done(function(returnVal, statusString) 
		{
			if(typeof(returnVal) != 'object') {
				errorHandler.call(undefined, 'Response not recognized');
				return;
			} 
				
			if(returnVal.status == 'error') {
				errorHandler.call(undefined, 'Error returned: '+returnVal.message);
				return;
			}
			
			completeHandler.call(returnVal.data);
		});
	}
};