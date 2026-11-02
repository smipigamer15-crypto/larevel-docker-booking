<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $backups = [];

        $files = $disk->files(config('backup.backup.name'));

        foreach ($files as $file) {
            if (substr($file, -4) === '.zip') {
                $backups[] = [
                    'path' => $file,
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'size_human' => $this->humanFileSize($disk->size($file)),
                    'created_at' => $disk->lastModified($file),
                    'created_at_formatted' => date('M d, Y H:i:s', $disk->lastModified($file)),
                ];
            }
        }

        usort($backups, function ($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        $totalSize = array_sum(array_column($backups, 'size'));

        return view('admin.backups.index', compact('backups', 'totalSize'));
    }

    public function create()
    {
        try {
            Artisan::call('backup:run');

            MonitoringService::info('backup_created_manually', 'Manual backup created by admin', ['admin_id' => auth()->id()]);

            return back()->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            MonitoringService::error('backup_failed', 'Manual backup failed: ' . $e->getMessage(), ['admin_id' => auth()->id()]);

            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $path = config('backup.backup.name') . '/' . $filename;

        if (!$disk->exists($path)) {
            abort(404, 'Backup file not found');
        }

        return $disk->download($path);
    }

    public function delete($filename)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $path = config('backup.backup.name') . '/' . $filename;

        if (!$disk->exists($path)) {
            return back()->with('error', 'Backup file not found');
        }

        $disk->delete($path);

        MonitoringService::warning('backup_deleted', 'Backup deleted: ' . $filename, ['admin_id' => auth()->id()]);

        return back()->with('success', 'Backup deleted successfully!');
    }

    public function clean()
    {
        try {
            Artisan::call('backup:clean');

            return back()->with('success', 'Old backups cleaned successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cleanup failed: ' . $e->getMessage());
        }
    }

    private function humanFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
