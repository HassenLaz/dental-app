{{-- resources/views/filament/widgets/google-calendar-status.blade.php --}}
<div class="rounded-xl border border-gray-700 bg-gray-800 px-5 py-4 p-6">
    <div class="flex items-center justify-between gap-4">
 
        <div class="flex items-center gap-3">
            <div style="width:10px;height:10px;border-radius:50%;background:{{ $connected ? '#22c55e' : '#ef4444' }};flex-shrink:0"></div>
            <span style="font-size:0.875rem;color:#d1d5db">
                Google Calendar :
                <strong style="color:{{ $connected ? '#22c55e' : '#f87171' }}">
                    {{ $connected ? 'Connecté — synchronisation automatique active' : 'Non connecté' }}
                </strong>
            </span>
        </div>
 
        @if(!$connected)
        <a href="{{ route('google.redirect') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;background:#2563eb;color:#fff;padding:6px 14px;border-radius:8px;text-decoration:none;white-space:nowrap">
            Connecter Google Calendar
        </a>
        @endif
 
    </div>
</div>