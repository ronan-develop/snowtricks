[![Codacy Badge](https://app.codacy.com/project/badge/Grade/93f95b3c1ec743c98f2da2afbb245c04)](https://www.codacy.com?utm_source=gitlab.com&utm_medium=referral&utm_content=ron2cuba/snowtricks&utm_campaign=Badge_Grade)

# Snowtricks

## Installation
L'insatllation du projet et de ses dépendances se fait grâce aux commandes :

```bash
composer install
# puis
npm i
```
dans un outil comme [phpMyAdmin](https://www.phpmyadmin.net/downloads/) que l'on
retourve par exemple dans [Wamp](https://www.wampserver.com/) | [Xampp](https://www.apachefriends.org/download.html) |
[LAMP](https://doc.ubuntu-fr.org/lamp) | [MAMP](https://www.mamp.info/en/downloads/) :
Se créer une base de donnée et la nommer `snowtricks` ou dans votre console lancer 
la commande :
```bash
php bin/console doctrine:database:create
```
Vérifier au préalable que le fichier `.env` à la racine du projet est correctement
renseigné.

DATABASE_URL="mysql://<u>"userMysql"</u>:<u>"Mot de Passe"</u>@127.0.0.1:3306/<u>"nom de la base"</u>?serverVersion=8&charset=utf8mb4"

Lancer ensuite les migrations pour créer les colonnes de la base de donnée Mysql:

```bash
doctrine:migrations:migrate
```

## Outils

| Requirements                             | Nom      | -       |
|------------------------------------------|----------|---------|
| <img src="./md/php.png" width="50">      | PHP      | v8.1    |
| <img src="./md/composer.png" width="50"> | Composer | v2.4.1  |
| <img src="./md/symfony.ico" width="50">  | Symfony  | v6.1    |
| <img src="./md/npm.png" width="50">      | NPM      | v8.17.0 |

## Assets management

<img src="./md/webpack-encore.jpg" height="250px">

Les Assets sont gérés avec le Bundle WebpackEncore qui est une intégration de
Webpack pour Symfony.

Les commandes suivantes peuvent être utilisées :
```bash
# compilation unique
npm run build
# compilation sur un server
npm run dev
# compilation avec refresh du navigateur
npm run dev-server
# écoute de modification
npm run watch
```

Les commandes sont disponilbles dans le fichier `package.json`
```json
"scripts": {
        "dev-server": "encore dev-server",
        "dev": "encore dev",
        "watch": "encore dev --watch",
        "build": "encore production --progress"
    },
```

Le micro framework css [`picocss`](https://picocss.com/) est importé comme
dépendances grâce à NPM tout comme [SassLoader](https://webpack.js.org/loaders/sass-loader/)
et [WebpackEncore](https://symfony.com/doc/current/frontend.html)

## Database

Il vous est possible de peupler la base de données, avec un jeu de data d'exemple.
IL est présent dans le fichier: `snowtrick.sql`. Por ce faire dans `phpMyAdmin`
cliquez sur SQL et coller le contenu de ce fichier.

## EasyAdmin Bundle

le backoffice a été implémenté avec le package [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html)

## Routes

```bash
php bin/console debug:router

 -------------------------- -------- -------- ------ ----------------------------------- 
  Name                       Method   Scheme   Host   Path                               
 -------------------------- -------- -------- ------ ----------------------------------- 
  admin                      ANY      ANY      ANY    /admin
  app_category               ANY      ANY      ANY    /category
  app_comment                ANY      ANY      ANY    /comment
  app_home                   ANY      ANY      ANY    /
  app_register               ANY      ANY      ANY    /register
  app_verify_email           ANY      ANY      ANY    /verify
  app_login                  ANY      ANY      ANY    /login
  app_logout                 ANY      ANY      ANY    /logout
  app_ask_new                ANY      ANY      ANY    /ask-new-password
  app_reset_password         ANY      ANY      ANY    /reset_password/{token}
  app_terms                  ANY      ANY      ANY    /terms
  app_trick                  ANY      ANY      ANY    /trick/{slug}
  app_delete_trick           ANY      ANY      ANY    /tricks/{slug}/delete
  app_media                  ANY      ANY      ANY    /see-medias/{slug}
 -------------------------- -------- -------- ------ -----------------------------------
```

## Users (pré-enregistrés)
|  Pseudo  | Mot de passe |     Statut     | Accès au BackOffice | Comptes vérifiés |
|:--------:|:------------:|:--------------:|:-------------------:|:----------------:|
|  Admin   |   password   | administrateur |          ✔          |        ✔         |
|   Bob    |   password   |  utilisateur   |          ❌          |        ❌         |
|   Bill   |   password   |      user      |          ❌          |        ❌         |
| ron2cuba |   password   |      user      |          ✔          |        ✔         |

### Droits
- user: 

Peut modifier son compte, créer/modifier/supprimmer des tricks

- admin: 


Peut modifier tous les comptes, créer/modifier/supprimmer des tricks,
 créer/modifier/supprimmer des catégories
