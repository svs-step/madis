Lancer la Stack de développement
================================

## Pré-requis
* docker

## Installation
Copier le fichier `.env.dist` vers `.env` et éditez le avec les valeurs vous correspondant.

Ensuite, connectez votre docker à Gitlab avec vos identifiants.
Cela vous permettra d'accéder aux images Docker du projet.
```bash
docker login gitlab.adullact.net:4567
```

Finissez par initialiser le projet.
Pour cela le fichier `docker-service` fourni un raccourci pour lancer toute la Stack.
```bash
./docker-service initialize
```

Pour aller plus loin, vous pouvez étudier le fichier `docker-service` pour voir les commandes lancées.

## Utilisation basique

* Project URL : http://127.0.0.1:8888

## Quelques identifiants clés

- **Admin:** admin@awkan.fr / 111111
- **Collectivity 1:** collectivite@awkan.fr / 111111
- **Collectivity 2:** collectivite2@awkan.fr / 111111
- **Preview:** lecteur@awkan.fr / 111111
- **Inactive:** inactif@awkan.fr / 111111

## Lancer les tests
```
./docker-service tests              # Run quality tests, unit tests and functionnal tests
./docker-service unitTests          # Run unit tests
./docker-service qualityTests       # Run quality tests
```

## Usages de développement
* Le process Git utilisé est le Git Flow
* Les tests sont écrits en PHPUnit
* Le style de code doit être vérifié avec CSFixer et PHPLint avant les commits
