Configurer MADIS pour la première fois
======================================

## Paramétrer l'application MADIS

Paramétrez maintenant MADIS.

- Copier le fichier *.env.dist* en *.env* puis supprimez la partie ###> DOCKER ### 

- Remplacez les informations restantes par celles vous concernant

- Vérifiez que MADIS communique bien avec la BDD avec la commande suivante : `bin/console doctrine:schema :update --dump-sql`.
Si des lignes SQL s’affichent, vous communiquez bien avec la BDD (cette commande vous affiche le différentiel entre le schéma de la BDD et MADIS). N’en faites rien, on va créer le schéma à l’étape suivante

- Lancez la commande `./bin/deploy`  pour finaliser l’installation de MADIS

 

## Initialiser la base de données pour utiliser MADIS

### Créer le premier utilisateur pour l’application

Vous devriez pouvoir vous connecter avec l’utilisateur [admin@soluris.fr](mailto:admin@soluris.fr)
et le mot de passe M4d1Ss0rur1s. Si ce n’est pas le cas, vérifiez que vous ayez un utilisateur dans
la base de données madis.user_user. Si vous avez réussi à vous connecter, changez les informations
administrateur ainsi que le mot de passe et vous pouvez commencer à créer votre compte utilisateur
et ceux de vos futurs utilisateurs.

Le tutoriel s’arrête donc ici pour vous.

 

Si ce n’est pas le cas, suivez les étapes suivantes : 

Créez un utilisateur « administrateur » qui vous servira à créer votre compte. Cet utilisateur doit être associé à une collecivité.

Connectez-vous à votre BDD `mysql -u madis -p` puis créez une structure et un utilisateur.

 

Voici un exemple de requête correspondant à MADIS v1.0.0 (susceptible d’évoluer) :

Vous pouvez les utiliser telles qu’elles sont écrites pour ensuite modifier les données dans l’application pour une saisie plus aisée (à l’exception du mot de passe pour l’administrateur que vous pouvez générer directement avec des générateurs en ligne ou par ligne de commande PHP ex : 
 `php -r 'echo (password_hash("pass",1));'`



```mysql
# Créer une structure
INSERT INTO `user_collectivity` (`id`, `name`, `short_name`, `type`, `siren`, `active`, `created_at`, `updated_at`, `address_line_one`, `address_line_two`, `address_city`, `address_zip_code`, `address_insee`, `legal_manager_civility`, `legal_manager_first_name`, `legal_manager_last_name`, `legal_manager_job`, `legal_manager_mail`, `legal_manager_phone_number`, `referent_civility`, `referent_first_name`, `referent_last_name`, `referent_job`, `referent_mail`, `referent_phone_number`, `dpo_civility`, `dpo_first_name`, `dpo_last_name`, `dpo_job`, `dpo_mail`, `dpo_phone_number`, `it_manager_civility`, `it_manager_first_name`, `it_manager_last_name`, `it_manager_job`, `it_manager_mail`, `it_manager_phone_number`, `website`, `different_dpo`, `different_it_manager`) VALUES
('3d62101b-bf48-11e8-ac9b-0242ac180002', 'Administration', 'Admin', 'departmental_union', 111111111, 1, NOW(), NOW(), 'A DEFINIR', NULL, 'A DEFINIR', 11111, '0000', 'm', 'A DEFINIR', 'A DEFINIR', 'A DEFINIR', 'm.edlich@soluris.fr', '0000000000', 'm', 'A DEFINIR', 'A DEFINIR', 'A DEFINIR', 'm.edlich@soluris.fr', '0000000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

# Créer un utilisateur
INSERT INTO `user_user` (`id`, `collectivity_id`, `first_name`, `last_name`, `email`, `password`, `roles`, `enabled`, `forget_password_token`) VALUES
(UUID(), '3d62101b-bf48-11e8-ac9b-0242ac180002', 'Admin', 'Admin', 'm.edlich@soluris.fr', '$2y$10$xofkbNT.c8U3AK6mpOdAEOU9LATv/X01nUJqag88zNF4pUweN7ddm', '[\"ROLE_ADMIN\"]', 1, NULL);

# Créer les catégories de base
INSERT INTO `registry_treatment_data_category` VALUES ('bank','Information bancaire',7,0),('bank-situation','Situation bancaire',8,0),('birth','Date, lieu de naissance',2,0),('caf','Numéro de CAF',17,0),('connexion','Connexion',15,0),('earning','Revenus',12,0),('email','Emails',13,0),('family-situation','Situation familiale',3,0),('filiation','Filiation',4,0),('geo','Géolocalisation',16,0),('health','Santé',18,1),('heritage','Patrimoine',9,0),('identity','Pièces d’identité',20,0),('ip','Adresse IP',14,0),('name','Nom, Prénom',1,0),('phone','Coordonnées téléphoniques',6,0),('picture','Photos-vidéos',21,0),('postal','Coordonnées postales',5,0),('professional-situation','Situation professionnelle',11,0),('public-religious-opinion','Opinion politique ou religieuse',23,1),('racial-ethnic-opinion','Origine raciale ou ethnique',24,1),('sex-life','Vie sexuelle',25,1),('social-security-number','Numéro de Sécurité Sociale',19,1),('syndicate','Appartenance Syndicale',22,1),('tax-situation','Situation fiscale',10,0);
```

Vous pouvez maintenant vous connecter à MADIS avec cet utilisateur et commencer la configuration.

Pour installer le modèle AIPD par défaut, lancez la commande suivante : 
```bash
bin/console aipd:model:import fixtures/default/aipd/Modele_par_defaut_AIPD.xml
```

Pour installer les réferentiels de maturité par défaut, lancez la commande suivante:
```bash
bin/console maturity:referentiel:import fixtures/default/maturity/
```
