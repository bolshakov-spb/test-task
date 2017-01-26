$(Document).ready(function () {

    $('.input-area #submit').on('click', function () {
        var nn = $('.input-area #nickname');
        var text = $('.input-area #comment-input');
        var at = $('.input-area #ans_id').val();
        var level = $('.comment a[data-id=' + at + ']').parent('.comment').data('level');
        if (nn.val().length == 0 || text.val().length == 0) {
            alert('Заполнены не все поля');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'core/ajax.php',
            data: {
                act: 'add',
                name: nn.val(),
                text: text.val(),
                answer_to: at,
                level: level
            },
            success: function (data) {
                text.val('');
                $('.reply_mode .btn-x').click();
                if (at == 0) {
                    $('.view-area').append(data);
                    $("html,body").animate({scrollTop: $('footer').offset().top}, 'fast');
                } else {
                    $('.comment a[data-id=' + at + ']').parent('.comment').after(data);
                }
            }
        });
    });

    $('.view-area').on('click', '.comment a', function () {
        $('.reply_mode #ans_id').val($(this).data('id'));
        $('.reply_mode #ans_to').val($(this).parent('.comment').find('.nickname').html());
        $('html, body').animate({scrollTop: 0}, 'fast');
        $('.reply_mode').removeClass('active').addClass('active');
    });

    $('.reply_mode .btn-x').on('click', function () {
        $('.reply_mode #ans_id').val(0);
        $('.reply_mode #ans_to').val('');
        $('.reply_mode').removeClass('active');
    });

    $('.view-area').on('click', '.comment .btn-x', function () {
        $.ajax({
            type: 'POST',
            url: 'core/ajax.php',
            data: {
                act: 'remove',
                id: $(this).data('id')
            },
            success: function (data) {
                data = JSON.parse(data);
                data.forEach(function (element, index) {
                    $('.comment a[data-id=' + element + ']').parent('.comment').remove();
                });
            }
        });
    });

});