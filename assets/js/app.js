$(document).ready(function(){

    $('input[type=password]').each(function() {
        let input = $(this);
        let span = $(this).parent().find('span');
        span.on('mousedown', function() {
            input.attr('type', 'text');
        });
        span.on('mouseup', function() {
            input.attr('type', 'password');
        });
    });

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

    // Check Collectivity dpo | onLoad & onChange
    checkCollectivityDifferentDpo();
    $('#collectivity_differentDpo').on('change', function() {
        checkCollectivityDifferentDpo();
    });

    // Check Collectivity it manager | onLoad & onChange
    checkCollectivityDifferentItManager();
    $('#collectivity_differentItManager').on('change', function() {
        checkCollectivityDifferentItManager();
    });

    // Check Mesurement status | onLoad & onChange
    checkMesurementStatus();
    $('#mesurement_status').on('change', function() {
        checkMesurementStatus();
    });

    // Check Profile collectivity dpo | onLoad & onChange
    checkProfileDifferentDpo();
    $('#collectivity_differentDpo').on('change', function() {
        checkProfileDifferentDpo();
    });

    // Check Profile collectivity it manager | onLoad & onChange
    checkProfileDifferentItManager();
    $('#collectivity_differentItManager').on('change', function() {
        checkProfileDifferentItManager();
    });

    // Check Request concerned people | onLoad & onChange
    checkRequestConcernedPeople();
    $('#request_applicant_concernedPeople').on('change', function() {
        checkRequestConcernedPeople();
    });
});

function checkMesurementStatus()
{
    let notApplicableCheck = $('#mesurement_status').find("input[value='not-applicable']");
    let etablishedCheck = $('#mesurement_etablished');
    let planificationDateCheck = $('#mesurement_planificationDate');

    etablishedCheck.prop('disabled', notApplicableCheck.is(':checked'));
    planificationDateCheck.find('select').each(function() {
        $(this).prop('disabled', notApplicableCheck.is(':checked'));
    });
}

function checkRequestConcernedPeople()
{
    let applicantConcernedPeople = $('#request_applicant_concernedPeople');
    let boxConcernedPeople = $('body.registry_request #box-concerned-people');

    boxConcernedPeople.find('input').each(function() {
        $(this).prop('disabled', applicantConcernedPeople.is(':checked'));
    });
    boxConcernedPeople.find('select').each(function() {
        $(this).prop('disabled', applicantConcernedPeople.is(':checked'));
    });
}

function checkDifferentDpo(id, boxId)
{
    boxId.find('input').each(function() {
        $(this).prop('disabled', !id.is(':checked'));
    });
    boxId.find('select').each(function() {
        $(this).prop('disabled', !id.is(':checked'));
    });
    // Be sure to not disable checkbox
    id.prop('disabled', false);
}

function checkDifferentItManager(id, boxId)
{
    boxId.find('input').each(function() {
        $(this).prop('disabled', !id.is(':checked'));
    });
    boxId.find('select').each(function() {
        $(this).prop('disabled', !id.is(':checked'));
    });
    // Be sure to not disable checkbox
    id.prop('disabled', false);
}

function checkCollectivityDifferentDpo()
{
    let differentDpo = $('#collectivity_differentDpo');
    let boxDpo = $('body.user_collectivity.form #box-dpo');

    checkDifferentDpo(differentDpo, boxDpo);
}

function checkCollectivityDifferentItManager()
{
    let differentItManager = $('#collectivity_differentItManager');
    let boxItManager = $('body.user_collectivity.form #box-it-manager');

    checkDifferentItManager(differentItManager, boxItManager);
}

function checkProfileDifferentDpo()
{
    let differentDpo = $('#collectivity_differentDpo');
    let boxDpo = $('body.user_profile_collectivity.form #box-dpo');

    checkDifferentDpo(differentDpo, boxDpo);
}

function checkProfileDifferentItManager()
{
    let differentItManager = $('#collectivity_differentItManager');
    let boxItManager = $('body.user_profile_collectivity.form #box-it-manager');

    checkDifferentItManager(differentItManager, boxItManager);
}
