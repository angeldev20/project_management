jQuery(document).ready(function($) {
    "use strict";
    
    //dummy exits
    $('#kb_exits_dummy_create__button').each(function( index ) {
        var actionButton = $(this);        
        var actionHref = actionButton.attr('href');
        var challenge = actionButton.attr('data-challenge');

        actionButton.click(function(event){
            event.preventDefault();
            var exitsCount = $('input#kb_exits_dummy_create__input').val();
            var exitsObject = $('select#kb_exits_dummy_type__input :selected').val();
            var c = confirm(challenge);
            if (c === true) {
                window.location.href = actionHref+'&count='+exitsCount+'&object='+exitsObject;
            } 
            
        });
    });


});