CHANGELOG
=========

## [UNRELEASED]
### Ajout
- [DUPLICATION] Un administrateur peut maintenant dupliquer des traitements / sous-traitants / actions de protections d'une collectivité vers des autres, #187
- [PREUVE] Possibilité de lier une preuve à une ou plusieurs données, #186
- [ADMINISTRATION] Subrogation d'un utilisateur de l'application, #107
- [GLOBAL] Ajout d'un DatePicker dans les formulaires pour les champs date, #37
### Changement
- [USER] La suppression (non fonctionnelle) a été remplacée par un archivage, #199
### Fix
- [COLLECTIVITE] Il est maintenant possible de supprimer le site web d'une collectivité, #202 
- [PREUVE] Un administrateur peut maintenant télécharger les documents qui ne sont pas de sa collectivité, #197
- [GLOBAL] Passage des dates au format FR (DD/MM/YYYY) dans les listes, #37 #205

## [1.5.2] - 2019-07-27
### Fix
- [TABLEAU DE BORD] Le type de demande de personne concerné "Autre" ne fait plus planter le Dashboard, #195

## [1.5.1] - 2019-07-18
### Fix
- [DOCUMENTATION] Ajout de la table des matières dans le fichier README.md

## [1.5.0] - 2019-07-18
### Ajout
- [DOCUMENTATION] Création d'un dossier `doc` pour la documentation du projet (le fichier `README.md` en est le point d'entré), #184
- [PAGE CREDIT] Mise à jour du contenu et suppression de la partie "Hébergement", #179
- [PREUVE] Possibilité de supprimer une preuve, #178
- [TRAITEMENT] Ajout du champ "Personnes habilitées" dans le bloc "Mesures de sécurité", #177
- [TRAITEMENT] Ajout de la base légale "Intérêt légitime", #176
- [DEMANDE] Ajout de l'objet de demande "Autre", #130
### Changement
- [GLOBAL] Changement des entêtes de fichiers PHP pour mentionner la license AGPLv3, #181
- [TRAITEMENT] Renommage de la section "Mesures de sécurité" en "Mesures de sécurité et confidentialité", #177
- [DEMANDE] Le champ "Motif" est devenu facultatif, #167
### Fix
- [DEMANDE] Le champ "Réponse" ne retourne plus d'erreur s'il dépasse 255 caractères, #193
- [AUTHENTIFICATION] Je suis déconnecté si je suis resté 1h30 inactif, #185
- [DEMANDE] Afficher la personne concernée qui n'a pas de civilité est de nouveau fonctionnel, #173
- [MATURITE] Les questions sont dorénavant odonnées dans l'ordre alphabétique, #170
- [MATURITE] Le score de l'indice de maturité n'était pas calculé en cas d'édition, #169

## [1.4.3] - 2019-05-17
### Fix
- [BILAN] Correction d'une faute lexicale dans la partie 3.2, #162
- [WORD] Changement du Content-Type pour permettre le téléchargement sur iOS, #161
- [BILAN] Correction d'une faute d'orthographe dans la partie 3.3, #157

## [1.4.2] - 2019-05-06
### Changement
- [TECH] Mise à jour Symfony 4.2.2 à 4.2.8 + MAJ des vulnérabilités, #153
### Fix
- [CONNEXION] Modification de la durée de session et du temps d'invalidation de la session selon l'inactivité, #152 

## [1.4.1] - 2019-04-03
### Fix
- [USER] Suppression du bouton "Retour" dans l'onglet "Mon compte" car il était inutile et pointait vers la liste des sous-traitants

## [1.4.0] - 2019-04-03
### Ajout
- [USER] Pouvoir modifier son mot de passe dans son profil, #135
- [CHARTE] Possibilité de cliquer sur un icone "oeil" dans les champs mot de passe pour le voir en clair, #126
- [TRAITEMENT] Ajout d'un champ "Observations", #121
- [TRAITEMENT] Ajout d'un champ "Origine des données", #117
- [LOGO] Pouvoir configurer les logos/le fournisseur de service et l'URL associé, #99
### Changement
- [TECH] Mise à jour Symfony 4.2.2 en 4.2.4 + autres packages (dont vulnérabilité Twig), #148
- [USER] Ré-agencement des blocs du formulaire "Utilisateurs" pour les admins, #135
- [CONNEXION] Passage du temps de connexion de 4h à 1h30, #125
- [TRAITEMENT] Passage en BDD de la liste des catégories de données (table `registry_treatment_data_category`), #105
- [TRAITEMENT] Remplacement de la catégorie de données "Etat civil" par "Nom, prénom", "Date et lieu de naissance", "Situation pro", #105
### Fix
- [TRAITEMENT] Le champ de formulaire "Délai de conservation" n'était pas bien aligné, #149
- [USER] Lors de la création d'un utilisateur, la saisie de son mot de passe n'était pas prise en compte, #147 
- [TRAITEMENT] Le champ "Autre délai" ne s'affichait pas dans la visualisation d'un traitement, #144
- [TRAITEMENT] Le champ "Délai de conservation" n'étais pas traduit sur le word (on pouvait lire "month" par exemple), #144
- [USER] Modifier uniquement un mot de passe ne fonctionnait pas, #139
- [GLOBAL] La sidebar se décalait lorsque nous allions sur l'onglet "Ma collectivité", #139
- [USER] Le lecteur ne pouvait pas accéder aux infos de sa collectivité et son profil, #139
- [VIOLATION] Erreur d'affichage lors de la visualisation d'une violation qui n'a pas de champ notification renseigné, #139

## [1.3.1] - 2019-01-31
### Changement
- [TECH] Mise à jour Symfony 4.2.1 à 4.2.2, #133
### Fix
- [BILAN] Le DPO moral par défaut est bien chargé au lieu des coordonnés SOLURIS, #132

## [1.3.0] - 2018-12-12
### Changement
- [TECH] Mise à jour Symfony 3.4 à 4.2.1, #129
### Fix
- [BILAN] Le graphique de l'indice de maturité est maintenant bien ordonné, #128
- [GLOBAL] Les générations WORD acceptent maintenant les caractères spéciaux, #127

## [1.2.2] - 2018-11-16
### Fix
- [GLOBAL] Le code postal commencant par 0 ne fonctionnait pas pour les adresses du registre, #119

## [1.2.0] - 2018-11-07
### Changement
- [MATURITE] Les catégories sont ordonnées lors de la visualisation et la génération Word, #109 
### Fix
- [GLOBAL] Le numéro de téléphone respecte maintenant les normes 0[1-9]XXXXXXXX, #119
- [GLOBAL] Le code postal peut maintenant commencer par 0 (07100 ne fonctionnait pas par exemple), #119
- [UTILISATEUR] Le mot de passe s'encode maintenant correctement lors de la création/édition, #113 

## [1.1.0] - 2018-09-20
### Ajout
- [DOC] Ajout du README, #93
- [GLOBAL] Style - Ajout du favicon & logo Soluris, #41
### Changement
- [VIOLATION] Création - La date de la violation est par défaut la date du jour, #79
- [BILAN] Bilan global - Ajout du DPD dans le bilan s'il est différent de celui par défaut, #72
### Fix
- [BILAN] La génération LibreOffice n'affichait pas le document correctement, #90 #89 #88 #87
- [GLOBAL] Fix de typo, #92 #84 #80 #46
- [TABLEAU DE BORD] Ajout de la couleur pour le donuts des statuts des demandes (le statut "Incomplet" n'avait pas de couleur)
- [TABLEAU DE BORD] Les traitements inactifs ne sont plus comptabilisés dans les stats "Mesures de sécurité", #82
- [GLOBAL] Fil d'ariane - Les URLs qui poitaient vers la liste des collectivités n'en sont plus, #62

## [1.0.0] - 2018-08-29
### Ajout
- Release initiale
