$(document).ready(function() {
    $('a#button-star').click(function () {
        var button = $(this);
        var params = {
            'id': $(this).attr('data-id')
        };
        $.post("/product/star", params, function (data) {
            if (data.success) {
                button.hide();
                button.siblings('#button-unstar').show();
                button.siblings('#stars-count').html(data.starsCount);
            }
        });
        return false;
    });

    $('a#button-unstar').click(function () {
        var button = $(this);
        var params = {
            'id': $(this).attr('data-id')
        };
        $.post('/product/unstar', params, function (data) {
            if (data.success) {
                button.hide();
                button.siblings('#button-star').show();
                button.siblings('#stars-count').html(data.starsCount);
            }
        });
        return false;
    });
});
