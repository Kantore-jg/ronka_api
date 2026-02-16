# RONKA Event Multi Service - API Backend

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=RonkaSeeder
```

## Configuration

### .env

- `APP_URL` : URL du backend (ex: http://localhost:8000)
- `FRONTEND_URL` : URL du frontend pour CORS (ex: http://localhost:5173)
- `MAIL_*` : Configuration SMTP pour l'envoi des emails de feedback vers codewithkantox@gmail.com

### Frontend (ronka_service)

Créer `.env` avec :
```
VITE_API_URL=http://localhost:8000/api
```

## Comptes de démo

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@ronka.com | admin123 |
| Membre | membre@ronka.com | membre123 |
| Public | public@ronka.com | public123 |

## Endpoints API

### Public (sans auth)
- `POST /api/login` - Connexion
- `POST /api/register` - Inscription (compte public)
- `POST /api/feedback` - Envoyer feedback/suggestion (envoie aussi un email à codewithkantox@gmail.com)
- `GET /api/gallery` - Liste galerie
- `GET /api/partners` - Partenaires approuvés
- `POST /api/bookings` - Créer réservation
- `POST /api/donations` - Créer don
- `POST /api/partners` - Demande partenariat

### Authentifié (Bearer token)
- `POST /api/logout` - Déconnexion
- `GET /api/me` - Utilisateur connecté
- `GET /api/bookings` - Liste réservations (admin)
- `GET /api/donations` - Liste dons (admin)
- `GET /api/feedbacks` - Liste feedbacks (admin)
- `POST /api/partners/{id}/approve` - Approuver partenaire (admin)
- `GET /api/members` - Liste membres (admin)
- `POST /api/members` - Créer membre (admin)
- `DELETE /api/members/{id}` - Supprimer membre (admin)
- `GET /api/events` - Liste événements
- `POST /api/events` - Créer événement (admin)
- `POST /api/events/{id}/assign` - Assigner membre (admin)
- `POST /api/events/{id}/comment` - Commenter (auth)
- `POST /api/gallery` - Ajouter image/vidéo (admin)
- `DELETE /api/gallery/{id}` - Supprimer (admin)

## Feedback & Email

Lors de l'envoi d'un feedback :
1. Le feedback est enregistré en base
2. Un email est envoyé à **codewithkantox@gmail.com**
3. L'expéditeur (From) est l'email de l'utilisateur connecté, ou le contact si non connecté

## Lancer le serveur

```bash
php artisan serve
```

API disponible sur http://localhost:8000/api
