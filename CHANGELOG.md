# CHANGELOG

## [UNRELEASED]
### Ajout
- [USER] Pouvoir modifier son mot de passe dans son profil, #135
- [CHARTE] Possibilité de cliquer sur un icone "oeil" dans les champs mot de passe pour le voir en clair, #126
- [TRAITEMENT] Ajout d'un champ "Observations", #121
- [TRAITEMENT] Ajout d'un champ "Origine des données", #117
- [LOGO] Pouvoir configurer les logos/le fournisseur de service et l'URL associé, #99
### Changement
- [CONNEXION] Passage du temps de connexion de 4h à 1h30, #125
- [USER] Ré-agencement des blocs du formulaire "Utilisateurs" pour les admins, #135
### Fix
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
