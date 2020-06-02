Cycle de vie d'une contribution
===============================

Le but de cette page est de présenter le cycle de vie d'une contribution. Ceci dans le but de permettre aux contributeurs habituels de s'y référer (en cas d'oubli), et aux nouveaux contributeurs de comprendre les mécanismes de collaboration en travail à distance avec l'aide d'un Gitlab.

Tout ces points s'inspirent très largement des documents suivants

* [Introduction to GitLab Flow](https://docs.gitlab.com/ee/workflow/gitlab_flow.html#do-not-order-commits-with-rebase)
* [GitLab Workflow: An Overview](https://about.gitlab.com/2016/10/25/gitlab-workflow-an-overview/)

## Étapes du cycle de vie d'une contribution

Vue synthétique des étapes : 

1. Vérification qu'une demande similaire n'est pas déjà en cours
2. Création d'un ticket (*issue*)
3. Discussion
4. Si besoin, enrichissement de l'issue
5. Création "automatique" de la Demande de fusion (*merge request*)
6. Développement
7. Prévention des conflits
8. Finalisation de la Demande de fusion
9. Traitement de la Demande de fusion par les mainteneurs

## 1. Vérification qu'une demande similaire n'est pas déjà en cours

Avant toute contribution, merci de vérifier qu'une demande similaire n'est pas déjà en cours ou résolu.
Pour cela, veuillez vérifier :
- Qu'aucune mention de votre problème n'est présent dans la documentation
- Qu'un ticket ouvert ne décrit pas déjà votre demande (dans ce cas, participez à la discussion de ce dernier)

## 2. Création d'un ticket

Dans le but de travailler de manière répartie (tout le monde n'est pas dans le même bureau) et asynchrone
(tout le monde n'est pas sur le projet en même temps), il recommandé de consigner par écrit toute idée, suggestion, bug. 

Dit autrement "tout est issue".

Il est recommandé de donner à l'issue un titre concis et le plus explicite possible. 

**Recommandation MADIS** : il est proposé que chaque issue soit préfixée par le nom du module.
Par exemple une issue dans le registre des traitements aura
comme titre `[Registre des traitements] - mon-titre-explicite-et-concis`

La description de l'issue détaille et précise le propos.
Il est possible d'ajouter des images, voire des fichiers.

L'intérêt de créer au plus tôt une issue est d'afficher au reste de la communauté ses intentions et de la faire réagir.
Exemple : je souhaite me lancer dans le développement d'une fonctionnalité, je créé l'issue afférente.
Si d'autres personnes ont la même idée, une discussion peut commencer.

## 3. Discussion

La discussion se fait sous forme de commentaires de l'issue.

## 4. Enrichissement de l'issue

Pour les contributeur ayant un peu d'expérience,
il est suggéré de mettre à jour la description de l'issue au gré de la disccussion.
(On peut aussi imaginer créer d'autres issues et fermer la première au profit de celles nouvellement créées).

## 5. Création "automatique" de la Demande de fusion

Utiliser le bouton "Create merge request"

Dans les recommandations de travail collaboratif, il est proposé de créer une nouvelle branche pour chaque nouveau travail.
Par convention, la branche nouvellement créée verra son nom préfixé par l'id de l'issue associé. 

Dans la foulée, il est recommandé de créer la demande de fusion "merge request" et
de préfixer son nom par la chaîne `WIP: ` (WIP : work in progress).
Ceci permet d'afficher à la communauté le fait qu'une personne travaille sur le sujet, et éventuellement d'avoir des retours d'autres contributeurs.

Le bouton "Create merge request" permet de faire toutes ces opérations en un seul clic.

## 6. Développement

Le / les développeurs produisent leur code (commits dans la branche dédiée à l'issue, puis push vers le Gitlab)

## 7. Prévention des conflits

TODO à détailler le pourquoi du comment

Depuis la branche associée à l'issue : 

```
git pull origin master
git merge master
```

Gérer les éventuels conflits. Si conflits, corriger, commiter, pusher.

## 8. Finalisation de la Demande de fusion

Sur la page de la merge request, faire un "resolve WIP status" puis lancer la merge request

## 9. Traitement de la Demande de fusion par les mainteneurs

Le lancement de la merge request génère une notification aux mainteneurs. Ces derniers traitent la merge request.

Un nouvel espace de discussion est possible entre mainteneur et développeur.

Le mainteneur valide la merge request, le code est passé dans la branche develop.
