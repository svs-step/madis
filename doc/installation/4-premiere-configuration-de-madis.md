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

Connectez-vous à votre BDD `mysql -u madis -p` puis créez une collectivité et un utilisateur.

 

Voici un exemple de requête correspondant à MADIS v1.0.0 (susceptible d’évoluer) :

Vous pouvez les utiliser telles qu’elles sont écrites pour ensuite modifier les données dans l’application pour une saisie plus aisée (à l’exception du mot de passe pour l’administrateur que vous pouvez générer directement avec des générateurs en ligne ou par ligne de commande PHP ex : 
 `php -r 'echo (password_hash("pass",1));'`



```mysql
# Créer une collectivité
INSERT INTO `user_collectivity` (`id`, `name`, `short_name`, `type`, `siren`, `active`, `created_at`, `updated_at`, `address_line_one`, `address_line_two`, `address_city`, `address_zip_code`, `address_insee`, `legal_manager_civility`, `legal_manager_first_name`, `legal_manager_last_name`, `legal_manager_job`, `legal_manager_mail`, `legal_manager_phone_number`, `referent_civility`, `referent_first_name`, `referent_last_name`, `referent_job`, `referent_mail`, `referent_phone_number`, `dpo_civility`, `dpo_first_name`, `dpo_last_name`, `dpo_job`, `dpo_mail`, `dpo_phone_number`, `it_manager_civility`, `it_manager_first_name`, `it_manager_last_name`, `it_manager_job`, `it_manager_mail`, `it_manager_phone_number`, `website`, `different_dpo`, `different_it_manager`) VALUES
('3d62101b-bf48-11e8-ac9b-0242ac180002', 'Administration', 'Admin', 'departmental_union', 111111111, 1, NOW(), NOW(), 'A DEFINIR', NULL, 'A DEFINIR', 11111, '0000', 'm', 'A DEFINIR', 'A DEFINIR', 'A DEFINIR', 'm.edlich@soluris.fr', '0000000000', 'm', 'A DEFINIR', 'A DEFINIR', 'A DEFINIR', 'm.edlich@soluris.fr', '0000000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

# Créer un utilisateur
INSERT INTO `user_user` (`id`, `collectivity_id`, `first_name`, `last_name`, `email`, `password`, `roles`, `enabled`, `forget_password_token`) VALUES
(UUID(), '3d62101b-bf48-11e8-ac9b-0242ac180002', 'Admin', 'Admin', 'm.edlich@soluris.fr', '$2y$10$xofkbNT.c8U3AK6mpOdAEOU9LATv/X01nUJqag88zNF4pUweN7ddm', '[\"ROLE_ADMIN\"]', 1, NULL);

# Créer les catégories de base
INSERT INTO `registry_treatment_data_category` VALUES ('bank','Information bancaire',7,0),('bank-situation','Situation bancaire',8,0),('birth','Date, lieu de naissance',2,0),('caf','Numéro de CAF',17,0),('connexion','Connexion',15,0),('earning','Revenus',12,0),('email','Emails',13,0),('family-situation','Situation familiale',3,0),('filiation','Filiation',4,0),('geo','Géolocalisation',16,0),('health','Santé',18,1),('heritage','Patrimoine',9,0),('identity','Pièces d’identité',20,0),('ip','Adresse IP',14,0),('name','Nom, Prénom',1,0),('phone','Coordonnées téléphoniques',6,0),('picture','Photos-vidéos',21,0),('postal','Coordonnées postales',5,0),('professional-situation','Situation professionnelle',11,0),('public-religious-opinion','Opinion politique ou religieuse',23,1),('racial-ethnic-opinion','Origine raciale ou ethnique',24,1),('sex-life','Vie sexuelle',25,1),('social-security-number','Numéro de Sécurité Sociale',19,1),('syndicate','Appartenance Syndicale',22,1),('tax-situation','Situation fiscale',10,0);

# Créer les questions de l’indice
INSERT INTO `maturity_domain` (`id`, `name`, `color`, `position`) VALUES
('2773db9d-9447-11e8-ad93-000c298bb17a', 'Technique', 'danger', 1),
('277c689e-9447-11e8-ad93-000c298bb17a', 'Vie privée', 'info', 2),
('277e7a13-9447-11e8-ad93-000c298bb17a', 'Violation de données', 'primary', 3),
('27825dcb-9447-11e8-ad93-000c298bb17a', 'Organisation', 'success', 4),
('278b79e0-9447-11e8-ad93-000c298bb17a', 'Juridique', 'warning', 5),
('28320b43-9447-11e8-ad93-000c298bb17a', 'Sensibilisation Formation', 'info', 6);

INSERT INTO `maturity_question` (`id`, `domain_id`, `name`) VALUES
('018a30d6-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Les sessions Windows sont protégées par un mot de passe et se vérrouillent automatiquement'),
('018d03e0-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Chaque utilisateur dispose d\'un identifiant unique (pas de compte générique)'),
('018d1f65-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Les éléments de sécurité de base ont été installés sur les postes clients (Antivirus, parefeu)'),
('0191ea66-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Une politique de mot de passe utilisateur rigoureuse a été adoptée (les mots de passe sont strictement confidentiels)'),
('0192e833-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Les permissions d\'accès obsolètes ont été supprimées'),
('01946667-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Une politique de protection des données a été mise en place et est connue de tous (interne et externe)'),
('0195eb20-9449-11e8-ad93-000c298bb17a', '277c689e-9447-11e8-ad93-000c298bb17a', 'Les données à l\'issu des traitements sont supprimées ou anonymisées'),
('07ae7fd8-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'Les supports de sauvegarde sont stockés dans un endroit sûr'),
('07af1ab7-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'Les supports de sauvegarde sont externalisés'),
('07af362d-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'Les jeux de sauvegarde sont régulièrement testés'),
('07b46150-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'Des études d\'impact sur la vie privée sont conduites lorsque le traitement le nécessite'),
('07b4775d-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'L\'intégrité des document est régulièrement vérifiée'),
('07b4d7d0-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'Les procédures à suivre en cas de violation de données sont définies et connues'),
('07b5206b-9449-11e8-ad93-000c298bb17a', '277e7a13-9447-11e8-ad93-000c298bb17a', 'Les clients mobiles sont régulièrement synchronisés'),
('0d892944-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'Les procédures d\'exploitation du SI ont été documentées'),
('0d8968c8-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'Un registre des traitements est tenu dans la collectivité'),
('0d8c82c0-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'Les conditions de restitution et de destruction des données sont prévues'),
('0d8cbc4b-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'L\'identité du destinataire est confirmée (en cas de transmission)'),
('0d8e7488-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'La demande de droit d\'accès est écrite ou repose sur un texte juridique'),
('0d919125-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'Un DPD a été désigné dans la collectivité'),
('0d93fe5b-9449-11e8-ad93-000c298bb17a', '27825dcb-9447-11e8-ad93-000c298bb17a', 'Les coordonnées du DPD et son rôle sont connus des personnes concernées, à l\'interne et à l\'externe'),
('12cd278d-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'Une clause spécifique est prévue dans les contrats de sous traitance et dans les cahiers des charges'),
('12cd5a73-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'Les mentions font apparaître la finalité, les droits des personnes et la durée de conservation et sont présentes sur les formulaires (papier et électronique)'),
('12cee5b1-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'Un comité informatique et liberté a été mis en place et se réunit au moins une fois par an'),
('12d0680f-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'Une charte informatique a été rédigée et annexée au règlement interieur'),
('12d0ee43-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'Le consentement des personnes concernées est organisé (classement, conservation, ...)'),
('12d1dea0-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'La conformité de chaque sous-traitant est vérifiée'),
('12d21db4-9449-11e8-ad93-000c298bb17a', '278b79e0-9447-11e8-ad93-000c298bb17a', 'Une politique de gestion des données à caractère personnel a été mise en place'),
('17e5b8bf-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Des actions de sensibilisation sont régulièrement menées'),
('17e8eeb2-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Les élus ont été sensibilisés à la protection des données à caractère personnel'),
('17ea3348-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Le personnel a été sensibilisé à la protection des données à caractère personnel'),
('17eb6123-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Le personnel a été formé à la protection des données à caractère personnel'),
('17ecb83b-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Les élus connaissent le principe d\'interdiction d\'utilisation des données à caractère personnel à des fins de communication politique'),
('17ef7566-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Les agents connaissent et appliquent le principe de minimisation des données collectées'),
('17efc682-9449-11e8-ad93-000c298bb17a', '28320b43-9447-11e8-ad93-000c298bb17a', 'Le principe d\'interdiction de la collecte des données sensibles est connu par les agents et élus de la collectivité'),
('f2be456a-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Les éléments de sécurité ont été installés sur le réseau (Antivirus, parefeu)'),
('f2c4ad0d-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Les mises à jours des logiciels sont faites régulièrement (WindowsUpdate, Antivirus, Navigateur, client email)'),
('f2c4f5cb-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Des moyens de chiffrement pour les ordinateurs portables et les unités de stockage amovibles ( clefs USB, CD, DVD,…) ont été prévus'),
('f2ce7bbe-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Les accès distants des appareils informatiques nomades sont sécurisés par VPN'),
('f2cef0e7-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Une politique de mot de passe administrateur rigoureuse a été adoptée'),
('f2cf66af-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Des alarmes anti intrusion ont été installées et sont vérifiées périodiquement'),
('f2d04f22-9448-11e8-ad93-000c298bb17a', '2773db9d-9447-11e8-ad93-000c298bb17a', 'Un détecteur de fumée et un système anti-feu ont été mis en place');

```

Vous pouvez maintenant vous connecter à MADIS avec cet utilisateur et commencer la configuration.
