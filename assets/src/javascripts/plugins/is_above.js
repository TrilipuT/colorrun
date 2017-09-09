(function($) {

  var $ = window.jQuery || this.jQuery;

  $.fn.is_above = function(el, gap) {
    
    var $t            = $(this),
        _top          = $t.offset().top,
        _bottom       = _top + $t.outerHeight(),
        el_top        = el.offset().top,
        el_bottom     = el_top + el.outerHeight(),
        thisGap       = gap || 0;

    return ((_bottom >= el_top - thisGap) && (_top <= el_bottom + thisGap));

  };
    
}).call(this);