@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')

@section('content')
<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
    <div style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['total'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Total Messages</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--danger) 0%, #a71d2a 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['unread'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Unread</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--success) 0%, #20873a 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['read'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Read</div>
    </div>
</div>

@if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:1rem;">
        {{ session('success') }}
    </div>
@endif

<!-- Filters -->
<form method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; margin-bottom: 1.5rem; align-items: end;">
    <div>
        <label style="font-size:0.8rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, subject, message..."
               style="width:100%;padding:0.75rem;border:1px solid var(--border-gray);border-radius:8px;font-size:0.875rem;">
    </div>
    <div>
        <label style="font-size:0.8rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">Status</label>
        <select name="status" style="width:100%;padding:0.75rem;border:1px solid var(--border-gray);border-radius:8px;font-size:0.875rem;">
            <option value="">All</option>
            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
            <option value="read"   {{ request('status') == 'read'   ? 'selected' : '' }}>Read</option>
        </select>
    </div>
    <div>
        <label style="font-size:0.8rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">From Date</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               style="width:100%;padding:0.75rem;border:1px solid var(--border-gray);border-radius:8px;font-size:0.875rem;">
    </div>
    <div>
        <label style="font-size:0.8rem;font-weight:600;color:var(--text-muted);display:block;margin-bottom:4px;">To Date</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               style="width:100%;padding:0.75rem;border:1px solid var(--border-gray);border-radius:8px;font-size:0.875rem;">
    </div>
    <div>
        <x-admin.button type="primary">Filter</x-admin.button>
    </div>
</form>

<x-admin.card title="All Messages ({{ $messages->total() }})">
    @if($messages->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--text-muted);">
            <svg style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;opacity:0.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p>No messages found.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:0;">
            @foreach($messages as $msg)
            <div style="padding:1.25rem 1rem;border-bottom:1px solid var(--border-gray);display:flex;gap:1rem;align-items:flex-start;
                        {{ !$msg->is_read ? 'background:#fffbeb;' : '' }}">
                <!-- Left: icon -->
                <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                             background:{{ $msg->is_read ? '#f3f4f6' : 'linear-gradient(135deg,var(--primary-gold),var(--dark-gold))' }};
                             color:{{ $msg->is_read ? 'var(--text-muted)' : 'white' }};">
                    <svg style="width:1.2rem;height:1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <!-- Middle: content -->
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.3rem;">
                        <span style="font-weight:700;color:var(--text-dark);">{{ $msg->name }}</span>
                        @if(!$msg->is_read)
                            <span style="font-size:0.7rem;font-weight:700;background:var(--primary-gold);color:#2c2c2c;padding:2px 8px;border-radius:999px;">NEW</span>
                        @endif
                        <span style="font-size:0.8rem;color:var(--text-muted);">{{ $msg->email }}</span>
                        @if($msg->phone)
                            <span style="font-size:0.8rem;color:var(--text-muted);">· {{ $msg->phone }}</span>
                        @endif
                    </div>
                    <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.4rem;font-size:0.95rem;">{{ $msg->subject }}</div>
                    <div style="color:var(--text-muted);font-size:0.875rem;line-height:1.5;word-break:break-word;">
                        {{ Str::limit($msg->message, 200) }}
                        @if(strlen($msg->message) > 200)
                            <span style="color:var(--primary-gold);cursor:pointer;" onclick="toggleMessage({{ $msg->id }})"> Show more</span>
                            <span id="full-msg-{{ $msg->id }}" style="display:none;">{{ substr($msg->message, 200) }}
                                <span style="color:var(--primary-gold);cursor:pointer;" onclick="toggleMessage({{ $msg->id }})"> Show less</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Right: timestamp + actions -->
                <div style="flex-shrink:0;text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:0.5rem;">
                    <span style="font-size:0.8rem;color:var(--text-muted);white-space:nowrap;">{{ $msg->created_at->format('M d, Y g:i A') }}</span>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;justify-content:flex-end;">
                        @if(!$msg->is_read)
                            <form method="POST" action="{{ route('admin.contact-messages.markRead', $msg) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Mark as read"
                                        style="padding:4px 10px;background:var(--success);color:white;border:none;border-radius:6px;font-size:0.78rem;cursor:pointer;">
                                    ✓ Mark Read
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.contact-messages.markUnread', $msg) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Mark as unread"
                                        style="padding:4px 10px;background:#6b7280;color:white;border:none;border-radius:6px;font-size:0.78rem;cursor:pointer;">
                                    Mark Unread
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $msg) }}"
                              onsubmit="return confirm('Delete this message?');">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete"
                                    style="padding:4px 10px;background:var(--danger);color:white;border:none;border-radius:6px;font-size:0.78rem;cursor:pointer;">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
        <div style="padding:1.25rem 1rem;display:flex;justify-content:center;">
            {{ $messages->links() }}
        </div>
        @endif
    @endif
</x-admin.card>

<script>
    function toggleMessage(id) {
        const full = document.getElementById('full-msg-' + id);
        full.style.display = full.style.display === 'none' ? 'inline' : 'none';
    }
</script>
@endsection
