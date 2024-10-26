<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Throwable;

class FormatCode extends Command
{
    protected $signature = 'app:format-code
                           {--no-ide-helper : Run the code formatter without barryvdh/laravel-ide-helper}
                           {--no-git-add    : Do not automatically run the command `git add .` after a successful format}
                           {--dry-run       : Inspect code style errors without changing the files}';

    protected $description = 'Enforces Laravel coding standards and enhances IDE integration';

    public function handle(): int
    {
        /**
         * Laravel Pint is used as the code styler
         *
         * @see https://laravel.com/docs/11.x/pint#main-content
         */
        $pintTestArg = $this->option('dry-run') ? '--test' : '';
        $dirSep = DIRECTORY_SEPARATOR;
        $pintCommand = "vendor{$dirSep}bin{$dirSep}pint $pintTestArg";

        $this->info("\u{1F9F9} Cleaning up your dirty code...");
        $exitCode = Command::SUCCESS;

        exec($pintCommand, $output, $exitCode);

        foreach ($output as $message) {
            echo $message.PHP_EOL;
        }

        /**
         * An IDE helper generator package is used for improved intellisense is used
         *
         * @see https://github.com/barryvdh/laravel-ide-helper
         */
        $ideHelperCommands = [];
        if (! $this->option('no-ide-helper')) {
            $ideHelperCommands[] = ['cmd' => 'clear-compiled', 'args' => []];
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

        // Add the changes to Git if successful
        $dryRun = (bool) $this->option('dry-run');
        $noGitAdd = (bool) $this->option('no-git-add');
        if ($exitCode === Command::SUCCESS && ! $dryRun && ! $noGitAdd) {
            exec('git add .');
        }

        return $exitCode;
    }
}
