<!DOCTYPE html>
<html>
<head>
    <title>Fuzzy Search</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="wrap">
        <h2>Levenshtein Algorithm</h2>
        <form id="levenshtein-form">
            <input type="text" id="initial-text" name="initial-text" placeholder="Please enter original text..."/>
            <input type="text" id="expected-text" name="expected-text" placeholder="Please enter expected text..."/>
            <input type="submit" id="levenshtein-button" value="Run"/>
            <p id="levenshtein-result"></p>
        </form>

    </div>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>