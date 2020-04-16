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

namespace App\Domain\Admin\Cloner;

use App\Domain\Registry\Model as RegistryModel;
use App\Domain\User\Model as UserModel;

class ContractorCloner extends AbstractCloner
{
    /**
     * {@inheritdoc}
     *
     * @param RegistryModel\Contractor $referent
     *
     * @throws \Exception
     */
    protected function cloneReferentForCollectivity($referent, UserModel\Collectivity $collectivity): RegistryModel\Contractor
    {
        $contractor = new RegistryModel\Contractor();

        $contractor->setName($referent->getName());
        $contractor->setReferent($referent->getReferent());
        $contractor->setContractualClausesVerified($referent->isContractualClausesVerified());
        $contractor->setAdoptedSecurityFeatures($referent->isAdoptedSecurityFeatures());
        $contractor->setMaintainsTreatmentRegister($referent->isMaintainsTreatmentRegister());
        $contractor->setSendingDataOutsideEu($referent->isSendingDataOutsideEu());
        $contractor->setOtherInformations($referent->getOtherInformations());
        $contractor->setDpo($referent->getDpo());
        $contractor->setLegalManager($referent->getLegalManager());

        if (null !== $referent->getAddress()) {
            $contractor->setAddress($referent->getAddress());
        }

        $contractor->setCollectivity($collectivity);
        $contractor->setClonedFrom($referent);

        return $contractor;
    }
}
