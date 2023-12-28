<?php

namespace VulcanPhp\PrettyError\Frame;

class PrettyFrame implements IFrame
{
    protected int $total_line;

    public function __construct(int $total_line = 25)
    {
        $this->total_line = $total_line;
    }

    public function render(string $file, int $line_no): string
    {
        if (!file_exists($file)) {
            return 'File Not Found:';
        }

        $file = highlight_file($file, true);

        if (!$file) {
            return 'This File is Empty:';
        }

        $i          = 1;
        $start_line = ceil($line_no - ($this->total_line / 2));

        ob_start();

        foreach (explode('<br />', $file) as $line) {

            if ($i < $start_line) {
                $i++;
                continue;
            }

            if ($i > ($this->total_line + $start_line)) {
                continue;
            }

            echo '<div class="syntax-highlight-line ' . ($i == $line_no ? 'active' : '') . '"><code><span class="syntax-highlight-no">' . $i . '</span> <div class="syntax-highlight-code">' . $line . '</div></code></div>';

            $i++;
        }

        return ob_get_clean();
    }
}
