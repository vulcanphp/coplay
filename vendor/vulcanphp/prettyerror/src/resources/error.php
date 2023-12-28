<?php
if (!function_exists('clearFilePath')) {
    function clearFilePath(string $file): string
    {
        return trim(str_ireplace([(defined('ROOT_DIR') ? ROOT_DIR : (function_exists('root_dir') ? root_dir() : getcwd()))], [''], $file), '/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error: <?= $exception->getMessage() ?></title>
    <style>
        <?= file_get_contents(__DIR__ . '/perror.css') ?><?= <<<EOT
            #code-editor code,
            .code-font {font-family: "Fira Code", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";}
            #code-editor code {font-size: 13px}
            .syntax-highlight-line code {display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-align: center;-ms-flex-align: center;align-items: center}
            span.syntax-highlight-no {width: 30px;text-align: right;color: #6b7280;font-weight: 400;font-size: 12px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;display: block;padding-right: 7px}
            .syntax-highlight-code {width: calc(100% - 30px);white-space: nowrap}
            .syntax-highlight-line {margin-bottom: 2px}
            .syntax-highlight-line.active {background: #fee2e2}
            .syntax-highlight-line.active span.syntax-highlight-no {background: #fecaca;color: #b91c1c;font-weight: 700}
            #stact-tabs {max-height: 580px;overflow-y: scroll}
            ::-webkit-scrollbar {width: 4px;height: 4px}
            ::-webkit-scrollbar-track {background: #f3f4f6}
            ::-webkit-scrollbar-thumb {background: #d1d5db}
            ::-webkit-scrollbar-thumb:hover {background: #9ca3af}
        EOT ?>
    </style>
</head>

<body class="bg-gray-200">
    <div class="container my-16" style="text-align: left; font-weight: normal;">
        <div class="shadow-lg rounded-tl rounded-tr border-b bg-white  px-6 py-4">
            <div class="flex items-center justify-between">
                <?php
                $trace = $exception->getTrace()[0] ?? [];
                if (!empty($trace)) :
                ?>
                    <span class="bg-red-100 code-font text-red-800 py-1 px-3 rounded font-medium">
                        <?= isset($trace['class']) ? $trace['class'] . '->' : '' ?><?= isset($trace['function']) ? $trace['function'] . '()' : '' ?>:<?= $exception->getLine() ?>
                    </span>
                <?php endif ?>
                <span class="text-gray-600 text-sm font-semibold">PHP <?= phpversion() ?></span>
            </div>
            <p class="mt-4 text-xl font-medium text-gray-700"><?= $exception->getMessage() ?></p>
        </div>
        <div class="shadow-lg rounded-bl rounded-br bg-white">
            <div class="flex">
                <div class="w-4/12 border-r">
                    <div class="border-b px-6 h-[50px] flex items-center">
                        <h2 class="text-gray-600 font-medium">Stack frames (<?= count($exception->getTrace()) + 1 ?>)</h2>
                    </div>
                    <div id="stact-tabs">
                        <?php foreach ($frames as $key => $frame) : ?>
                            <a class="trace-btn hover:border-red-500 <?= count($frames) == ($key + 1) ? 'rounded-bl' : 'border-b' ?> <?= $key === 0 ? 'active' : '' ?>" file="<?= $frame['file']; ?>:<?= $frame['line']; ?>" href="<?= $key ?>">
                                <?= ClearFilePath($frame['file']); ?>:<?= $frame['line']; ?>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="w-8/12">
                    <div class="border-b px-6 h-[50px] flex items-center">
                        <h2 id="trace-editor-file" class="text-gray-600 font-medium text-sm"><?= $exception->getFile() ?>:<?= $exception->getLine() ?></h2>
                    </div>
                    <div id="code-editor" class="px-3 py-4 overflow-x-scroll">
                        <?php foreach (array_column($frames, 'frame') as $key => $frame) : ?>
                            <div class="trace-editor <?= $key === 0 ?: 'hidden' ?>" index="<?= $key ?>">
                                <pre>
                                    <code><?= $frame ?></code>
                                </pre>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        for (const element of document.querySelectorAll('.trace-btn')) {
            element.addEventListener('click', (event) => {
                event.preventDefault();

                for (const btn of document.querySelectorAll('.trace-btn')) {
                    btn.classList.remove('active');
                }

                for (const btn of document.querySelectorAll('.trace-editor')) {
                    btn.classList.add('hidden');
                }

                var target = event.target,
                    index = target.getAttribute('href');
                target.classList.add('active');

                document.querySelector('.trace-editor[index="' + index + '"]').classList.remove('hidden');
                document.querySelector('#trace-editor-file').innerHTML = target.getAttribute('file');
            });
        }
    </script>
</body>

</html>