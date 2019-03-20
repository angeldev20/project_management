$.ajaxSetup({
    // Disable caching of AJAX responses
    cache: false
});

lightbox.option({
    'resizeDuration': 180,
    'fadeDuration': 180,
    'imageFadeDuration': 180,
    'wrapAround': true
});

Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

// Support for AJAX loaded modal window.
// Focuses on first input textbox after it loads the window.
function mainModalClick(e) {
    e.preventDefault();
    NProgress.start();

    var url = $(this).attr('href');

    if (url.indexOf('#') === 0) {
        $('#mainModal').modal('open');
    } else {
        $.get(url, function (data) {
            $('#mainModal').modal();
            $('#mainModal').html(data);


        }).done(function () {
            NProgress.done();
        });
    }
}
function modalfunc() {

    $("#profile-link").on("click", mainModalClick);
    $(document).on("click", '*[data-toggle="mainmodal"]', mainModalClick);

    /*
    $(document).on('click', '*[data-toggle="mainmodal"]', function (e) {
        e.preventDefault();
        NProgress.start();

        var url = $(this).attr('href');

        if (url.indexOf('#') === 0) {
            $('#mainModal').modal('open');
        } else {
            $.get(url, function (data) {
                $('#mainModal').modal();
                $('#mainModal').html(data);


            }).done(function () {
                NProgress.done();
            });
        }
    });
    */
}
modalfunc();


function easyPie() {
    $('.easyPieChart').easyPieChart({
        barColor: function (percent) {
            return (percent < 100 ? '#11A7DB' : percent = 100 ? '#5cb85c' : '#cb3935');
        },
        trackColor: '#E5E9EC',
        scaleColor: false,
        size: 55

    });

};
easyPie();
//Ajax loaded content
$(document).on("click", '.ajax', function (e) {
    2
    e.preventDefault();
    NProgress.start();

    $(".message-list ul.list-striped li").removeClass('active');
    $(this).parent().addClass('active');

    //$("html, body").animate({ scrollTop: 0 }, 600);
    var url = $(this).attr('href');
    if (url.indexOf('#') === 0) {

    } else {
        $.get(url, function (data) {
            $('#ajax_content').html(data);
            $(".message_content:gt(1)").hide();
            $('#ajax_content').velocity("transition.fadeIn");
        }).done(function () {
            $(".message_content:gt(1)").velocity("transition.fadeIn");
            NProgress.done();
        });
    }
});

//Ajax background load
$(document).on("click", '.ajax-silent', function (e) {
    e.preventDefault();
    e.stopPropagation();
    NProgress.start();
    var url = $(this).attr('href');
    var element = $(this);

    if (element.hasClass("label-changer")) {
        element.parents(".dropdown").removeClass("open");
        element.parents(".dropdown").children(".dropdown-toggle").children("span").html('<i class="icon dripicons-loading spin-it"></i>');
    }

    $.get(url, function (data) {

    }).done(function () {
        if (element.hasClass("label-changer")) {
            val = element.html();
            newClass = element.data("status");
            element.parents(".dropdown").children(".dropdown-toggle").children("span").html(val);
            element.parents("td").removeClass("Paid").removeClass("Open")
                .removeClass("Sent").removeClass("PartiallyPaid").removeClass("Canceled").addClass(newClass);
            element.parents(".dropdown").children(".dropdown-toggle").removeClass("label-success").removeClass("label-warning");
            if (newClass == "Open") {
                element.parents(".dropdown").children(".dropdown-toggle").addClass("label-success");
            }
            if (newClass == "Sent") {
                element.parents(".dropdown").children(".dropdown-toggle").addClass("label-warning");
            }

        }
        $('.message-list ul li a').first().click();
        NProgress.done();

    });

});

Number.prototype.secondsToHoursAndMinutes = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours < 10) {
        hours = "0" + hours;
    }
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    $result = [hours, minutes];
    return $result;
}

//button loaded on click
function buttonLoader() {
    $(document).on("click", '.button-loader', function (e) {
        var value = $(this).text();
        $(this).html('<i class="icon dripicons-loading spin-it"></i> ' + value);
    });
}
buttonLoader();

function autogrowLoader() {
    $('.autogrow').autogrow();
}
autogrowLoader();

function chatActionLoader() {
    $(document).on("click", '.chat-submit', function (e) {
        $(this).closest('form').children('.message').trigger('summernote.change');

        if ($(this).closest('form').children('.message').val() == "" && $(this).closest('form').children('.options').children('.chat-attachment').val() == "") {
            return false;
        }
        var formData = new FormData($(this).closest('form')[0]);
        var url = $(this).closest('form').attr('action');
        var baseurl = $(this).closest('form').data("baseurl");
        var active = $(this);
        var list = $(this).closest('.comment-list-li').children('.task-comments');
        var message = $(this).closest('form').children('.message').val();
        var imageholder = active.closest('form').children('.options').children('input').data("image-holder");
        template = $(".chat-message-add-template").html();
        var template = template.replace("[[message]]", message);
        $('.chat-dettach').remove();
        var getPreview = $('#' + imageholder).html();
        list.prepend('<li class="chat-message-add">' + template + getPreview + '</li>');
        $(this).closest('form').children('.message').val('');
        chatDettach(imageholder);

        $('.summernote-with-mentions').summernote('reset');

        $.ajax({
            type: "POST",
            url: url,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            data: formData,
            success: function (data) {
                $('.chat-message-add').children('.task-comments-footer').addClass("green");
                $('.chat-message-add').removeClass("chat-message-add");
            },
            error: function () {
                $('.chat-message-add').children('.task-comments-footer').children('i').removeClass("ion-android-done").addClass("ion-android-close");
                $('.chat-message-add').children('.task-comments-footer').addClass("red");
            },
            complete: function () {

            }

        });
    });
    $(document).on("click", '.chat-attach', function (e) {
        var imageselector = $(this).closest('.options').children('.chat-attachment');
        var imageholder = $(this).closest('.options').children('.image_holder');
        imageselector.click();
    });
    function chatDettach(imageholder) {
        var imageselector = $("#" + imageholder).prev().children('.chat-attachment');
        imageselector.replaceWith(imageselector = imageselector.clone(true));
        $("#" + imageholder).children().remove();
    }

    $(document).on("click", '.chat-dettach', function (e) {
        chatDettach($(this).data("image-holder"));
    });
    function handleFileSelect(evt, element) {

        var imageholder = element;
        var files = evt.target.files;
        var file = files[0];

        if (files && file) {
            var reader = new FileReader();

            reader.onload = function (readerEvt) {
                if (file.type.match('image.*')) {
                    var binaryString = readerEvt.target.result;
                    $('#' + imageholder).html('<img class="image_holder" width="80px" src="data:' + file.type + ';base64,' + btoa(binaryString) + '" /><i class="ion-close-circled chat-dettach" data-image-holder="' + imageholder + '"></i>');
                } else {
                    $('#' + imageholder).html('<div class="image_holder chat-file"><i class="ion-android-attach"></i> ' + file.name + ' <i class="ion-close-circled chat-dettach" data-image-holder="' + imageholder + '"></i></div>');
                }
            };

            reader.readAsBinaryString(file);
        }
    };
    $(document).on('change', '.chat-attachment', function (evt) {
        imageholder = $(this).data('image-holder');
        handleFileSelect(evt, imageholder);
    });
}
chatActionLoader();

//Ajax background load
$(document).on("change", '.description-setter', function (e) {

    var itemid = $(this).val();
    var description = $("#item" + itemid).html();
    $("#description").val(description);


});

//Ajax background load
$(document).on("change", '.task-check', function (e) {
    e.preventDefault();
    var parents = $(this).parents('li');
    var url = $(this).data('link');
    var tag = '#task-' + $(this).attr('task');
    var elem = $(this);

    $.get(url, function (data) {
        response = $.parseJSON(data);

        if (response.status == "success") {
            parents.toggleClass("done").toggleClass("open");
            $(tag).toggleClass("task-done");
        } else {
            elem.attr("checked", false);
            show_alert(response.status, response.message);
        }
    }).done(function () {
    });

});

function show_alert(status, message) {
    $(".ajax-notify").html(message).addClass('active').addClass(status);
    setTimeout(function () {
        $(".ajax-notify").removeClass('active').removeClass(status);
    }, 3500);
}

$(document).on("click", '.ajax-click-request', function (e) {
    url = $(this).data("url");
    hide = $(this).data("hide");
    element = $(this);
    NProgress.start();
    $.get(url, function (data) {
        response = $.parseJSON(data);

        if (response.status == "success") {
            show_alert(response.status, response.message);
            if (typeof hide != "undefined") {
                element.closest("." + hide).hide();
            }
        } else {
            show_alert(response.status, response.message);
        }
    }).done(function () {
        NProgress.done();
    });

});

//Ajax background check for updates
function checkForUdates(link) {
    NProgress.start();
    var url = link;

    $.get(url, function (data) {

    }).done(function () {
        NProgress.done();
    });


}

//message list delete item
$(document).on("click", '.message-list-delete', function (e) {

    $(this).parent().fadeTo("slow", 0.01, function () { //fade
        $(this).slideUp("fast", function () { //slide up
            $(this).remove(); //then remove from the DOM
        });
    });
});

$(document).on('submit', 'form.quick-add-subtask', function () {
    var form = $(this);
    var url = form.attr('action');
    var data = form.serializeArray();
    var name_field = form.find('input[name="name"]');
    var name = name_field.val();
    var $list = $(this).next('ul');

    var baseurl = $(this).data("baseurl");
    var task_id = $(this).data("taskid");
    var active = $(this);
    var $dummy_elem = $("#subtask-dummy-" + task_id);

    var reload2 = form.data('reload2');
    var reload3 = form.data('reload3');
    var reload4 = form.data('reload4');

    $dummy_elem.clone().prependTo("#task-subtasks-" + task_id);
    $dummy_elem.find("p.name").html(name);

    name_field.attr('disabled', 'disabled');
    name_field.val('Please wait...');

    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function (data) {
            //$list.append('<li>' + name + '</li>');
            name_field.val('');
            name_field.removeAttr('disabled');

            $dummy_elem.find("#subtask-check-link").attr("href", baseurl + "check/" + data);
            $dummy_elem.find("#subtask-check-radio").data("link", baseurl + "check/" + data);
            $dummy_elem.find("#subtask-edit-href").attr("href", baseurl + "update/" + data);
            $dummy_elem.find("p.name").data("subtaskid", "task-details-" + data);

            $dummy_elem.attr("id", "subtask_" + data);

            //reload Modal
            modalfunc();

            $.get(document.URL, function (data) {
                $('#' + reload2).parent("div").html($(data).find('#' + reload2));
                $('#' + reload3).parent("div").html($(data).find('#' + reload3));
                $('#' + reload4).parent("div").html($(data).find('#' + reload4));
                $('#' + reload2 + ' .checkbox-nolabel').labelauty({label: false});
                $('#' + reload4 + ' .checkbox-nolabel').labelauty({label: false});

                $dummy_elem.removeClass("hidden");

                $(".timer__span").each(function () {
                    timertime = $(this).data("timertime");
                    timerid = "#" + $(this).attr("id");
                    timerstate = $(this).data("timerstate");

                    startTimer(timerstate, timertime, timerid);

                });
                $(".todo__close").click();
                $('#task_' + task_id + " p.name").trigger('click');
                sorting_list(baseurl);
                modalfunc();
                summernote();
            });
            hideClosedTasks();
        },
        error: function (e) {
            console.log(e);
        }
    });

    return false;
});

//Ajax for adding rows
$(document).on('click', '.add-row-ajax', function () {
    var formData = new FormData($(this).closest('form')[0]);
    var url = $(this).closest('form').attr('action');
    var active = $(this);
    $("#dummyTR").clone().insertBefore("#dummyTR").removeClass("hidden").attr("id", "addedfield");

    $("#addedfield .user_id").html($(".input-fields .user_id option:selected").text());
    $("#addedfield .hours").html($(".input-fields .hours").val());
    $(".input-fields .hours").val("00");
    $("#addedfield .minutes").html($(".input-fields .minutes").val());
    $(".input-fields .minutes").val("00");
    $("#addedfield .start_time").html($(".input-fields .start_time").next("input").val());
    $(".input-fields .start_time").next("input").val("");
    $("#addedfield .end_time").html($(".input-fields .end_time").next("input").val());
    $(".input-fields .end_time").next("input").val("");


    taskname = $("#quick-add-task-name").val();
    $("#quick-add-task-name").val("");

    $.ajax({
        type: "POST",
        url: url,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        data: formData,
        success: function (data) {
            console.log(data);
            $("#addedfield .option_button").attr("href", $(".input-fields .delete_link").html() + data);
            $("#addedfield").attr("id", "");

        }
    });

    return false;
});


//message reply

$(document).on("click", '#reply', function (e) {

    $("#reply").velocity({'height': '240px'}, {
        queue: false, complete: function () {
            $('#reply').wysihtml5({"size": 'small'});
            $('.reply #send').fadeIn('slow');

        }
    });


});
$(".nano").nanoScroller();

//Ajax for quick task add
$(document).on('submit', 'form.quick-add-task', function () {
    var formData = new FormData($(this).closest('form')[0]);
    var url = $(this).closest('form').attr('action');
    var baseurl = $(this).closest('form').data("baseurl");
    var active = $(this);
    $("#task_dummy").clone().prependTo("#task-list");
    taskname = $("#quick-add-task-name").val();

    // taskname = $(taskname).text();
    taskname = $("<div>"+taskname+"</div>").text();

    for (var i in app.mentions) {
        taskname = taskname.replaceAll('@' + app.mentions[i], '');
    }

    $("#quick-add-task-name").val("");

    prio = $(".priority-input").val();
    $("ul li#task_dummy").addClass("priority" + prio);
    $("ul li#task_dummy p.name").html(taskname);
    $("ul li#task_dummy").removeClass("hidden");

    $("#quick-add-task-name").summernote('reset');

    $.ajax({
        type: "POST",
        url: url,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        data: formData,
        success: function (data) {
            console.log(data);
            $("ul li#task_dummy #dummy-href").attr("href", baseurl + "check/" + data);
            $("ul li#task_dummy #dummy-href2").data("link", baseurl + "check/" + data);
            $("ul li#task_dummy #dummy-href3").attr("href", baseurl + "update/" + data);
            $("ul li#task_dummy p.name").data("taskid", "task-details-" + data);

            $("ul li#task_dummy").attr("id", "task_" + data);
            var task_id = data;


            //reload Modal
            modalfunc();
            //var reload1 = active.closest('form').data('reload');
            var reload2 = active.closest('form').data('reload2');
            var reload3 = active.closest('form').data('reload3');
            $.get(document.URL, function (data) {
                //$('#' + reload1).parent("div").html($(data).find('#' + reload1));
                $('#' + reload2).parent("div").html($(data).find('#' + reload2));
                $('#' + reload3).parent("div").html($(data).find('#' + reload3));
                $('#' + reload2 + ' .checkbox-nolabel').labelauty({label: false});

                $(".timer__span").each(function () {
                    timertime = $(this).data("timertime");
                    timerid = "#" + $(this).attr("id");
                    timerstate = $(this).data("timerstate");

                    startTimer(timerstate, timertime, timerid);

                });

                $("#task_" + task_id).html($(data).find("#task_" + task_id).html());
                $("#task_" + task_id).find('.checkbox-nolabel').labelauty({label: false});
                $("#task_" + task_id).find('.tt').tooltip();

                $(".todo__close").click();

                sorting_list(baseurl);
                modalfunc();
                summernote();
            });
            hideClosedTasks();
        }
    });

    return false;
});

//Ajax reply form submit
$(document).on("click", '.ajaxform #send', function (e) {

    var content = $('textarea[name="message"]').html($('#reply').summernote('code'));
    var url = $(this).closest('form').attr('action');
    var active = $(this);
    var formData = new FormData($(this).closest('form')[0]);

    if ($('textarea[name="message"]').val() === "") {
        $('.comment-content .note-editable').css("border-top", "2px solid #D43F3A");

        var value = $('.button-loader').html().replace('<i class="icon dripicons-loading spin-it"></i> ', "");
        $('.button-loader').html(value);
    }
    else {
        $.ajax({
            type: "POST",
            url: url,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            data: formData,
            success: function (data) {

                $('#message-list li.active').click().click();

                $(".ajaxform #send").html('<i class="ion-ios-checkmark-outline"></i>');

                $('.message-content-reply, #timeline-comment').slideUp('slow').velocity(
                    {opacity: 0},
                    {queue: false, duration: 'slow'}
                );
                $(".note-editable").html("");
                var reload = active.closest('form').data('reload');
                if (reload) {
                    $('#' + reload).load(document.URL + ' #' + reload, function () {
                        $('#' + reload + ' ul li:nth-child(2) .timeline-panel').addClass("highlight");
                        $('#' + reload + ' ul li:nth-child(2) .timeline-panel').delay("5000").removeClass("highlight");

                        summernote();
                    });

                }


            },
            error: function (data) {

                $('#message-list li.active').click().click();

                $(".ajaxform #send").html('<i class="ion-ios-circle-outline"></i>');

                $('.message-content-reply, #timeline-comment').slideUp('slow').velocity(
                    {opacity: 0},
                    {queue: false, duration: 'slow'}
                );
                $(".note-editable").html("");
                var reload = active.closest('form').data('reload');
                if (reload) {
                    $('#' + reload).load(document.URL + ' #' + reload, function () {
                        $('#' + reload + ' ul li:nth-child(2) .timeline-panel').addClass("highlight");
                        $('#' + reload + ' ul li:nth-child(2) .timeline-panel').delay("5000").removeClass("highlight");

                        summernote();
                    });

                }


            }
        });
    }

    return false;
});

//ajax page section reload
$(document).on("click", '.section-reload #send', function (e) {
    e.preventDefault();
    NProgress.start();
    $('#tasks-tab').load(document.URL + ' #tasks-tab');

    NProgress.done();

});

$(document).on("click", '.dynamic-reload', function (e) {
    var reload = $(this).data('reload');
    if (reload) {
        $('#' + reload).load(document.URL + ' #' + reload, function (data) {
            easyPie();
        });

    }
});


$(document).on("click", '.dynamic-form .send', function (e) {
    $(this).closest('form').validator();
    e.stopPropagation();
    e.preventDefault();
    valid = true;
    var thisinput = $(this);
    $('input').filter('[required]:visible').each(function (i, requiredField) {

        if ($(requiredField).val() == '') {
            valid = false;
            $('.modal').animate({
                scrollTop: $(requiredField).offset().top
            }, 500);
            $(requiredField).parent().addClass("has-error");
            thisinput.text().replace('<i class="icon dripicons-loading spin-it"></i> ', '');
        }
    });

    if (valid) {
        var content = $('textarea.summernote-modal').summernote('code');
        var url = $(this).closest('form').attr('action');
        var baseurl = $(this).closest('form').data('baseurl');
        var active = $(this);
        var data = new FormData($(this).closest('form')[0]);

        $.ajax({
            type: "POST",
            url: url,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            data: data,
            success: function (data, textStatus, jqXHR) {
                if (typeof data.error === 'undefined') {

                }
                else {

                    console.log('ERRORS: ' + data.error);
                }
                var reload = active.closest('form').data('reload');
                var reload2 = active.closest('form').data('reload2');
                var reload3 = active.closest('form').data('reload3');

                if (reload) {

                    $.get(document.URL, function (data) {
                        $('#' + reload).parent("div").html($(data).find('#' + reload));
                        $('#' + reload2).parent("div").html($(data).find('#' + reload2));
                        $('#' + reload3).parent("div").html($(data).find('#' + reload3));
                        $('#gantData').html($(data).find('#gantData'));

                        $('#' + reload).velocity("transition.slideDownOut", {duration: 300});
                        $('#' + reload2).velocity("transition.slideDownOut", {duration: 300});

                        $('#' + reload + ' .checkbox-nolabel').labelauty({label: false});
                        $('#' + reload2 + ' .checkbox-nolabel').labelauty({label: false});

                        if ($('#' + reload3 + ' .checkbox-nolabel').length > 0)
                            $('#' + reload3 + ' .checkbox-nolabel').labelauty({label: false});

                        $('#' + reload).velocity("transition.slideUpIn", {duration: 300});
                        $('#' + reload2).velocity("transition.slideUpIn", {duration: 300});

                        //reload Modal
                        modalfunc();
                        keepmodal = active.data('keepmodal');
                        if (keepmodal === undefined) {
                            $('#mainModal').modal('hide');
                        } else {
                            active.closest('form')[0].reset();
                            $("#mainModal .note-editable").html("");
                        }
                        //remove loader icon from button
                        var value = active.text().replace('<i class="icon dripicons-loading spin-it"></i> ', '');
                        active.html(value);
                        //reload timers on task details
                        $(".timer__span").each(function () {
                            timertime = $(this).data("timertime");
                            timerid = "#" + $(this).attr("id");
                            timerstate = $(this).data("timerstate");

                            startTimer(timerstate, timertime, timerid);

                        });
                        $(".todo__close").click();
                        sorting_list(baseurl);
                        hideClosedTasks();
                        summernote();
                    });


                }

            },
            error: function (formData) {

                var reload = active.closest('form').data('reload');
                var reload2 = active.closest('form').data('reload2');
                var reload3 = active.closest('form').data('reload3');

                if (reload) {

                    $.get(document.URL, function (data) {
                        $('#' + reload).parent("div").html($(data).find('#' + reload));
                        $('#' + reload2).parent("div").html($(data).find('#' + reload2));
                        $('#' + reload3).parent("div").html($(data).find('#' + reload3));
                        $('#gantData').html($(data).find('#gantData'));

                        $('#' + reload).velocity("transition.slideDownOut", {duration: 300});
                        $('#' + reload2).velocity("transition.slideDownOut", {duration: 300});

                        $('#' + reload + ' .checkbox-nolabel').labelauty({label: false});
                        $('#' + reload2 + ' .checkbox-nolabel').labelauty({label: false});

                        $('#' + reload).velocity("transition.slideUpIn", {duration: 300});
                        $('#' + reload2).velocity("transition.slideUpIn", {duration: 300});

                        //reload Modal
                        modalfunc();
                        keepmodal = active.data('keepmodal');
                        if (keepmodal === undefined) {
                            $('#mainModal').modal('hide');
                        } else {
                            active.closest('form')[0].reset();
                            $("#mainModal .note-editable").html("");
                        }
                        //remove loader icon from button
                        var value = active.text().replace('<i class="icon dripicons-loading spin-it"></i> ', '');
                        active.html(value);
                        //reload timers on task details
                        $(".timer__span").each(function () {
                            timertime = $(this).data("timertime");
                            timerid = "#" + $(this).attr("id");
                            timerstate = $(this).data("timerstate");

                            startTimer(timerstate, timertime, timerid);

                        });
                        $(".todo__close").click();
                        sorting_list(baseurl);
                        hideClosedTasks();
                        summernote();
                    });


                }

            }
        });

        return false;
    }
});


//fc-dropdown


$(document).on("click", '.fc-dropdown--trigger', function (e) {
    e.preventDefault();
    if (!$(this).hasClass('fc-dropdown--active')) {
        $(this).addClass('fc-dropdown--active');
        $(this).next('.fc-dropdown').addClass('fc-dropdown--open animated fadeIn');
    } else {
        $('.fc-dropdown--trigger').removeClass('fc-dropdown--active');
        $(this).next('.fc-dropdown').removeClass('fc-dropdown--open animated fadeIn');

    }
});

$(".notification-center__header > a").on("click", function (e) {
    e.preventDefault();

    $this = $(this);
    $href = $this.attr("data-href");

    $this.parents(".fc-dropdown").find(".dropdown-content").fadeOut(function () {
        $this.parents(".fc-dropdown").addClass("fc-dropdown--open");
        $($href).show();
    });

    $(".notification-center__header a").removeClass("active");
    $this.addClass("active");
});

$('.content-area, .fc-dropdown a').click(function () {
    /* hide fc-dropdown */
    $('.fc-dropdown').removeClass('fc-dropdown--open animated fadeIn');
    /* hide side menu */
    $(".side").removeClass('menu-action');
    $(".sidebar-bg").removeClass('show');
});


$('.fc-dropdown').click(function (event) {
    event.stopPropagation();
});

//Project Notes
$(document).on("click", '.note-form #send', function (e) {
    var button = this;
    var content = $('textarea[name="note"]').html($('#textfield').summernote('code'));
    var url = $(this).closest('form').attr('action');
    var note = $(this).closest('form').serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: note,
        success: function (data) {
            var value = $(button).text();
            var str = value.replace('<i class="icon dripicons-loading spin-it"></i> ', "");
            $(button).html(str);
            $('#changed').velocity("transition.fadeOut");
        },
        error: function (data) {

            var value = $(button).text();
            var str = value.replace('<i class="icon dripicons-loading spin-it"></i> ', "");
            $(button).html(str);
            $('#changed').velocity("transition.fadeOut");
        }
    });

    return false;

});
$(document).on("focus", '#_notes .note-editable', function (e) {
    $('#changed').velocity("transition.fadeIn");
});
$(document).on("click", '#_notes .addtemplate', function (e) {
    $('#changed').velocity("transition.fadeIn");
});
$(document).on("click", '.expand', function (e) {
    $('.sec').velocity("transition.fadeIn");
});


$('.to_modal').click(function (e) {
    e.preventDefault();
    var href = $(e.target).attr('href');
    if (href.indexOf('#') == 0) {
        $(href).modal('open');
    } else {
        $.get(href, function (data) {
            $('<div class="modal fade" >' + data + '</div>').modal();
        });
    }
});


//Clickable rows table#cprojects td,
$(document).on("click", 'table#projects td, table#clients td, table#invoices td,  table#cinvoices td, table#tinvoices td, table#estimates td, table#cestimates td, table#quotations td, table#messages td, table#cmessages td, table#subscriptions td, table#csubscriptions td, table#tickets td, table#ctickets td', function (e) {

    var id = $(this).parent().attr("id");
    if (id && !$(this).hasClass("noclick")) {
        var site = $(this).closest('table').attr("rel") + $(this).closest('table').attr("id");
        if (!$(this).hasClass('option')) {
            window.location = site + "/view/" + id;
        }
    }
});
//Clickable rows
$(document).on("click", 'table#cprojects td', function (e) {

    var id = $(this).parent().attr("id");
    if (id && !$(this).hasClass("noclick")) {
        var site = $(this).closest('table').attr("rel") + $(this).closest('table').attr("id");
        if (!$(this).hasClass('option')) {
            alert(site + "/view/" + id+"/tasks");
            window.location = site + "/view/" + id+"/tasks";

        }
    }
});
$(document).on("click", 'table#media td', function (e) {
    var id = $(this).parent().attr("id");
    if (id) {
        var site = $(this).closest('table').attr("rel");
        if (!$(this).hasClass('option')) {
            window.location = site + "/view/" + id;
        }
    }
});
$(document).on("click", 'table#custom_quotations_requests td', function (e) {
    var id = $(this).parent().attr("id");
    if (id) {
        var site = $(this).closest('table').attr("rel");
        if (!$(this).hasClass('option')) {
            window.location = "quotations/cview/" + id;
        }
    }
});
$(document).on("click", 'table#quotation_form td', function (e) {
    var id = $(this).parent().attr("id");
    if (id) {
        var site = $(this).closest('table').attr("rel");
        if (!$(this).hasClass('option')) {
            window.location = "formbuilder/" + id;
        }
    }
});


/* -------------- Summernote WYSIWYG Editor ------------- */
function summernote() {
    $('.summernote').summernote({
        height: "200px",
        shortcuts: false,
        disableDragAndDrop: true,
        toolbar: [
            ['style', ['style']], // no style button
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link']], //for Custom Templates
        ]
    });

    $('.summernote-with-mentions').summernote({
        height: "200px",
        shortcuts: false,
        disableDragAndDrop: true,
        callbacks: {
            onKeydown: function (e) {
                if (e.keyCode === 13) {
                    var e = jQuery.Event("keydown", {keyCode: 20});

                    $(this).trigger(e);
                    $(this).trigger('summernote.change');
                }
            }
        },
        toolbar: [
            ['style', ['style']], // no style button
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link']], //for Custom Templates
        ],
        hint: [
            {
                mentions: app.mentions,
                match: /\B@(\w*)$/,
                search: function (keyword, callback) {
                    var mentions = this.mentions;

                    callback($.grep(mentions, function (item) {
                        return item.indexOf(keyword) == 0;
                    }));
                },
                content: function (item) {
                    return '@' + item;
                }
            }
        ]
    });

    $('.summernote-nb-with-mentions').summernote({
        height: "auto",
        shortcuts: false,
        disableDragAndDrop: true,
        toolbar: [],
        callbacks: {
            onKeydown: function (e) {
                if (e.keyCode === 13) {
                    if (!$('.note-hint-popover').is(":visible")) {
                        $(this).trigger('summernote.change');
                        $(this).closest('form').submit();
                        return false;
                    }
                }
            }
        },
        hint: [
            {
                mentions: app.mentions,
                match: /\B@(\w*)$/,
                search: function (keyword, callback) {
                    var mentions = this.mentions;

                    callback($.grep(mentions, function (item) {
                        return item.indexOf(keyword) == 0;
                    }));
                },
                content: function (item) {
                    return '@' + item;
                }
            }
        ]
    });

    // var postForm = function () {
    //     var content = $('textarea[name="content"]').html($('#textfield').summernote('code'));
    // }
}
summernote();
$('.summernote-note').summernote({
    height: "360px",
    shortcuts: false,
    disableDragAndDrop: true,
    toolbar: [
        ['insert', ['link']], //for Custom Templates
        ['style', ['style']], // no style button
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],

    ]
});
var postForm = function () {
    var content = $('textarea[name="note"]').html($('#textfield').summernote('code'));
}

$('.summernote-big').summernote({
    height: "450px",
    shortcuts: false,
    disableDragAndDrop: true,
    toolbar: [
        ['insert', ['link']], //for Custom Templates
        ['style', ['style']], // no style button
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],

    ]
});


/* -------------- Summernote WYSIWYG Editor ------------- */


//Custom select plugin
$(".chosen-select").chosen({scroll_to_highlighted: false, disable_search_threshold: 4, width: "100%"});


//notify

$('.notify').velocity({
    opacity: 1,
    right: "10px",
}, 800, function () {
    $('.notify').delay(3000).fadeOut();
});


// List striped
$("ul.striped li:even").addClass("listevenitem");

//Form validation
$("input,select,textarea").not("[type=submit]").jqBootstrapValidation();

$('.use-tooltip').tooltip();
$('.tt').tooltip();

$('.po').popover({html: true});

//change comma to point
$(document).on("change", '.comma-to-point', function (e) {
    var str = $(this).val().replace(",", ".");
    $(this).val(str);
});

$(document).on("click", '.po-close', function (e) {
    $('.po').popover('hide');
});
$(document).on("click", '.po-delete', function (e) {
    $(this).closest('tr').velocity("transition.slideRightOut");
});

// Checkbox Plugin
$(".checkbox").labelauty();
$(".checkbox-nolabel").labelauty({label: false});

//Checkbox for slider enable/disable
$(".lbl").click(function () {
    var isDisabled = $("#slider-range").slider("option", "disabled");
    if (isDisabled) {
        $("#slider-range").slider("option", "disabled", false);
    } else {
        $("#slider-range").slider("option", "disabled", true);
    }

});

$("body").on("click", "#toggle_class_checkboxes", function () {
    tglclass = $(this).data("toggle-class");
    $(".checkboxlist ." + tglclass + " .checkbox").each(function () {
        chk = $(this).is(":checked");
        $(this).prop("checked", !chk);
    });
});

$("body").on("click", "#toggle_all_checkboxes", function () {
    alltoggled = $(this).data("all-toggled");
    toggleValue = ($(this).data("all-toggled") == "true") ? false : true;
    $(".checkboxlist .checkbox").each(function () {
        $(this).prop("checked", toggleValue);
    });
    (toggleValue) ? $(this).data("all-toggled", "true") : $(this).data("all-toggled", "false");
});

$("body").on("click", ".clear-date", function () {
    input = $(this).next();
    var inst = input.flatpickr();
    inst.clear();
});


//slider config
$("#slider-range").slider({
    range: "min",
    min: 0,
    max: 100,
    value: 1,
    slide: function (event, ui) {
        $("#progress-amount").html(ui.value);
        $("#progress").val(ui.value);
    }
});

//upload button
function uploaderButtons(preClass) {
    $(document).on("change", preClass + ' #uploadBtn', function (e) {
        var value = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $(preClass + " #uploadFile").val(value);
    });
    $(document).on("change", preClass + ' #uploadBtn2', function (e) {
        var value = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $(preClass + " #uploadFile2").val(value);
    });
    $(document).on("change", preClass + ' #uploadBtn3', function (e) {
        var value = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $(preClass + " #uploadFavicon").val(value);
    });
}
uploaderButtons("");

// Item Selector
function itemSelector() {
    $('.additem').click(function (e) {
        $('#item-selector').slideUp('fast');
        $('#item-editor').delay(300).slideDown('fast');
        $('#item-editor input').attr('required', true);
        $('form').validator();
    });
}

// Calendar Color Selector
function colorSelector() {
    $('.color-selector input[type="radio"]').click(function (e) {
        $('.color-selector').removeClass("selected");
        $(this).parent().addClass("selected");
    });
}

// InmputMask
function customInputMask() {
    $(".decimal").inputmask("decimal",
        {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            digitsOptional: false,
            autoGroup: true,
            placeholder: "00.00",
            rightAlign: false,
            removeMaskOnSubmit: true
        });
}
customInputMask();

//field disable switcher
$(document).on("change", '.switcher', function (e) {
    var fieldID = $(this).data('switcher');

    if ($(this).val() == "" || $(this).val() == "0") {
        $('#' + fieldID).attr("disabled", true);
        $('#' + fieldID).val('0');
        $('#' + fieldID).trigger("chosen:updated");

    } else {
        $('#' + fieldID).removeAttr("disabled");
        $('#' + fieldID).trigger("chosen:updated");
    }
});

//client -> project choser
$(document).on("change", '.getProjects', function (e) {
    var fieldID = $(this).data('destination');
    var selectedValue = $(this).val();

    if (selectedValue == "" || selectedValue == "0") {
        $('#' + fieldID + ' optgroup').attr("disabled", true);
        $('#' + fieldID).val('0');
        $('#' + fieldID).trigger("chosen:updated");

    } else {
        $('#' + fieldID + ' optgroup').attr("disabled", true);
        $('#' + fieldID).val('0');
        $('#optID_' + selectedValue).removeAttr("disabled");
        $('#' + fieldID).trigger("chosen:updated");
    }
});


//on todo-checkbox click
/*      $(document).on("click", '.todo-checkbox', function (e) {

 var url = $(this).data('link');
 console.log($(this).parents('li'));


 $.get(url, function(data) {

 }).done(function() {  });


 }); */

//message reply slide down
$(document).on("click", '.message-reply-button', function (e) {

    $('.summernote-ajax').summernote({
        height: "200px",
        shortcuts: false,
        toolbar: [
            //['style', ['style']], // no style button
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', []], //for Custom Templates
        ]
    });
    $(".message-content-reply").slideDown('slow').velocity(
        {opacity: 1},
        {queue: false, duration: 'slow'}
    );
})

//Timeline Comment field slide down
$(document).on("click", '.open-comment-box', function (e) {
    $(".add-comment").slideToggle('slow').velocity(
        {opacity: 1},
        {queue: false, duration: 'slow'}
    );

});

//Mobile Menu
$(document).on("click", '.menu-trigger', function (e) {
    $(".side").addClass('menu-action');
    $(".sidebar-bg").addClass('show');
    /*$(".sidebar, .navbar-header").addClass( 'show');*/


});

//check all checkboxes
$("#checkAll").click(function () {
    $('input:checkbox').not(this).prop('checked', this.checked);
});
$('#checkAll, .bulk-box').click(function () {
    if ($('.bulk-box:checked').length) {
        $("#bulk-button").addClass("btn-success");
    } else {
        $("#bulk-button").removeClass("btn-success");
    }
});
$(".bulk-dropdown li").click(function () {
    NProgress.start();
    var values = $('input:checkbox:checked.bulk-box').map(function () {
        return this.value;
    }).get();
    $('#list-data').val(values);
    var action = $("#bulk-form").attr('action');
    $("#bulk-form").attr('action', action + $(this).data("action"));
    $('#bulk-form').submit();

});

//bulk action setter
$(document).on("click", '.bulk-dropdown ul li a', function (e) {
    var action = $("#bulk-form").attr('action');
    $("#bulk-form").attr('action', action + $(this).data("action"));

});

//fade in
$(document).on("click", '#fadein', function (e) {
    $(".fadein").toggleClass("slide");


});

$(document).on("click", '.sortListTrigger', function (e) {
    sortList();

});
function sortList() {
    var mylist = $('ul.sortlist');
    var listitems = mylist.children('li').get();
    listitems.sort(function (a, b) {
        var compA = $(a).attr("class").split(' ').toString().toUpperCase();
        var compB = $(b).attr("class").split(' ').toString().toUpperCase();
        return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
    })
    $.each(listitems, function (idx, itm) {
        mylist.append(itm);
    });
}


function startTimer(state, starttime, timerId) {
    $(timerId).timer({
        seconds: starttime
    });
    $(timerId).timer(state);
}

function refreshNotification(){
    $.ajax({
        url: '/projects/gettasknotifiction',
        dataType:"JSON",
        type: "GET",
        success: function (data) {
            if(data.length > 0){
                $("#i_task_alert").html('<span class="topbar__icon_alert"></span>');
                $("#ul_task_notifiction").html("");
                var notifcation_content;
                for(var i=0; i<data.length;i++){
                    $("#ul_task_notifiction").append("<li><span>"+data[i].project_name+"</span><a href=\"/projects/view/"+data[i].project_id+"/tasks\">"+data[i].name+"</a><span id=\"notification_timer"+data[i].id+"\"  class=\"pull-right badge timer__badge resume\"></span></li>");

                    startTimer("resume", data[i].time_track, "#notification_timer"+data[i].id);
                }
            }else{
                $("#i_task_alert").html("");
                $("#ul_task_notifiction").html("<li><p class=\"truncate\">"+"No timers running"+"</p></li>");
            }
        },
        error: function(xhr){

        }
    });
}

/* 2.5.0 Form styling */
function fancyforms() {
    $(".form-control").each(function (index) {
        if ($(this).val().length > 0) {
            $(this).closest('.form-group').addClass('filled');
        }
    });
    $("select.chosen-select").each(function (index) {
        if ($(this).val() && $(this).val().length > 0) {
            $(this).closest('.form-group').addClass('filled');
        }
    });

    $(".form-control").on("focusin", function () {
        $(this).closest('.form-group').addClass("focus");
    });
    $(".chosen-select").on("chosen:showing_dropdown", function () {
        $(this).closest('.form-group').addClass("focus");
    });
    $(".chosen-select").on("chosen:hiding_dropdown", function () {
        $(this).closest('.form-group').removeClass("focus");
    });

    $(".form-control").on("focusout", function () {
        $(this).closest('.form-group').removeClass("focus");
        if ($(this).val().length > 0) {
            $(this).closest('.form-group').addClass('filled');
        } else {
            $(this).closest('.form-group').removeClass('filled');
        }
    });
}
fancyforms();

/* Task list sorting function */
function sorting_list(baseurl) {
    $(".sortable-list").sortable({
        items: "li:not(.ui-state-disabled)",
        cancel: "p.truncate",
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true,
        forceHelperSize: true,
        connectWith: "ul.sortable-list",
        dropOnEmpty: true,
        receive: function (event, ui) {

            taskId = ui.item.context.id;
            taskId = taskId.replaceAll("milestonetask_", "");
            milestoneId = event.target.id;
            milestoneId = milestoneId.replaceAll("milestonelist_", "");
            href2 = baseurl + "projects/move_task_to_milestone/" + taskId + "/" + milestoneId;

            $.get(href2, function (data) {

                console.log(" task added to milestone ");
            });
            $("#" + event.target.id + " .notask").remove();
            if (ui.sender.context.childElementCount == 0) {
                $("#" + ui.sender.context.id).html('<li class="notask list-item ui-state-disabled">No tasks yet</li>');
                $("#" + event.target.id + " .notask").fadeIn();
            }
        },
        update: function (event, ui) {
            formData = $(this).sortable("serialize", {key: "x"});
            formData = formData.replaceAll("&", "-");
            formData = formData.replaceAll("x=", "");
            list = $(this).attr("id");
            href = baseurl + "projects/sortlist/" + formData + "/" + list;
            $.get(href, function (data) {
                console.log("sorting updated");
            });
        },


    });
    $(".sortable-list").disableSelection();

    //Sorting function for Milestones
    $(".sortable-list2").sortable({
        items: "li.hasItems",
        cancel: "p.truncate",
        connectWith: "ul.sortable-list2",
        placeholder: "ui-state-highlight-milestone",
        forcePlaceholderSize: true,
        forceHelperSize: true,
        dropOnEmpty: true,
        update: function (event, ui) {
            formData3 = $(this).sortable("serialize", {key: "x"});
            formData3 = formData3.replaceAll("&", "-");
            formData3 = formData3.replaceAll("x=", "");
            list3 = $(this).attr("id");
            href3 = baseurl + "projects/sort_milestone_list/" + formData3 + "/" + list3;
            $.get(href3, function (data) {
                console.log(" Milestone list sorting updated");
            });

        },
        beforeStop: function (ev, ui) {
            if ($(ui.item).hasClass('hasItems') && $(ui.placeholder).parent()[0] != this) {
                $(this).sortable('cancel');
            }
        }


    });
}

function taskviewer() {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 216) {
            $('.pin-to-top').addClass('fixed-div');
            height = $(window).height();
            height = height - 50;
            $(".taskviewer-content").css("height", height);
        } else {
            height = $(window).height();
            height = height - 270 + $(this).scrollTop();
            $(".taskviewer-content").css("height", height);
            $('.pin-to-top').removeClass('fixed-div');
        }
    });
    //on task click
    $(document).on("click", '#task-list li p.name', function (e) {
        taskId = $(this).data("taskid");
        $(".todo-details").hide();
        $("#" + taskId).show();
        $(".highlight__task").removeClass("highlight__task");
        $(this).parents("li").addClass("highlight__task");
        itemdetails = $(this).parents("li").find(".todo-details").html();
        $(".taskviewer-content").html(itemdetails);
        $(".taskviewer-content").show();
        $(".task-container-left").addClass("col-sm-8");
        tkKey = $("#tkKey").html();
        baseURL = $("#baseURL").html();
        projectId = $("#projectId").html();

        inlineEdit(tkKey, baseURL, projectId);


    });
    $(document).on("click", '.task__options__button', function (e) {
        timerId = $(this).data("timerid");
        $(".task__options__timer." + timerId).toggleClass("hidden");
        if ($(this).hasClass("task__options__button--red")) {
            $("#" + timerId).timer('pause');
            $("#notification_" + timerId).timer('pause');

        } else {
            $("#" + timerId).timer('resume');
            $("#notification_" + timerId).timer('resume');
        }
        $("#" + timerId).toggleClass("pause");
        $("#notification_" + timerId).toggleClass("pause");
    });
    $(document).on("click", '.todo__close', function (e) {
        $(".taskviewer-content, .todo-details").fadeOut();
        $(".task-container-left").removeClass("col-sm-8");
        $(".highlight__task").removeClass("highlight__task");
    });

    height = $(window).height();
    height = height - 270;
    $(".taskviewer-content").css("height", height);
    $('.pin-to-top').removeClass('fixed-div');


}
taskviewer();

$.fn.editable.defaults.mode = 'inline';
function inlineEdit(tkKey, baseURL, projectId) {
    $('.synced-edit').on('save', function (e, params) {
        syncid = $(this).data("syncto");
        $("#" + syncid + " .name").html(params.newValue);
        $("#milestone" + syncid + " .name").html(params.newValue);
    });
    $('.synced-process-edit').on('save', function (e, params) {
        syncid = $(this).data("syncto");
        $("#" + syncid).css("width", params.newValue + "%");
    });
    $('.editable').editable({
        params: {
            fcs_csrf_token: tkKey
        },
        success: function (response, newValue) {
            console.log("attribute saved" + response);
        },
        error: function (response, newValue) {
            console.log(response);
        }

    });

    $('.editable-select').editable({
        params: {
            fcs_csrf_token: tkKey
        },
        //value: 2, 
        escape: false,
        sourceCache: false,

        source: baseURL + 'get_milestone_list/' + projectId,


    });
}

function ganttChart(ganttData) {

    $(function () {

        "use strict";
        $(".gantt").gantt({
            source: ganttData,
            minScale: "years",
            maxScale: "years",
            navigate: "scroll",
            itemsPerPage: 30,
            onItemClick: function (data) {
                console.log(data.id);
            },
            onAddClick: function (dt, rowId) {

            },
            onRender: function () {
                console.log("chart rendered");
            }
        });

    });


}
$('.priority-selector--group span').on('click', function (e, params) {
    valueOfSelector = $(this).data("priority");
    $('.priority-selector--group span').css("z-index", "1");
    $(this).css("z-index", "2");
    $(".priority-input").val(valueOfSelector);
    $(".priority-selector--group span:nth-child(1)").velocity({'right': '0px'}, {
        queue: false,
        easing: "easeOutCubic",
        duration: 200
    });
    $(".priority-selector--group span:nth-child(2)").velocity({'right': '0px'}, {
        queue: false,
        easing: "easeOutCubic",
        duration: 200
    });

});
$('.priority-selector--group').on({
    mouseenter: function () {
        $(".priority-selector--group span:nth-child(2)").velocity({'right': '15px'}, {
            easing: "easeOutCubic",
            duration: 200
        });
        $(".priority-selector--group span:nth-child(1)").velocity({'right': '30px'}, {
            easing: "easeOutCubic",
            duration: 200
        });
    },
    mouseleave: function () {
        $(".priority-selector--group span:nth-child(1)").velocity({'right': '0px'}, {
            queue: false,
            easing: "easeOutCubic",
            duration: 200
        });
        $(".priority-selector--group span:nth-child(2)").velocity({'right': '0px'}, {
            queue: false,
            easing: "easeOutCubic",
            duration: 200
        });

    }
});
$(document).on("keydown", "#new-user-emails .form-group:not([data-used]) .email", function () {
    var val = $(this).val();

    if ($(this).parents('.form-group').attr('data-used') === undefined && val !== "") {
        var $new_elem = $("#new-user-emails .form-group").first().clone();
        $new_elem.find("input").val("");
        $new_elem.removeAttr('data-used');

        $(this).parents('.form-group').attr('data-used', 'true');

        $("#new-user-emails").append($new_elem);
    }
});
$(document).on("click", "#btn-advanced-settings", function(e) {
    e.preventDefault();
    if($("#advanced-settings").is(":visible")) {
        $("#btn-advanced-settings i").addClass("fa-angle-down");
        $("#btn-advanced-settings i").removeClass("fa-angle-up");
    } else {
        $("#btn-advanced-settings i").addClass("fa-angle-up");
        $("#btn-advanced-settings i").removeClass("fa-angle-down");
    }
    $("#advanced-settings").slideToggle();
});
function blazyloader() {
    // Initialize
    var bLazy = new Blazy({
        loadInvisible: true,
    });

}
function hideClosedTasks() {
    if (localStorage.hide_tasks == "1") {
        $("li.done").addClass("hidden");
        $(".toggle-closed-tasks").css("opacity", "0.6");
    }
}
function deleteRow() {
    $('.deleteThisRow').on('click', function (e, params) {
        $(this).parents("tr").slideUp("fast");
    });
}
function dropzoneloader(url, dropslug) {
    Dropzone.autoDiscover = false;
    Dropzone.options.dropzoneForm = {
        previewsContainer: ".mediaPreviews",
        dictDefaultMessage: dropslug,
        thumbnailWidth: 200,
        thumbnailHeight: 200,
        init: function () {
            this.on("success", function (file) {
                console.log(file);
                $('.data-media tbody').prepend('<tr id="' + file.xhr.responseText + '" role="row" class="odd"><td class="hidden sorting_1"></td><td onclick="">' + file.name + '</td><td class="hidden-xs">' + file.name + '</td><td class="hidden-xs"></td><td class="hidden-xs"><span class="label label-info tt" title="" data-original-title="Download Counter">0</span></td><td class="option " width="10%"><button type="button" class="btn-option btn-xs" ><i class="icon dripicons-cross"></i></button><a href="/projects/media/12/update/' + file.xhr.responseText + '" class="btn-option" data-toggle="mainmodal"><i class="icon dripicons-gear"></i></a></td></tr>');
            });
        }

    };
    $("#dropzoneForm").dropzone({url: url});

}

jQuery(function ($) {
    $(".topbar__center > span").on("click", function(e) {
        e.preventDefault();

        var $title_chooser = $(this).next();

        if($title_chooser.is(".chooser") && !$title_chooser.hasClass("open")) {
            setTimeout(function() {
                $title_chooser.addClass("open");
                $title_chooser.find("button").attr("aria-expanded", true);
            }, 100);
        }
    });
    
    $(".side .menu-trigger").on("click", function (e) {
        e.preventDefault();
        var $body = $("body");
        var $btn = $(this);
        var is_nav_open = $body.hasClass("nav-close");
        var $toggle_icon = $btn.find("i");
        if (!is_nav_open) {
            $body.addClass("nav-close");
            $("#mainwrapper > .side .nav-text, #mainwrapper > .side h4").fadeOut(500);
            $(".side .branding .navbar-brand").fadeOut(500);
            setTimeout(function () {
                $("#mainwrapper > .content-area").animate({
                    marginLeft: 55
                });
                $(".side .branding").animate({
                    width: 55
                });
                $("#mainwrapper > .side, #mainwrapper > .side > .sidebar-bg").animate({
                    width: 55
                }, {
                    complete: function () {
                        $btn.removeClass("transitioning");
                        $toggle_icon.attr("class", "fa fa-bars");
                        Cookies.set('navopen', "nav-close", {
                            expires: 365,
                        });
                    }
                });
            }, 0);
            $("#main").css("left","60px");
        } else {
            console.log('asd');
            $("#main").css("left","200px");
            $body.removeClass("nav-close");
            $("#mainwrapper > .content-area").animate({
                marginLeft: 200
            });

            $(".side .branding").animate({
                width: 200
            });

            $("#mainwrapper > .side, #mainwrapper > .side > .sidebar-bg").animate({
                width: 200
            }, {
                complete: function () {
                    $("#mainwrapper > .side .nav-text, #mainwrapper > .side h4").fadeIn();
                    $(".side .branding .navbar-brand").fadeIn();

                    $toggle_icon.attr("class", "far fa-arrow-to-left");
                    $btn.removeClass("transitioning");
                    localStorage.setItem('navopen', "true");
                    Cookies.set('navopen', "nav-open", {
                        expires: 365,
                    });
                }
            });
        }
    });
});