Qualité du code
===============

## Générer un rapport

Pour cela il vous suffit d'aller sur le [projet Gitlab](https://gitlab.com/soluris/madis/pipelines)
et d'y lancer les jobs de qualité et génération de rapports.

Vous pourrez alors lancer la dernière étape qui est "Quality-assurance-report".
Cela va déployer une page Gitlab sur laquelle vous pourrez visualiser les indicateurs de qualité du projet :
- PHPMD (Mess Detector)
- PHPMetrics
- PHPUnit code coverage

Lien de la Gitlab Page : [https://soluris.gitlab.io/madis/](https://soluris.gitlab.io/madis/)

## Règles de qualité

Certaines règles de qualités ont parallèlement été établies :
- PHPStan
- PHP-CS-FIXER
- lint de fichiers YAML et PHP
- check de sécurité Symfony
