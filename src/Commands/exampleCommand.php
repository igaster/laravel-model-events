<?php namespace Igaster\ModelEvents\Commands;

use Illuminate\Console\Command;

class exampleCommand extends Command
{
    protected $signature = 'namespace:command {argument?} {--option} {--option2=default}';
  
    protected $description = 'Load + Parse + Save to DB a geodata file.';

    public function __construct()
    {
        parent::__construct();
        // Add init code here
    }

    public function handle()
    {
        $start = microtime(true);

        // Get argument value
        $argument = $this->argument('argument');

        // Get option value
        $option =  $this->option('option');

        // Output a text
        $this->info("Write some text");

        // Simple input
        $value = $this->ask('Prompt for input');

        // Input / Pick from a list
        $value = $this->choice('What is your name?', ['Taylor', 'Dayle']);

        // Input value with autocomplete
        $list = ['item1','item2','item3'];
        $value = $this->anticipate("Prompt for input:", $list);

        // Input Yes/No
        $value = $this->confirm('Continue?');

        // Create a Progressbar
        $progressBar = new \Symfony\Component\Console\Helper\ProgressBar($this->output, 100);
        $progressBar->setProgress(50);
        $progressBar->finish();
        $this->info("");


        // Execute a system command
        $command = "ls -a";
        system($command);

        $time_elapsed_secs = microtime(true) - $start;
        $this->info("Timing: $time_elapsed_secs sec</info>");
    }
}
