<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '256M'); // Set memory limit to 256 MB
        ini_set('max_execution_time', 0); // Set max execution time to unlimited

        // Assume the file path is relative to the Laravel project's root
        $filePath = public_path('data/sample.txt');

        // Open the file for reading
        $fileHandle = fopen($filePath, 'r');

        $this->info('Counting total lines in the given file.....');
        $totalLineCount = count(file($filePath));

        $this->info('Total line: ' . $totalLineCount);


        if ($fileHandle) {
            $this->info('String process');
            $currentLineCount = 0;
            $totalInvalidCount = 0;
            $totalSuccessCount = 0;

            // Loop through the file line by line until the end of the file
            while (($line = fgets($fileHandle)) !== false) {
                // Process the current line here
                // For example, you can echo the line or store it in an array, etc.

                $email = $this->extractEmailFromLine($line);

                if ($email) {
                    $this->info('Email: ' . $email);

                    \App\Models\Subscriber::updateOrCreate([
                        'email' => $email
                    ], [
                        'userID' => 1,
                        'list' => 1,
                        'timestamp' => time()
                    ]);
                    $totalSuccessCount++;
                } else {
                    $this->error('Invalid email');
                    $totalInvalidCount++;
                }

                $currentLineCount++;

                $this->info('Processing:[' . $currentLineCount . '/' . $totalLineCount . ']');
            }

            // Close the file handle when done reading
            fclose($fileHandle);
            $this->info('All Process done :)');
            $this->info('Total Line:' . $totalLineCount);
            $this->info('Total Success:' . $totalSuccessCount);
            $this->info('Total Error:' . $totalInvalidCount);


        } else {
            // Handle the case when the file couldn't be opened
            $this->error('Error opening the file.');
        }
    }

    public function extractEmailFromLine($line)
    {
        $pattern = "/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/"; // Regular expression for an email address

        preg_match($pattern, trim($line), $matches);

        return !empty($matches) ? $matches[1] : null;
    }
}
