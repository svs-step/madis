CHANGELOG
=========
## [2.0.1] - 2022-12-13
### Changement
- Ajout du parametre APP_URL dans le .env pour afficher les liens corrects dans les emails de notifications

## [2.0] - 2022-12-13
### Changement
- Mise à jour vers Symfony 5.4 et PHP 8.1
- Mise à jour vers nodejs 18
- Ajout du système de notifications
- Ajout de la connexion SSO

## [1.10] - 2022-04-15
### Changement
- Ajout du module Espace Documentaire

## [1.9] - 2022-03-25
### Changement
- Incorporation de tout le module AIPD : Modèle d'analyse, Mesure protection et Analyse d'impact

## [1.8.14] - 2022-03-25
### Changement
- Correction d'un bug sur le champs service dans la conformité des traitements
- Correction d'un bug permettant d'afficher les boutons d'actions pour un lecteur
- Correction d'un bug où des propriétés des actions de protections n'étaient pas dupliquées
- Correction d'un bug déconnectant l'utilisateur lors d'une tentative de modification de son propre profil
- Correction d'un bug empêchant de se connecter en tant que user avec des services supprimés


## [1.8.10] - 2022-03-14
### Changement
- Modification des services pour afficher tous les services aux utilisateurs n'appartenant à aucun

## [1.8.8] - 2022-03-10
### Changement
- Correction d'un bug empêchant l'administrateur de se connecter en un utilisateur d'une collectivités comportant des services supprimés

## [1.8.7] - 2022-03-04
### Changement
- Correction d'un bug affichant tous les services de la collectivité lors de la création d'éléments sans prise en compte des services de l'utilisateur
- Correction d'un bug empêchant les utilisateurs sans service de supprimer les services des éléments
- Correction d'une erreur 500 lors de la duplication d'éléments
- Correction d'un bug empêchant d'affecter des services lors de la création d'un utilisateur si la collectivité n'est pas celle par défaut du formulaire

## [1.8.6] - 2022-02-17
### Changement
- Correction d'un bug entraînant la multiplication du nombre d'éléments dupliqués
- Correction d'un bug affichant toujours les services comme inactifs lors de la modification d'une collectivité, empêchant de les désactiver 

## [1.8.5] - 2022-02-15
### Changement
- Correction d'un bug affichant les services dans les éléments d'une collectivité n'ayant pas de services

## [1.8.4] - 2022-02-14
### Changement
- Correction d'une erreur entraînant la multiplication des éléments dupliqués
- Correction d'un bug affichant tous les services de toutes les collectivités sur certains éléments
- Ajout de la possibilité pour un utilisateur sans service de modifier tous les éléments de sa collectivité quelque soit le service
- Renommage "publique" en "public"

## [1.8.3] - 2022-02-02
### Changement
- Correction d'une erreur empêchant l'affichage de la liste des traitements

## [1.8.2] - 2022-01-27
### Changement
- Correction d'un bug rendant incorrect le nombre d'élément affiché dans le texte des tableaux #391
- Correction d'un bug permettant de rediriger vers un lien externe lorsqu'une balise était ajouté dans le nom d'un élément #392

## [1.8.1] - 2022-01-17
### Changement
- Corrections de diverses erreur mineures
- Registre public : suppression des liens

## [1.8] - 2022-01-05
### Changement
- Ajout des webservices
- Ajout du registre grand public
- Nombreuses améliorations ergonomiques diverses
- Nombreuses corrections et améliorations mineures

## [1.7.13] - 2021-12-22
### Changement
- Correction d'un bug concernant le calcul de l'indice de maturité #337
- Augmentation de la limite du nombre de caractère de la justification de refus des demandes #338
- Inversion des couleurs du graphique des sous-traitants #297
- Mise à jour de librairies suite à des vulnérabilités découvertes ou des changements de propriété (dont #380)

## [1.7.12] - 2021-01-05
### Changement
- Correction d'un bug empêchant la suppression d'une action de protection ou d'un sous-traitant pour lesquels il existe un duplicat

## [1.7.11] - 2020-10-16
### Changement
- Correctif bug utilisateur archivé visible dans la liste "Ma collectivité" #295
- Dans le bilan chapitre 4.1 le nombre de domaine est désormais calculé
- Correctif sur la liste des plans d'actions qui affiche bien les actions de protections non planifiées et non appliquées

## [1.7.10] - 2020-10-12
### Changement
- Correctif des cases à cocher (Mesures de sécurité et confidentialité) lors de la modification d'un traitement.

## [1.7.9] - 2020-10-06
### Changement
- Ajout des liens pour accéder à la vue de consultation depuis la vue la liste d'un sous-traitant et d'une violation 


## [1.7.8] - 2020-09-30
### Changement
- Correction sur le dashboard utilisateur et correction des placeholders sur la liste des collectivités. 

## [1.7.7] - 2020-09-21
### Ajout
- Ajout du Lot 4
- Ajout d'un export de CSV des actions de protections, sous-traitants sur le dashboard
- Ajout du traitement côté serveur pour les sous-traitants, utilisateurs, demandes, collectivités, preuves et violations
- Ajout du référent multi-collectivités

## [1.7.6] - 2020-09-03
### Ajout
- Personnaliser les listes et les trier (Traitements, Actions de protection et Utilisateurs) #286
- Journalisation des actions, log, traçabilité #286
- Imprimer (PDF) une fiche (traitements, sous-traitants, ...) #286
### Changement
- Divers changements sur le dashboard des collectivités #285

## [1.7.5] - 2020-07-29
### Changement
- Divers changements suite à la mise en production du lot 2

## [1.7.4] - 2020-07-28
### Ajout
- [MODULE] Conformité des traitements, #279
- [MODULE] Conformité de l'organisation, #280

## [1.7.3] - 2020-10-06
### Ajout
- [MA COLLECTIVITE] Comité IL afficher dans le bilan, #258
- [BILAN] Ajout de paragraphes, #257
- [DASHBOARD] Cartographie, stats, export csv traitement, export csv collectivité, #255
- [DASHBOARD] Nombre de collectivités (par type)(par DPO), #225
### Changement
- [SOUS-TRAITANT] Fiche sous-traitants - traitements effectués par sous-traitant, #260
- [TRAITEMENTS] Fiche Traitements - Précision des personnes concernées, #259

## [1.6.3] - 2019-12-03
### Changement
- [TECHNIQUE] Mise à jour de PHP CS Fixer en v2.16
- [TECHNIQUE] Mise à jour de Symfony (de la version 4.2.8 à la version 4.3.8)
### Fix
- [TRAITEMENT] Il est désormais possible de supprimer un traitement ayant servi de template à la duplication, #221

## [1.6.2] - 2019-10-07
### Ajout
- [TECHNIQUE] Mise en place d'outils de qualité de code (PHPMD, PHPStan, csFixer, ...), #188
### Changement
- [TRAITEMENT] Word / Modification du "Personne référentes" en "Personnes concernées", #208

## [1.6.1] - 2019-09-23
### Fix
- [ADMINISTRATION] Subrogation / Créer une donnée avec l'admin en tant que créateur ne génère plus de 500, #207 

## [1.6.0] - 2019-09-20
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
