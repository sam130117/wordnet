$(document).ready(function () {

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

    $('#main-form').submit(function () {
        event.preventDefault();
        var form = $(this);

        $.post( "algorithms/levenstain.php", form.serialize(), function(data) {
            data = JSON.parse(data);
            var list = "";
            var result = data.result;
            for (var property in result){
                console.log(result[property]);
                if(result[property].indexOf('/') !== -1)
                {
                    console.log(result[property]);
                    list = list + '<span class="error-message">' + result[property] + '</span>' + '-';
                }
                else {
                    list = list + result[property] + '-';
                }
            }
            form.find('#processed-text').innerHTML = list;
        });
    });
});

/*

 Alice was beginning to get very tired of sitting by her sister on the bank, and of having nothing to do:
 once or twice she had peeped into the book her sister was reading, but it had no pictures or conversations
 in it, ‘and what is the use of a book,’ thought Alice ‘without pictures or conversation?’

 */