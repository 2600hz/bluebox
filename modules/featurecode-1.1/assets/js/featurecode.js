/* JS for Feature Codes */

var Featurecode = {
  init: function() {
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

    $('textarea.section-editor')
      .focus(function(evt) {
	editAreaLoader.init({
	  id : evt.target.id // textarea id
	  ,syntax: "xml" // syntax to be used for highlighting
	  ,start_highlight: false // to display with highlight mode on start-up
	  ,font_size: 9
	  ,save_callback: 'Featurecode.saveSnippet'
	  ,toolbar: "save, |, search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
	});
      });
  },

  saveSnippet: function(id, content) {
    $('#submit_Save').focus();
  }
};



$(document).ready(function(){
  Featurecode.init();
});
