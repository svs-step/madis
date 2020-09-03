Les bonnes pratiques de développement
=====================================

Des bonnes pratiques sont à adopter pour le développement :

-	**Git Flow** : Le dépôt GIT est maintenu en suivant le Git flow. Appliquez-le durant vos développements.
-	**GitHook** : Utilisez le git-hook mis à disposition (nécessite Docker) qui va lancer un php-cs-fixer afin que le code respecte des conventions de formatage.
-	**Tests** : A chaque développement de fonctionnalité, pensez à créer les tests associés (PHPUnit, Behat).
-	**Migration de BDD** : Si des modifications de base de données sont à effectuer, générez les doctrines migrations associées.
-	**Merge requests** : Si vous travaillez à plusieurs développeurs, utilisez des Merge Requests afin de faire valider votre code par une tierce personne.
-	**Ubiquitous language** : Le projet a été développé initialement sans utiliser l'ubiquitous language, menant à une compréhension ardue des modèles/propriétés et de mauvaises traductions. Il est désormais utilisé afin de faciliter la compréhension et d'éviter ces problèmes
