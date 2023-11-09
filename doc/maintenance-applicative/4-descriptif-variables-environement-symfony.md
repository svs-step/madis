Descriptif des variables d'environnement Symfony
================================================

Vous avez une certaine flexibilité dans la configuration de votre application MADIS.
Pour cela, vous pouvez modifier les variables d'environnement,
généralement trouvable dans le fichier `.env` de votre application.

Vous trouverez ci-dessous une liste exhaustive des variables de ce fichier ainsi que des explications de ses dernières

```bash
# APP_MAIL_RECEIVER_DEV : redirige tous les mails envoyés sur un seul destinataire (ça écrase la liste des destinataires pour celle-ci)
APP_MAIL_RECEIVER_DEV=~

# APP_IMPERSONATE_CREATOR_IS_ADMIN : défini si l'admin doit être identifié comme le créateur d'un élément lors de subrogation. Si à 0, l'utiliasteur est utilisé.
APP_IMPERSONATE_CREATOR_IS_ADMIN=0

DOCTRINE
========
# URL de votre base de données
# cf. doc pour le format à utiliser http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
DATABASE_URL=mysql://user:user_pass@db:3306/madis

APPLICATIF MADIS
================
# Ces variables d'environnement existent, mais vous ne devriez pas y toucher sans connaissance de cause
APP_ENV=dev
APP_MAIL_RECEIVER_DEV=~

CONFIGURATION OBLIGATOIRE MADIS
===============================

# ATTENTION : Modifiez impérativement cette valeur lors de votre première installation
# Chaine de caractère qui est utilisée dans la sécurité de votre application
APP_SECRET=a98f56b9ea67f189df8ed6a39c548503

CONFIGURATION MADIS
===================
# Nom de l'application
APP_APPLICATION_NAME="Madis"

# Temps d'inactivité pour un utilisateur (en secondes).
# Une fois le temps écoulé, l'utilisateur est automatiquement déconnecté, même si le cookie est encore valide.
APP_COOKIE_IDLE_TIMEOUT=5400

# Durée de vie du cookie (en secondes)
APP_COOKIE_LIFETIME=14400

# Nom du référent RGPD par défaut
APP_DEFAULT_REFERENT="Référent RGPD"

# Adresse du DPD par défaut.
# Si vous êtes une structure mutualisante assurant le rôle de DPD, saisissez votre adresse
APP_DPO_ADDRESS_CITY="Saintes"
APP_DPO_ADDRESS_STREET="2 rue des Rochers"
APP_DPO_ADDRESS_ZIP_CODE="17100"
APP_DPO_CIVILITY=m
APP_DPO_FIRST_NAME=Michaël
APP_DPO_LAST_NAME=Edlich
APP_DPO_COMPANY=Soluris
APP_DPO_JOB="Chargé de mission Solutions Documentaires Electroniques"
APP_DPO_MAIL=m.edlich@soluris.fr
APP_DPO_PHONE_NUMBER=0546923905

# Configuration du FOOTER du site (URL et nom de la structure mutualisante)
APP_FOOTER_PROVIDER_NAME=SOLURIS
APP_FOOTER_PROVIDER_URL="https://example.fr"

# Dashboard utilisateur paramétrage du nombre de données affiché dans le tableau de journalisation des logs
APP_USER_DASHBOARD_JOURNALISATION_LIMIT=15

# Nombre d'entrées affichées par page dans les tableaux des vues listes
APP_DATATABLE_DEFAULT_PAGE_LENGTH=15

# Configuration des images MADIS
# Favicon
APP_IMAGE_FAVICON_PATH="favicon.ico"
# Lien utilisé lors du clic sur l'image dans la sidebar
APP_IMAGE_SIDEBAR_BOTTOM_TARGET_URL="htpps//example.fr"
# Lien vers l'image utilisée dans la sidebar lorsqu'elle est étendue
# (chemin à configurer à partir du dossier `public` présent à la racine du projet)
APP_IMAGE_SIDEBAR_BOTTOM_PATH="images/soluris-logo-white.png"
# Lien vers l'image utilisée dans la sidebar lorsqu'elle est réduite
# (chemin à configurer à partir du dossier `public` présent à la racine du projet)
APP_IMAGE_SIDEBAR_REDUCED_PATH="images/icon-32x32.png"

# Défini si l'admin doit être identifié comme le créateur d'un élément lors de subrogation.
# Si à 0, ce sera le nom de l'utilisateur subrogé qui apparaitra
# Si à 1, ce sera le nom de l'administrateur qui subroge qui apparaitra
APP_IMPERSONATE_CREATOR_IS_ADMIN=0

# Adresse email qui apparaitra dans les mails envoyés par MADIS
APP_MAIL_SENDER_EMAIL=ne-pas-repondre@example.fr
# Nom de l'expéditeur qui apparaitra dans les mails envoyés par MADIS
APP_MAIL_SENDER_NAME="Madis"

# Date relative permettant de supprimer, lors de la connexion de l'admin, les logs antérieurs à cette date.
Doit respecter le format de date relative de PHP (ligne "unit") : https://www.php.net/manual/fr/datetime.formats.relative.php
APP_LOG_JOURNAL_DURATION=6months

# Valeurs utilisées pour les tooltips sur les réponses dans les formulaires de création/édition
d'évaluation de conformité d'organisation
###> CONFORMITE ORGANISATION TOOLTIP ###
TOOLTIP_CONFORMITE_ORGANISATION_INEXISTANTE="Rien n'est fait"
TOOLTIP_CONFORMITE_ORGANISATION_TRES_ELOIGNEE="La ou les pratique(s) sont très éloignées de la définition (Pratique <20%)."
TOOLTIP_CONFORMITE_ORGANISATION_PARTIELLE="La ou les pratique(s) sont partielles (20%<Pratique>80%) au regard de la définition.<br/>Elles ne sont pas documentées."
TOOLTIP_CONFORMITE_ORGANISATION_QUASI_CONFORME="La ou les pratiques sont conformes ou quasiment conforme à la définition (80%<Pratique>100%)."
TOOLTIP_CONFORMITE_ORGANISATION_MESURABLE="La ou les pratiques sont conforme à la définition.<br/>Elles sont documentées et contrôlables dans le cas d'un audit."
TOOLTIP_CONFORMITE_ORGANISATION_REVISEE="La ou les pratiques sont coordonnées et conforme à la définition.<br/>Des évaluations sont réalisées.<br/>Des améliorations sont systématiquement apportées à partir de l'analyse des évaluations effectuées. "


# URL vers le mailer que doit utiliser MADIS
###> symfony/swiftmailer-bundle ###
# Pour GMAIL, utiliser: "gmail://username:password@localhost"
# Pour un SMTP générique, utiliser: "smtp://localhost:25?encryption=&auth_mode="
# Pour désactiver les mails, utiliser: "null://localhost"
MAILER_URL=gmail://username:password@localhost

# Configuration de KnpSnappyBundle : Emplacement de wkhtmltopdf
###> knplabs/knp-snappy-bundle ###
WKHTMLTOPDF_PATH=/usr/local/bin/wkhtmltopdf
WKHTMLTOIMAGE_PATH=/usr/local/bin/wkhtmltoimage
###< knplabs/knp-snappy-bundle ###
```
