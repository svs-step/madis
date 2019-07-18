Réaliser une montée de version
==============================

## Mettre à jour votre dépôt de code 

Pour cela, vous allez devoir mettre à jour votre dépôt de code :  

```shell
# Faire un dump de la base de données actuelle,
# afin de prévenir d'éventuels problèmes lors de la mise à jour
mysqldump -u madis -pmonMotDePass madis > madis_bdd_DATE.sql

# Se placer dans le dossier de Madis
cd /var/www/madis

# Prendre connaissance des modifications GIT
git fetch -p

# Aller sur la version de votre choix (ici 1.2.0)
git checkout v1.2.0

# Mettre à jour
./bin/deploy
```

## Suivre les éventuels manipulation à effectuer

Avec certaines versions, vous aurez peut-être des actions à effectuer
pour faire fonctionner la nouvelle version de MADIS correctement.

Pour cela, prenez le réflexe après (ou avant) chaque montée de version
de prendre connaissance du fichier [UPDATE.md](../../UPGRADE.md).

## [AVANCE] Rétrograder de version MADIS

**Attention : Avant toute utilisation de ce qui suit, assurez-vous d'avoir effectué un dump de votre base de données,
voire de contacter une personne du Support MADIS pour vous aiguiller.
Toute intervention non maitrisée peut amener à de la perte de données.**

Vous pourrez être confronté au besoin de rétrograder MADIS à une version antérieure.
Cependant, il peut y avoir eu des modifications de votre Base de données durant la montée de versions.

### Cas 1 : Il n'y a pas eu de migration de données

Il suffit uniquement de remettre le code applicatif souhaité. 
```shell
# Faire un dump de la base de données actuelle,
# afin de prévenir d'éventuels problèmes lors de la mise à jour
mysqldump -u madis -pmonMotDePass madis > madis_bdd_DATE.sql

# Se placer dans le dossier de Madis
cd /var/www/madis

# Je suis en v1.3.0 et je souhaite revenir en v1.2.0. 
# De ce fait, je retourne sur le code de la v1.2.0
git checkout v1.2.0

# Je relance la mise à jour, qui va me ré-installer l'ancien code
./bin/deploy
```

### Cas 2 : Des données ont migrées, "sans perte"

Cela signifie que durant la montée de version MADIS, vos données ont été
modifiée et/ou déplacées. Dans ce cas précis, les scripts MADIS permettent
rejouer les scénarios inverse.
 
```shell
# Faire un dump de la base de données actuelle,
# afin de prévenir d'éventuels problèmes lors de la mise à jour
mysqldump -u madis -pmonMotDePass madis > madis_bdd_DATE.sql

# Se placer dans le dossier de Madis
cd /var/www/madis

# Je suis en v1.3.0 et je souhaite revenir en v1.2.0. 
# Je commence par regarder les migrations que je possède
ls src/Application/Migrations

# Je dois avoir la liste de toutes les migrations BDD qui ont été créées depuis le lancement de MADIS.
# Il me faut donc isoler les migrations associées aux versions que je veux downgrader.
# Par exemple j'ai les versions suivantes (formaté Années-Mois-Jours-Heures-Minutes-Secondes) : 
# Version20180101134957.php -- v1.3.0
# Version20171230090807.php -- v1.3.0
# Version20161230195842.php -- v1.2.0
# Version20160513152408.php -- v1.1.0

# Je vais donc lancer les scripts inverses dans l'ordre décroissant de création
# pour migrer les données à leur état "initial"
bin/console doctrine:migration:execute --down 20180101134957
bin/console doctrine:migration:execute --down 20171230090807

# Ainsi ma BDD vient d'être remise à son état de v1.2.0.
# Je peux donc relancer la mise à jour, qui va me ré-installer l'ancien code applicatif
./bin/deploy
```

### Cas 3 : Des données ont migrées, "avec perte"

Cela signifie que des données ont été migrées voire supprimées de votre BDD durant la montée de version MADIS.
Vous ne pourrez donc récupérer toutes vos données si vous revenez à une version antérieure de MADIS.

Une alternative est de croiser les données actuelles avec les données de vos anciens dump de BDD
mais cette opération reste très laborieuse...

Sinon les étapes sont les mêmes que pour le cas 2. Veillez simplement à faire vos modifications manuelles
juste après avoir downgradé les migrations de Doctrine.
