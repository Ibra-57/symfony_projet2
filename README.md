# Backoffice — Matériel de Randonnée Haut de Gamme

Application Symfony de gestion du backoffice pour une entreprise de matériel de randonnée.

## Prérequis

- PHP 8.2+
- Composer
- MySQL / MariaDB (serveur local démarré)
- Node.js (pour Tailwind CSS)

---

## Installation

### 1. Cloner et installer les dépendances

```bash
composer install
```

### 2. Configurer la base de données

Créez un fichier `.env.local` à la racine :

```dotenv
DATABASE_URL="mysql://root:password@127.0.0.1:3306/app2?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

Adaptez les valeurs `root`, `password`, `app2` selon votre configuration locale.

### 3. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Charger les données de test (fixtures)

```bash
php bin/console doctrine:fixtures:load
```

Cela crée **7 utilisateurs** et **8 produits** de test.

### 5. Compiler les assets Tailwind CSS

```bash
php bin/console tailwind:build
copy var\tailwind\app.built.css public\css\app.css
```

### 6. Lancer le serveur de développement

```bash
symfony server:start
```

Accédez à [http://localhost:8000](http://localhost:8000).

---

## Rôles disponibles

| Rôle | Accès |
|------|-------|
| `ROLE_ADMIN` | Tableau de bord, Produits, Clients, Utilisateurs (CRUD complet) |
| `ROLE_MANAGER` | Tableau de bord, Produits (lecture), Clients (CRUD sans suppression) |
| `ROLE_USER` | Tableau de bord, Produits (lecture seule) |

### Comptes de test (après fixtures)

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| `admin@example.com` | `password` | ROLE_ADMIN |
| `manager@example.com` | `password` | ROLE_MANAGER |
| `user@example.com` | `password` | ROLE_USER |

---

## Commandes disponibles

### Importer des produits depuis un CSV

Placez votre fichier CSV dans `/public` puis exécutez :

```bash
php bin/console app:import-products produits.csv
```

**Format CSV attendu** (séparateur `;`) :

```csv
Nom;Type;Description;Prix;Poids;Stock;Clé de licence
Sac Pro 50L;physical;Sac robuste;199.99;1.5;30;
Guide PDF TMB;digital;Guide Tour du Mont Blanc;29.99;;;KEY-TMB-2024
```

- `Type` : `physical` ou `digital`
- `Poids` et `Stock` : requis pour les produits physiques
- `Clé de licence` : requise pour les produits numériques

---

### Créer un client interactivement

```bash
php bin/console app:create-client
```

La commande demandera successivement :
1. Prénom
2. Nom
3. Email (valide et unique)
4. Téléphone (optionnel)
5. Adresse (optionnel)
6. Confirmation avant enregistrement

---

## Lancer les tests

```bash
php bin/phpunit
```

Tests disponibles :
- **`CsvExportServiceTest`** — Test unitaire du service d'export CSV (mocks repository)
- **`UserCreationTest`** — Tests fonctionnels : création utilisateur avec mock EntityManager, unicité email, attribution des rôles

---

## Structure du projet

```
src/
├── Command/
│   ├── ImportProductsCommand.php   # Import CSV produits
│   └── CreateClientCommand.php     # Création client interactive
├── Controller/
│   ├── DashboardController.php
│   ├── ProductController.php
│   ├── ClientController.php
│   ├── UserController.php
│   └── SecurityController.php
├── Entity/
│   ├── User.php
│   ├── Product.php
│   └── Client.php
├── Form/
│   ├── UserType.php
│   ├── ClientType.php
│   └── ProductFlow/               # Formulaire multi-étapes produit
├── Security/Voter/
│   ├── UserVoter.php
│   ├── ProductVoter.php
│   └── ClientVoter.php
└── Service/
    └── CsvExportService.php
tests/
├── Service/
│   └── CsvExportServiceTest.php
└── Controller/
    └── UserCreationTest.php
```

---

## Formulaire multi-étapes produit (Craue FormFlow)

La création de produit suit 5 étapes :

1. **Type de produit** — Physique ou Numérique
2. **Détails** — Nom, Description, Prix
3. **Logistique** *(physique uniquement)* — Poids, Stock
4. **Licence** *(numérique uniquement)* — Clé d'accès
5. **Confirmation** *(si prix > 500€)* — Checkbox de validation
6. **Récapitulatif** — Aperçu avant validation
