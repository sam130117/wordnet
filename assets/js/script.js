$(document).ready(function () {

    $('#levenshtein-form').submit(function () {
        event.preventDefault();

        var form = $(this);
        $.post( "algorithms/levenstain.php", form.serialize(), function(data) {
            form.find('#levenshtein-result').html('Levenshtain distance: ' + data);
        });
    });


});