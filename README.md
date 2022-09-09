[![Codacy Badge](https://app.codacy.com/project/badge/Grade/93f95b3c1ec743c98f2da2afbb245c04)](https://www.codacy.com?utm_source=gitlab.com&utm_medium=referral&utm_content=ron2cuba/snowtricks&utm_campaign=Badge_Grade)
[![First Reposityory](https://img.shields.io/badge/First%20repository-1abc9c.svg)](https://gitlab.com/ron2cuba/p6_snowtricks)

# Snowtricks

Pourquoi deux repository ?

Dans la premiere version j'ai rencontré un souci avec le versioning de la base
de donnée du à :

```bash
git reset --hard
```
## Installation
L'insatllation du projet et de ses dépendances se fait grâce aux commandes :

```bash
composer install
# puis
npm i
```

## Outils

Requirements | Nom | - |
 --- | --- | --- |
<img src="./md/php.png" width="50"> | PHP | v8.1
<img src="./md/composer.png" width="50">| Composer | v2.4.1
<img src="./md/symfony.ico" width="50">| Symfony | v6.1
<img src="./md/npm.png" width="50">| NPM | v8.17.0

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
npm run dev --server
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
dependances grâce à NPM