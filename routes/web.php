<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // Assume the file path is relative to the Laravel project's root
    $filePath = public_path('data/sample.txt');

    // Open the file for reading
    $fileHandle = fopen($filePath, 'r');

    if ($fileHandle) {
        // Loop through the file line by line until the end of the file
        while (($line = fgets($fileHandle)) !== false) {
            // Process the current line here
            // For example, you can echo the line or store it in an array, etc.
            \App\Models\Subscriber::updateOrCreate([
                'email' => trim($line)
            ],[
                'userID' => 1,
                'list' => 1,
                'timestamp' => time()
            ]);
            echo $line;
        }

        // Close the file handle when done reading
        fclose($fileHandle);
    } else {
        // Handle the case when the file couldn't be opened
        echo "Error opening the file.";
    }
});
