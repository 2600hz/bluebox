(function($){
  $.fn.caret = function(s, e) {
    var setPosition = function(el, start, end) {
      if (el.setSelectionRange) {
        el.focus();
        el.setSelectionRange(start, end);
      }
      else if(el.createTextRange) {
        var range = el.createTextRange();
        range.collapse(true);
        range.moveEnd('character', end);
        range.moveStart('character', start);
        range.select();
      }
    };

    if (s != null && e != null) { //setting range
      return this.each(function() {
        setPosition(this, s, e);
      });      
    }
    else if (s != null) { //setting position
      return this.each(function() {
        setPosition(this, s, s);
      });
    }
    else { //getting
      var el = this[0];
      if (el.createTextRange) {
        var r = document.selection.createRange().duplicate();

        var end = el.value.lastIndexOf(r.text) + r.text.length;

        r.moveEnd('character', el.value.length);
        var start = (r.text == '') ? el.value.length : el.value.lastIndexOf(r.text);
        
        return [start, end];
      }
      else {
        return [el.selectionStart, el.selectionEnd];
      }
    }

  };
})(jQuery);
