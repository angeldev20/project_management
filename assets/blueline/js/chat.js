var me = {};
me.name = "me";
me.avatar = "https://lh6.googleusercontent.com/-lr2nyjhhjXw/AAAAAAAAAAI/AAAAAAAARmE/MdtfUmC0M4s/photo.jpg?sz=48";

var slack = {};
slack.name = "slack";
slack.avatar = "https://a.slack-edge.com/7f1a0/plugins/app/assets/service_36.png";

function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

//-- No use time. It is a javaScript effect.
function insertChat(info, delay){
    if (delay === undefined){
        delay = 0;
    }
    var control = "";
    var avatar = "";
    var sec_avatar = "";
    var date = formatAMPM(new Date(info.date_time));


    if( info.second_url )
        sec_avatar = '<div class="ch-bg-secavatar"><img src="' + info.second_url + '"></div>';

    if( info.avatar_url )
        avatar = '<img class="img-circle ch-userpic" src="'+ info.avatar_url +'" />';
    else
        avatar = '<span class="topbar__userimage">' + info.avatar_str + '</span>';

    avatar += sec_avatar;

    if (info.is_me){

        control = '<li style="width:100%">' +
            '<div class="ch-msj ch-macro">' +
            '<div class="ch-avatar">' + avatar + '</div>' +
            '<div class="ch-text ch-text-l">' +
            '<p>'+ info.chat_message +'</p>' +
            '<p><small>'+date+'</small></p>' +
            '</div>' +
            '</div>' +
            '</li>';
    }else{

        control = '<li style="width:100%;">' +
            '<div class="ch-msj-rta ch-macro">' +
            '<div class="ch-text ch-text-r">' +
            '<p>'+info.chat_message+'</p>' +
            '<p><small>'+date+'</small></p>' +
            '</div>' +
            '<div class="ch-avatar">' + avatar + '</div>' +
            '</li>';
    }
    setTimeout(
        function(){
            $(".ch-ul").append(control);
            var content = $(".ch-content-frame");//document.getElementsByClassName("ch-content-frame");//
            content.animate({scrollTop: content[0].scrollHeight},"fast");
        }, delay);

}

function resetChat(){
    $(".ch-ul").empty();
}

/*$(".ch-mytext").on("keyup", function(e){
    if (e.which == 13){
        var text = $(this).val();
        if (text !== ""){
            insertChat("me", text);
            $(this).val('');
        }
    }
});*/

//-- Clear Chat
resetChat();

//-- NOTE: No use time on insertChat.