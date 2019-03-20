jQuery(document).ready(function($) {
    "use strict";

    console.log('hkb-admin-settings-page.js loaded');

    var activeTab = $('input#tabindex').val();

    var errorMessage = '';

    var settingsPageObjects = window.settingsPageObjects;
    //console.log(activeTab);

    //tabs (jquery-ui tabs replacement)
    $('#hkb-settings-tabs ul li a').click(function(e){
        //prevent default
        e.preventDefault();
        var clickedLink = $(this);
        var clickedLinkHREF = clickedLink.attr('href');
        var clickedLinkHREFnohash = clickedLinkHREF.substring(1);
        setActiveTab(clickedLinkHREFnohash);

    });

    function setActiveTab(tabName){
        $('#hkb-settings-tabs ul li a').removeClass('active');
        $('#hkb-settings-tabs ul li a#'+tabName+'-link').addClass('active');
        //set active section
        $('.hkb-settings-section').each(function(index, item){
            var currentItem = $(item);
            if(tabName==item.id){
                currentItem.show();
                currentItem.addClass('active');
            } else {
                currentItem.removeClass('active');
                currentItem.hide();
            }
        });


        //set active input
        $('input#activetab').val(tabName);
    }

    //initial load
    var initialActiveTab = $('#activetab').val();
    //or if we've been sent to a specific tab
    var hashTab  = window.location.hash.replace("#", "");

    if(''!==hashTab){
        initialActiveTab = hashTab;
    }
    //console.log(initialActiveTab);
    if(''!==initialActiveTab){
        setActiveTab(initialActiveTab);
    }else{
        setActiveTab('general-section');
    }


    //Display Sub Category Depth
    $('.ht-knowledge-base-settings-display-sub-cats__input').change(function () {
        updateSubCatOptionsDisplay();
    });

    function updateSubCatOptionsDisplay(){
        var displaySubCategories = $('.ht-knowledge-base-settings-display-sub-cats__input');
        if (displaySubCategories.attr("checked")) {
            $('.ht-knowledge-base-settings-sub-cat-depth__input').parents('tr').show(400);
            $('.ht-knowledge-base-settings-display-sub-cat-articles__input').parents('tr').show(400);
            return;
        } else {
            $('.ht-knowledge-base-settings-sub-cat-depth__input').parents('tr').hide(400);
            $('.ht-knowledge-base-settings-display-sub-cat-articles__input').parents('tr').hide(400);
            return;
        }
    }
    updateSubCatOptionsDisplay();


    //Display Live Search
    $('.ht-knowledge-base-settings-display-live-search__input').change(function () {
        updateLiveSearchDisplay();
    });

    function updateLiveSearchDisplay(){
        var displayLiveSearch = $('.ht-knowledge-base-settings-display-live-search__input');
        if (displayLiveSearch.attr("checked")) {
            $('.ht-knowledge-base-settings-focus-live-search__input').parents('tr').show(400);
            $('.ht-knowledge-base-settings-search-placeholder-text__input').parents('tr').show(400);
            return;
        } else {
            $('.ht-knowledge-base-settings-focus-live-search__input').parents('tr').hide(400);
            $('.ht-knowledge-base-settings-search-placeholder-text__input').parents('tr').hide(400); 
            return;
        }
    }
    updateLiveSearchDisplay();

    //Display Feedback Options
    $('.ht-knowledge-base-settings-enable-article-feedback__input').change(function () {
        updateFeedbackOptions();
    });

    function updateFeedbackOptions(){
        var articleFeedbackEnabled = $('.ht-knowledge-base-settings-enable-article-feedback__input');
        if (articleFeedbackEnabled.attr("checked")) {
            $('.ht-knowledge-base-settings-enable-anon-article-feedback__input').parents('tr').show(400);
            $('.ht-knowledge-base-settings-enable-upvote-article-feedback__input').parents('tr').show(400);
            $('.ht-knowledge-base-settings-enable-downvote-article-feedback__input').parents('tr').show(400);
            return;
        } else {
            $('.ht-knowledge-base-settings-enable-anon-article-feedback__input').parents('tr').hide(400);
            $('.ht-knowledge-base-settings-enable-upvote-article-feedback__input').parents('tr').hide(400);
            $('.ht-knowledge-base-settings-enable-downvote-article-feedback__input').parents('tr').hide(400);
            return;
        }
    }
    updateFeedbackOptions();

    //number validation
    $('.ht-validate-number-input').blur(function(){
        var currentInput = $(this);
        var currentInputVal = currentInput.val();
        var lowerLimit = currentInput.data('lower-limit');
        var upperLimit = currentInput.data('upper-limit');
        if(  Math.floor(currentInputVal) == currentInputVal &&
             $.isNumeric(currentInputVal) &&
             currentInputVal <= upperLimit &&
             currentInputVal >= lowerLimit
          ){
            //ok
        } else {
            alert(currentInput.data('validation-requirements'));
            currentInput.focus();
        }
    });


    function validateSlugs(){
        //get values
        var articleSlugVal = $('.ht-knowledge-base-settings-kb-article-slug__input').val();
        var categorySlugVal = $('.ht-knowledge-base-settings-kb-category-slug__input').val();
        var tagSlugVal = $('.ht-knowledge-base-settings-kb-tag-slug__input').val();
        var slugsValid = true;
        //check not conflicted
        slugsValid = slugsValid && slugsNotConflicted(articleSlugVal, categorySlugVal, tagSlugVal);
        //check not reserved
        slugsValid = slugsValid && slugNotReserved(articleSlugVal);
        slugsValid = slugsValid && slugNotReserved(categorySlugVal);
        slugsValid = slugsValid && slugNotReserved(tagSlugVal);
        //check not in use
        slugsValid = slugsValid && slugNotInUse(articleSlugVal);
        slugsValid = slugsValid && slugNotInUse(categorySlugVal);
        slugsValid = slugsValid && slugNotInUse(tagSlugVal);

        return slugsValid;
    }

    function slugsNotConflicted(articleSlugVal, categorySlugVal, tagSlugVal){
        var slugsValid = true;
        slugsValid = slugsValid && articleSlugVal != categorySlugVal;
        slugsValid = slugsValid && articleSlugVal != tagSlugVal;
        slugsValid = slugsValid && categorySlugVal != tagSlugVal;
        if(!slugsValid){
            errorMessage = settingsPageObjects.conflictedSlugError;
            return false;
        }else{
            return true;
        } 
    }

    function slugNotReserved(slug){
        if($.inArray(slug, settingsPageObjects.reservedTerms)>=0){
            errorMessage = settingsPageObjects.reservedTermError;
            return false;
        }else{
            return true;
        }        
    }

    function slugNotInUse(slug){
        if($.inArray(slug, settingsPageObjects.existingPostNames)>=0){
            errorMessage = settingsPageObjects.slugInUseError;
            return false;
        }else{
            return true;
        }        
    }

    function validateSlugInput(slugInput){
        $(slugInput).blur(function(){
            //slugify input in first instance
            $(this).val(slugify($(this).val()));
            if(validateSlugs()){
                //slugs ok
            } else {
                //problem
                //var errorMessage = $(this).data('error');
                alert(errorMessage);
                $(this).focus();
                $(this).select();
            }
        });
    }    

    validateSlugInput('.ht-knowledge-base-settings-kb-article-slug__input');
    validateSlugInput('.ht-knowledge-base-settings-kb-tag-slug__input ');
    validateSlugInput('.ht-knowledge-base-settings-kb-category-slug__input');

    //slugify
    function slugify(text){
        return text.toLowerCase().replace(/ /g,'-').replace(/[^\w-\/]+/g,'');
    }

});