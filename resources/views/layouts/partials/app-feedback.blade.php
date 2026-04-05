@if (session('toast'))
    @php
        $toast = session('toast');
    @endphp
    <div class="app-toast-stack" data-toast-stack>
        <div class="app-toast app-toast-{{ $toast['type'] ?? 'info' }}" data-toast role="status" aria-live="polite">
            <span class="app-toast-icon" aria-hidden="true">
                <i class="fa-solid {{ ($toast['type'] ?? 'info') === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info' }}"></i>
            </span>
            <div class="app-toast-copy">
                <strong>{{ ($toast['type'] ?? 'info') === 'error' ? 'Permission denied' : 'Notice' }}</strong>
                <p>{{ $toast['message'] ?? '' }}</p>
            </div>
            <button type="button" class="app-toast-close" data-toast-close aria-label="Dismiss notification">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
@endif

<div class="app-confirm-shell" data-confirm-shell hidden>
    <div class="app-confirm-card" role="alertdialog" aria-modal="true" aria-labelledby="app-confirm-title" aria-describedby="app-confirm-message">
        <div class="app-confirm-icon" aria-hidden="true">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="app-confirm-copy">
            <strong id="app-confirm-title">Confirm action</strong>
            <p id="app-confirm-message">Are you sure you want to continue?</p>
        </div>
        <div class="app-confirm-actions">
            <button type="button" class="button secondary app-confirm-cancel" data-confirm-cancel>Cancel</button>
            <button type="button" class="app-confirm-submit" data-confirm-submit>Confirm</button>
        </div>
    </div>
</div>
