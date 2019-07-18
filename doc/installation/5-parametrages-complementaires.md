Paramétrages complémentaires
============================

# Limite de taille des pièces jointes déposées

Dans la configuration Nginx, il est possible de définir la taille limite d'upload de fichier :
```nginx
server {
    client_max_body_size 5M;
}
```

```shell
#Recharger la configuration de nginx
service nginx reload
```

Il faut aussi modifier la limite présente dans le php.ini (/etc/php/7.1/fpm/php.ini)
```php
upload_max_filesize = 5M
```
```shell
#Recharger la configuration de php
service php7.1-fpm reload
```

Il faut ensuite modifier l'application si nécessaire (config/domain/registry/validation/proof.yaml).

## Exemple de fichier de configuration nginx

```nginx
server {
    server_name madis.soluris.fr;
    root /var/www/madis/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }
	client_max_body_size 5M;
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

     
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/madis_error.log;
    access_log /var/log/nginx/madis_access.log;
}
```
