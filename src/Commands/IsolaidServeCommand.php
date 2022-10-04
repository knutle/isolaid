<?php

/** @noinspection PhpComposerExtensionStubsInspection */

namespace Knutle\Isolaid\Commands;

use Closure;
use Exception;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Knutle\Isolaid\Commands\Concerns\ProxiesSignalsToChildProcess;
use Knutle\Isolaid\Isolaid;
use const PHP_OS;
use function shell_exec;
use const SIGINT;
use function str_contains;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class IsolaidServeCommand extends ServeCommand implements SignalableCommandInterface
{
    use ProxiesSignalsToChildProcess;

    protected $name = 'isolaid:serve';

    public function __construct()
    {
        parent::__construct();

        $this->setProcessTitle('isolaid:serve');
    }

    public function handle(): ?int
    {
        $this->input->setOption('host', '127.0.0.1');
        $this->input->setOption('port', '8010');
        $this->input->setOption('tries', 0);

        Isolaid::bootstrap();

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

        return Str::of(shell_exec('ps -ef | grep "[p]hp -S 127.0.0.1:8010"'))->rtrim("\n")->explode("\n")->mapWithKeys(
            function (string $processLine) {
                $pid = (string) Str::of($processLine)->match('/^\s*\d+\s*(\d+)\s*/');

                if (blank($pid)) {
                    return [null => null];
                }

                $command = Str::of(shell_exec("ps -p $pid -o command"))->replaceMatches('/^COMMAND\s+/', '')->rtrim("\n")->explode("\n")->first();

                return [
                    $pid => "PID $pid ($command)",
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
