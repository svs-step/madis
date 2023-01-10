(function ($) {
    console.log('BLA')
    $(document).ready(function() {
        console.log('ready', $)
        var $wrapper = $('.js-collection-wrapper');

        $wrapper.on('click', '.js-remove-item', function(e) {
            e.preventDefault();
            $(this).closest('.js-collection-item')
                .fadeOut()
                .remove();
        });

        $wrapper.on('click', '.js-collection-add', function(e) {
            console.log('adding')
            e.preventDefault();
            // Get the data-prototype explained earlier
            var prototype = $(this).parent().data('prototype');
            // get the new index
            var index = $(this).parent().data('index');
            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);
            // increase the index with one for the next item
            $(this).parent().data('index', index + 1);
            // Display the form in the page before the "new" link
            $(this).before(newForm);
        });
    });
})(jQuery);
