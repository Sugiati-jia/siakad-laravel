<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\DB;
use Monolog\LogRecord;

class DatabaseLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('database');
        $logger->pushHandler(new DatabaseHandler());
        return $logger;
    }
}

class DatabaseHandler extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        try {
            DB::connection('log_eror')->table('error_logs')->insert([
                'level'       => $record->level->getName(),
                'message'     => $record->message,
                'context'     => json_encode($record->context),
                'created_at'  => $record->datetime->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Fallback to prevent infinite loop if DB fails
        }
    }
}
