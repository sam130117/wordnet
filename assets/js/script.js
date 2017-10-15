// $(document).ready(function () {

    // $('#levenshtein-form').submit(function () {
    //     event.preventDefault();
    //
    //     var form = $(this);
    //     $.post( "algorithms/levenstain.php", form.serialize(), function(data) {
    //         data = JSON.parse(data);
    //         console.log(data.info);
    //         form.find('#levenshtein-result').html('Levenshtain distance: ' + data.distance);
    //         form.find('#levenshtein-insert').html('Insert number: ' + data.insert);
    //         form.find('#levenshtein-replace').html('Replace number: ' + data.replace);
    //         form.find('#levenshtein-delete').html('Delete number: ' + data.delete);
    //         // form.append(data.info[0]['lemma']);
    //     });
    // });

    // $('#main-form').submit(function () {
    //     event.preventDefault();
    //
    //     var form = $(this);
    //     $.post( "algorithms/levenstain.php", form.serialize(), function(data) {
    //         data = JSON.parse(data);
    //         form.find('#levenshtein-result').html('Results: ' + data.result);
    //     });
    // });
// });