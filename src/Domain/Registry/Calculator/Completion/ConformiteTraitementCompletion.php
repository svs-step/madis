<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Calculator\Completion;

use App\Domain\Registry\Dictionary\ConformiteTraitementLevelDictionary;
use App\Domain\Registry\Model;

class ConformiteTraitementCompletion
{
    /**
     * Calcul nbConformes, nbNonConformesMineures and nbNonConformesMajeures for a ConformiteTraitement.
     */
    private function calculConformite(Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement): array
    {
        $calculs = [
            'nbConformes'            => 0,
            'nbNonConformesMineures' => 0,
            'nbNonConformesMajeures' => 0,
        ];
        foreach ($conformiteTraitement->getReponses() as $reponse) {
            if (true === $reponse->isConforme()) {
                ++$calculs['nbConformes'];
            } else {
                if (empty($reponse->getActionProtections())) {
                    ++$calculs['nbNonConformesMajeures'];
                } else {
                    $nbPlanified = array_filter(\iterable_to_array($reponse->getActionProtections()),
                        function (Model\Mesurement $mesurement) {
                            return !\is_null($mesurement->getPlanificationDate());
                        });

                    if (count($nbPlanified) > 0) {
                        ++$calculs['nbNonConformesMineures'];
                    } else {
                        ++$calculs['nbNonConformesMajeures'];
                    }
                }
            }
        }

        return $calculs;
    }

    public function setCalculsConformite(Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement)
    {
        $calculs = $this->calculConformite($conformiteTraitement);

        $conformiteTraitement->setNbConformes($calculs['nbConformes']);
        $conformiteTraitement->setNbNonConformesMineures($calculs['nbNonConformesMineures']);
        $conformiteTraitement->setNbNonConformesMajeures($calculs['nbNonConformesMajeures']);
    }

    public static function getConformiteTraitementLevel(?Model\ConformiteTraitement\ConformiteTraitement $conformiteTraitement)
    {
        switch (true) {
            case \is_null($conformiteTraitement):
                return ConformiteTraitementLevelDictionary::NON_EVALUE;
                break;
            case $conformiteTraitement->getNbNonConformesMajeures() >= 1:
            case $conformiteTraitement->getNbNonConformesMineures() >= 3:
                return ConformiteTraitementLevelDictionary::NON_CONFORMITE_MAJEURE;
                break;
            case $conformiteTraitement->getNbNonConformesMineures() >= 1:
                return ConformiteTraitementLevelDictionary::NON_CONFORMITE_MINEURE;
                break;
            default:
                return ConformiteTraitementLevelDictionary::CONFORME;
        }
    }
}
