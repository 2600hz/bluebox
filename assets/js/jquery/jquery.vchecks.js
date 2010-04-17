jQuery.fn.vchecks = function() {
	
	object = jQuery(this);
	object.addClass('geogoer_vchecks');

	//removing checkboxes
	object.find("input[type=checkbox]").each(function(){
		$(this).hide();
	});

	//adding images true false
	object.find("li").each(function(){
		if($(this).find("input[type=checkbox]").attr('checked') == true){
			$(this).addClass('checked');
			$(this).append('<div class="check_div"></div>');
		}
		else{
			$(this).addClass('unchecked');
			$(this).append('<div class="check_div"></div>');
		}
	});

	//binding onClick function
    object.find("li").click(function(e){
		e.preventDefault();
		check_li = $(this); //.parent('li');
		checkbox = $(this).find("input[type=checkbox]");
		if(checkbox.attr('checked') == true){
			checkbox.attr('checked',false);
			check_li.removeClass('checked');
			check_li.addClass('unchecked');
		}
		else{
			checkbox.attr('checked',true);
			check_li.removeClass('unchecked');
			check_li.addClass('checked');
		}
	});
	
	//mouse over / out
	//simple
	object.find("li").bind('mouseover', function(e){
		$(this).addClass('hover');
	});
	object.find("li").bind('mouseout', function(e){
		$(this).removeClass('hover');
	});
}