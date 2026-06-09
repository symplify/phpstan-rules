<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Console;

/**
 * Copied mostly from symfony/console, to avoid hard dependency for single method
 * @see https://github.com/symfony/symfony/blob/e16aea4e88bfecec4986bf7a693f84a86c074109/src/Symfony/Component/Console/Terminal.php#L86
 */
final class Terminal
{
    /**
     * @var array<int, array<string>>
     */
    private const DESCRIPTORSPEC = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    private static ?int $width = null;

    private static ?bool $stty = null;

    public static function getWidth(): int
    {
        $width = \getenv('COLUMNS');
        if ($width !== \false) {
            return (int) \trim($width);
        }

        if (self::$width === null) {
            self::initDimensions();
        }

        return self::$width ?: 80;
    }

    private static function hasSttyAvailable(): bool
    {
        if (self::$stty !== null) {
            return self::$stty;
        }

        if (! \function_exists('exec')) {
            return \false;
        }

        \exec('stty 2>&1', $output, $exitcode);
        return self::$stty = $exitcode === 0;
    }

    private static function initDimensions(): void
    {
        $consoleMode = self::getConsoleMode();

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $width = self::getAnsiconWidth();
            if ($width !== null) {
                self::$width = $width;
            } elseif (! self::hasVt100Support() && self::hasSttyAvailable()) {
                self::initDimensionsUsingStty();
            } elseif ($consoleMode) {
                self::$width = $consoleMode[0];
            }
        } else {
            self::initDimensionsUsingStty();
        }
    }

    private static function hasVt100Support(): bool
    {
        if (! \function_exists('sapi_windows_vt100_support')) {
            return \false;
        }

        $stdoutStream = \fopen('php://stdout', 'w');
        if ($stdoutStream === \false) {
            return \false;
        }

        return \sapi_windows_vt100_support($stdoutStream);
    }

    private static function initDimensionsUsingStty(): void
    {
        $sttyColumns = self::getSttyColumns();

        if ($sttyColumns) {
            if (\preg_match('#rows.(\d+);.columns.(\d+);#i', $sttyColumns, $matches)) {
                self::$width = (int) $matches[2];
            } elseif (\preg_match('#;.(\d+).rows;.(\d+).columns#i', $sttyColumns, $matches)) {
                self::$width = (int) $matches[2];
            }
        }
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private static function getConsoleMode(): ?array
    {
        $info = self::readFromProcess('mode CON');
        if ($info === null) {
            return null;
        }

        if (! \preg_match('#--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n#', $info, $matches)) {
            return null;
        }

        return [(int) $matches[2], (int) $matches[1]];
    }

    private static function getSttyColumns(): ?string
    {
        return self::readFromProcess('stty -a | grep columns');
    }

    private static function readFromProcess(string $command): ?string
    {
        if (! \function_exists('proc_open')) {
            return null;
        }

        $process = \proc_open($command, self::DESCRIPTORSPEC, $pipes, null, null, [
            'suppress_errors' => \true,
        ]);
        if (! \is_resource($process)) {
            return null;
        }

        $info = \stream_get_contents($pipes[1]);
        \fclose($pipes[1]);
        \fclose($pipes[2]);
        \proc_close($process);
        return $info;
    }

    private static function getAnsiconWidth(): ?int
    {
        $ansicon = \getenv('ANSICON');
        if (! is_string($ansicon)) {
            return null;
        }

        if (\preg_match('#^(\d+)x(\d+)(?: \((\d+)x(\d+)\))?$#', \trim($ansicon), $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
