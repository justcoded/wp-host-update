(function ($) {
  "use strict";
  
  $(document).ready(function(){
    init_form_tables_switch();
  })
  
  function init_form_tables_switch(){
    $('#replace-form input[name=tables]').on('click', function(){
      var val = $('#replace-form input[name=tables]:checked').val();
      if ( val == 'custom' ) {
        $('#custom-tables').removeClass('hidden');
      } else {
        $('#custom-tables').addClass('hidden');
      }
    });
  }
  
}(jQuery));