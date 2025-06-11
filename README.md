# 📚 Pile à Lire — Back-End (Laravel)

Ce dépôt contient le code back-end du projet **Pile à Lire**, développé avec le framework **Laravel** (PHP).

---

## 🚀 Prérequis

Avant de commencer, assurez-vous d’avoir installé :

- PHP 8.2 (via PPA ondrej/php)
- Composer
- MySQL ou MariaDB
- Node.js et npm (si assets front à compiler)
- Git

---

## 🔧 Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/nom-utilisateur/pal-back-v3.git
   cd pal-back-v3
   ```

2. **Configurer PHP 8.2**
   ```bash
   sudo add-apt-repository ppa:ondrej/php
   sudo apt update
   sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-mysql php8.2-bcmath php8.2-zip unzip curl
   ```

3. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

4. **Configurer l’environnement**
   ```bash
   cp .env.example .env
   nano .env
   ```
   > Modifier les paramètres de connexion à la base de données (`DB_DATABASE`, `DB_USERNAME`, etc.)

5. **Générer la clé d'application**
   ```bash
   php artisan key:generate
   ```

6. **Lancer les migrations**
   ```bash
   php artisan migrate
   ```

---

## ▶️ Lancement du projet

Démarrer le serveur de développement Laravel :

```bash
php artisan serve
```

Accéder à l'application via [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🧪 Tests

*À compléter selon les tests intégrés (PHPUnit, Pest, etc.)*

```bash
php artisan test
```

---

## 📁 Structure du projet

- `app/` — Logique métier (Controllers, Models, etc.)
- `routes/` — Fichiers de routage
- `database/` — Migrations et seeders
- `resources/views/` — Fichiers Blade (si utilisés)
- `public/` — Point d'entrée web

---

## 🤝 Contribution

1. Fork du projet
2. Créer une branche (`git checkout -b feature/ma-feature`)
3. Commit (`git commit -am 'Ajout de ma feature'`)
4. Push (`git push origin feature/ma-feature`)
5. Créer une Pull Request

---

## 🛠 Dépannage courant

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 📜 Licence

Projet sous licence [MIT](LICENSE).

---

## 👤 Auteurs

- [Ton Nom / Equipe] — Développement back