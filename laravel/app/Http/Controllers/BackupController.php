<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Record;
use App\Display;

class BackupController extends Controller
{

  var $root_dir;
  var $disk;

  public function __construct()
  {
      $this->middleware(function ($request, $next) {
          $this->root_dir = config('app.name');
          $this->disk = Storage::disk('backup');
          return $next($request);
      });
  }

  public function show()
  {
      $files = $this->disk->files($this->root_dir);
      $backups = [];
      // make an array of backup files, with their filesize and creation date
      foreach ($files as $k => $f) {
          // only take the zip files into account
          if (substr($f, -4) == '.zip' && $this->disk->exists($f)) {
              $backups[] = [
                  'file_path' => $f,
                  'file_name' => str_replace($this->root_dir . '/', '', $f),
                  'file_size' => Display::filesize($this->disk->size($f)),
                  'last_modified' => Display::file_last_modified($this->disk->lastModified($f)),
              ];
          }
      }
      // reverse the backups, so the newest one would be on top
      $backups = array_reverse($backups);
      return view("backups.index")->with(compact('backups'));
  }
  public function create()
  {
      try {
        if(env('DB_DUMP_PATH'))
            Config::set('mysql.dump.dump_binary_path', env('DB_DUMP_PATH'));
          // start the backup process
          Artisan::call('backup:run');
          $output = Artisan::output();
          // log the results
          Log::info("[BackupController] - New backup started launched on website: \r\n" . $output);
          return redirect()->back();
      } catch (Exception $e) {
          Flash::error($e->getMessage());
          return redirect()->back();
      }
  }
  /**
   * Downloads a backup zip file.
   *
   * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
   */
  public function download($file_name)
  {
      $file = $this->root_dir . '/' . $file_name;
  
      if ($this->disk->exists($file)) {
  
          $stream = $this->disk->readStream($file);
  
          return response()->stream(function () use ($stream) {
              fpassthru($stream);
          }, 200, [
              "Content-Type" => $this->disk->mimeType($file),  // Utiliser mimeType pour obtenir le type MIME
              "Content-Length" => $this->disk->size($file),    // Utiliser size pour obtenir la taille du fichier
              "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
          ]);
  
      } else {
          abort(404, "The backup file doesn't exist.");
      }
  }
  
  public function restore($file_name)
  {
    if ($this->disk->exists($this->root_dir . '/' . $file_name)) {
        $file_path = $this->disk->url($this->root_dir . '/' . $file_name);
        Record::restore($file_path);
        return redirect()->back();
    } else {
        abort(404, "The backup file doesn't exist.");
    }
  }
  /**
   * Deletes a backup file.
   */
  public function delete($file_name)
  {
      if ($this->disk->exists($this->root_dir . '/' . $file_name)) {
          $this->disk->delete($this->root_dir . '/' . $file_name);
          return redirect()->back();
      } else {
          abort(404, "The backup file doesn't exist.");
      }
  }
}
