<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Autodoc</title>
    <style>
        *, *::after, *::before {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-size: 16px;
            font-family: "Roboto Light", "Roboto Thin", monospace;
        }
        body {
            padding: 20px;
            background-color: black;
            color: white;
        }
        header {
            padding-bottom: 40px;
        }
        footer {
            padding-top: 40px;
        }
        code {
            color: #01c601;
        }
        h1 {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Documentation</h1>
    </header>
    <main>
        <?= '<pre><code>' . file_get_contents(__DIR__ . '/../Controller/autodoc.json') . '</pre></code>'?>
    </main>
    <footer>
        <p>Build with <a href="https://github.com/worldWarmWorm/autodoc" target="_blank">autodoc</a></p>
    </footer>
</body>
</html>
