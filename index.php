<!DOCTYPE html>
<html>
<head>
    <title>Fuzzy Search</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap">
    <h2>Levenshtein Algorithm</h2>


    <form id="main-form" action="" method="post">
        <textarea id="original-text" class="text-input" rows="12" name="original-text" placeholder="Please enter your text...">Alice was beginning to get very tired of sitting by her sister on the bank, and of having nothing to do: once or twice she had peeped into the book her sister was reading, but it had no pictures or conversations in it, ‘and what is the use of a book,’ thought Alice ‘without pictures or conversation?’
        </textarea>
        <div id="processed-text" class="text-output">Processed text goes here... </div>
        <input type="submit" id="levenshtein-button" value="Run"/>
        <p id="levenshtein-result"></p>
    </form>
    <!--        <form id="levenshtein-form">-->
    <!--            <input type="text" id="initial-text" name="initial-text" placeholder="Please enter original text..."/>-->
    <!--            <input type="text" id="expected-text" name="expected-text" placeholder="Please enter expected text..."/>-->
    <!--            <input type="submit" id="levenshtein-button" value="Run"/>-->
    <!--            <p id="levenshtein-result"></p>-->
    <!--            <p id="levenshtein-insert"></p>-->
    <!--            <p id="levenshtein-replace"></p>-->
    <!--            <p id="levenshtein-delete"></p>-->
    <!--        </form>-->
    <?php
    use models\Wordnet;
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
<div class="modal"></div>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<!--<script src="assets/js/script.js"></script>-->
</body>
</html>