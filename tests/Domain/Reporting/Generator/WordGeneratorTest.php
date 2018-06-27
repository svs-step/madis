<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Domain\Reporting\Generator;

use App\Domain\Reporting\Generator\ContractorGenerator;
use App\Domain\Reporting\Generator\WordGenerator;
use App\Tests\Utils\ReflectionTrait;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WordGeneratorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var PhpWord
     */
    private $documentProphecy;

    /**
     * @var ContractorGenerator
     */
    private $contractorGeneratorProphecy;

    /**
     * @var array
     */
    private $dpo;

    /**
     * @var WordGenerator
     */
    private $generator;

    protected function setUp(): void
    {
        $this->documentProphecy            = $this->prophesize(PhpWord::class);
        $this->contractorGeneratorProphecy = $this->prophesize(ContractorGenerator::class);
        $this->dpo                         = [
            'firstName'   => 'John',
            'lastName'    => 'Doe',
            'company'     => 'Company',
            'job'         => 'job',
            'mail'        => 'john.doe@foo.bar',
            'phoneNumber' => '0123456789',
        ];

        $this->generator = new WordGenerator(
            $this->documentProphecy->reveal(),
            $this->contractorGeneratorProphecy->reveal(),
            $this->dpo
        );
    }

    public function testGenerateRegistryContractorReport(): void
    {
        $contractors = [];
        $response    = $this->prophesize(BinaryFileResponse::class)->reveal();

        $this->contractorGeneratorProphecy
            ->generateHeader($this->documentProphecy->reveal())
            ->shouldBeCalled()
        ;

        $this->contractorGeneratorProphecy
            ->generateOverview($this->documentProphecy->reveal(), $contractors)
            ->shouldBeCalled()
        ;

        $this->contractorGeneratorProphecy
            ->generateDetails($this->documentProphecy->reveal(), $contractors)
            ->shouldBeCalled()
        ;

        $this->contractorGeneratorProphecy
            ->generateResponse($this->documentProphecy->reveal(), 'sous-traitant')
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->generator->generateRegistryContractorReport($contractors)
        );
    }
}
