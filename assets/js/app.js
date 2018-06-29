$(document).ready(function(){
    $('.complex-choice-group').each(function(idx) {
        var check = $(this).find('.check input');
        var comment = $(this).find('.comment input');

        // Disable comment on unchecked line
        comment.prop('disabled', !check.is(':checked'));

        // Check | Uncheck : Disable comment on unchecked line
        check.on('change', function() {
            var comment = $(this).closest('.complex-choice-group').find('.comment input');
            comment.prop('disabled', !check.is(':checked'));
        })
    })
});
