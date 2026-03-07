@extends('layouts.admin')

@section('title', 'Backup & Recovery')
@section('page-title', 'Backup & Recovery')

@section('content')
<x-admin.card title="Database Backup — Export Tables as CSV">
    <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">
        Download a CSV export of any table below. Use these files to back up or recover data.
    </p>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Table</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $table)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem; font-weight: 600; text-transform: capitalize;">
                        {{ str_replace('_', ' ', $table) }}
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400; margin-left: 0.5rem;">({{ $table }})</span>
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <a href="{{ route('admin.backup.export', $table) }}"
                           style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #d4af37, #f4e4c1); color: #2c2c2c; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export CSV
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin.card>
@endsection
