<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Unavailable - <?= cms('title', 'CoPlay') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            background-color: #020617;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        h2 {
            font-size: 28px;
            font-weight: 400;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 14c-3 0-4 3-4 3h8s-1-3-4-3z"></path>
        <path fill="currentColor"
            d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z">
        </path>
        <path fill="currentColor"
            d="m17.555 8.832-1.109-1.664-3 2a1.001 1.001 0 0 0 .108 1.727l4 2 .895-1.789-2.459-1.229 1.565-1.045zm-6.557 1.23a1 1 0 0 0-.443-.894l-3-2-1.11 1.664 1.566 1.044-2.459 1.229.895 1.789 4-2a.998.998 0 0 0 .551-.832z">
        </path>
    </svg>

    <h2><?= __e('Video Unavailable') ?></h2>

</body>

</html>