{{-- Shown in the topbar on every environment except production, so nobody edits staging thinking it is live. --}}
@unless (app()->isProduction())
    <span class="sy-env-badge" title="Environnement : {{ app()->environment() }}">
        {{ str(app()->environment())->upper() }}
    </span>
@endunless
