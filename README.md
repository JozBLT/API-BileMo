# Blog OpenClassrooms

![banner](https://github.com/user-attachments/assets/000bdfe6-a392-4a96-8ea5-7395207a34c7)

Projet de la formation ***Développeur d'application - PHP / Symfony***.

**Créez un web service exposant une API** - [Lien de la formation](https://openclassrooms.com/fr/paths/876-developpeur-dapplication-php-symfony)

## Contexte

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme. Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo.
Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface).
Il s’agit donc de vente exclusivement en B2B (business to business).
Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.

## Descriptif du besoin 

Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.
Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :
- consulter la liste des produits BileMo ;
- consulter les détails d’un produit BileMo ;
- consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
- consulter le détail d’un utilisateur inscrit lié à un client ;
- ajouter un nouvel utilisateur lié à un client ;
- supprimer un utilisateur ajouté par un client.

Seuls les clients référencés peuvent accéder aux API. Les clients de l’API doivent être authentifiés via OAuth ou JWT.
Vous avez le choix entre mettre en place un serveur OAuth et y faire appel (en utilisant le FOSOAuthServerBundle), et utiliser Facebook, Google ou LinkedIn.
Si vous décidez d’utiliser JWT, il vous faudra vérifier la validité du token ; l’usage d’une librairie est autorisé.

Présentation des données
Le premier partenaire de BileMo est très exigeant : il requiert que vous exposiez vos données en suivant les règles des niveaux 1, 2 et 3 du modèle de Richardson.
Il a demandé à ce que vous serviez les données en JSON. Si possible, le client souhaite que les réponses soient mises en cache afin d’optimiser les performances des requêtes en direction de l’API.


## Installation du projet 

### 1. Prérequis

- PHP ≥ 8.2
- Composer
- Symfony CLI (recommandé)
- Serveur web local ou Docker (MySQL/PostgreSQL recommandé)

### 2. Cloner le dépôt

```bash
git clone https://github.com/JozBLT/API-BileMo.git
cd API-BileMo
```

### 3. Installer les dépendances

```bash
composer install
```

### 4. Configurer l'environnement

*   Créer un fichier .env.local à partir du fichier d’exemple
```bash
cp .env .env.local
```

*   Modifier les variables suivantes selon votre configuration
```bash
DATABASE_URL="mysql://root:password@127.0.0.1:3306/bilemo_db?serverVersion=8.0"
```

*   Générer la base de données et charger les fixtures
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 5. Authentification JWT

*   Générer les clés JWT
```bash
php bin/console lexik:jwt:generate-keypair
```

*   Renseigner ces clés dans le .env.local
```bash
JWT_SECRET_KEY=***********
JWT_PUBLIC_KEY=***********
JWT_PASSPHRASE=***********
```

### 6. Lancer le serveur

```bash
symfony server:start
```

*   Documentation Swagger accessible depuis :

```bash
http://127.0.0.1:8000/api/docs
```

*   Vous avez la possibilité d'utiliser un des comptes créés grace aux fixtures :
    - Super admin
        POST /api/login_check
        Content-Type: application/json
        
        {
          "email": "admin@bilemo.com",
          "password": "adminpassword"
        }

    - Admin Client
        POST /api/login_check
        Content-Type: application/json
        
        {
          "email": "admin@techcorp.com",
          "password": "password"
        }

    - User simple
        POST /api/login_check
        Content-Type: application/json
        
        {
          "email": "alice@techcorp.com",
          "password": "password"
        }

*   Le token obtenu devra être transmis dans l'en-tête Authorization des requêtes :

```bash
Authorization: Bearer <token>
```



## Auteur

**Jonathan Dumont** - *OC-P7-API BileMo* - [Joz](https://github.com/JozBLT)
