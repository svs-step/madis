Installation
============

## Pré-requis technique

### Installation pour le Développement
Afin de permettre un développement rapide sans s’occuper de la stack technique,
un Docker est mis à disposition. Il va utiliser les paquets suivants :

* PHP-FPM 8.1
* NGINX 1.23
* MySQL 8.0 ou mariaDB 10.8
* Composer
* GIT
* NodeJS 18 et npm 9

Pour cela, reportez vous au [lancement de la Stack de developpement](../developpement/1-lancer-stack-developpement.md).

### Installation pour un déploiement de MADIS sur un serveur

Les Docker n'ont pas été étudiés pour fonctionner en mode production.
De ce fait, et pour des problématiques de sécurités, veuillez installer MADIS sans docker.


Pour cela il vous faudra installer manuellement la stack technique.
Vous trouverez ci-dessous la stack technique utilisée et testée.
-	PHP-FPM 8.1
-	NGINX 1.23
-	MySQL 8.0 (ou MariaDB à partir de 10.8.3)
-	NodeJS 18
-	Composer
-	GIT
-	npm >= 8
-   Wkhtmltopdf

Ainsi que des extensions PHP : *php8.1-apcu php8.1-common php-fdomdocument php8.1-xml php8.1-cli php8.1-common php8.1-curl php8.1-fpm php8.1-gd php8.1-intl php8.1-json php8.1-mbstring php8.1-mysql php8.1-opcache php8.1-readline php8.1-bz2 php8.1-zip*.

(L'application peut néanmoins être compatible avec d'autres versions mais le support a lieu sur les versions énumérées ci-dessus)

#### Où aller télécharger ces packages ?


Installer nodeJS en suivant les indications suivantes:
- https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions 

Installer YARN en version 1+
- https://yarnpkg.com/lang/fr/docs/install/#debian-stable

Installer php-fpm 8.1

   Pour Ubuntu :
​      - https://www.linuxcapable.com/how-to-install-php-8-1-on-ubuntu-22-04-lts/

   Pour Debian 11 (Bullseye) :
​      - https://www.it-connect.fr/installation-de-php-8-1-sur-debian-11-pour-son-serveur-web/

(Si nécessaire) Installer MariaDB 10.8 :

​	Pour Debian 11 (Bullseye)

  - https://www.linuxcapable.com/install-upgrade-mariadb-10-8-on-debian-11-bullseye/

Pour installer Wkhtmltopdf suivre la documentation suivante : [Installer wkhtmltopdf](6-installer-wkhtmltopdf.md).
