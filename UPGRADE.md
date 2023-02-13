# Informations de mises à jour

Il est possible que vous deviez réaliser des actions manuelles lors
de vos montées de version de MADIS.

De ce fait, répertoriez-vous à ce qui est écrit ci-dessous.

**_Note: Si aucune trace n'est présente dans ce fichier pour la version
que vous souhaitez installer, c'est que vous n'avez rien à faire._**

De manière préventive, n'oubliez pas de faire un dump de votre base de
données avant d'effectuer une montée de version.  


# Passage en v1.6.0

### Paramétrage de la subrogation d'un utilisateur

Dans le `.env` vous pouvez modifier les informations liées à la subrogation des utilisateurs.
Dans le cas où vous créez des données pour les structure,
vous pouvez choisir si vous souhaitez faire afficher le nom de la personne sur qui l'admin s'est connectée
ou le nom de l'admin lui même. 

_Note : Vous pouvez aller regarder le fichier `.env.dist` en guise d'exemple._

Pour cela, ajoutez la variable suivante dans votre `.env` et configurez la.
- Mettre `1` si le nom de l'admin doit apparaitre
- Mettre `0` si le nom de la personne sur laquelle l'admin s'est connecté doit apparaitre

```text
APP_IMPERSONATE_CREATOR_IS_ADMIN=0
```


# Passage en v1.5.0

### Paramétrage des timeout Cookie

Dans le `.env` vous pouvez modifier les informations liées à la durée de vie
des cookies ainsi que la deconnexion au bout d'un certain temps d'inactivité

Pour cela, ajoutez les variables suivantes dans votre `.env`.

```text
# Déconnexion pour une inactivité au bout de 1h30 (en secondes)
APP_COOKIE_IDLE_TIMEOUT=5400
# Déxonnexion au bout de 4h (en secondes), même si j'utilise l'application
APP_COOKIE_LIFETIME=14400
```

Il vous faudra également vérifier que dans votre fichier `php.ini`
la variable `session.gc_maxlifetime` égale ou supérieure à votre durée d'inactivité.
Le cas échéant, la session pourrait être supprimée pour inactivité par PHP et non par
votre configuration de MADIS.

Pour prendre connaissance de l'emplacement de votre fichier `php.ini`,
utilisez la commande `php --ini`. Une fois modifié, il vous suffira de restarter votre PHP.



# Passage en v1.4.0

**Note d'attention :** Cette nouvelle version va modifier vos données présentes
en Base de Données. Pensez à créer une sauvegarde de votre BDD au cas où.

### Possibilité de configurer les logos & le fournisseur de service

Dans le `.env` vous pouvez modifier les informations des logos et
du fournisseur de service MADIS (si vous ne souhaitez modifier les
informations, copiez les variables comme affichées dans l'exemple)

Ajoutez les variables suivantes dans votre `.env`.

```text
APP_FOOTER_PROVIDER_NAME=SOLURIS
APP_FOOTER_PROVIDER_URL="https://soluris.fr"
APP_IMAGE_FAVICON_PATH="favicon.ico"
APP_IMAGE_SIDEBAR_BOTTOM_TARGET_URL="https://soluris.fr"
APP_IMAGE_SIDEBAR_BOTTOM_PATH="images/soluris-logo-white.png"
APP_IMAGE_SIDEBAR_REDUCED_PATH="images/icon-32x32.png"
```

_Note: Vous pourrez venir modifier ces variables plus tard._

Si vous souhaitez ajouter vos propres images, le dossier `public/custom`
n'est pas versionné dans GIT. 
De ce fait, si vous souhaitez ajouter un favicon dans
`public/custom/images/favicon.ico` par exemple, il vous suffira de changer
`APP_IMAGE_FAVICON_PATH="favicon.ico"` par `APP_IMAGE_FAVICON_PATH="custom/images/favicon.ico"`
(le dossier `public` étant déjà ciblé par défaut).

Finissez par lancer la commande `bin/console cache:clear` pour que ces
données soient appliquées



# Passage en v1.3.1

### Ajout de l'adresse du DPD moral / par défaut
 
Modifier le fichier `.env` pour y ajouter les 3 champs suivants,
dans la section `symfony/framework-bundle` comme montré ci-dessous. 

_Note: Vous pouvez vous aider du fichier `.env.dist`
qui est le fichier de référence, avec des valeurs par défaut._

```text
###> symfony/framework-bundle ###
APP_DPO_ADDRESS_CITY="Saintes"
APP_DPO_ADDRESS_STREET="2 rue des Rochers"
APP_DPO_ADDRESS_ZIP_CODE="17100"
###< symfony/framework-bundle ###
```
