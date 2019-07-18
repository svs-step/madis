Requêtes SQL
============

## Dupliquer les traitements sur une collectivité

Une procédure SQL a été créée afin de dupliquer les traitements
sur les collectivités de destination.

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
