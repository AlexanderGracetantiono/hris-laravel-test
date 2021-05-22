$(function() {
    var txt = $(".remove_space");
    var func = function() {
      txt.val(txt.val().replace(/\s/g, ''));
    }
    txt.keyup(func).blur(func);
  });