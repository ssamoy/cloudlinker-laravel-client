<?php

namespace Stesa\CloudlinkerClient\Commands;

use Illuminate\Console\Command;
use Stesa\CloudlinkerClient\CloudlinkerClient;
use Stesa\CloudlinkerClient\Exceptions\AuthenticationException;
use Stesa\CloudlinkerClient\Exceptions\CloudlinkerException;

class TestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudlinker:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the connection to the Cloudlinker API';

    /**
     * Execute the console command.
     */
    public function handle(CloudlinkerClient $client): int
    {
        $this->info('Testing Cloudlinker API connection...');

        try {
            $result = $client->test();

            if ($result) {
                $this->info('Connection successful! Your Cloudlinker credentials are valid.');

                return self::SUCCESS;
            }

            $this->error('Connection test returned unexpected result.');

            return self::FAILURE;
        } catch (AuthenticationException $e) {
            $this->error('Authentication failed. Please check your CLOUDLINKER_ORG_ID and CLOUDLINKER_API_KEY in your .env file.');

            return self::FAILURE;
        } catch (CloudlinkerException $e) {
            $this->error('Connection failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
