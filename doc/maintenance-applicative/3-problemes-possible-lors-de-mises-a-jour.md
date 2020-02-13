Problèmes connus de mise à jour
=============================

## Des fichiers sont en cours de modification

Des fichiers ont été modifiés, le checkout est avorté.
```shell
root@madis:/var/www/madis# git checkout v1.3.1
error: Your local changes to the following files would be overwritten by checkout:
        .env.dist
        config/domain/user/translations/messages.fr.yaml
        src/Domain/Reporting/Generator/Word/AbstractGenerator.php
Please, commit your changes or stash them before you can switch branches.
Aborting

# Consulter les différences entre les fichiers
root@madis:/var/www/madis# git diff

# Abandonner les modifications
root@madis:/var/www/madis# git reset --hard
HEAD is now at 28aabb01 Merge branch 'release/v1.1.0'

# Relancer le checkout
root@madis:/var/www/madis# git checkout v1.3.1
Previous HEAD position was 28aabb01... Merge branch 'release/v1.1.0'
HEAD is now at bfab85a... Merge branch 'release/v1.3.1'
```

## Vous n'arrivez pas à effectuer une commande vers le Git distant

```shell
root@madis:/var/www/madis# git fetch
ssh: connect to host gitlab.adullact.net port 22: Connection timed out
fatal: Impossilble de lire le dépôt distant.

Veuillez vérifier que vous avez les droits d'accès
et que le dépôt existe.
```

Dans ce cas, vérifiez les accès réseau sur le port 22 vers gitlab.adullact.net et réessayer

Une fois cette manipulation effectuée, `git fetch` ou autre commande git nécessitant
le contact du dépôt distant devrait fonctionner.

## Débugguer Madis

Logs interessants :

- */var/log/nginx/madis_access.log* : Logs d'accès Nginx
- */var/log/nginx/madis_error.log* : Logs d'erreur Nginx
- */var/www/madis/var/log/prod.log* : Logs de Madis

Configuration du fichier */var/www/madis/.env* pour passer l'application en mode DEV :

**Attention** : Evitez d'utiliser ceci en production, celà engendrerait des fuites de sécurité
de votre application (comme la divulgation de votre configuration du fichier `.env`et ses mots de passe).
Vous pouvez cependant l'utiliser sur des environnements n'ayant pas d'enjeux ou qui possède des accès restreints.

* *APP_ENV=prod* : à passer en *APP_ENV=dev*
* *APP_DEBUG=0* : à passer à 1 pour augmenter les logs et profiter d'une barre de logs/debug (profiler) sur l'application
