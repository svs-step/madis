$(document).ready(function() {
    /* Simple fields */
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

    /* REGISTRY complex forms */
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
        console.log(number);
        console.log(period);
        console.log(check);
        console.log(comment);

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

    var textArea = $('#treatment_coordonneesResponsableTraitement');
    $('#treatment_author').on('change', function() {
        var textArea = $('#treatment_coordonneesResponsableTraitement');
        textArea.prop('disabled', ($(this).val() === 'processing_manager'));
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

    // Check Collectivity services | onLoad & onChange
    checkCollectivityDifferentServices();
    $('#collectivity_isServicesEnabled').on('change', function() {
        checkCollectivityDifferentServices();
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

    // Check request state hide or show reason block | onLoad & onChange
    checkRequestStateRejectionReason();
    $("#request_state").on('change', function() {
        checkRequestStateRejectionReason()
    });

    checkUserCollectivitesReferees();
    $('input:radio[name="user[roles]"]').change(function() {
        checkUserCollectivitesReferees();
    });

    // Check Contractor dpo | onLoad & onChange
    checkContractorHasDpo();
    $('#contractor_hasDpo').on('change', function() {
        checkContractorHasDpo();
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

function checkDifferentServices(id, boxId)
{
    boxId.find('input').each(function() {
        $(this).prop('disabled', !id.is(':checked'));
    });
    boxId.find('select').each(function() {
        $(this).prop('disabled', !id.is(':checked'));
    });
    boxId.find('#add-services').each(function() {
        if ($('#collectivity_isServicesEnabled').prop('checked')) {
            $('#add-services').css('display', 'inline-table');
        } else {
            $('#add-services').css('display', 'none');
        }
    })
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

function checkCollectivityDifferentServices()
{
    let differentServices = $('#collectivity_isServicesEnabled');
    let boxServices = $('body.user_collectivity.form #box-services');

    checkDifferentServices(differentServices, boxServices);
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

function checkRequestStateRejectionReason()
{
    let selectedCountry = $("#request_state").children("option:selected").val();
    let blockSateRejectionReason = $('#request_StateRejectionReason_div');
    let fieldSateRejectionReason = $('#request_stateRejectionReason');

    if (selectedCountry === "denied") {
        blockSateRejectionReason.show();
        fieldSateRejectionReason.prop('required',true);
    } else {
        blockSateRejectionReason.hide();
        fieldSateRejectionReason.prop('required',false);
        fieldSateRejectionReason.val("");
    }
}

function checkUserCollectivitesReferees()
{
    let selectedRole = $('input:radio[name="user[roles]"]:checked').val();
    let blockUserCollectivitesReferees = $('#user_collectivitesReferees_div');
    let fieldUserCollectivitesReferees = $('#user_collectivitesReferees');

    if ("ROLE_REFERENT" === selectedRole) {
        blockUserCollectivitesReferees.show();
        fieldUserCollectivitesReferees.prop('required',true);
    } else {
        blockUserCollectivitesReferees.hide();
        fieldUserCollectivitesReferees.prop('required',false);
        fieldUserCollectivitesReferees.val("");
    }
}

function checkContractorHasDpo()
{
    let hasDpo = $('#contractor_hasDpo');
    let boxDpo = $('body.registry_contractor.form #box-dpo');

    checkDifferentDpo(hasDpo, boxDpo);
}
