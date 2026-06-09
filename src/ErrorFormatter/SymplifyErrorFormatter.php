<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ErrorFormatter;

use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\Output;
use PHPStan\Command\OutputStyle;
use Symplify\PHPStanRules\Console\Terminal;
use Symplify\PHPStanRules\Enum\ResultStatus;
use Symplify\PHPStanRules\FileSystem\FilesystemHelper;

/**
 * @see \Symplify\PHPStanRules\Tests\ErrorFormatter\SymplifyErrorFormatterTest
 */
final class SymplifyErrorFormatter implements ErrorFormatter
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     */
    private const int BULGARIAN_CONSTANT = 8;

    /**
     * @see https://regex101.com/r/1ghDuM/1
     */
    private const string FILE_WITH_TRAIT_CONTEXT_REGEX = '#(?<file>.*?)(\s+\(in context.*?)?$#';

    private ?Output $output = null;

    /**
     * @return ResultStatus::*
     */
    public function formatErrors(AnalysisResult $analysisResult, Output $output): int
    {
        $outputStyle = $output->getStyle();
        $this->output = $output;

        if ($analysisResult->getTotalErrorsCount() === 0 && $analysisResult->getWarnings() === []) {
            $outputStyle->success('No errors');
            return ResultStatus::SUCCESS;
        }

        $this->reportErrors($analysisResult, $outputStyle);

        $notFileSpecificErrors = $analysisResult->getNotFileSpecificErrors();
        foreach ($notFileSpecificErrors as $notFileSpecificError) {
            $outputStyle->warning($notFileSpecificError);
        }

        $warnings = $analysisResult->getWarnings();
        foreach ($warnings as $warning) {
            $outputStyle->warning($warning);
        }

        return ResultStatus::FAILURE;
    }

    private function reportErrors(AnalysisResult $analysisResult, OutputStyle $outputStyle): void
    {
        if ($analysisResult->getFileSpecificErrors() === []) {
            return;
        }

        foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
            $this->printSingleError($fileSpecificError, $outputStyle);
        }

        $outputStyle->newLine();

        $errorMessage = sprintf('Found %d errors', $analysisResult->getTotalErrorsCount());
        $outputStyle->error($errorMessage);
    }

    private function separator(): void
    {
        $separator = str_repeat('-', Terminal::getWidth() - self::BULGARIAN_CONSTANT);
        $this->writeln($separator);
    }

    private function getRelativePath(string $filePath): string
    {
        // remove trait clutter
        /** @var string $clearFilePath */
        $clearFilePath = preg_replace(self::FILE_WITH_TRAIT_CONTEXT_REGEX, '$1', $filePath);

        if (! file_exists($clearFilePath)) {
            return $clearFilePath;
        }

        return FilesystemHelper::resolveFromCwd($clearFilePath);
    }

    private function regexMessage(string $message): string
    {
        // remove extra ".", that is really not part of message
        $message = rtrim($message, '.');
        return '#' . preg_quote($message, '#') . '#';
    }

    private function writeln(string $separator): void
    {
        $this->output?->writeLineFormatted(' ' . $separator);
    }

    private function printSingleError(Error $error, OutputStyle $outputStyle): void
    {
        $this->separator();

        $relativeFilePath = $this->getRelativePath($error->getFile());
        $relativeLine = $relativeFilePath . ':' . $error->getLine();

        // template error
        $templateFilePath = $error->getMetadata()['template_file_path'] ?? null;
        $templateLine = $error->getMetadata()['template_line'] ?? null;

        if ($templateFilePath && $templateLine) {
            $templateFileLine = $this->getRelativePath($templateFilePath) . ':' . $templateLine;
            $this->writeln($templateFileLine);

            $this->writeln('rendered in: ' . $relativeLine);
            $this->separator();
        } else {
            // clickable path
            $this->writeln(' ' . $relativeLine);
            $this->separator();
        }

        // ignored path - @todo include file
        $regexMessage = $this->regexMessage($error->getMessage());
        $itemMessage = sprintf(" - '%s'", $regexMessage);
        $this->writeln($itemMessage);

        if ($error->getIdentifier() !== null && $error->canBeIgnored()) {
            $this->writeln(' 🪪 ' . $error->getIdentifier());
        }

        $this->separator();
        $outputStyle->newLine();
    }
}
