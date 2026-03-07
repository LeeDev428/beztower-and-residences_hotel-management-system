<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    private array $exportableTables = [
        'users', 'rooms', 'room_types', 'amenities', 'bookings',
        'guests', 'payments', 'extras', 'activity_logs', 'contact_messages',
    ];

    public function index()
    {
        $tables = $this->exportableTables;
        return view('admin.backup.index', compact('tables'));
    }

    public function export(string $table): StreamedResponse
    {
        if (!in_array($table, $this->exportableTables)) {
            abort(403, 'Export not allowed for this table.');
        }

        $columns = Schema::getColumnListing($table);
        $rows = DB::table($table)->get();

        $filename = $table . '_backup_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($columns, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($rows as $row) {
                fputcsv($handle, (array) $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
