jQuery(document).ready(function($) {
    "use strict";
    
    //dummy views
    $('#ht_kb_views_dummy_create__button').each(function( index ) {
        var actionButton = $(this);
        var votesCount = $('input#ht_kb_views_dummy_create__input').val();
        var actionHref = actionButton.attr('href');
        var challenge = actionButton.attr('data-challenge');

        actionButton.click(function(event){
            event.preventDefault();
            var c = confirm(challenge);
            if (c===true) {
                window.location.href = actionHref+'&count='+votesCount;
            } 
            
        });
    });

});