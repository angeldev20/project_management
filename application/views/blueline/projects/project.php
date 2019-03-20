<div id="project-section" data-project="<?= $id; ?>"></div>
<script>
    jQuery(function ($) {
        var $top = $(".mainnavbar .topbar__center");
        var $button = $('<button type="button" class="dropdown-toggle transparent" data-toggle="dropdown" aria-expanded="false"> <i class="far fa-angle-down"/> </button>');
        var $ul = $('<ul class="dropdown-menu dropdown-menu--small" role="menu"/>');
        var $dd = $('<div class="chooser"/>');
        var $icon_div = $('<div class="icon-div"/>');
        var $fav_icon = $('<i class="<?= ( $project->sticky ? 'fa' : 'far' ) ?> fa-star" />');

        $top.addClass("has-chooser");

        $fav_icon.on("click", function (e) {
            e.preventDefault();

            if ($(this).hasClass('far')) {
                $(this).removeClass('far');
                $(this).addClass('fa');
            } else {
                $(this).removeClass('fa');
                $(this).addClass('far');
            }

            $.ajax({
                url: '/projects/sticky_json/<?= $id ?>',
                dataType: 'JSON',
                success: function (d) {
                    console.log(d);
                },
                error: function (e) {
                    console.log(e);
                }
            });
        });

        $dd.append($button);
        $dd.append($ul);
        $icon_div.append($fav_icon);

        $top.append($dd);
        $top.prepend($icon_div);

        $.ajax({
            url: '/projects/data',
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
                if (data.status) {
                    data.projects.map(({id, name}) => {
                        $ul.append(`<li><a href="/projects/view/${id}/tasks">${name}</a></li>`);
                    });
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
</script>