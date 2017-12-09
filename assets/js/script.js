$(document).ready(function () {
    $body = $("body");
    $(document).on({
        ajaxStart: function() { $body.addClass("loading"); },
        ajaxStop: function() { $body.removeClass("loading"); }
    });

    $('#main-form').submit(function () {
        event.preventDefault();
        var form = $(this);
        var t0 = performance.now();
        $.post( "algorithms/levenstain.php", form.serialize(), function(data) {
            console.log(data);
            data = JSON.parse(data);
            var list = "";
            var result = data.result;
            for (var property in result){
                if(result[property].indexOf('/') !== -1)
                {
                    var word = result[property].replace(/\//g, '');
                    list = list + '<span class="error-message">' + word + '</span>' + ' ';
                }
                else if(result[property].indexOf('-') !== -1){
                    var word = result[property].replace(/-/g, '');
                    list = list + '<span class="success-message">' + word + '</span>' + ' ';
                }
                else {
                    list = list + result[property] + ' ';
                }
            }
            form.find('#processed-text').html(list);
            var t1 = performance.now();
            console.log("Call took " + (t1 - t0) + " milliseconds.");
        });


    });
});

/*

 Alice was beginning to get very tired of sitting by her sister on the bank, and of having nothing to do:
 once or twice she had peeped into the book her sister was reading, but it had no pictures or conversations
 in it, ‘and what is the use of a book,’ thought Alice ‘without pictures or conversation?’

 */