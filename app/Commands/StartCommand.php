<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class StartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command to start the game';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $wordsCount = DB::table('words')->count();
        $selectedId = random_int(1, $wordsCount);
        $selectedWord = DB::table('words')->where('id', $selectedId)->first();
        $won = false;

        $tries = [];
        for ($i=1; $i <= 6; $i++) {

            $wordExists = false;
            do {
                $wordGuess = $this->ask("your {$i} try");
                $wordGuess = strtolower($wordGuess);

                if ($this->validateWord($wordGuess)) {
                    $existedWord = DB::table('words')->where('word', $wordGuess)->first();
                    if ($existedWord) {
                        $wordExists = true;
                    } else {
                        $this->info('the word doesnt exists');
                    }
                } else {
                    $this->info('the word is invalid');
                }

            } while (!$wordExists);

            array_push($tries, $existedWord->word);

            if ($existedWord->word == $selectedWord->word){
                $won = true;
                break;
            } else {
                $this->info($this->evaluateWord($wordGuess, $selectedWord->word));
            }
        }

        if ($won) {
            $this->info('You guessed it!');
        } else {
            $this->info("The word is {$selectedWord->word}");
        }
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

    private function validateWord($guessedWord)
    {
        $valid = true;

        if (strlen($guessedWord) != 5) {
            $valid = false;
        }

        return $valid;
    }

    private function evaluateWord($guessedWord, $actualWord)
    {
        $flags = [];
        for ($i=0; $i < 5; $i++) { 
            if ($guessedWord[$i] == $actualWord[$i]) {
                array_push($flags, 'G');
            } else if (strpos($actualWord, $guessedWord[$i])){
                array_push($flags, 'Y');
            } else {
                array_push($flags, 'B');
            }
        }

        return implode('', $flags);
    }
}
