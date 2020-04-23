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

namespace App\Tests\Domain\Admin\Cloner;

use App\Domain\Admin\Cloner\TreatmentCloner;
use App\Domain\Registry\Dictionary\DelayPeriodDictionary;
use App\Domain\Registry\Model;
use App\Domain\User\Model as UserModel;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TreatmentClonerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var TreatmentCloner
     */
    private $sut;

    protected function setUp(): void
    {
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->sut = new TreatmentCloner(
            $this->entityManagerProphecy->reveal()
        );
    }

    /**
     * Test cloneReferentForCollectivity
     * The referent is empty.
     */
    public function testCloneReferentForCollectivityEmpty(): void
    {
        $collectivity = new UserModel\Collectivity();
        $referent     = new Model\Treatment();
        $referent->setName('name');
        $referent->setLegalBasis('legal basis');

        /** @var Model\Treatment $cloned */
        $cloned = $this->invokeMethod(
            $this->sut,
            'cloneReferentForCollectivity',
            [$referent, $collectivity]
        );

        $this->assertEquals($referent->getName(), $cloned->getName());
        $this->assertNull($cloned->getGoal());
        $this->assertNull($cloned->getManager());
        $this->assertNull($cloned->getSoftware());
        $this->assertFalse($cloned->isPaperProcessing());
        $this->assertEquals($referent->getLegalBasis(), $cloned->getLegalBasis());
        $this->assertNull($cloned->getLegalBasisJustification());
        $this->assertCount(0, $cloned->getDataCategories());
        $this->assertNull($cloned->getDataCategoryOther());
        $this->assertNull($cloned->getDataOrigin());
        $this->assertNull($cloned->getRecipientCategory());
        $delay = $cloned->getDelay();
        $this->assertInstanceOf(Model\Embeddable\Delay::class, $delay);
        $this->assertNull($delay->getNumber());
        $this->assertNull($delay->getPeriod());
        $this->assertNull($delay->getComment());
        $securityAccessControl = $cloned->getSecurityAccessControl();
        $this->assertInstanceOf(Model\Embeddable\ComplexChoice::class, $securityAccessControl);
        $this->assertFalse($securityAccessControl->isCheck());
        $this->assertNull($securityAccessControl->getComment());
        $securityTracability = $cloned->getSecurityTracability();
        $this->assertInstanceOf(Model\Embeddable\ComplexChoice::class, $securityTracability);
        $this->assertFalse($securityTracability->isCheck());
        $this->assertNull($securityTracability->getComment());
        $securitySaving = $cloned->getSecuritySaving();
        $this->assertInstanceOf(Model\Embeddable\ComplexChoice::class, $securitySaving);
        $this->assertFalse($securitySaving->isCheck());
        $this->assertNull($securitySaving->getComment());
        $securityUpdate = $cloned->getSecurityUpdate();
        $this->assertInstanceOf(Model\Embeddable\ComplexChoice::class, $securityUpdate);
        $this->assertFalse($securityUpdate->isCheck());
        $this->assertNull($securityUpdate->getComment());
        $securityOther = $cloned->getSecurityOther();
        $this->assertInstanceOf(Model\Embeddable\ComplexChoice::class, $securityOther);
        $this->assertFalse($securityOther->isCheck());
        $this->assertNull($securityOther->getComment());
        $this->assertFalse($cloned->isSystematicMonitoring());
        $this->assertFalse($cloned->isLargeScaleCollection());
        $this->assertFalse($cloned->isVulnerablePeople());
        $this->assertFalse($cloned->isDataCrossing());
        $this->assertTrue($cloned->isActive());
        $this->assertFalse($cloned->isSecurityEntitledPersons());
        $this->assertFalse($cloned->isSecurityOpenAccounts());
        $this->assertFalse($cloned->isSecuritySpecificitiesDelivered());
        $this->assertNull($cloned->getAuthor());
        $this->assertNull($cloned->getCollectingMethod());
        $this->assertNull($cloned->getEstimatedConcernedPeople());
        $this->assertNull($cloned->getUltimateFate());
        $this->assertEquals($collectivity, $cloned->getCollectivity());
        $this->assertEquals($referent, $cloned->getClonedFrom());
    }

    /**
     * Test cloneReferentForCollectivity
     * The referent is full filled.
     *
     * @throws \ReflectionException
     */
    public function testCloneReferentForCollectivityFullFilled(): void
    {
        $collectivity = new UserModel\Collectivity();
        $referent     = new Model\Treatment();
        $referent->setName('name');
        $referent->setGoal('goal');
        $referent->setManager('manager');
        $referent->setSoftware('software');
        $referent->setPaperProcessing(true);
        $referent->setLegalBasis('legal basis');
        $referent->setLegalBasisJustification('legal basis justification');
        $referent->setObservation('observation');
        $referent->getConcernedPeopleParticular()->setCheck(true);
        $referent->setDataCategories([
            new Model\TreatmentDataCategory('code1', 'name1', 1),
            new Model\TreatmentDataCategory('code2', 'name2', 2),
        ]);
        $referent->setDataCategoryOther('data category other');
        $referent->setDataOrigin('data origin');
        $referent->setRecipientCategory('recipient category');
        $delay = $referent->getDelay();
        $delay->setNumber(2);
        $delay->setPeriod(DelayPeriodDictionary::PERIOD_DAY);
        $delay->setComment('delay comment');
        $securityAccessControl = $referent->getSecurityAccessControl();
        $securityAccessControl->setCheck(true);
        $securityAccessControl->setComment('comment');
        $securityTracability = $referent->getSecurityTracability();
        $securityTracability->setCheck(true);
        $securityTracability->setComment('comment');
        $securitySaving = $referent->getSecuritySaving();
        $securitySaving->setCheck(true);
        $securitySaving->setComment('comment');
        $securityUpdate = $referent->getSecurityUpdate();
        $securityUpdate->setCheck(true);
        $securityUpdate->setComment('comment');
        $securityOther = $referent->getSecurityOther();
        $securityOther->setCheck(true);
        $securityOther->setComment('comment');
        $referent->setSystematicMonitoring(true);
        $referent->setLargeScaleCollection(true);
        $referent->setVulnerablePeople(true);
        $referent->setDataCrossing(true);
        $referent->setActive(false);
        $referent->setSecurityEntitledPersons(false);
        $referent->setSecurityOpenAccounts(true);
        $referent->setSecuritySpecificitiesDelivered(true);
        $referent->setAuthor('foo');
        $referent->setCollectingMethod('bar');
        $referent->setEstimatedConcernedPeople(1);
        $referent->setUltimateFate('baz');

        /** @var Model\Treatment $cloned */
        $cloned = $this->invokeMethod(
            $this->sut,
            'cloneReferentForCollectivity',
            [$referent, $collectivity]
        );

        $this->assertEquals($referent->getName(), $cloned->getName());
        $this->assertEquals($referent->getGoal(), $cloned->getGoal());
        $this->assertEquals($referent->getManager(), $cloned->getManager());
        $this->assertEquals($referent->getSoftware(), $cloned->getSoftware());
        $this->assertEquals($referent->isPaperProcessing(), $cloned->isPaperProcessing());
        $this->assertEquals($referent->getLegalBasis(), $cloned->getLegalBasis());
        $this->assertEquals($referent->getLegalBasisJustification(), $cloned->getLegalBasisJustification());
        $this->assertEquals($referent->getObservation(), $cloned->getObservation());
        $this->assertEquals($referent->getConcernedPeopleParticular(), $cloned->getConcernedPeopleParticular());
        $this->assertEquals($referent->getDataCategories(), $cloned->getDataCategories());
        $this->assertEquals($referent->getDataCategoryOther(), $cloned->getDataCategoryOther());
        $this->assertEquals($referent->getDataOrigin(), $cloned->getDataOrigin());
        $this->assertEquals($referent->getRecipientCategory(), $cloned->getRecipientCategory());

        $referentDelay = $referent->getDelay();
        $clonedDelay   = $cloned->getDelay();
        $this->assertEquals($referentDelay->getNumber(), $clonedDelay->getNumber());
        $this->assertEquals($referentDelay->getPeriod(), $clonedDelay->getPeriod());
        $this->assertEquals($referentDelay->getComment(), $clonedDelay->getComment());

        $referentSecurityAccessControl = $referent->getSecurityAccessControl();
        $clonedSecurityAccessControl   = $cloned->getSecurityAccessControl();
        $this->assertEquals($referentSecurityAccessControl->isCheck(), $clonedSecurityAccessControl->isCheck());
        $this->assertEquals($referentSecurityAccessControl->getComment(), $clonedSecurityAccessControl->getComment());

        $referentSecurityTracability = $referent->getSecurityTracability();
        $clonedSecurityTracability   = $cloned->getSecurityTracability();
        $this->assertEquals($referentSecurityTracability->isCheck(), $clonedSecurityTracability->isCheck());
        $this->assertEquals($referentSecurityTracability->getComment(), $clonedSecurityTracability->getComment());

        $referentSecuritySaving = $referent->getSecuritySaving();
        $clonedSecuritySaving   = $cloned->getSecuritySaving();
        $this->assertEquals($referentSecuritySaving->isCheck(), $clonedSecuritySaving->isCheck());
        $this->assertEquals($referentSecuritySaving->getComment(), $clonedSecuritySaving->getComment());

        $referentSecurityUpdate = $referent->getSecurityUpdate();
        $clonedSecurityUpdate   = $cloned->getSecurityUpdate();
        $this->assertEquals($referentSecurityUpdate->isCheck(), $clonedSecurityUpdate->isCheck());
        $this->assertEquals($referentSecurityUpdate->getComment(), $clonedSecurityUpdate->getComment());

        $referentSecurityOther = $referent->getSecurityOther();
        $clonedSecurityOther   = $cloned->getSecurityOther();
        $this->assertEquals($referentSecurityOther->isCheck(), $clonedSecurityOther->isCheck());
        $this->assertEquals($referentSecurityOther->getComment(), $clonedSecurityOther->getComment());

        $this->assertEquals($referent->isSystematicMonitoring(), $cloned->isSystematicMonitoring());
        $this->assertEquals($referent->isLargeScaleCollection(), $cloned->isLargeScaleCollection());
        $this->assertEquals($referent->isVulnerablePeople(), $cloned->isVulnerablePeople());
        $this->assertEquals($referent->isDataCrossing(), $cloned->isDataCrossing());
        $this->assertEquals($referent->isActive(), $cloned->isActive());
        $this->assertEquals($referent->isSecurityEntitledPersons(), $cloned->isSecurityEntitledPersons());
        $this->assertEquals($referent->isSecurityOpenAccounts(), $cloned->isSecurityOpenAccounts());
        $this->assertEquals($referent->isSecuritySpecificitiesDelivered(), $cloned->isSecuritySpecificitiesDelivered());
        $this->assertEquals($referent->getAuthor(), $cloned->getAuthor());
        $this->assertEquals($referent->getCollectingMethod(), $cloned->getCollectingMethod());
        $this->assertEquals($referent->getEstimatedConcernedPeople(), $cloned->getEstimatedConcernedPeople());
        $this->assertEquals($referent->getUltimateFate(), $cloned->getUltimateFate());
        $this->assertEquals($collectivity, $cloned->getCollectivity());
        $this->assertEquals($referent, $cloned->getClonedFrom());
    }
}
