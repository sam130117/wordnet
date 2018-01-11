<!DOCTYPE html>
<html>
<head>
    <title>Fuzzy Search</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrap">
    <h2>Fuzzy Search</h2>

    <p>To add a new word to the database, select the word and press Ctr + Enter or press Ctr + Enter.</p>
    <form id="main-form" action="" method="post">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <label for="original-text">Original text</label>
                <textarea id="original-text" class="text-input" name="original-text"
                          placeholder="Please enter your text..."></textarea></div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <label for="processed-text">Processed text</label>
                <div id="processed-text" class="text-output">Processed text goes here...</div>
            </div>
        </div>

        <input type="submit" id="levenshtein-button" value="Run"/>
    </form>

    <div id="id03">
        <div class="alert alert-danger">
            <strong>String can not be processed!</strong>
        </div>
    </div>

    <div class="panel-group statistics">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#collapse1" id="stats-expander">Word statistics
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
                <div class="statistics-area panel-body"></div>
                <div class="time-area panel-body"></div>
            </div>
        </div>
    </div>

    <?php
    use models\StringManager;

    require_once('models/WordnetManager.php');
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

<div class="w3-container">
    <div id="id01" class="w3-modal">
        <div class="w3-modal-content">
            <header class="w3-container w3-teal">
                <span onclick="document.getElementById('id01').style.display='none'"
                      class="w3-button w3-display-topright">&times;</span>
                <h2>Add new word</h2>
            </header>
            <form id="add-word-form" action="" method="post">
                <input type="text" name="lemma" placeholder="Lemma">
                <span id="lemma-error" class="error-message-color"></span>
                <input type="text" name="frequency" placeholder="Word frequency">
                <input type="text" name="sample" placeholder="Sample">
                <input type="submit" value="Submit"/>
            </form>
            <div id="id02">
                <div class="alert alert-success">
                    <strong>The word was successfully added.</strong>
                </div>
            </div>

        </div>
    </div>
</div>


<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>