/*
 * jQuery PHP Plugin
 * version: 0.8.3 (16/03/2009)
 * author:  Anton Shevchuk (http://anton.shevchuk.name)
 * @requires jQuery v1.2.1 or later
 *
 * Examples and documentation at: http://jquery.hohli.com/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 */
(function($) {

$.extend({
    php: function (url, params) {
        // do an ajax post request
        $.ajax({
           // AJAX-specified URL
           url: url,
           // JSON
           type: "POST",
           data: params,
           dataType : "json",

           /* Handlers */

           // Handle the beforeSend event
           beforeSend: function(){
               return php.beforeSend();
           },
           // Handle the success event
           success: function(data, textStatus){
               return php.success(data, textStatus);
           },
           // Handle the error event
           error: function (xmlEr, typeEr, except) {
               return php.error(xmlEr, typeEr, except);
           },
           // Handle the complete event
           complete: function (XMLHttpRequest, textStatus) {
               return php.complete(XMLHttpRequest, textStatus);
           }
        });
    }
});


php = {
    /**
     * beforeSend
     */
    beforeSend:function() {
        return true;
    },
    /**
     * success
     * parse AJAX response
     * @param object response
     * @param string textStatus
     */
     success:function (response, textStatus) {
        // call jQuery methods

		for (var i=0;i<response['q'].length; i++) {

   			var selector  = $(response['q'][i]['s']);
			var methods   = response['q'][i]['m'];
			var arguments = response['q'][i]['a'];

			for (var j=0;j<methods.length; j++) { 
	    
				try {
					var method   = methods[j];
					var argument = arguments[j];

					/**
					 * This section added by K Anderson.  The intent it to convert all 
					 * responses into a function if avaliable in the window or attempt
					 * to convert to objects.  In this way all arguments are standardized.
					 * 
					 *  TODO: Write some protection/security for the eval
					 *  TODO: Currently you can not have a string and window.function() have the same name!
					 */
                    funcIdentifier = 'function';
					for (var k = 0; k < argument.length; k++) 
					{
                        arg = argument[k];
						if (typeof(window[argument[k]]) != "undefined") {
							argument[k] = window[argument[k]];
						} else {
							try {
                                possibleFunc = eval('(' + argument[k] + ')');

                                if (typeof(possibleFunc) != "undefined" && typeof(possibleFunc) != "xml") {
                                    argument[k] = possibleFunc;
                                }
							} 
							catch (error) {
							}
						}
					}

					if (method && method!= '' && method!= 'undefined') {
					    switch (true) {
					        // exception for 'ready', 'map', 'queue'
					        case (method == 'ready' || method == 'map' || method == 'queue'):
					           selector = selector[method](argument[0]);
					           break;
					        // exception for 'bind' and 'one'
					        case ((method == 'bind' || method == 'one') && argument.length == 3):
					           selector = selector[method](argument[0],argument[1],argument[2]);
					           break;
					        // exception for 'toggle' and 'hover'
					        case ((method == 'toggle' || method == 'hover') && argument.length == 2):
					           selector = selector[method](argument[0], argument[1]);
					           break;
					        // exception for 'filter'
					        case (method == 'filter' && argument.length == 1):
					           // try run method
							   selector = selector[method](argument[0]);
					           break;
					        // exception for effects with callback
					        case ((   method == 'show'      || method == 'hide'
					               || method == 'slideDown' || method == 'slideUp' || method == 'slideToggle'
					               || method == 'fadeIn'    || method == 'fadeOut'
					               
					             ) && argument.length == 2):
					           selector = selector[method](argument[0],argument[1]);
					           break;
					        // exception for events with callback
					        case ((   method == 'blur'      || method == 'change'
					               || method == 'click'     || method == 'dblclick'
					               || method == 'error'     || method == 'focus'
					               || method == 'keydown'   || method == 'keypress'  || method == 'keyup'
					               || method == 'load'      || method == 'unload'
					               || method == 'mousedown' || method == 'mousemove' || method == 'mouseout'
					               || method == 'mouseover' || method == 'mouseup'
					               || method == 'resize'    || method == 'scroll'
					               || method == 'select'    || method == 'submit'
					             ) && argument.length == 1):
					           selector = selector[method](argument[0]);
					           break;
					        // exception for 'fadeTo' with callback
					        case (method == 'fadeTo' && argument.length == 3):
					           selector = selector[method](argument[0],argument[1],argument[2]);
					           break;
					        // exception for 'animate' with callback
					        case (method == 'animate' && argument.length == 4):
					           selector = selector[method](argument[0],argument[1],argument[2],argument[3]);
					           break;

					        // universal
					        case (argument.length == 0):
					           selector = selector[method]();
					           break;
					        case (argument.length == 1):
					           selector = selector[method](argument[0]);
					           break;
					        case (argument.length == 2):
					           selector = selector[method](argument[0],argument[1]);
					           break;
					        case (argument.length == 3):
					           selector = selector[method](argument[0],argument[1],argument[2]);
					           break;
					        case (argument.length == 4):
					           selector = selector[method](argument[0],argument[1],argument[2],argument[3]);
					           break;
					        case (argument.length == 5):
					           selector = selector[method](argument[0],argument[1],argument[2],argument[3],argument[4]);
					           break;
					        case (argument.length == 6):
					           selector = selector[method](argument[0],argument[1],argument[2],argument[3],argument[4],argument[5]);
					           break;
					        default:
					           selector = selector[method](argument);
					           break;
					    }
					}
				} catch (error) {
					// if is error
					debug.warn('onAction: $("'+ response['q'][i]['s'] +'").'+ method +'("'+ argument +'")\n'
									+' in file: ' + error.fileName + '\n'
									+' on line: ' + error.lineNumber +'\n'
									+' error:   ' + error.message);
				}
		    }
	    }

        // predefined actions named as 
        // Methods of ObjResponse in PHP side 
        $.each(response['a'], function (func, params) {
            for (var i=0;i<params.length; i++) {
                try {
                    php[func](params[i]);
                } catch (error) {
                    // if is error
                    debug.warn('onAction: ' + func + '('+ params[i] +')\n'
                                       +' in file: ' + error.fileName + '\n'
                                       +' on line: ' + error.lineNumber +'\n'
                                       +' error:   ' + error.message);
                }
            }
        });
             
    },

    /**
     * error
     * 
     * @param object xmlEr
     * @param object typeEr
     * @param object except
     */
     error:function (xmlEr, typeEr, except) {
        var exObj = except ? except : false;
        
        $('#php-error').remove();
        
        var printCss  = 
            "<style type='text/css'>" +
                "#php-error{ width:640px; position:absolute; top:4px; right:4px; border:1px solid #f00; }"+
                "#php-error .php-title{ width:636px; height:26px; position:relative; line-height:26px; background-color:#f66; color:#fff; font-weight:bold; font-size:12px;padding-left:4px; }"+
                "#php-error .php-more { width:20px;  height:20px; position:absolute; top:2px; right:24px; line-height:20px; text-align:center; cursor:pointer; border:1px solid #f00; background-color:#fee; color:#333; }"+
                "#php-error .php-close{ width:20px;  height:20px; position:absolute; top:2px; right:2px;  line-height:20px; text-align:center; cursor:pointer; border:1px solid #f00; background-color:#fee; color:#333; }"+
                "#php-error .php-desc { width:636px; position:relative; background-color:#fee;padding-left:4px;}"+
                "#php-error .php-content{ display:none;}"+
                "#php-error textarea{ width:634px;height:400px;overflow:auto;padding:2px;}"+
            "</style>";
        
        // error report for popup window coocking
        var printStr  = 
            "<div id='php-error'>"+
                "<div class='php-title'>Error in AJAX request"+
                    "<div class='php-more'>&raquo;</div>"+
                    "<div class='php-close'>X</div>"+
                "</div>"+
                "<div class='php-desc'>";
                
            printStr += "<b>XMLHttpRequest exchange</b>: ";
        
        // XMLHttpRequest.readyState status
        switch (xmlEr.readyState) {
            case 0:
                readyStDesc = "not initialize";
                break;
            case 1: 
                readyStDesc = "open";
                break;
            case 2: 
                readyStDesc = "data transfer";
                break;
            case 3: 
                readyStDesc = "loading";
                break;
            case 4: 
                readyStDesc = "finish";
                break;
            default:
                return "unknown state";  
        }
        
        printStr += readyStDesc+" ("+xmlEr.readyState+")";
        printStr += "<br/>\n";
        
        if (exObj!=false) {
            printStr += "exception was catch: "+except.toString();
            printStr += "<br/>\n";
        }
        
        // add http status description
        printStr += "<b>HTTP status</b>: "+xmlEr.status +" - "+xmlEr.statusText;
        printStr += "<br/>\n";
        // add response text
        printStr += "<b>Response text</b> (<small><a href='#' class='php-more2'>show more information &raquo;</a></small>):"; 
        printStr += "</div>\n"; 
        printStr += "<div class='php-content'><textarea>"+ xmlEr.responseText+"</textarea></div>";
        printStr += "</div>" ;
        
        $(document.body).append(printCss);
        $(document.body).append(printStr);
        
        
        $('#php-error .php-more').hover(
            function(){
                $(this).css('background-color','#fff')
            },
            function(){
                $(this).css('background-color','#fee')
            });
        $('#php-error .php-more').click(function(){
            $('#php-error .php-content').slideToggle();
        });
        $('#php-error .php-more2').click(function(){
            $('#php-error .php-content').slideToggle();
            return false;
        });
        
        $('#php-error .php-close').click(function(){
            $('#php-error').fadeOut('fast',function(){$('#php-error').remove()})
        });
        
        $('#php-error .php-close').hover(
            function(){
                $(this).css('background-color','#fff')
            },
            function(){
                $(this).css('background-color','#fee')
            });
    },
    
    /**
     * complete
     * 
     * @param object XMLHttpRequest
     * @param String textStatus
     */
    complete:function(XMLHttpRequest, textStatus) {
        return true;
    },
    
    /* Static actions */
    
    /**
     * addMessage
     * system messages callback handler
     * @param object data
     */
    addMessage:function(data) {
        // call registered or default func
        var message        = data.msg      || "";
        var callBackFunc   = data.callback || "defaultCallBack";
        var callBackParams = data.params   || {};
        php.messages[callBackFunc](message, callBackParams);
    }, 
       
    /**
     * addError
     * system errors callback handler
     * @param object data
     */
    addError:function(data) {
        // call registered or default func
        var message        = data.msg      || "";
        var callBackFunc   = data.callback || "defaultCallBack";
        var callBackParams = data.params   || {};
        php.errors[callBackFunc](message, callBackParams);
    },

    /**
     * addData
     *
     * @param object data
     */
    addData:function(data) {
        // call registered or default func
        var callBackFunc   = data.callback || "defaultCallBack";
        php.data[callBackFunc](data.k, data.v);
    },

    /**
     * evalScript
     * @param object data
     */
    evalScript:function(data) {
        // why foo?
        var func = data.foo || '';
        eval(func);
    },
    
    /* Default realization of callback functions */
    data : {
        defaultCallBack : function (key, value){
            alert("Server response: " + key + " = " + value);
        }
    },
    messages : {
        defaultCallBack : function (msg, params){
            alert("Server response message: " + msg);
        }
    },
    errors : {
        defaultCallBack : function (msg, params){
            alert("Server response error: " + msg);
        }
    }
};
// end of php actions
})(jQuery);