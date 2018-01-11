$(document).ready(function () {
    $body = $("body");
    $(document).on({
        ajaxStart: function () {
            $body.addClass("loading");
        },
        ajaxStop: function () {
            $body.removeClass("loading");
        }
    });


    $(document).on('click', '.tooltip-text span', function () {
        var oldText = $(this).parent().siblings(".error-message");
        var newText = $(this).text();

        $(this).text(oldText.text());
        oldText.text(newText);
    });


    $(document).keydown(function (e) {

        if (e.ctrlKey && e.keyCode == 13) {
            $('input[name="lemma"]').val('');
            document.getElementById('id01').style.display = 'block';
            var selection = window.getSelection().toString();
            if(selection){
                $('input[name="lemma"]').val(selection);
            }
        }
    });


    function getStatisticWords(array)
    {
        var words = [];
        for (var h = 0; h < array.length; h++) {
            if (h != array.length - 1)
                words += '<span>' + array[h] + ', </span>';
            else words += '<span>' + array[h] + '</span>';
        }
        return words;
    }


    function showStatistics(result)
    {
        var stats = '';
        var recognizedPercent = Math.round((result['statistics']['recognized'].length / (result['statistics']['recognized'].length + result['statistics']['fixed'].length +
                result['statistics']['unknown'].length) * 100) * 100) / 100;
        var fixedPercent = Math.round((result['statistics']['fixed'].length / (result['statistics']['recognized'].length + result['statistics']['fixed'].length +
                result['statistics']['unknown'].length) * 100) * 100) / 100;
        var unknownPercent = Math.round((result['statistics']['unknown'].length / (result['statistics']['recognized'].length + result['statistics']['fixed'].length +
                result['statistics']['unknown'].length) * 100) * 100) / 100;
        stats += '<p><strong>Number of recognized words:</strong> <span>' + result['statistics']['recognized'].length +
            ' <span>(' + recognizedPercent + '%)</span>' + '</span></p>';
        stats += '<p><strong>Recognized words:</strong> <span>' + getStatisticWords(result['statistics']['recognized']) + '</span></p><br/>';
        stats += '<p><strong>Number of fixed words:</strong> <span>' + result['statistics']['fixed'].length +
            ' <span>(' + fixedPercent + '%)</span>'+ '</span></p>';
        stats += '<p><strong>Fixed words:</strong> <span>' + getStatisticWords(result['statistics']['fixed']) + '</span></p><br/>';
        stats += '<p><strong>Number of unknown words:</strong> <span>' + result['statistics']['unknown'].length +
            ' <span>(' + unknownPercent + '%)</span>'+ '</span></p>';
        stats += '<p><strong>Unknown words:</strong> <span>' + getStatisticWords(result['statistics']['unknown']) + '</span></p><br/>';

        $('.statistics').css('display', 'block');
        $('.statistics-area').html(stats);
    }


    $('#main-form').submit(function () {
        event.preventDefault();
        var form = $(this);
        var t0 = performance.now();
        $.post("algorithms/levenstain.php", form.serialize(), function (data) {

            if(data) {
                try {
                    data = JSON.parse(data);
                    var list = "";
                    var result = data.result;
                    if (result['statistics']) {
                        showStatistics(result);
                    }
                    for (var i = 0; i < Object.keys(result).length; i++) {

                        if (!result[i]) continue;
                        if (result[i]['word'].indexOf('/') !== -1) {
                            var word = result[i]['word'].replace(/\//g, '');
                            if (result[i]['similar-words']) {
                                list = list + '<div class="tooltip-container"><span class="error-message">' + word + '</span>' +
                                    '<span class="tooltip-text">';
                                for (var j = 0; j < result[i]['similar-words'].length; j++) {
                                    list = list + '<span>' + result[i]['similar-words'][j] + '</span>';
                                }
                                list = list + '</span></div> ';
                            }
                            else {
                                list = list + '<span class="error-message">' + word + '</span>' + ' ';
                            }
                        }
                        else if (result[i]['word'].indexOf('&') !== -1) {
                            word = result[i]['word'].replace(/&/g, '');
                            list = list + '<span>' + word + '</span>' + ' ';
                        }
                        else {
                            word = result[i]['word'].replace(/#/g, '');
                            list = list + '<span class="unknown-message">' + word + '</span>' + ' ';
                        }
                    }
                    form.find('#processed-text').html(list);
                    var t1 = performance.now();
                    $('.time-area').html('<p><strong>Execution time:</strong> ' + Math.round((t1 - t0) * 100) / 100 + ' milliseconds</p>');
                    console.log("Call took " + (t1 - t0) + " milliseconds.");
                } catch(e) {
                    $('#id03').css('display', 'block');
                }
            }
        });
    });


    $('#stats-expander').click(function(){
        var span = $('#stats-expander span');
        if(span.hasClass('glyphicon-chevron-down'))
        {
            span.removeClass( 'glyphicon-chevron-down' );
            span.addClass( 'glyphicon-chevron-up' );
        }
        else {
            span.removeClass( 'glyphicon-chevron-up' );
            span.addClass( 'glyphicon-chevron-down' );
        }
    });


    $('#add-word-form').submit(function () {
        event.preventDefault();

        $('#lemma-error').text('');

        var infoBlock = $('#id02');
        infoBlock.css('display', 'none');

        var form = $(this);

        $.post( "algorithms/add-word.php", form.serialize(), function(data) {
            if(data)
            {
                data = JSON.parse(data);
                var result = data.result;

                if(result === 'Lemma already exists!')
                {
                    $('#lemma-error').text('Lemma already exists!');
                }
                else if(result === 'Lemma was added to frequency dictionary.' ||
                    result === 'Lemma was added to words and frequency dictionary.' ||
                    result === 'Lemma was added to words dictionary.' )
                {
                    infoBlock.find('strong').text(result);
                    infoBlock.css('display', 'block');
                }
                else {
                    infoBlock.find('strong').text('Unknown error!');
                    infoBlock.css('display', 'block');
                }
            }
        });
    });
});

/*

 Alice was beginning to get very tired of sitting by her sister on the bank, and of having nothing to do:
 once or twice she had peeped into the book her sister was reading, but it had no pictures or conversations
 in it, 'and what is the use of a book,' thought Alice 'without pictures or conversation?'

 */