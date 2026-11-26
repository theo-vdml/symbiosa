# Symbiosa

Site et billetterie de Symbiosa ASBL : pages publiques, vente de billets via
Stripe, envoi des billets en PDF, check-in à l'entrée, administration Filament.

## Stack

- **Backend** : Laravel 13, PHP 8.4 (extensions `bcmath`, `intl`, `gd`)
- **Admin** : Filament 5 (`/admin`)
- **Frontend** : Inertia 3 + Vue 3 + TypeScript, Vite, Tailwind, Wayfinder
- **Paiement** : Stripe Checkout + webhooks
- **Billets** : PDF (dompdf), envoyés par une tâche en file d'attente

## Installation locale

```bash
composer setup        # install, .env, clé, migrations, npm install, build
composer dev          # serveur, worker de queue, logs et Vite en parallèle
```

Créer un compte admin : `php artisan make:filament-user`.

### Variables d'environnement

Voir `.env.example`. En plus des variables Laravel habituelles :

| Variable | Rôle |
|---|---|
| `STRIPE_KEY`, `STRIPE_SECRET` | Clés API Stripe |
| `STRIPE_WEBHOOK_SECRET` | Secret de signature du webhook (`whsec_…`) |
| `SENTRY_LARAVEL_DSN` | Optionnel : remontée des erreurs et alertes de paiement |
| `MAIL_*` | Indispensable : les billets partent par email |

### Webhook Stripe

Endpoint : `POST /webhooks/stripe`. Événements à activer :

- `checkout.session.completed`
- `checkout.session.async_payment_succeeded`
- `checkout.session.async_payment_failed`
- `checkout.session.expired`

En local : `stripe listen --forward-to localhost:8000/webhooks/stripe`.

### Worker de queue

Les billets sont générés et envoyés par `FulfillCheckoutJob`. **Sans worker
(`php artisan queue:work`), aucun billet n'est envoyé.** `composer dev` le lance
en local ; en production, c'est supervisord (voir plus bas).

## Qualité

```bash
composer test         # Pint (vérification) + suite Pest
composer lint         # Pint (correction)
npm run lint          # ESLint (correction)
npm run format        # Prettier
npm run types:check   # vue-tsc
```

La CI (`.github/workflows/`) lance `style` (Pint, ESLint, Prettier) et `tests`
(Pest, PHP 8.4) sur chaque PR vers `staging` ou `main`.

## Branches

- `main` : production.
- `staging` : intégration, cible de toutes les PR.
- `dev-<feature>` : une branche par sujet, créée depuis `staging` et rebasée
  dessus avant la PR.

## Déploiement

Le déploiement utilise [Railpack](https://railpack.com) (FrankenPHP) :

- `Caddyfile` : configuration du serveur web ;
- `start-container.sh` : migrations, cache Laravel/Filament, puis supervisord ;
- `supervisord.conf` : lance FrankenPHP et le worker de queue.

`RAILPACK_SKIP_MIGRATIONS=true` désactive les migrations au démarrage.

## Licence

Propriétaire, tous droits réservés : voir [LICENSE](LICENSE).
