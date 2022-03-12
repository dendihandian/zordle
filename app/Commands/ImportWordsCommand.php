<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class ImportWordsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command to import text contains words into database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('importing...');

        DB::table('words')->truncate();

        $file = fopen("words_en.txt","r");
        while(! feof($file)){
            $word = fgets($file);
            DB::table('words')->insert(['word' => trim($word)]);
        }
        fclose($file);

        $this->info('done');
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
