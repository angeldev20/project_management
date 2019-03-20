"use strict";

jQuery(document).ready(function($) {
    
    //dummy voting
    $('#kb_voting_dummy_create__button').each(function( index ) {
        var actionButton = $(this);
        var votesCount = $('input#kb_voting_dummy_create__input').val();
        var actionHref = actionButton.attr('href');
        var challenge = actionButton.attr('data-challenge');

        actionButton.click(function(event){
            event.preventDefault();
            var c = confirm(challenge);
            if (c == true) {
                window.location.href = actionHref+'&count='+votesCount;
            } 
            
        });
    });


});