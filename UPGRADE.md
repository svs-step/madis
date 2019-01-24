# Informations de mises à jour

Il est possible que vous deviez réaliser des actions manuelles lors
de vos montées de version de MADIS.

De ce fait, répertoriez-vous à ce qui est écrit ci-dessous.

**_Note: Si aucune trace n'est présente dans ce fichier pour la version
que vous souhaitez installer, c'est que vous n'avez rien à faire._**


# Version v1.3 --> v1.3.1

**Ajout de l'adresse du DPD moral / par défaut**
 
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
