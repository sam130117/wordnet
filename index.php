<!DOCTYPE html>
<html>
<head>
    <title>Fuzzy Search</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap">
    <h2>Levenshtein Algorithm</h2>

<!--    <p>To add a new word to the database, select the word and press Ctr + Enter.</p>-->
    <form id="main-form" action="" method="post" >
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <label for="original-text">Original text</label>
             <textarea id="original-text" class="text-input" name="original-text" placeholder="Please enter your text...">The rabbit-hole went straight on like a tunnel for some way, and then dipped suddenly down, so suddenly that Alice had not a moment to think about stopping herself before she found herself falling down a very deep well.
Either the well was very deep, or she fell very slowly, for she had plenty of time as she went down to look about her and to wonder what was going to happen next. First, she tried to look down and make out what she was coming to, but it was too dark to see anything; then she looked at the sides of the well, and noticed that they were filled with cupboards and book-shelves; here and there she saw maps and pictures hung upon pegs. She took down a jar from one of the shelves as she passed; it was labelled 'ORANGE MARMALADE', but to her great disappointment it was empty: she did not like to drop the jar for fear of killing somebody, so managed to put it into one of the cupboards as she fell past it.
'Well!' thought Alice to herself, 'after such a fall as this, I shall think nothing of tumbling down stairs! How brave they'll all think me at home! Why, I wouldn't say anything about it, even if I fell off the top of the house!' (Which was very likely true.)</textarea>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <label for="processed-text">Processed text</label>
                <div id="processed-text" class="text-output">Processed text goes here... </div>
            </div>
        </div>

        <input type="submit" id="levenshtein-button" value="Run"/>
    </form>
    <?php
    use models\StringManager;

    require_once('models/Wordnet.php');
    require_once('models/StringManager.php');


    $originalString = isset($_POST['original-text']) ? $_POST['original-text'] : '';
    $result = '';

    if (!empty($originalString)) {
        $result = StringManager::process($originalString);
        var_dump($result);
    }
    ?>


</div>
<div class="modal">
</div>

<!--<div class="w3-container">-->
<!--    <div id="id01" class="w3-modal">-->
<!--        <div class="w3-modal-content">-->
<!--            <header class="w3-container w3-teal">-->
<!--                <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>-->
<!--                <h2>Add new word</h2>-->
<!--            </header>-->
<!--            <form id="add-word-form" action="" method="post">-->
<!--                <input type="text" name="lemma" placeholder="Lemma">-->
<!--                <input type="text" name="frequency" placeholder="Word frequency">-->
<!--                <input type="text" name="sample" placeholder="Sample">-->
<!--                <input type="submit" value="Submit"/>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>