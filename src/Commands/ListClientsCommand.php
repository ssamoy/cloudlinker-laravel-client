<?php

namespace Stesa\CloudlinkerClient\Commands;

use Illuminate\Console\Command;
use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;

class ListClientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudlinker:clients
                            {--hostname= : Filter clients by hostname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all Cloudlinker clients';

    /**
     * Execute the console command.
     */
    public function handle(CloudlinkerClient $client): int
    {
        $this->info('Fetching Cloudlinker clients...');

        try {
            $hostname = $this->option('hostname');
            $clients = $client->clients()->all($hostname);

            if (empty($clients)) {
                $this->warn('No clients found.');

                return self::SUCCESS;
            }

            $rows = array_map(fn ($c) => [
                $c->id,
                $c->name,
                $c->hostname,
                $c->status,
                $c->lastSeen,
            ], $clients);

            $this->table(
                ['ID', 'Name', 'Hostname', 'Status', 'Last Seen'],
                $rows
            );

            $this->info(sprintf('Total: %d client(s)', count($clients)));

            return self::SUCCESS;
        } catch (CloudlinkerException $e) {
            $this->error('Failed to fetch clients: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
