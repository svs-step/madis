Installation
============

## Pré-requis technique

### Installation pour le Développement
Afin de permettre un développement rapide sans s’occuper de la stack technique,
un Docker est mis à disposition. Il va utiliser les paquets suivants :

* PHP-FPM 7.1
* NGINX 1.10.3
* MySQL 5.7
* Composer
* GIT
* NodeJS 8 et YARN

Pour cela, reportez vous au [lancement de la Stack de developpement](../developpement/1-lancer-stack-developpement.md).

### Installation pour un déploiement de MADIS sur un serveur

Les Docker n'ont pas été étudiés pour fonctionner en mode production.
De ce fait, et pour des problématiques de sécurités, veuillez installer MADIS sans docker.


Pour cela il vous faudra installer manuellement la stack technique.
Vous trouverez ci-dessous la stack technique utilisée et testée.
-	PHP-FPM 7.1
-	NGINX 1.10.3
-	MySQL 5.7 (ou MariaDB à partir de 10.2.3)
-	NodeJS 8
-	Composer
-	GIT
-	YARN 1
-   Wkhtmltopdf

Ainsi que des extensions PHP : *php7.1-apcu php7.1-common php-fdomdocument php7.1-xml php7.1-cli php7.1-common php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-json php7.1-mbstring php7.1-mysql php7.1-opcache php7.1-readline php7.1-bz2 php7.1-zip*.

(L'application peut néanmoins être compatible avec d'autres versions mais le support a lieu sur les versions énumérées ci-dessus)

#### Où aller télécharger ces packages ?


Installer nodeJS en suivant les indications suivantes:
- https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions 

Installer YARN en version 1+
- https://yarnpkg.com/lang/fr/docs/install/#debian-stable

Installer php-fpm 7.1

   Pour Ubuntu :
​      - https://www.rosehosting.com/blog/install-php-7-1-with-nginx-on-an-ubuntu-16-04-vps/

   Pour Debian 9 (Stretch) :
​      - https://linuxhostsupport.com/blog/how-to-install-wordpress-with-php-7-1-and-nginx-on-a-debian-9-vps/

(Si nécessaire) Installer MariaDB 10.3 :

​	Pour Debian 9 (Stretch)

  - https://computingforgeeks.com/how-to-install-mariadb-10-3-on-debian-9-debian-8/

Pour installer Wkhtmltopdf suivre la documentation suivante : [Installer wkhtmltopdf](6-installer-wkhtmltopdf.md).
