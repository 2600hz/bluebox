/********************************************************************
 * jQuery Dependent Select plug-in									*
 *																	* 
 * @version		2.1													*
 * @copyright	(c) Bau Alexandru 2009 								*
 * @author 		Bau Alexandru										*
 * @email		bau.alexandru@gmail.com								*
 *																	*
 * @depends		jQuery									            *
 * 																	*
 * 																	*
 * Do not delete or modify this header!								*
 *																	* 
 * 																	*
 * Plugin call example:												*
 * 																	*
 * jQuery(function($){												*
 *		$('#child_id').dependent({							        *
 *			parent:	'parent_id',									*
 *			group:	'common_class'									*
 *		});															*
 *	});																*
 *																	*
 ********************************************************************/

(function($){	// create closure
	
	/**
	 * Plug-in initialization
	 * @param	object	plug-in options
	 * @return 	object	this
	 */
	$.fn.dependent = function(settings){
		// merge default settings with dynamic settings
		$param = $.extend({}, $.fn.dependent.defaults, settings);
		
		this.each(function(){														// for each element
			$this = $(this);														// current element object
			
			var $parent 	= '#'+$param.parent;
			var $child	 	= $this;
			var $group	 	= '.'+$param.group;
			var $index 		= 0;
			var $value, $class, $title, $text;
			
			var $holder  	= 'dependent-select-options-mask';
			var $holder_cls	= '.dependent-select-options-mask';
			var $is_created;
			
			// create a select to hold the options from all the childs
			$is_created = $($holder_cls).size();
			if( $is_created == 0 ){
				$('body').append('\n\n<select class="'+$holder+'" style="display:none">\n</select>\n');
			}
			
			// add options to the holder
			$($child).find('option[value!=]').each(function(){
				
				$value = $(this).attr('value');
				$class = $(this).attr('class');
				$title = $(this).attr('title');
				$text  = $(this).text();
				
				$($holder_cls).append('<option value="'+$value+'" class="'+$class+'" title="'+$title+'">'+$text+'</option>\n');
			});
			
			// check if parent allready has an option selected
			if( $($parent).val() != 0 ) {
				$title = $($parent).find('option:selected').attr('title');
				$($child).find('option[class!='+$title+']').remove();
				$($child).prepend('<option value="">Select</option>');
			} else {
				// remove the child's options and add a default option
				$($child).find('option').remove();
				$($child).append('<option value="">Select</option>');
			}
			
			
			// on change event
			$($parent).bind('change', function(){
				
				// remove all the child's options
				$($child).find('option[value!=]').remove();
				
				$index = $('select').index($(this))
				// set all the selects from the group to the default option
				$($group).each(function(){
					if( $('select').index($(this)) > $index ){
						$(this).find('option[value!=]').remove();
					}
				});
				
				$title = $(this).find('option:selected').attr('title');
				// add options to the child mask from the holder
				$($holder_cls).find('option[class='+$title+']').each(function(){
																			  
					$value = $(this).attr('value');
					$class = $(this).attr('class');
					$title = $(this).attr('title');
					$text  = $(this).attr('text');
																			  
					$($child).append('<option value="'+$value+'" class="'+$class+'" title="'+$title+'">'+$text+'</option>');
				});
				
			});

			
		});
			
		return this;
	};
		
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/************************************
	 * BEGIN PLUG-IN DEFAULT PARAMETERS *
	 ************************************/
	
	$.fn.dependent.defaults = {	
		parent:		'parent_id',
		group:		'common_class'
	};
	
	/***********************************
	 * /END PLUG-IN DEFAULT PARAMETERS *
	 ***********************************/
	
})(jQuery);		// end closure