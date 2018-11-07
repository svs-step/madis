# CHANGELOG

## [UNRELEASED]

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
