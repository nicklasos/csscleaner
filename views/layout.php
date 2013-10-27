<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find unused css selectors">
    <meta name="author" content="Nicklasos">
    <title>CssCleaner</title>
    <style>
        .site-links {
            width: 400px;
            height: 80px;
        }
        .site-parse {
            width: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?= $this->render($view, $data) ?>
    </div>
</body>
</html>
