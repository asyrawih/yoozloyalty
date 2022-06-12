<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RestartingQueue extends Command
{
    const FILE_LOCATION = 'json/commands/queue.json';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restarting:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $works = array_keys(config('queue.works'));

        $items = [];

        foreach ($works as $work) {
            $items[] = config('queue.works.'.$work);
        }

        $value = [
            'name' => $this->queueName(),
            'interpreter' => 'php',
            'script' => 'artisan',
            'args' => 'queue:work --queue='.implode(',', $items)
        ];

        Storage::disk('blueprint')->put(self::FILE_LOCATION, json_encode($value, JSON_PRETTY_PRINT));

        $this->info('Queue file regenerated ...');
        $this->info(json_encode($value, JSON_PRETTY_PRINT));

        $this->info('Restarting queue ...');
        $this->restartQueue();

        $this->info('Queue is running ...');
    }

    public function queueName()
    {
        return env('APP_URL').' Queue';
    }

    public function killCurrentQueueConnection()
    {
        $process = new Process(['pm2','kill']);
        $process->run();
    }

    public function deleteCurrentQueueConnection()
    {
        $process = new Process(['pm2','delete', $this->queueName()]);
        $process->run();
    }

    public function restartQueue()
    {

        $process = new Process(['pm2','restart', 'storage/'.self::FILE_LOCATION]);
                $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info($process->getOutput());
    }
}
