<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error: Something Wrong Happened To This Website..</title>
    <style>
        <?= file_get_contents(__DIR__ . '/perror.css') ?>
    </style>
</head>

<body class="bg-gray-200">
    <div class="container my-16">
        <div class="w-6/12 mx-auto">
            <div class="bg-white shadow-xl px-8 py-10 rounded text-center">
                <svg height="80" width="80" class="text-yellow-500 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="currentColor" d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32s32-14.3 32-32z" />
                </svg>
                <h2 class="text-gray-600 text-xl mt-5 font-semibold">There has been critical error on this website</h2>
                <p class="text-gray-500 text-sm mt-4">open console to track the error.</p>
                <span class="text-sm block text-gray-500">PHP V. <?= phpversion() ?></span>
            </div>
        </div>
    </div>

    <script>
        <?php

        $trace = $exception->getTrace()[0] ?? [];
        if (!empty($trace)) {
            echo 'console.log("' . (isset($trace['class']) ? $trace['class'] . '->' : '') . (isset($trace['function']) ? $trace['function'] . '()' : '') . (isset($trace['class']) || isset($trace['function']) ? ':' . $exception->getLine() : '') . '");';
        }

        echo 'console.log("' . $exception->getMessage() . '");';
        echo 'console.log("Trace:");';
        echo 'console.log("' . sprintf("(1) %s:%s", addslashes($exception->getFile()), $exception->getLine()) . '");';

        foreach ($exception->getTrace() as $key => $trace) {
            if ($key == 0 || !isset($trace['file'])) {
                continue;
            }

            echo 'console.log("' . sprintf("(%d) %s:%s", $key, addslashes($trace['file']), $trace['line']) . '");';
        }
        ?>
    </script>
</body>

</html>