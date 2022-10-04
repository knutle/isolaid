<?php

/** @noinspection PhpComposerExtensionStubsInspection */

namespace Knutle\IsoView\Commands;

use function blank;
use Closure;
use Exception;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Knutle\IsoView\Commands\Concerns\ProxiesSignalsToChildProcess;
use Knutle\IsoView\IsoView;
use const PHP_OS;
use const SIGINT;
use function str_contains;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class IsoViewServeCommand extends ServeCommand implements SignalableCommandInterface
{
    use ProxiesSignalsToChildProcess;

    protected $name = 'isoview:serve';

    public function __construct()
    {
        parent::__construct();

        $this->setProcessTitle('isoview:serve');
    }

    public function handle(): ?int
    {
        $this->input->setOption('host', '127.0.0.1');
        $this->input->setOption('port', '8010');
        $this->input->setOption('tries', 0);

        IsoView::bootstrap();

        try {
            $this->ensureNoLockingProcess();
        } catch (Throwable $throwable) {
            if (! $this->option('check-lock')) {
                $this->error($throwable->getMessage());
            }

            return Command::FAILURE;
        }

        if ($this->option('check-lock')) {
            $this->output->success('No locking process found');

            return Command::SUCCESS;
        }

        return parent::handle();
    }

    protected function handleProcessOutput(): Closure
    {
        return function ($type, $buffer) {
            parent::handleProcessOutput()($type, $buffer);

            if ($this->option('fast-exit')) {
                $this->process->setTimeout(5);
            }

            if ($this->option('fast-exit') && str_contains($buffer, 'Development Server (http://127.0.0.1:8010) started')) {
                $this->comment('Fast exit after successful init');

                $this->handleSignal(SIGINT);
            }
        };
    }

    protected function startProcess($hasEnvironment): Process
    {
        return $this->process = parent::startProcess($hasEnvironment);
    }

    /**
     * @throws Exception
     */
    protected function ensureNoLockingProcess()
    {
        $lockOwnerSuggestions = $this->findLockingProcessSuggestions();

        if ($lockOwnerSuggestions->isNotEmpty()) {
            $lockOwnerDescription = $lockOwnerSuggestions->containsOneItem() ? $lockOwnerSuggestions->first() : "\n- ".$lockOwnerSuggestions->join("\n- ");
            $lockOwnerPids = $lockOwnerSuggestions->containsOneItem() ? $lockOwnerSuggestions->keys()->first() : $lockOwnerSuggestions->keys()->join(', ');

            if ($this->option('check-lock')) {
                $this->line($lockOwnerPids);
            } else {
                $this->comment("Locked by: $lockOwnerDescription");
            }

            throw new RuntimeException("Listening interface locked by existing process: $lockOwnerDescription");
        }
    }

    protected function findLockingProcessSuggestions(): Collection
    {
        if (PHP_OS == 'WINNT') {
            return collect([
                '?' => 'Cannot detect locking process on Windows',
            ]);
        }

        $listCommand = new Process(['ps', '-ef']);
        $listCommand->start();
        $listCommand->wait();
        $output = $listCommand->getOutput();
        $lines = Str::of($output)->rtrim("\n")->explode("\n");

        $keys = Str::of($lines->first())->lower()->replaceMatches('/ +/', "\t")->explode("\t");

        return $lines->filter(
            fn (string $line) => Str::of($line)->is('* 127.0.0.1:8010 *')
        )->mapWithKeys(
            function (string $processLine, int $index) use ($keys) {
                $attributes = Str::of($processLine)->replaceMatches('/ +/', "\t")->explode("\t", $keys->count())->mapWithKeys(
                    fn (string $line, int $index) => [$keys[$index] => (string) Str::of($line)->replace("\t", ' ')]
                );

                $pid = $attributes->get('pid');
                $command = $attributes->get('cmd') ?? '?';

                if (blank($pid)) {
                    return ["?:$index" => "Unable to resolve PID from line $processLine"];
                }

                return [
                    $pid => "PID $pid - $command",
                ];
            }
        )->whereNotNull();
    }

    protected function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                ['check-lock', null, InputOption::VALUE_OPTIONAL, 'Only check for locking process and output only PID if found'],
                ['fast-exit', null, InputOption::VALUE_OPTIONAL, 'Stop process immediately after successful init'],
            ]
        );
    }
}
