Cloner le dépot GIT de MADIS
============================

Pour cloner le projet, il vous faut tout d’abord autoriser votre serveur à accéder au dépôt GIT du projet. Pour autoriser le serveur à ne faire que cloner (et non mettre à jour) le code de MADIS, allez sur https://gitlab.adullact.net/soluris/madis/settings/repository et ajouter la clé SSH de votre serveur sans cocher « autoriser l’accès d’écriture ».

Votre serveur a maintenant accès au dépôt MADIS. Clonez le dépôt

```bash
# Supprimer le dossier de test de tout à l’heure
rm -Rf /var/www/madis

# Génération d'une clé (pour accès GIT)
# laisser les options par défaut
ssh-keygen -t rsa -b 4096
# Récupérer la clé SSH et la coller dans GitLab dans les clés de déploiements
cat ~/.ssh/id_rsa.pub

# Cloner MADIS
cd /var/www
git clone git@gitlab.adullact.net:soluris/madis.git madis

# Placez vous sur la version à utiliser (prendre la dernière disponible)
cd madis
git checkout v1.10.3
```
