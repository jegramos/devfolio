<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

class CodeFormatter extends Command
{
    protected $signature = 'app:code-format
                           {--no_ide_helper : Run the code formatter without barryvdh/laravel-ide-helper}
                           {--no_git_add    : Do not automatically run the command `git add .` after a successful format}
                           {--t|test        : Inspect code style errors without changing the files}';

    protected $description = 'Enforces Laravel coding standards and enhances IDE integration';

    public function handle(): int
    {
        $pintTestArg = $this->option('test') ? '--test' : '';
        $dirSep = DIRECTORY_SEPARATOR;
        $pintCommand = "vendor{$dirSep}bin{$dirSep}pint $pintTestArg";

        $this->info("\u{1F9F9} Cleaning up your dirty code...");
        $exitCode = Command::SUCCESS;

        exec($pintCommand, $output, $exitCode);

        foreach ($output as $message) {
            echo $message.PHP_EOL;
        }

        $ideHelperCommands = [];
        if (! $this->option('no_ide_helper')) {
            $ideHelperCommands[] = ['cmd' => 'ide-helper:generate', 'args' => []];
            $ideHelperCommands[] = ['cmd' => 'ide-helper:meta', 'args' => []];
            $ideHelperCommands[] = ['cmd' => 'ide-helper:models', 'args' => ['--nowrite' => true]];
        }

        foreach ($ideHelperCommands as $ideHelperCommand) {
            try {
                $this->call($ideHelperCommand['cmd'], $ideHelperCommand['args']);
            } catch (Throwable $th) {
                $this->error("\u{1F645}  Error: ".$th->getMessage());
                $exitCode = Command::FAILURE;
            }
        }

        $this->info("\u{1F9FA} Code cleanup done!");

        // Add changes to Git if success
        if ($exitCode === Command::SUCCESS && ! $this->option('test') && ! $this->option('no_git_add')) {
            exec('git add .');
        }

        return $exitCode;
    }
}