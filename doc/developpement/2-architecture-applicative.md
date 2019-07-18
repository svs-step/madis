Architecture applicative
========================

### Organisation des dossiers

L’architecture de base d’un projet Symfony Flex est la suivante : 

![Image 005](Image 005.jpg)

L’architecture est standard mais voici un petit rappel :
- *.env* et *.env.dist* sont les paramètres de l’application

- *composer.json* et *composer.lock* sont les gestionnaires de versions de paquets

- *config/bundles.php* contient la configuration des librairies

- *config/packages* contient la définition et la configuration des librairies utilisées (des paramétrages spécifiques peuvent être ajoutés selon l’environnement, il s’agit des dossiers *config/dev/\** et config/test/*).

- *config/routes.yaml* contient la définition des routes de l’application

- *config/services.yaml* contient la définition des services 

- *public/index.php* est le fichier qui permet de lancer l’application (le serveur web pointe sur ce fichier)

- *src* contient le code métier de l’application (Controller par exemple)

- vendor contient les librairies (généré par composer)

### Concept de développement DDD & Hexagonal

![Image 006](Image 006.jpg)



Vous vous y perdez sans doute dans cette arborescence ? Vous pouvez tout d’abord commencer par vous renseigner sur :

- L’architecture hexagonale : <http://blog.xebia.fr/2016/03/16/perennisez-votre-metier-avec-larchitecture-hexagonale/>

- Le Domain Driven Development : <https://blog.xebia.fr/2009/01/28/ddd-la-conception-qui-lie-le-fonctionnel-et-le-code/>

 Le concept de développement utilisé ici est un mélange de ces deux principes.

### Utilisation de l’architecture hexagonale

L’architecture hexagonale est utilisée ici pour s’affranchir de la contrainte technique MySQL. Il sera ainsi très simple de connecter une autre source de donnée. On pourra facilement brancher une API, une base de données Postgres par exemple, si besoin.

C’est pour cela que vous possédez deux dossiers : Domain et Infrastructure.

-	Domain va contenir les classes PHP utilisées dans l’application. Elles sont décorrélées des implémentations de Base de données et va mettre à disposition des Interfaces PHP qu’il faudra implémenter pour récupérer les données.

-	Infrastructure va contenir les implémentations aux interfaces du Domain. On y trouvera ici toute l’implémentation technique pour chaque source de données que l’on souhaitera brancher sur l’application. Actuellement, l’implémentation utilisée est ORM (soit le dossier *Infrastructure/ORM*). On va y trouver notamment le mapping Doctrine pour la création de la base de données et l’implémentation des *Repository* qui vont être utilisés dans l’application.

La configuration Symfony de cette architecture se déroule dans le dossier *config/domain* et *config/infrastructure* qui va respecter la même architecture précédemment expliqué avec ces mêmes dossiers.
