/**
 * HT Post Format JS
 */
jQuery(document).ready(function() {

// Gallery Post Format
var galleryOptions = jQuery('#ht_pf_metabox_gallery');
var galleryTrigger = jQuery('#post-format-gallery');
galleryOptions.css('display', 'none');

// Link Post Format
var linkOptions = jQuery('#ht_pf_metabox_link');
var linkTrigger = jQuery('#post-format-link');
linkOptions.css('display', 'none');

// Image Post Format
var imageOptions = jQuery('#ht_pf_metabox_image');
var imageTrigger = jQuery('#post-format-image');
imageOptions.css('display', 'none');

// Quote Post Format
var quoteOptions = jQuery('#ht_pf_metabox_quote');
var quoteTrigger = jQuery('#post-format-quote');
quoteOptions.css('display', 'none');
	
// Status Post Format
var statusOptions = jQuery('#ht_pf_metabox_status');
var statusTrigger = jQuery('#post-format-status');
statusOptions.css('display', 'none');

// Video Post Format
var videoOptions = jQuery('#ht_pf_metabox_video');
var videoTrigger = jQuery('#post-format-video');
videoOptions.css('display', 'none');

// Audio Post Format
var audioOptions = jQuery('#ht_pf_metabox_audio');
var audioTrigger = jQuery('#post-format-audio');
audioOptions.css('display', 'none');
	
// Chat Post Format
var chatOptions = jQuery('#ht_pf_metabox_chat');
var chatTrigger = jQuery('#post-format-chat');
chatOptions.css('display', 'none');
	

	
/**
 * Functions
 */
var group = jQuery('#post-formats-select input');

group.change( function() {

		
		if (jQuery(this).val() == 'gallery') {
			galleryOptions.css('display', 'block');
			htHideAll(galleryOptions);					
		} else if (jQuery(this).val() == 'link') {
			linkOptions.css('display', 'block');
			htHideAll(linkOptions);
		} else if (jQuery(this).val() == 'image') {
			imageOptions.css('display', 'block');
			htHideAll(imageOptions);
		} else if (jQuery(this).val() == 'quote') {
			quoteOptions.css('display', 'block');
			htHideAll(quoteOptions);		
		} else if (jQuery(this).val() == 'status') {
			statusOptions.css('display', 'block');
			htHideAll(statusOptions);					
		} else if (jQuery(this).val() == 'video') {
			videoOptions.css('display', 'block');
			htHideAll(videoOptions);
		} else if (jQuery(this).val() == 'audio') {
			audioOptions.css('display', 'block');
			htHideAll(audioOptions);		
		} else if (jQuery(this).val() == 'chat') {
			chatOptions.css('display', 'block');
			htHideAll(chatOptions);											
		} else {
			galleryOptions.css('display', 'none');
			linkOptions.css('display', 'none');
			imageOptions.css('display', 'none');
			quoteOptions.css('display', 'none');
			statusOptions.css('display', 'none');
			videoOptions.css('display', 'none');
			audioOptions.css('display', 'none');
			chatOptions.css('display', 'none');
		}
		
	});
	
	if(galleryTrigger.is(':checked'))
		galleryOptions.css('display', 'block');
	if(linkTrigger.is(':checked'))
		linkOptions.css('display', 'block');
	if(imageTrigger.is(':checked'))
		imageOptions.css('display', 'block');
	if(quoteTrigger.is(':checked'))
		quoteOptions.css('display', 'block');
	if(statusTrigger.is(':checked'))
		statusOptions.css('display', 'block');
	if(videoTrigger.is(':checked'))
		videoOptions.css('display', 'block');
	if(audioTrigger.is(':checked'))
		audioOptions.css('display', 'block');
	if(chatTrigger.is(':checked'))
		chatOptions.css('display', 'block');	
		
	function htHideAll(notThisOne) {
		
		galleryOptions.css('display', 'none');
		linkOptions.css('display', 'none');
		imageOptions.css('display', 'none');
		quoteOptions.css('display', 'none');
		statusOptions.css('display', 'none');
		videoOptions.css('display', 'none');
		audioOptions.css('display', 'none');
		chatOptions.css('display', 'none');
		notThisOne.css('display', 'block');
	}

});



jQuery(document).ready(function($){

	// Hide conditional fields first

	jQuery('.cmb_id__ht_pf_status_oembed').hide();
	jQuery('.cmb_id__ht_pf_status_custom').hide();

	// Set display of conditional fields on load

	// Change conditional fields on switch
	$("input[name=_ht_pf_status_picker]:radio").change(function () {
		htUpdatePostformatStatus();
	});
	$("input[name=_ht_pf_video_picker]:radio").change(function () {
		htUpdatePostformatVideo();
	});
	$("input[name=_ht_pf_audio_picker]:radio").change(function () {
		htUpdatePostformatAudio();
	});

	// Update conditional fields on page load
	htUpdatePostformatStatus();
	htUpdatePostformatVideo();
	htUpdatePostformatAudio();

	function htUpdatePostformatStatus() {
		if ($("#_ht_pf_status_picker1").attr("checked")) {        
			jQuery('.cmb2-id--ht-pf-status-oembed').show();
			jQuery('.cmb2-id--ht-pf-status-custom').hide();
		} else if ($("#_ht_pf_status_picker2").attr("checked"))  {
			jQuery('.cmb2-id--ht-pf-status-custom').show();
			jQuery('.cmb2-id--ht-pf-status-oembed').hide();
		} else {
			jQuery('.cmb2-id--ht-pf-status-custom').hide();
			jQuery('.cmb2-id--ht-pf-status-oembed').hide();
		}
	}

	function htUpdatePostformatVideo() {
		if ($("#_ht_pf_video_picker1").attr("checked")) {        
			jQuery('.cmb2-id--ht-pf-video-oembed').show();
			jQuery('.cmb2-id--ht-pf-video-upload').hide();
		} else if ($("#_ht_pf_video_picker2").attr("checked")) {  
			jQuery('.cmb2-id--ht-pf-video-upload').show();
			jQuery('.cmb2-id--ht-pf-video-oembed').hide();
		} else {
			jQuery('.cmb2-id--ht-pf-video-upload').hide();
			jQuery('.cmb2-id--ht-pf-video-oembed').hide();
		}1
	}

	function htUpdatePostformatAudio() {
		if ($("#_ht_pf_audio_picker1").attr("checked")) {        
			jQuery('.cmb2-id--ht-pf-audio-oembed').show();
			jQuery('.cmb2-id--ht-pf-audio-upload').hide();
		} else if ($("#_ht_pf_audio_picker2").attr("checked")) {  
			jQuery('.cmb2-id--ht-pf-audio-upload').show();
			jQuery('.cmb2-id--ht-pf-audio-oembed').hide();
		} else {
			jQuery('.cmb2-id--ht-pf-audio-upload').hide();
			jQuery('.cmb2-id--ht-pf-audio-oembed').hide();
		}
	}


});