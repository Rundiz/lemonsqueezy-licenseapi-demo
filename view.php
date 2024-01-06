<?php
require __DIR__ . '/_config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            pre {
                background-color: #eee;
                color: #222;
                margin: 0;
                padding: 10px;
                white-space: pre-wrap;
                word-break: break-all;
                word-wrap: break-word;
            }
        </style>
        <title>Web hook log viewer</title>
    </head>
    <body>
        <h1>Web hook log viewer</h1>
        <h2>Log files.</h2>
        <?php
        $DI = new \FilesystemIterator(__DIR__, \FilesystemIterator::SKIP_DOTS);
        $DI = new \CallbackFilterIterator(
            $DI,
            function ($item) {
                return strtolower($item->getExtension()) === 'txt' ? true : false;
            }
        );
        $II = new \IteratorIterator($DI);

        echo '<div style="margin-bottom: 40px;">' . "\n";
        if (is_iterable($II) && iterator_count($II) > 0) {
            foreach ($II as $File) {
                echo '        <a href="view.php?file=' . rawurlencode($File->getFilename()) . '">' . $File->getFilename() . '</a>';
                echo '        <br>' . "\n";
            }// endforeach;
            unset($File);
        } else {
            echo '        There is no log files.' . "\n";
        }// endif;
        echo '    </div>' . "\n";


        unset($DI, $II);


        if (
            isset($_GET['file']) &&
            stripos($_GET['file'], '..') === false &&
            is_file(__DIR__ . DIRECTORY_SEPARATOR . trim(str_replace(['\\', '/', DIRECTORY_SEPARATOR], '', $_GET['file']))) &&
            strtolower(pathinfo(trim($_GET['file']), PATHINFO_EXTENSION)) === 'txt'
        ) {
            echo '        <h2>File content of &quot;' . htmlspecialchars(trim($_GET['file']), ENT_QUOTES) . '&quot;</h2>' . "\n";
            $fileContents = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . trim(str_replace(['\\', '/', DIRECTORY_SEPARATOR], '', $_GET['file'])));
            if (is_string($fileContents)) {
                $fileContents = trim($fileContents);
            }
            echo '        <pre>' . print_r($fileContents, true) . '</pre>' . "\n";
            unset($fileContents);
        }// endif; view file contents.
        ?>
    </body>
</html>