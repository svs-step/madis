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

namespace App\Domain\Reporting\Generator\Word;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\Language;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class OverviewGenerator
{
    const DATE_TIME_FORMAT = 'd/m/Y à H:i';
    const DATE_TIME_ZONE   = 'Europe/Paris';

    const TABLE_ORIENTATION_HORIZONTAL = 'horizontal';
    const TABLE_ORIENTATION_VERTICAL   = 'vertical';

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var TreatmentGenerator
     */
    protected $treatmentGenerator;

    /**
     * @var ContractorGenerator
     */
    protected $contractorGenerator;

    /**
     * @var array
     */
    protected $tableStyle;

    /**
     * @var array
     */
    protected $cellHeadStyle;

    /**
     * @var array
     */
    protected $textHeadStyle;

    public function __construct(
        UserProvider $userProvider,
        TreatmentGenerator $treatmentGenerator,
        ContractorGenerator $contractorGenerator
    ) {
        $this->userProvider        = $userProvider;
        $this->treatmentGenerator  = $treatmentGenerator;
        $this->contractorGenerator = $contractorGenerator;

        $this->tableStyle = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cantSplit'   => true,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ];

        $this->cellHeadStyle = [
            'bgColor' => '3c8dbc',
        ];

        $this->textHeadStyle = [
            'bold'  => true,
            'color' => 'ffffff',
        ];
    }

    protected function defineStyle(PhpWord $document): void
    {
        // CONFIGURATION
        $document->getSettings()->setThemeFontLang(new Language(Language::FR_FR));
        $document->getSettings()->setUpdateFields(true);

        $document->setDefaultFontName('Verdana');
        $document->setDefaultFontSize(10);
        $document->setDefaultParagraphStyle(['spaceAfter' => 200]);

        // STYLE
        // Numbered heading
        $document->addNumberingStyle(
            'headingNumbering',
            ['type'      => 'multilevel',
                'levels' => [
                    ['pStyle' => 'Heading1', 'format' => 'decimal', 'text' => '%1.'],
                    ['pStyle' => 'Heading2', 'format' => 'decimal', 'text' => '%1.%2.'],
                ],
            ]
        );

        // Title style
        $document->addTitleStyle(
            2,
            [
                'allCaps'   => true,
                'bold'      => true,
                'size'      => 16,
                'underline' => Style\Font::UNDERLINE_SINGLE,
            ],
            [
                'pageBreakBefore' => false,
                'numLevel'        => 0,
                'numStyle'        => 'headingNumbering',
                'spaceBefore'     => 500,
                'spaceAfter'      => 250,
            ]
        );
        $document->addTitleStyle(
            3,
            [
                'bold' => true,
            ],
            [
                'pageBreakBefore' => false,
                'numLevel'        => 1,
                'numStyle'        => 'headingNumbering',
                'spaceBefore'     => 500,
                'spaceAfter'      => 250,
            ]
        );
    }

    public function generateFirstPage(PhpWord $document): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $legalManager = $collectivity->getLegalManager();

        $this->defineStyle($document);

        $section = $document->addSection();
        $section->addText(
            'Bilan de gestion des données à caractère personnel',
            [
                'bold'  => true,
                'color' => '3c8dbc',
                'size'  => 36,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 1000,
            ]
        );
        $section->addText(
            \utf8_decode((string) $this->userProvider->getAuthenticatedUser()->getCollectivity()),
            [
                'bold'   => true,
                'italic' => true,
                'size'   => 20,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 1000,
            ]
        );

        // TREATMENT RESPONSABLE
        $section->addText(
            'Responsable du traitement',
            ['bold'        => true, 'size' => 15],
            ['spaceBefore' => 2500]
        );
        $section->addText(
            ContactCivilityDictionary::getCivilities()[$legalManager->getCivility()] . ' ' . $legalManager->getFullName(),
            ['size' => 12]
        );
        $section->addText(
            'Signature',
            ['italic' => true, 'size' => 10]
        );

        // DPO
        $section->addText(
            'Délégué à la Protection des Données :',
            ['bold'        => true, 'size' => 15],
            ['spaceBefore' => 1000]
        );
        $section->addText(
            'SOLURIS',
            ['size' => 12]
        );
        $section->addText(
            '2 rue des Rochers',
            ['size' => 12]
        );
        $section->addText(
            '17100 Saintes',
            ['size' => 12]
        );

        $section->addText(
            "{$this->getDate(new \DateTimeImmutable(), 'd/m/Y')}",
            ['italic'    => true],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 1000]
        );
    }

    public function generateTableOfContent(Section $section): void
    {
        $section->addText(
            'Table des matières',
            [
                'name' => 'Verdana',
                'size' => 16,
            ],
            [
                'alignment'  => Jc::CENTER,
                'spaceAfter' => 500,
            ]
        );
        $section->addTOC();
        $section->addPageBreak();
    }

    public function createContentSection(PhpWord $document): Section
    {
        $section = $document->addSection();
        $header  = $section->addHeader();
        $table   = $header->addTable([
            'borderColor' => '000000',
            'borderSize'  => 3,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ]);
        $row  = $table->addRow(10);
        $cell = $row->addCell(15 * 50);
        $cell = $row->addCell(70 * 50, ['alignment' => Jc::CENTER]);
        $cell->addText('Bilan de gestion des données à caractère personnel', ['alignment' => Jc::CENTER, 'bold' => true]);
        $cell = $row->addCell(15 * 50, ['alignment' => Jc::CENTER]);
        $cell->addPreserveText('Page {PAGE}/{NUMPAGES}', ['alignment' => Jc::CENTER]);
        $header->addTextBreak();

        $footer = $section->addFooter();
        $table  = $footer->addTable([
            'borderColor'       => 'FFFFFF',
            'borderBottomColor' => '000000',
            'borderSize'        => 6,
            'unit'              => TblWidth::PERCENT,
            'width'             => 100 * 50,
        ]);
        $table->addRow()->addCell();

        return $section;
    }

    public function generateObjectPart(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Objet', 2);

        $section->addText(
            "Ce document constitue le bilan de gestion des données à caractère personnel de la collectivité '{$collectivity->getName()}'."
        );
    }

    public function generateOrganismIntroductionPart(Section $section): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Présentation de l\'organisme', 2);

        $section->addTitle('Mission de l\'organisme', 3);
        $section->addText(\ucfirst($collectivity->getName()) . ' est une collectivité territoriale.');

        $section->addTitle('Engagement de la direction', 3);
        $section->addText("La direction de '{$collectivity->getName()}' a établi, documenté, mis en œuvre une politique de gestion des données à caractère personnel.");
        $section->addText('Cette politique décrit les mesures techniques et organisationnelles.');
        $section->addText("Cette politique a pour objectif de permettre à '{$collectivity->getName()}' de respecter dans le temps les exigences du RGPD et de pouvoir le démontrer.");

        $section->addTitle('Composition du comité Informatique et Liberté', 3);
        $section->addListItem('Foo');
        $section->addListItem('Bar');
        $section->addListItem('Baz');
    }

    public function generateRegistries(Section $section, array $treatments = [], array $contractors = []): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $section->addTitle('Bilan des registres', 2);

        $section->addText("{$collectivity->getName()} recense 2 registres : ");
        $section->addListItem('Traitements');
        $section->addListItem('Sous-traitants');

        $this->treatmentGenerator->generateGlobalOverview($section, $treatments);
        $this->contractorGenerator->generateGlobalOverview($section, $contractors);
    }

    /**
     * Generate the response
     * - This response parse PhpWord to a Word document
     * - Prepare it in a BinaryFileResponse.
     *
     * @param string $documentName The document name to use
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return BinaryFileResponse The response
     */
    public function generateResponse(PhpWord $document, string $documentName): BinaryFileResponse
    {
        // Save document as tmp OOXML file
        $objWriter = IOFactory::createWriter($document, 'Word2007');

        $fileName  = "{$documentName}.docx";
        $temp_file = \tempnam(\sys_get_temp_dir(), $fileName);

        $objWriter->save($temp_file);

        // Create response and return it
        $response = new BinaryFileResponse($temp_file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

    protected function addTable(Section $section, array $data = [], bool $header = false, string $orientation = self::TABLE_ORIENTATION_HORIZONTAL): void
    {
        $table = $section->addTable($this->tableStyle);

        foreach ($data as $nbLine => $line) {
            $table->addRow();
            foreach ($line as $nbCol => $col) {
                $col = \is_array($col) ? $col : [$col];

                if ($header && self::TABLE_ORIENTATION_HORIZONTAL === $orientation && 0 === $nbLine
                    || $header && self::TABLE_ORIENTATION_VERTICAL === $orientation && 0 === $nbCol) {
                    $cell = $table->addCell(2500, $this->cellHeadStyle);
                    foreach ($col as $item) {
                        $cell->addText($item, $this->textHeadStyle);
                    }
                } else {
                    $cell = $table->addCell();
                    foreach ($col as $item) {
                        $cell->addText($item);
                    }
                }
            }
        }

        $section->addTextBreak(2);
    }

    /**
     * Format a date for Word document.
     *
     * @param \DateTimeInterface $dateTime The date to parse
     * @param string|null        $format
     *
     * @throws \Exception
     *
     * @return string The parsed date with the good timezone
     */
    protected function getDate(\DateTimeInterface $dateTime, string $format = null): string
    {
        $parsedDateTime = new \DateTimeImmutable();
        $parsedDateTime = $parsedDateTime->setTimestamp($dateTime->getTimestamp());
        $parsedDateTime = $parsedDateTime->setTimezone(new \DateTimeZone(self::DATE_TIME_ZONE));

        $usableFormat = $format ?? self::DATE_TIME_FORMAT;

        return $parsedDateTime->format($usableFormat);
    }
}
