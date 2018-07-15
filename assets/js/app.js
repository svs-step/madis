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
    });

    $('.delay-group').each(function(idx) {
        var number = $(this).find('input[id$="number"]');
        var period = $(this).find('select[id$="period"]');
        var check = $(this).find('input[id$="otherDelay"]');
        var comment = $(this).find('textarea[id$="comment"]');

        // Disable comment on unchecked line
        number.prop('disabled', check.is(':checked'));
        period.prop('disabled', check.is(':checked'));
        comment.prop('disabled', !check.is(':checked'));

        // Check | Uncheck : Disable comment on unchecked line
        check.on('change', function() {
            var number = $(this).closest('.delay-group').find('input[id$="number"]');
            var period = $(this).closest('.delay-group').find('select[id$="period"]');
            var comment = $(this).closest('.delay-group').find('textarea[id$="comment"]');
            number.prop('disabled', check.is(':checked'));
            period.prop('disabled', check.is(':checked'));
            comment.prop('disabled', !check.is(':checked'));
        })
    });

    $('#mesurement_status').on('change', function() {
        var notApplicableCheck = $(this).find("input[value='not-applicable']");
        var etablishedCheck = $('#mesurement_etablished');
        var planificationDateCheck = $('#mesurement_planificationDate');

        console.log(etablishedCheck);
        console.log(planificationDateCheck);

        etablishedCheck.prop('disabled', notApplicableCheck.is(':checked'));
        planificationDateCheck.find('select').each(function() {
            $(this).prop('disabled', notApplicableCheck.is(':checked'));
        });
    });
});
