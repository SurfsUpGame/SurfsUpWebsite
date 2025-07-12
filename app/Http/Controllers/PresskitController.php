<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class PresskitController extends Controller
{
    public function index()
    {
        return view('presskit');
    }

    public function download()
    {
        $zipFileName = 'surfs-up-presskit.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        File::ensureDirectoryExists(storage_path('app/temp'));

        // Create new zip archive
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Add press kit files from public/presskit directory
            $presskitPath = public_path('presskit');

            if (File::exists($presskitPath)) {
                $files = File::allFiles($presskitPath);

                foreach ($files as $file) {
                    $relativeNameInZipFile = str_replace($presskitPath . '/', '', $file->getPathname());
                    $zip->addFile($file->getPathname(), $relativeNameInZipFile);
                }
            }

            $zip->close();

            // Return download response and delete file after sending
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend();
        }

        return redirect()->route('presskit')->with('error', 'Failed to create press kit download.');
    }
}
