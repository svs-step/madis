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
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE registry_confomite_organization_processus SET description = "Définir et communiquer aux personnes concernées la politique générale de protection des données de la structure. Définir, mettre en œuvre et réexaminer la politique de gestion des données (processus, ressources, mesures). S\'assurer que la politique de gestion des données est communiquée, comprise et appliquée au sein de la structure.
            " where position = 1');
        $this->addSql('UPDATE registry_confomite_organization_processus SET description = "S\'assurer que le personnel à qui ont été affectées les responsabilités définies dans le SMDCP (Système de management de la protection des données), a les compétences nécessaires pour exécuter les tâches requises. S\'assurer que tout le personnel approprié a conscience de la pertinence et de l\'importance de ses activités liées aux traitements des DCP.
            " where position = 8');
        $this->addSql('UPDATE registry_confomite_organization_processus SET description = "A intervalles réguliers, vérifier que le Système de mangament de la protection des données est conforme à la politique définie. Apporter la preuve que les traitements effectués par le responsable du traitement et les sous-traitants sont conformes au règleme
            " where position = 10');

        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La structure a identifié son DPD et/ou son référent DPD. Dans les deux cas, il dispose des compétences nécessaires et/ou a reçu une formation. Sa mission est reconnue, positionnée et légitimée dans l\'organisme. Il existe une fiche de mission qui formalise son rôle, ses responsabilités et précise le temps et les ressources qui lui sont allouées. Lorsque les tâches du DPD sont réparties entre plusieurs personnes, cette répartition est claire.
            " where nom = "L\'entreprise a identifié son DPO ou son référent DPO. Dans les deux cas, il dispose des compétences nécessaires et/ou a reçu une formation. Sa mission est reconnue, positionnée et légitimée dans l\'organisation. Il existe une fiche de mission qui formalise ses rôles responsabilités et précise les temps et ressources alloués. Lorsque les tâches du DPO sont réparties sur plusieurs personnes, la répartition des activités est claire."');

        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La structure dispose d\'un registre des traitements. Le registre renseigne sur les critères minimum précisés à l\'article 30 du règlement : nom du responsable du traitement et du DPD, les finalités du traitement, les catégories de personnes concernées et les catégories de DCP, les catégories de destinataires, les transferts de données à caractère personnel vers un pays tiers, les délais de conservation, une description générale des mesures de sécurité.
            " where nom ""');

        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "A chaque traitement est associé un gestionnaire. Tout nouveau traitement est signalé par le gestionnaire au DPD ou au référent DPD pour mise à jour du registre. Tout traitement ultérieur est porté à la connaissance du DPD ou du référent DPD pour avis puis partagé par la gouvernance. 
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "Le DPD ou le référent DPD peut disposer d\'une cartographie des flux de données mettant en évidence les points de collecte des données et les échanges inter-application et/ou avec les parties prenantes externes.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La structure dispose des connaissances nécessaires pour gérer les transferts de données à caractère personnel transfrontaliers dans l\'UE, vers des pays tiers ou des organisations internationales. L\'inventaire de ces transferts est tenu à jour. 
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "L\'organisation partage une méthode de gestion de projet. Le lancement d\'un projet traitant de DCP implique une information préalable auprès du DPD ou du référent DPD. La note de cadrage définit la licéité du traitement, précise les droits que la personne concernée pourra exercer et renseigne sur la finalité, la minimisation des données, la limitation des durées de conservation.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "Une étude de la sécurité est réalisée pour définir les mesures techniques et organisationnelles qui préserveront la disponibilité, l\'intégrité et la confidentialité des données. Les données de test sont anonymisées.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La procédure de livraison du projet permet au DPD ou référent DPD de vérifier qu\'il est conforme à la règlementation et que les procédures visant à permettre à la personne concernée d\'exercer ses droits sont opérationnelles.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "En relation avec le service sécurité de l\'information de l\'entreprise, mettre en œuvre les mesures de protection sélectionnées afin de répondre aux objectifs de disponibilité, d’intégrité et de confidentialité des données à caractère personnel. Détecter les incidents de sécurité pouvant avoir pour conséquence une violation de données
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La structure  dispose d\'un document (Politique de sécurité du système d\'information - PSSI) qui rassemble l\'ensemble des mesures de sécurité appliquées 
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "Il existe un responsable de la sécurité ou un référent sécurité de l\'information. Cette personne est clairement identifiée au sein de la structure. Ses responsabilités couvrent la disponibilité, l\'intégrité et la confidentialité de l\'information. Elles font l\'objet d\'une fiche de poste ou d’une lettre de mission.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La structure est organisée pour détecter et gérer les incidents de sécurité. Les incidents sont historisés et partagés avec le DPD pour déterminer s\'il s\'agit d\'une violation de données.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "La structure  dispose d\'une méthode formalisée d\'analyse de risque notamment celle proposée par la CNIL. Si d\'autres méthodes de sécurité globale existent, elles peuvent être mise en cohérence avec la protection des données (ISO 31000, ISO 27005, ISO 27000,EBIOS, HAZOP,HACCP, MEHARI). Dans ce cas, une vigilence devra être apportée car ces dernières ne couvrent pas l\'ensemble des mesures permettant la protection des données (par exemple au niveau juridique).
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "Conformément à l\'article 35 du RGPD et aux lignes directrices portant sur le PIA/AIPD , les nouveaux traitements de DCP "à risque élevé" pour les droits et libertés des personnes comme par exemple de contrôle des personnes, d\'évaluations systématiques font l\'objet d'une analyse d\'impact. La structure a pris connaissance des listes pour lesquelles les analyses sont obligatoires ou non publié pa rla CNIL. Dans le cas ou le traitement ne figure pas sur cette liste , elle applique la règle des critères derterminants l\'analyse (Cf.Infographie "Dois-je faire une AIPD")
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "Les résultats des analyses sont documentés, partagés avec le DPD ou le référent DPD. Ils font l\'objet d\'un plan d\'action formalisé et approuvé par le responsable de traitement. Le DPO ou référent RGPD contrôle la réalisation du plan de traitement.
            " where nom ""');
        $this->addSql('UPDATE registry_confomite_organization_question SET nom = "" where nom ""');

    }

    public function down(Schema $schema) : void
    {

    }
}
