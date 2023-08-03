<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

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
        $string = "This is a sample text with an email address: example@example.com
           and another line without an email address.
           Here's another line: test123@gmail.com";

        $lines = explode("\n", $string);

        foreach ($lines as $line) {
            $email = $this->extractEmailFromLine($line);
            if ($email) {
                echo "Email found: " . $email . "\n";
            } else {
                echo "No email address found in this line.\n";
            }
        }

    }

    public function extractEmailFromLine($line)
    {
        $pattern = "/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/"; // Regular expression for an email address

        preg_match($pattern, trim($line), $matches);

        return !empty($matches) ? $matches[1] : null;
    }

}
