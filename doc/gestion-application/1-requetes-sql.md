Requêtes SQL
============

## Dupliquer les traitements sur une structure

Une procédure SQL a été créée afin de dupliquer les traitements
sur les structures de destination.

```sql
CREATE DEFINER=`madis`@`localhost` PROCEDURE `dupliquer_traitements_sur_une_CT`(id_coll_source CHAR(36), id_user_dest CHAR(36), id_coll_destination CHAR(36))
BLOCK1: BEGIN
	DECLARE done1 INT DEFAULT FALSE;
    DECLARE a CHAR(36);
	DECLARE b VARCHAR(255);
	DECLARE listetraitements CURSOR FOR	SELECT id, name FROM registry_treatment where collectivity_id = id_coll_destination;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done1 = TRUE;
	INSERT INTO registry_treatment(id, collectivity_id, creator_id, name, goal, software, legal_basis, legal_basis_justification, concerned_people, recipient_category, active, created_at, updated_at, delay_number, delay_period, manager, security_access_control_check, security_access_control_comment, security_tracability_check, security_tracability_comment, security_saving_check, security_saving_comment, paper_processing, security_update_check, security_update_comment, security_other_check, security_other_comment, delay_other_delay, delay_comment, data_category_other, systematic_monitoring, large_scale_collection, vulnerable_people, data_crossing, completion, template, template_identifier) SELECT UUID(), id_coll_destination, id_user_dest, name, goal, software, legal_basis, legal_basis_justification, concerned_people, recipient_category, "1", "2018-09-27 18:00:00", "2018-09-27 18:00:00", delay_number, delay_period, manager, security_access_control_check, security_access_control_comment, security_tracability_check, security_tracability_comment, security_saving_check, security_saving_comment, paper_processing, security_update_check, security_update_comment, security_other_check, security_other_comment, delay_other_delay, delay_comment, data_category_other, systematic_monitoring, large_scale_collection, vulnerable_people, data_crossing, completion, "1", template_identifier FROM registry_treatment WHERE collectivity_id = id_coll_source;
	OPEN listetraitements;
	LOOP1: LOOP
		FETCH listetraitements INTO a, b;
		IF done1 THEN
			LEAVE LOOP1;
		END IF;
		BLOCK2: BEGIN
			DECLARE done2 INT DEFAULT FALSE;
			DECLARE c VARCHAR(50);
			DECLARE listecategories	CURSOR FOR SELECT treatment_data_category_code from registry_assoc_treatment_data_category where registry_assoc_treatment_data_category.treatment_id = (SELECT id FROM registry_treatment WHERE registry_treatment.name = b AND collectivity_id = id_coll_source);
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
			OPEN listecategories;
			LOOP2: LOOP
				FETCH listecategories INTO c;
				IF done2 THEN
					LEAVE LOOP2;
				END IF;
				INSERT INTO registry_assoc_treatment_data_category(treatment_id, treatment_data_category_code) VALUES (a, c);
			END LOOP LOOP2;
            CLOSE listecategories;
        END BLOCK2;
	END LOOP LOOP1;
  	CLOSE listetraitements;
END BLOCK1
```

## Conformité des traitements
### Ajouter une question 

Pour ajouter une nouvelle question dans la conformité des traitements il faut vérifier en base de données la 
dernière valeur pour le champ qui ordonne les questions.

Dans l'exemple suivant l'ordre de la question est à 13 car la dernière question en base a la valeur 12. 
Bien faire attention à la génération de la clef primaire qui est ici un uuid.

```sql
INSERT INTO `conformite_traitement_question` (`id`, `question`, `position`) VALUES ('4d66c04e-62e7-4216-85a2-6d9feb71722a', 'Ceci est le texte de la question', '13')
```

## Conformité de la structure
### Ajouter une question 

2 cas se présentent pour ajouter une question à la conformité de la structure :

####1 - Pour ajouter une nouvelle question dans à un processus existant 
Il faut vérifier en base de données la dernière valeur pour le champ `position` qui ordonne les questions liées à ce processus.

Dans l'exemple suivant la position de la question est à 5 car la dernière question en base a la valeur 4. 
Bien faire attention à la génération de la clef primaire qui est ici un uuid.

```sql
INSERT INTO `registry_conformite_organisation_question` (`id`, `processus_id`, `nom`, `position`) VALUES ('4d66c04e-62e7-4216-85a2-6d9feb71722a', 'b2a186df-cf81-4199-a292-53dbdb43b609', 'Ceci est le texte de la question', '5')
```

####2 - Pour ajouter un nouveau processus et une nouvelle question
Il faut vérifier en base de données la dernière valeur pour le champ `position` qui ordonne les processus.

Dans l'exemple suivant la position du processus est à 13 car le dernier processus en base a la valeur 12. 
Bien faire attention à la génération de la clef primaire qui est ici un uuid.

```sql
INSERT INTO `registry_conformite_organisation_processus` (`id`, `nom`, `couleur`, `description`, `position`) VALUES ('b2a186df-cf81-4199-a292-53dbdb43b609', 'Nom du processus', 'info', 'Description du processus', '13')
```
Puis, ajouter la question en utilisant l'uuid du processus précédemment créé pour la colonne `processus_id`. 
Suivre alors la procédure décrite dans le cas n°1