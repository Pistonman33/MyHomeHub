<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Display;

class DocumentController extends Controller
{

  public function __construct()
  {
  }

  public function home(){
    $dir = '/';
    $recursive = false; // Get subdirectories also?
    $contents = collect(Storage::cloud()->listContents($dir, $recursive));
    $list_files = $contents->where('type', '=', 'file');
    $documents = [];
    // make an array of backup files, with their filesize and creation date
    foreach ($list_files as $file) {
      $documents[] = [
          'name' => $file['filename'],
          'extension' => $file['extension'],
          'url' => Storage::cloud()->url($file['path']),
          'size' => Display::filesize($file['size']),
          'last_modified' => Display::file_last_modified($file['timestamp']),
      ];
    }
    // reverse the backups, so the newest one would be on top
    $documents = array_reverse($documents);

    return view('documents.home')->with(compact('documents'));
  }

  public function download(){
    $filename = "chassis_boulemberg.pdf";

    $dir = '/';
    $recursive = false; // Get subdirectories also?
    $contents = collect(Storage::cloud()->listContents($dir, $recursive));

    $file = $contents
        ->where('type', '=', 'file')
        ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
        ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
        ->first(); // there can be duplicate file names!

    //return $file; // array with file info

    $rawData = Storage::cloud()->get($file['path']);

    return response($rawData, 200)
        ->header('ContentType', $file['mimetype'])
        ->header('Content-Disposition', "attachment; filename=$filename");
  }

}
