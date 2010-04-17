$(document).ready(function () {
    /**
     * NAVIGATION FADE EFFECTS
     ***************************************************************/
    // Apply the hoving fade effect to the navigation menu
    $('.navIcon').fadeTo(0, 0.8);
    $('.navIcon').hover(function (){$(this).fadeTo('fast', 1.0);}, function(){$(this).fadeTo('fast', 0.8);});

    /**
     * NAVIGATION CAROUSEL
     ***************************************************************/
    // Get the inner width of our navigation contianer
    navInnerWidth = $('#navigation').innerWidth();
    
    // for each of the nav groups lets correct their formating
    $('.navGroup ul').each(function () {
        // calculate the actual width of the ul elements
        totalWidth = 0;
        $(this).find('li').each(function () { totalWidth += $(this).outerWidth(); });

        $(this).parent().parent().addClass('infiniteCarousel');
        $(this).parent().parent().infiniteCarousel();
    });

    /**
     * NAVIGATION PERSISTENT CATEGORIES
     ***************************************************************/
    // create a new client-side persistent data store
    var persistent_category = new Persist.Store('freepbx_persistent_category');

    // Calculate how wide each of the category items should be (keeping them as identical as possible)
    navCategoriesWidth = $('#navCategories').innerWidth() - 10; // 10px for the padding
    categories = $('#navCategories li');
    categoryWidth = Math.floor(navCategoriesWidth/categories.length);
    categories.each(function (){
        $(this).width(categoryWidth);
    });

    // Bind a function to the click of each category
    categories.click(function (e) {
        // stop the defualt execution
        e.preventDefault();

        // save selected category in the persist store
        persistent_category.set('lastCategory', this.id);

        // hide the navigation items
        $('.navGroup').hide();
        // show the navigation items that belong to this group
        $('#navGroup' + $(this).text()).show();

        // change the class to reflect the new category on the category bar
        categories.removeClass('current_category');
        $(this).addClass('current_category');
    });

    // on start up see if we can get the users last selected category
    persistent_category.get('lastCategory', function(ok, val) {
      if (ok)
        lastCategory = val;
    });

    // if we know the users last catergory go to it, otherwise just go to the first
    if ($('#' + lastCategory).length > 0) {
        $('#' + lastCategory).trigger('click');
    } else {
        categories.filter(':first').trigger('click');
    }

    /**
     * SCROLL TO THE FIRST ERROR
     ***************************************************************/
    firstError = $('.has_error').filter(':first');
    if (firstError.length > 0) {
        $.scrollTo($('.has_error').filter(':first'));
        $.jGrowl('Please correct the fields in red.', { theme: 'error', life: 5000 });
    }

    /**
     * DEPENDENT GROUP ENGINE
     ***************************************************************/
    function detrminant() {
        agents = $('.determinant');
        agents.change(function () {
            agentClasses = this.className;
            dependGroup = $(this).attr('dependGroup');
            if (!dependGroup) {

                agentClasses = agentClasses.split(' ');

                for (i in agentClasses) {
                    if (agentClasses[i].match('agent_for_')) {
                        dependGroup = agentClasses[i].substr(10);
                        $(this).attr('dependGroup', dependGroup);
                        break;
                    }
                }
            }
            if ($(this).attr('checked')) {
                $('.dependent_positive.rely_on_' + dependGroup).parent().show();
                $('.dependent_negative.rely_on_' + dependGroup).parent().hide();
            } else {
                $('.dependent_positive.rely_on_' + dependGroup).parent().hide();
                $('.dependent_negative.rely_on_' + dependGroup).parent().show();
            }
        }).trigger('change');
    }
    detrminant();

    /**
     * LANGUAGE BAR
     ***************************************************************/
    $('#lang').change(function () {
        $(this).fadeOut('fast', function () { $('#change_lang').fadeIn('fast'); });
        $.post(document.location.href, { lang: $(this).val() }, function (data) {
            $('#lang_bar').text('On the next page reload the language will be ' + data);
            $('#lang_bar').slideDown();
        });
    });
    $('#change_lang').click(function (e) {
        e.preventDefault();
        $(this).fadeOut('fast', function () { $('#lang').fadeIn('fast'); });
    });

    /**
     * TOOLTIP HELP
     ***************************************************************/
    function toolTips() {
        $('.help').each(function() {
            $(this).qtip({
                content: {
                    title: {
                        text: 'Additional Help...'
                    },
                    text: $(this).attr('tooltip')
                },
                position: {
                    corner: {
                        tooltip: 'topLeft',
                        target: 'bottomRight'
                    }
                },
                show: {
                    solo: true // And hide all other tooltips
                },
                style: {
                    width: 550,
                    padding: '8px',
                    title: {
                        'background-color': '#72a400',
                        color: '#ffffff',
                        padding: '3px 10px 5px 10px',
                        'font-size': '110%'
                    },
                    border: {
                        width: 8,
                        radius: 4,
                        color: '#72a400'
                    }
                }
            });

        });
    };
    toolTips();

    /**
     * NUMBER SELECTION HELPER
     ***************************************************************/
    function numberDropdown() {
        $('.numbers_dropdown').dropdownchecklist({ maxDropHeight: 100, multipleSelect: true });
    }
    numberDropdown();

    $('.destination_select').each(function() {

        $(this).qtip({
            content: {
                title: {
                    text: 'Select a Destination...'
                },
                url: '../../destinations/selector/' + this.id + '/' + $(this).attr('numberId')
            },
            position: {
                target: $(document.body),
                corner: 'center'
            },
            show: {
                when: 'click', // Show it on click
                solo: true // And hide all other tooltips
            },
            hide: {
                when: 'unfocus'
            },
            api: {
                beforeShow: function() {
                    // Fade in the modal "blanket" using the defined show speed
                    $('#qtip-blanket').fadeIn(this.options.show.effect.length);
                },
                beforeHide: function() {
                    // Fade out the modal "blanket" using the defined hide speed
                    $('#qtip-blanket').fadeOut(this.options.hide.effect.length);
                },
                onHide: function() {
                    // Do something with the selections/results?
                }
            },
            style: {
                width: 700,
                padding: '8px',
                title: {
                    'background-color': '#72a400',
                    color: '#ffffff',
                    padding: '3px 10px 5px 10px',
                    'font-size': '110%'
                },
                border: {
                    width: 8,
                    radius: 4,
                    color: '#72a400'
                }
            }
        });
    });

    // Create the modal backdrop on document load so all modal tooltips can use it
    $('<div id="qtip-blanket">')
    .css({
        position: 'absolute',
        top: $(document).scrollTop(), // Use document scrollTop so it's on-screen even if the window is scrolled
        left: 0,
        height: $(document).height(), // Span the full document height...
        width: '100%', // ...and full width
        opacity: 0.7, // Make it slightly transparent
        backgroundColor: 'black',
        zIndex: 5000  // Make sure the zIndex is below 6000 to keep it below tooltips!
    })
    .appendTo(document.body) // Append to the document body
    .hide(); // Hide it initially


    /**
     * EXECUTE AFTER EVERY AJAX REQUEST
     ***************************************************************/
    $(document).bind('ajaxStop.jqueryGetResponse', function(){
        numberDropdown();
        toolTips();
        detrminant();
    });

    
});