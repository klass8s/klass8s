<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\UI;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ModernStyle extends SymfonyStyle
{
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        self::applyTheme($output);
    }

    public function title(string $message): void
    {
        $this->newLine();
        $this->writeln('<title>' . OutputFormatter::escape($message) . '</title>');
    }

    public function success(string|array $message): void
    {
        $messages = is_array($message) ? array_values($message) : [$message];
        foreach ($messages as $message) {
            $this->line('success', $message, '✔︎');
        }
    }

    public function error(string|array $message): void
    {
        $messages = is_array($message) ? array_values($message) : [$message];
        foreach ($messages as $message) {
            $this->line('error', $message, '❌');
        }
    }

    public function warning(string|array $message): void
    {
        $messages = is_array($message) ? array_values($message) : [$message];
        foreach ($messages as $message) {
            $this->line('warning', $message, '⚠️');
        }
    }

    public function info(string|array $message): void
    {
        $messages = is_array($message) ? array_values($message) : [$message];
        foreach ($messages as $message) {
            $this->line('info', $message, 'ℹ️');
        }
    }

    public function note(string|array $message): void
    {
        $this->writeln(sprintf('<info>%s</info> <muted>%s</muted>', '📝', 'Notes:'));

        $messages = is_array($message) ? array_values($message) : [$message];
        foreach ($messages as $message) {
            $this->writeln('  <muted>-</muted> ' . OutputFormatter::escape($message));
        }
    }

    public function debug(string $message): void
    {
        $this->writeln(sprintf('<muted>%s %s</muted>', '🔎', OutputFormatter::escape($message)));
    }

    private function line(string $style, string $message, string $emoji): void
    {
        $this->writeln(sprintf(' <%s>%s</%s> %s', $style, $emoji, $style, $message));
    }

    public static function applyTheme(OutputInterface $output): void
    {
        $f = $output->getFormatter();

        // Keep colors basic for the best cross-terminal consistency.
        $f->setStyle('success', new OutputFormatterStyle('green', null));
        $f->setStyle('error', new OutputFormatterStyle('red', null));
        $f->setStyle('warning', new OutputFormatterStyle('yellow', null));
        $f->setStyle('info', new OutputFormatterStyle('cyan'));
        $f->setStyle('muted', new OutputFormatterStyle('gray'));
        $f->setStyle('title', new OutputFormatterStyle('magenta', null));
        $f->setStyle('k', new OutputFormatterStyle('white', null));
    }
}
