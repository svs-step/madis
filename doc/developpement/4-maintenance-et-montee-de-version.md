Maintenance applicative
=======================

## Montées de versions de librairies

Les dépendances sont gérées avec le gestionnaire de paquet *composer*. Ce dernier permet de tenir à jour les librairies.

Une simple commande permet de mettre à jour les librairies (les fix de bugs ou de failles de sécurité).

Utilisez la commande `composer update` qui va modifier le fichier *composer.lock*. Pensez ensuite à l’ajouter au GIT puis créer une nouvelle release pour pouvoir effectuer cette montée de version en production.


## Déploiement d’une nouvelle version de l’application

### Préparer la release

Parlons GIT.
Nous suivons le Git Flow. Le code qui est prêt à mettre en production se trouve donc sur la branche develop. Il faut donc créer une branche de release, y monter la version de l’application dans le fichier *config/packages/framework.yaml* et modifier le « *CHANGELOG.md* » avec les dernières modifications.

Il vous suffit de merger cette release dans la develop et  master puis de tagger la master avec le numéro de version correspondant.

### Déployer en pré-production et prodution

Pour déployer, rien de plus simple. Rendez-vous sur le site correspondant, dans le dossier du projet

-	/var/www/gestion-rgpd* pour la pré-production
-	/var/www/madis* pour la production

Lancez-y la commande `./bin/deploy` pour la préproduction, cela mettra à jour avec la branche `develop`.
Pour la production, reportez vous à la partie de la documentation traitant de ce sujet.
