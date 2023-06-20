Configurer les services
=======================

## Configurer NGINX

NGINX doit être configuré pour recevoir MADIS. La configuration devra respecter les conditions suivantes :
-	Toujours rediriger /sur /index.php si la ressource statique n’a pas su être servi
-	Utiliser « /*index.php* » lance PHP-FPM pour exécuter Symfony via son Front Controller
-	Rediriger tout autre appel en 404 

Tous les détails de la configuration sont exposés ici : 
https://symfony.com/doc/current/setup/web_server_configuration.html#nginx

Faites pointer NGINX sur le path  /var/www/madis/public. Pour tester rapidement votre configuration, ajoutez les fichiers correspondant dans le dossier « public » :
-	index.php (avec un `phpinfo();` ) par exemple
-	image.png (ou tout autre image statique)

Si vous avez suivi la documentation Symfony, vous obtiendrez les comportements suivants :
-	/ vous amènera sur la page de connexion MADIS
-	/index.php ne fonctionnera pas (la clause NGINX est en internal donc inaccessible depuis l’URL)
-	/image.png vous affichera votre image

## Configurer MySQL

Lors de l’installation de MySQL vous avez dû créer un utilisateur root pour MySQL. Vous allez maintenant créer un utilisateur pour l’application MADIS.

Connectez-vous avec ce compte root afin de pouvoir créer les accès utilisateur (évitez le copier/coller pour les caractères spéciaux).

```mysql
# Créer une BDD pour madis
CREATE DATABASE madis ;

# Créer un utilisateur avec un mot de passe que vous choisirez avec précaution
CREATE USER madis@'localhost' IDENTIFIED BY 'ThisIsAStrengthPassword111!';

# Donner les accès à la BDD à notre nouvel utilisateur
GRANT ALL PRIVILEGES ON madis.* TO 'madis'@'localhost';
```

Enfin, modifiez votre fichier `/etc/my.cnf` en y ajoutant ou remplaçant les lignes suivantes :

```
[mysqld]
sql_mode=STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```

Votre BDD est maintenant opérationnelle. Tentez de vous connecter en ligne de commande via mysql -u madis -p. Si vous réussissez à vous connecter, tout est en place.
