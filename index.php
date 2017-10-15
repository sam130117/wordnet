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
        <input type="text" id="original-text" name="original-text" placeholder="Please enter your text..."/>
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

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>