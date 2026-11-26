{{-- Brand panel shown beside the login / password reset forms (simple layout). Only visible on large screens. --}}
<aside class="sy-auth-aside" aria-hidden="true">
    <div class="sy-auth-aside-lines"></div>

    <div class="sy-auth-aside-brand">
        @include('filament.brand.logo')
    </div>

    <div class="sy-auth-aside-copy">
        <p class="sy-auth-aside-title">Préparer la prochaine <em>nuit</em>.</p>
        <p class="sy-auth-aside-text">Événements, billetterie, line-up et contenus du site, au même endroit.</p>
    </div>
</aside>
