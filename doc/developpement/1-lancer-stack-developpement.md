Lancer la Stack de développement
================================

## Pré-requis
* docker

## Installation
Copier le fichier `.env.dist` vers `.env` et éditez le avec les valeurs vous correspondant.

Si vous utilisez un processeur ARM (raspberry pi, mac M1 / M2) vous pouvez notamment changer les images docker pour des images ARM en remplaçant

```
DOCKER_IMAGE_PHP=gitlab.adullact.net:4567/soluris/madis/php:1.2
DOCKER_IMAGE_NGINX=gitlab.adullact.net:4567/soluris/madis/nginx:1.2
```
par 
```
DOCKER_IMAGE_PHP=gitlab.adullact.net:4567/soluris/madis/php:1.2-arm64
DOCKER_IMAGE_NGINX=gitlab.adullact.net:4567/soluris/madis/nginx:1.2-arm64
```

Ensuite, connectez votre docker à Gitlab avec vos identifiants.  
Cela vous permettra d'accéder aux images Docker du projet.
```bash
docker login gitlab.adullact.net:4567
```

#### SSL

Afin d'obtenir des certificats autosignés vous avez besoin d'installer [mkcert](https://github.com/FiloSottile/mkcert) sur votre machine.1  
Créez le certificat et la clef pour votre environnement de développement

```bash
mkcert -key-file key.pem -cert-file cert.pem 127.0.0.1 madis.local
```
Puis copiez les fichiers créés dans le dossier `docker/nginx/certificats/default`.
```bash
cp *.pem ./docker/nginx/certificats/default/
```


Finissez par initialiser le projet.  
Pour cela le fichier `docker-service` fourni un raccourci pour lancer toute la Stack.
```bash
./docker-service initialize
```

Pour aller plus loin, vous pouvez étudier le fichier `docker-service` pour voir les commandes lancées.

## Utilisation basique

* Project URL : http://127.0.0.1:8888 ou https://127.0.0.1 

## Quelques identifiants clés

- **Admin:** admin@awkan.fr / 111111
- **Collectivity 1:** collectivite@awkan.fr / 111111
- **Collectivity 2:** collectivite2@awkan.fr / 111111
- **Preview:** lecteur@awkan.fr / 111111
- **Inactive:** inactif@awkan.fr / 111111

## Lancer les tests
```bash
./docker-service tests              # Run quality tests, unit tests and functionnal tests
./docker-service unitTests          # Run unit tests
./docker-service qualityTests       # Run quality tests
```

## Usages de développement
* Le process Git utilisé est le Git Flow
* Les tests sont écrits en PHPUnit
* Le style de code doit être vérifié avec CSFixer et PHPLint avant les commits
