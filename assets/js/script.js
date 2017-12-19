$(document).ready(function () {
    $body = $("body");
    $(document).on({
        ajaxStart: function() { $body.addClass("loading"); },
        ajaxStop: function() { $body.removeClass("loading"); }
    });

    $(document).on('click','.tooltip-text span',function(){
        var oldText = $(this).parent().siblings(".error-message");
        var newText = $(this).text();

        $(this).text(oldText.text());
        oldText.text(newText);
    });
    //
    // $('#original-text').keydown(function (e) {
    //
    //     if (e.ctrlKey && e.keyCode == 13) {
    //         document.getElementById('id01').style.display='block';
    //     }
    // });

    $('#main-form').submit(function () {
        event.preventDefault();
        var form = $(this);
        var t0 = performance.now();
        $.post( "algorithms/levenstain.php", form.serialize(), function(data) {

            data = JSON.parse(data);
            var list = "";
            var result = data.result;
            for (var i = 0; i < Object.keys(result).length; i++){

                if(!result[i]) continue;
                if(result[i]['word'].indexOf('/') !== -1)
                {
                    var word = result[i]['word'].replace(/\//g, '');
                    if(result[i]['similar-words'])
                    {
                        list = list + '<div class="tooltip-container"><span class="error-message">' + word + '</span>' +
                            '<span class="tooltip-text">';
                        for(var j = 0; j < result[i]['similar-words'].length; j++)
                        {
                            list = list + '<span>' + result[i]['similar-words'][j] + '</span>';
                        }
                        list = list + '</span></div> ';
                    }
                    else {
                        list = list + '<span class="error-message">' + word + '</span>' + ' ';
                    }

                }
                else if(result[i]['word'].indexOf('&') !== -1){
                    word = result[i]['word'].replace(/&/g, '');
                    // list = list + '<span class="success-message">' + word + '</span>' + ' ';
                    list = list + '<span>' + word + '</span>' + ' ';
                }
                else {
                    word = result[i]['word'].replace(/#/g, '');
                    list = list + '<span class="unknown-message">' + word + '</span>' + ' ';
                    // list = list + result[property] + ' ';
                }
            }
            form.find('#processed-text').html(list);
            var t1 = performance.now();
            console.log("Call took " + (t1 - t0) + " milliseconds.");
        });


    });
    // $('#add-word-form').submit(function () {
    //     event.preventDefault();
    //     var form = $(this);
    //     console.log(123);
    //     $.post( "algorithms/add-word.php", form.serialize(), function(data) {
    //         console.log(data);
    //         data = JSON.parse(data);
    //
    //         var result = data.result;
    //         form.find('#processed-text').html(list);
    //     });
    //
    //
    // });
});

/*

 Alice was beginning to get very tired of sitting by her sister on the bank, and of having nothing to do:
 once or twice she had peeped into the book her sister was reading, but it had no pictures or conversations
 in it, ‘and what is the use of a book,’ thought Alice ‘without pictures or conversation?’

 */