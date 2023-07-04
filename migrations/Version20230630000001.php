<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE registry_conformite_organisation_processus SET description = "Définir et communiquer aux personnes concernées la politique générale de protection des données de la structure. Définir, mettre en œuvre et réexaminer la politique de gestion des données (processus, ressources, mesures). S\'assurer que la politique de gestion des données est communiquée, comprise et appliquée au sein de la structure."
            where position = 1');
        $this->addSql('UPDATE registry_conformite_organisation_processus SET description = "S\'assurer que le personnel à qui ont été affectées les responsabilités définies dans le SMDCP (Système de management de la protection des données), a les compétences nécessaires pour exécuter les tâches requises. S\'assurer que tout le personnel approprié a conscience de la pertinence et de l\'importance de ses activités liées aux traitements des DCP."
            where position = 8');
        $this->addSql('UPDATE registry_conformite_organisation_processus SET description = "A intervalles réguliers, vérifier que le Système de management de la protection des données est conforme à la politique définie. Apporter la preuve que les traitements effectués par le responsable du traitement et les sous-traitants sont conformes au règlement."
            where position = 10');
        $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "En relation avec le service sécurité de l\'information de l\'entreprise, mettre en œuvre les mesures de protection sélectionnées afin de répondre aux objectifs de disponibilité, d’intégrité et de confidentialité des données à caractère personnel. Détecter les incidents de sécurité pouvant avoir pour conséquence une violation de données"
            where position = 6');

        $this->allProcessus = $this->getData('SELECT * FROM registry_conformite_organisation_processus ORDER BY position ASC');

        foreach ($this->allProcessus as $processus) {
            switch ($processus['position']) {
                case 1:
                    // 1. Responsabilité
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure a identifié son DPD et/ou son référent DPD. Dans les deux cas, il dispose des compétences nécessaires et/ou a reçu une formation. Sa mission est reconnue, positionnée et légitimée dans l\'organisme. Il existe une fiche de mission qui formalise son rôle, ses responsabilités et précise le temps et les ressources qui lui sont allouées. Lorsque les tâches du DPD sont réparties entre plusieurs personnes, cette répartition est claire."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    break;
                case 2:
                    // 2. Traitements et transferts de données
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure dispose d\'un registre des traitements. Le registre renseigne sur les critères minimum précisés à l\'article 30 du règlement : nom du responsable du traitement et du DPD, les finalités du traitement, les catégories de personnes concernées et les catégories de DCP, les catégories de destinataires, les transferts de données à caractère personnel vers un pays tiers, les délais de conservation, une description générale des mesures de sécurité."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "A chaque traitement est associé un gestionnaire. Tout nouveau traitement est signalé par le gestionnaire au DPD ou au référent DPD pour mise à jour du registre. Tout traitement ultérieur est porté à la connaissance du DPD ou du référent DPD pour avis puis partagé par la gouvernance."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Le DPD ou le référent DPD peut disposer d\'une cartographie des flux de données mettant en évidence les points de collecte des données et les échanges inter-application et/ou avec les parties prenantes externes."
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure dispose des connaissances nécessaires pour gérer les transferts de données à caractère personnel transfrontaliers dans l\'UE, vers des pays tiers ou des organisations internationales. L\'inventaire de ces transferts est tenu à jour."
                        where processus_id = "' . $processus['id'] . '" and position = 4');
                    break;
                case 5:
                    // 5. Protection des données dès la conception
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "L\'organisation partage une méthode de gestion de projet. Le lancement d\'un projet traitant de DCP implique une information préalable auprès du DPD ou du référent DPD. La note de cadrage définit la licéité du traitement, précise les droits que la personne concernée pourra exercer et renseigne sur la finalité, la minimisation des données, la limitation des durées de conservation."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Une étude de la sécurité est réalisée pour définir les mesures techniques et organisationnelles qui préserveront la disponibilité, l\'intégrité et la confidentialité des données. Les données de test sont anonymisées."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La procédure de livraison du projet permet au DPD ou référent DPD de vérifier qu\'il est conforme à la règlementation et que les procédures visant à permettre à la personne concernée d\'exercer ses droits sont opérationnelles."
                        where processus_id = "' . $processus['id'] . '" and position = 4');
                    break;
                case 6:
                    // 6. Gestion des mesures de sécurité
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure  dispose d\'un document (Politique de sécurité du système d\'information - PSSI) qui rassemble l\'ensemble des mesures de sécurité appliquées"
                         where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Il existe un responsable de la sécurité ou un référent sécurité de l\'information. Cette personne est clairement identifiée au sein de la structure. Ses responsabilités couvrent la disponibilité, l\'intégrité et la confidentialité de l\'information. Elles font l\'objet d\'une fiche de poste ou d’une lettre de mission."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure est organisée pour détecter et gérer les incidents de sécurité. Les incidents sont historisés et partagés avec le DPD pour déterminer s\'il s\'agit d\'une violation de données."
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    break;
                case 7:
                    // 7. Analyse d'impacts sur la protection des données
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure  dispose d\'une méthode formalisée d\'analyse de risque notamment celle proposée par la CNIL. Si d\'autres méthodes de sécurité globale existent, elles peuvent être mise en cohérence avec la protection des données (ISO 31000, ISO 27005, ISO 27000,EBIOS, HAZOP,HACCP, MEHARI). Dans ce cas, une vigilence devra être apportée car ces dernières ne couvrent pas l\'ensemble des mesures permettant la protection des données (par exemple au niveau juridique)."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Conformément à l\'article 35 du RGPD et aux lignes directrices portant sur le PIA/AIPD , les nouveaux traitements de DCP &quot;à risque élevé&quot; pour les droits et libertés des personnes comme par exemple de contrôle des personnes, d\'évaluations systématiques font l\'objet d\'une analyse d\'impact. La structure a pris connaissance des listes pour lesquelles les analyses sont obligatoires ou non publié pa rla CNIL. Dans le cas ou le traitement ne figure pas sur cette liste , elle applique la règle des critères derterminants l\'analyse (Cf.Infographie &quot;Dois-je faire une AIPD&quot;)"
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Les résultats des analyses sont documentés, partagés avec le DPD ou le référent DPD. Ils font l\'objet d\'un plan d\'action formalisé et approuvé par le responsable de traitement. Le DPO ou référent RGPD contrôle la réalisation du plan de traitement."
                        where processus_id = "' . $processus['id'] . '" and position = 4');
                    break;
                case 8:
                    // 8. Sensibiliser, former et communiquer
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Le DPD ou le référent DPD peut justifier de ses connaissances en matière juridique. Il dispose de canaux d\'information pour maintenir à jour ses connaissances."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Les catégories de collaborateurs directement impliqués dans les traitements de DCP bénéficient de formations ou des sensibilisations spécifiques centrées sur les principes qui les concernent : gestion du consentement, privacy by design, privacy by default, protection contre les traitements non autorisés, exactitude, gestion des droits, violation de données."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Un plan de formation est établi lorsque le projet nécessite d\'acquérir de nouvelles connaissances pour avoir la compétence attendue : chef de projet privacy by design, gestionnaire de risque, gestionnaire de contrat."
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    break;
                case 9:
                    // 9. Exigences, sollicitations, violations, poursuites
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure veille de manière récurrente sur les évolutions de la réglementation, les spécificités nationales comme par exemple le seuil d\'âge des mineurs, le consentement parental pour les données hors ligne, la publication de nouveaux codes de conduite, les certifications ou encore les labels." 
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure  a clairement identifié une expertise juridique interne ou externe, spécialisée dans le RGPD et ses périmètres connexes comme par exemple les spécifications juridiques locales."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "La structure dispose d\'un processus lui permettant de réagir en cas de sollicitation ou de poursuite : disponibilité de preuves opposables, ressources juridiques identifiées, plan de communication interne et externe, contrat d\'assurance spécifique."
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Conformément à de multiples articles du RGPD, en cas de violation avérée, la structure  est organisée pour analyser et notifier l\'incident auprès de l\'autorité de contrôle au plus tard dans les 72 h, et si nécessaire en informer les personnes concernées. Elle serait en capacité de produire un bilan décrivant les circonstances, la chronologie, les dommages et leurs conséquences, les actions correctives décidées. Il existe un registre des violations."
                        where processus_id = "' . $processus['id'] . '" and position = 4');
                    break;
                case 10:
                    // 10. Evaluer et auditer
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Conformément à l\'article 32 du RGPD, l\'organisation teste, analyse, et évalue l\'efficacité des mesures techniques et organisationnelles, pour assurer la sécurité du traitement. Les mesures de sécurité en prévention de la violation de données à caractère personnel sont contrôlées régulièrement et sont aplliquées en cas de violation de données."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Les évaluations internes sont réalisées par le DPD ou le référent DPD. Lorsque l\'évaluation est réalisée par une autre personne physique ou morale, les critères d\'évaluation sont définis en collaboration avec le DPD ou le référent DPD."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Tout rapport d\'évaluation ou d\'audit fait l\'objet d\'un plan d\'action correctif et préventif dont la réalisation est contrôlée par le DPD ou le référent DPD. Ces rapports sont intégrés dans le bilan annuel du DPD ou du référent DPD."
                        where processus_id = "' . $processus['id'] . '" and position = 4');
                    break;
                case 11:
                    // 11. Gérer de la documentation et des peuves
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Il existe un processus de gestion de la documentation applicable au RGPD et comprenant a minima : une organisation du stockage, des règles de marquage, de nommage et de  versioning."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Le DPD ou le référent DPD a établi une liste des documents à produire comme par exemple : politique générale de protection des données, politique de gestion des données, compte rendu de gouvernance, registre des traitements, procédures relative à l\'exercice des droits de la personne concernée, modèle de cadrage d\'un projet Privacy by design, politique de sécurité."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Conformément à l\'article 47  du RGPD , les groupes de collectivités engagées dans une activité publique conjointe ayant définis des codes de conduite, ou des règles  contraignantes doivent préciser la procédure de validation et de révision de leur contenu par le responsable du traitement."
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    break;
                case 12:
                    // 12. Piloter le SMDCP
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "S\'il existe déjà une organisation interne basée sur les processus, elle communiquée au DPD pour qu\'il adapte l\'accompagnement. La cartographie des processus de gestion des données détaille les éléments d\'entrées et de sorties et mentionne les objectifs, les rôles et responsabilités, les activités."
                        where processus_id = "' . $processus['id'] . '" and position = 1');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "L\'ensemble des actions sont consolidées dans un seul et même tableau afin de s\'assurer de leure réalisation."
                        where processus_id = "' . $processus['id'] . '" and position = 2');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Le DPD ou le référent DPD a mis en place un tableau de bord qui comprend des indicateurs permettant de mesurer la conformité au règlement et les progrès réalisés.Les indicateurs sont simples à comprendre et partageable avec les membres de la gouvernance. Exemple d\'indicateurs : maturité des processus du système de gestion, ratio d\'actions réalisées et à réaliser, ratio nombre de traitement interne/sous-traités."
                        where processus_id = "' . $processus['id'] . '" and position = 3');
                    $this->addSql('UPDATE registry_conformite_organisation_question SET nom = "Conformément aux lignes directrices relatifs au DPD, le DPD ou le référent DPD produit un bilan annuel (Rapport annuel) partagé avec la structure de gouvernance et validé par le responsable du traitement.Ce rapport comprend entre autre : bilan de conformité des traitements, bilan des opérations de sensibilisation, bilan des PIA, bilan de plan d\'actions."
                        where processus_id = "' . $processus['id'] . '" and position = 4');
                    break;
            }
        }
    }

    public function down(Schema $schema): void
    {
    }

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }
}
