/* JS for Feature Codes */

function featurecodes_init() {
  var hidden_to_shown = function() {
    var h_div = $(this);
    var s_div = $('#' + this.id.replace("empty", "xml"));

    h_div.removeClass('section-unused')
      .addClass('section-used');
    s_div.removeClass('section-noxml')
      .addClass('section-xml');
  };

  var shown_to_hidden = function() {
      var s_div = $(this).parents('.section-xml');
      var h_div = $('#' + s_div[0].id.replace('xml', 'empty'));

      s_div.children('textarea').val('');

      h_div.removeClass('section-used')
	.addClass('section-unused');
      s_div.removeClass('section-xml')
	.addClass('section-noxml');
  };

  $('.unused')
    .each(function(i, div) {
      $(div).click(hidden_to_shown);
    });

  $('.clear-section').each(function(i, span) {
    $(span).click(shown_to_hidden);
  });
}

$(document).ready(function(){
  featurecodes_init();
});
