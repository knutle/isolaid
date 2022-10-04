<?php

namespace Knutle\IsoView\Commands\Concerns;

use const SIGINT;
use const SIGTERM;
use Symfony\Component\Process\Process;

trait ProxiesSignalsToChildProcess
{
    protected Process $process;

    public function getSubscribedSignals(): array
    {
        return [SIGTERM, SIGINT];
    }

    public function handleSignal(int $signal): void
    {
        if ($signal === SIGTERM || $signal === SIGINT) {
            $this->process->stop(signal: $signal);
        }
    }
}
