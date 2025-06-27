# 📚 Verso – Backend API (Laravel)

Backend de l’application **Verso**, un outil personnel de gestion de lectures. Cette API RESTful, sécurisée avec **Laravel Sanctum**, permet de gérer les utilisateurs, les livres, les listes personnalisées, les notes, les commentaires et le suivi de lecture.

## 🧩 Fonctionnalités

-   ✅ Authentification par email/mot de passe (JWT)
-   📂 Gestion des listes de lecture (wishlist, favoris, personnalisées)
-   ✍️ Ajout de commentaires et de notes sur chaque livre
-   ⏳ Suivi d’avancement de lecture (débuté, terminé, abandonné, etc.)
-   🔍 Recherche via API externe (BNF)
-   🔐 API sécurisée par middleware et validation serveur

## 🧰 Stack technique

-   **Laravel 12**
-   **MySQL 8**
-   **Sanctum** – Authentification par token
-   **Eloquent ORM** – Modélisation et relations
-   **Vite** – Intégration front & build assets
-   **Postman** – Tests API REST
-   **Composer** – Gestion des dépendances PHP

## ⚙️ Installation

```bash
git clone https://github.com/ton-compte/verso-backend.git
cd verso-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

> ⚠️ Assurez-vous que votre base de données est configurée dans `.env`.

## 🔒 Sécurité

-   Mots de passe hashés avec `bcrypt`
-   Authentification stateless avec **JWT** (via Laravel Sanctum)
-   Routes protégées par `auth:sanctum` + vérification de rôle (`user` ou `admin`)
-   Validation stricte des données avec **FormRequest**
-   Prévention :
    -   💉 Injections SQL via Eloquent (requêtes préparées)
    -   🧼 Failles XSS (sorties échappées par défaut)
    -   🛡️ Attaques CSRF inutiles (stateless)
    -   🚫 Brute force : `throttle:30,1` sur `/login`

## 🔍 Endpoints principaux

```http
POST    /api/user/login           → Connexion
GET     /api/books/search         → Recherche API BNF
POST    /api/userlists            → Créer une liste
POST    /api/userlistBooks        → Ajouter un livre à une liste
POST    /api/comments             → Ajouter un commentaire
POST    /api/notes                → Ajouter une note
DELETE  /api/favorites/isbn/:id   → Supprimer un favori
```

## 🧪 Tests

-   ✅ Tests des routes via **Postman**
-   🔎 Suivi des données en base via MySQL
-   📜 Logs d’erreur et debug dans `storage/logs`
-   ⚙️ Tests manuels des cas limites (doublons, accès interdit...)

## 📁 Arborescence partielle

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   ├── Models/
├── routes/
│   └── api.php
├── database/
│   ├── migrations/
├── .env.example
└── composer.json
```

## 📄 Licence

Projet réalisé dans le cadre du **Titre Professionnel Développeur Web et Web Mobile** – Certification niveau 5 (Bac +2).

## 👤 Auteur

**Gabriel Henin**  
Développeur Web Fullstack  
Projet personnel – 2025
