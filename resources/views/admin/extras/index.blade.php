@extends('layouts.admin')

@section('title', 'Extras Management')
@section('page-title', 'Extras Management')

@section('content')
<div style="display: grid; grid-template-columns: minmax(280px, 360px) 1fr; gap: 1.25rem; align-items: start;">
    <x-admin.card :title="$editExtra ? 'Edit Extra' : 'Add New Extra'">
        @if($editExtra)
            <form method="POST" action="{{ route('admin.extras.update', $editExtra) }}">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('admin.extras.store') }}">
                @csrf
        @endif

            <div style="display: grid; gap: 1rem;">
                <div>
                    <label for="name" style="display: block; margin-bottom: 0.4rem; font-weight: 600;">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $editExtra->name ?? '') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                </div>

                <div>
                    <label for="price" style="display: block; margin-bottom: 0.4rem; font-weight: 600;">Price (PHP)</label>
                    <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $editExtra->price ?? '') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                </div>

                <div>
                    <label for="description" style="display: block; margin-bottom: 0.4rem; font-weight: 600;">Description</label>
                    <textarea id="description" name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">{{ old('description', $editExtra->description ?? '') }}</textarea>
                </div>

                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', isset($editExtra) ? (int) $editExtra->is_active : 1) ? 'checked' : '' }}>
                    <label for="is_active" style="font-weight: 500;">Active</label>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <x-admin.button type="primary">{{ $editExtra ? 'Update Extra' : 'Create Extra' }}</x-admin.button>
                    @if($editExtra)
                        <a href="{{ route('admin.extras.index') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.65rem 1rem; border-radius: 8px; border: 1px solid var(--border-gray); color: var(--text-dark); text-decoration: none; font-weight: 500;">Cancel</a>
                    @endif
                </div>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card title="Extras List">
        <form method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search extras..." style="min-width: 240px; flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <x-admin.button type="primary">Filter</x-admin.button>
        </form>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 720px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-gray);">
                        <th style="text-align: left; padding: 0.75rem; font-size: 0.84rem; color: var(--text-muted);">Name</th>
                        <th style="text-align: left; padding: 0.75rem; font-size: 0.84rem; color: var(--text-muted);">Description</th>
                        <th style="text-align: right; padding: 0.75rem; font-size: 0.84rem; color: var(--text-muted);">Price</th>
                        <th style="text-align: center; padding: 0.75rem; font-size: 0.84rem; color: var(--text-muted);">Status</th>
                        <th style="text-align: center; padding: 0.75rem; font-size: 0.84rem; color: var(--text-muted);">Used In Bookings</th>
                        <th style="text-align: right; padding: 0.75rem; font-size: 0.84rem; color: var(--text-muted);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extras as $extra)
                        <tr style="border-bottom: 1px solid var(--border-gray);">
                            <td style="padding: 0.9rem 0.75rem; font-weight: 600;">{{ $extra->name }}</td>
                            <td style="padding: 0.9rem 0.75rem; color: var(--text-muted); max-width: 300px; white-space: normal;">{{ $extra->description ?: 'No description' }}</td>
                            <td style="padding: 0.9rem 0.75rem; text-align: right; font-weight: 600;">PHP {{ number_format((float) $extra->price, 2) }}</td>
                            <td style="padding: 0.9rem 0.75rem; text-align: center;">
                                @if($extra->is_active)
                                    <span style="display: inline-block; padding: 0.3rem 0.6rem; border-radius: 999px; background: #e7f8ed; color: #1f8a46; font-size: 0.78rem; font-weight: 600;">Active</span>
                                @else
                                    <span style="display: inline-block; padding: 0.3rem 0.6rem; border-radius: 999px; background: #f3f4f6; color: #475569; font-size: 0.78rem; font-weight: 600;">Inactive</span>
                                @endif
                            </td>
                            <td style="padding: 0.9rem 0.75rem; text-align: center;">{{ $extra->bookings_count }}</td>
                            <td style="padding: 0.9rem 0.75rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                    <a href="{{ route('admin.extras.index', array_merge(request()->query(), ['edit' => $extra->id])) }}" style="padding: 0.4rem 0.7rem; border: 1px solid var(--border-gray); border-radius: 6px; color: var(--text-dark); text-decoration: none; font-size: 0.8rem;">Edit</a>

                                    @if(auth()->user()->role === 'admin')
                                        @if($extra->bookings_count > 0)
                                            <button type="button" disabled title="Cannot delete extras already used in bookings." style="padding: 0.4rem 0.7rem; border: none; border-radius: 6px; background: #d9d9d9; color: #6b6b6b; font-size: 0.8rem; cursor: not-allowed;">Delete</button>
                                        @else
                                            <form method="POST" action="{{ route('admin.extras.destroy', $extra) }}" onsubmit="return confirm('Are you sure you want to delete this extra?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="padding: 0.4rem 0.7rem; border: none; border-radius: 6px; background: #dc3545; color: #fff; font-size: 0.8rem; cursor: pointer;">Delete</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 1rem; text-align: center; color: var(--text-muted);">No extras found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1rem;">
            {{ $extras->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
