<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('memory_limit', '256M'); // Set memory limit to 256 MB
        ini_set('max_execution_time', 0); // Set max execution time to unlimited

        // Assume the file path is relative to the Laravel project's root
        $filePath = public_path('data/emails.txt');

        // Open the file for reading
        $fileHandle = fopen($filePath, 'r');

        Log::info('Counting total lines in the given file.....');
        $totalLineCount = count(file($filePath));

        Log::info('Total line: ' . $totalLineCount);


        if ($fileHandle) {
            Log::info('String process');
            $currentLineCount = 0;
            $totalInvalidCount = 0;
            $totalSuccessCount = 0;

            // Loop through the file line by line until the end of the file
            while (($line = fgets($fileHandle)) !== false) {
                // Process the current line here
                // For example, you can echo the line or store it in an array, etc.

                $email = $this->extractEmailFromLine($line);

                if ($email) {
                    Log::info('Email: ' . $email);

                    \App\Models\Subscriber::updateOrCreate([
                        'email' => $email
                    ], [
                        'userID' => 1,
                        'list' => 1,
                        'timestamp' => time()
                    ]);
                    $totalSuccessCount++;
                } else {

                    Log::error('Invalid email');
                    $totalInvalidCount++;
                }

                $currentLineCount++;

                Log::info('Processing:[' . $currentLineCount . '/' . $totalLineCount . ']');
            }

            // Close the file handle when done reading
            fclose($fileHandle);
            Log::info('All Process done :)');
            Log::info('Total Line:' . $totalLineCount);
            Log::info('Total Success:' . $totalSuccessCount);
            Log::info('Total Error:' . $totalInvalidCount);


        } else {
            // Handle the case when the file couldn't be opened
            Log::error('Error opening the file.');
        }
    }

    public function extractEmailFromLine($line)
    {
        $pattern = "/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/"; // Regular expression for an email address

        preg_match($pattern, trim($line), $matches);

        return !empty($matches) ? $matches[1] : null;
    }
}
