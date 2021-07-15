Installation de l'API
============================

Documentation Api platform :
- https://api-platform.com/ 

Documentation LexikJWTAuthentication :
- https://github.com/lexik/LexikJWTAuthenticationBundle


Génération des clés publiques et privées pour le JWT

```shell
./docker-service jwtKeys
```

ou 

```shell
php bin/console lexik:jwt:generate-keypair
```


Documentation de l'API disponible sur /api/docs

Pour utiliser la documentation, la route /login permet de récupérer son Bearer token afin de s'authentifier et de pouvoir accéder aux autres requêtes.

1. Lancez la requete /login (Try it out)

2. Remplissez votre username et votre password

3. Execute

4. Copiez le token

5. Cliquez sur "Authentification" puis ecrivez "Bearer collezVotreToken"

6. Login

7. Vous êtes authentifié. Vous pouvez ensuite utiliser les autres requêtes.