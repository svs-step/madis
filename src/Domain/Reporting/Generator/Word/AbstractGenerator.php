<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
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

namespace App\Domain\Reporting\Generator\Word;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

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

    /**
     * AbstractGenerator constructor.
     */
    public function __construct(
        UserProvider $userProvider,
        ParameterBagInterface $parameterBag
    ) {
        $this->userProvider = $userProvider;
        $this->parameterBag = $parameterBag;

        $this->tableStyle = [
            'borderColor' => '006699',
            'borderSize'  => 6,
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

    /**
     * Initialize PHPWord document variables & default values.
     */
    public function initializeDocument(PhpWord $document): void
    {
        // CONFIGURATION
        $document->getSettings()->setThemeFontLang(new Style\Language(Style\Language::FR_FR));
        $document->getSettings()->setUpdateFields(true);

        $document->setDefaultFontName('Verdana');
        $document->setDefaultFontSize(10);
        $document->setDefaultParagraphStyle(['spaceAfter' => 200]);

        $properties = $document->getDocInfo();
        $properties->setTitle('Bilan de gestion des données à caractère personnel');

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
            1,
            [
                'allCaps'   => true,
                'bold'      => true,
                'size'      => 16,
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
            2,
            [
                'bold' => true,
                'size' => 12,
            ],
            [
                'pageBreakBefore' => false,
                'numLevel'        => 1,
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
                'spaceBefore'     => 500,
                'spaceAfter'      => 250,
            ]
        );

    }

    /**
     * Add PhpWord homepage.
     *
     * @throws \Exception
     */
    public function addHomepage(PhpWord $document, string $title): void
    {
        $collectivity = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $legalManager = $collectivity->getLegalManager();

        $section = $document->addSection();
        $section->addText(
            $title,
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
            ['spaceBefore' => 1000]
        );
        $section->addText(
            ContactCivilityDictionary::getCivilities()[$legalManager->getCivility()] . ' ' . $legalManager->getFullName(),
            ['size' => 12]
        );

        // DPO
        $section->addText(
            'Délégué à la Protection des Données',
            ['bold'        => true, 'size' => 15],
            ['spaceBefore' => 1000]
        );

        $hasDpo                  = $collectivity->isDifferentDpo();
        $dpoDefaultFullName      = $this->parameterBag->get('APP_DPO_FIRST_NAME') . ' ' . $this->parameterBag->get('APP_DPO_LAST_NAME');
        $dpoDefaultStreetAddress = $this->parameterBag->get('APP_DPO_ADDRESS_STREET');
        $dpoDefaultZipCodeCity   = $this->parameterBag->get('APP_DPO_ADDRESS_ZIP_CODE') . ' ' . \strtoupper($this->parameterBag->get('APP_DPO_ADDRESS_CITY'));

        $section->addText(
            $hasDpo ? $collectivity->getDpo()->getFullName() : $dpoDefaultFullName,
            ['size' => 12]
        );
        $section->addText(
            $hasDpo ? $collectivity->getAddress()->getLineOne() : $dpoDefaultStreetAddress,
            ['size' => 12]
        );
        $section->addText(
            $hasDpo ? "{$collectivity->getAddress()->getZipCode()} {$collectivity->getAddress()->getCity()}" : $dpoDefaultZipCodeCity,
            ['size' => 12]
        );

        //CNIL
        $section->addText(
            'Déclaration CNIL',
            ['bold'        => true, 'size' => 15],
            ['spaceBefore' => 1000]
        );

        $section->addText(
            'Numéro de désignation CNIL : ' . $collectivity->getNbrCnil(),
            ['size' => 12]
        );

        $section->addText(
            "{$this->getDate(new \DateTimeImmutable(), 'd/m/Y')}",
            ['size' => 12]
        );
    }

    /**
     * Create a table of content.
     *
     * @param Section $section  The section on which to add content
     * @param int     $maxDepth The max depth for TOC generation
     */
    public function addTableOfContent(Section $section, int $maxDepth = 1)
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

        $section->addTOC(['bold' => true], null, 1, $maxDepth);
        $section->addPageBreak();
    }

    /**
     * Create the section used to generate document.
     *
     * @param PhpWord $document The word document to use
     * @param string  $title    The title to add in header
     *
     * @return Section The created section
     */
    public function createContentSection(PhpWord $document, string $title): Section
    {
        // Create section
        $section = $document->addSection();

        // add page header
        $header = $section->addHeader();
        $table  = $header->addTable([
            'borderColor' => '000000',
            'borderSize'  => 3,
            'cellMargin'  => 100,
            'unit'        => TblWidth::PERCENT,
            'width'       => 100 * 50,
        ]);
        $row  = $table->addRow(10, ['tblHeader' => true]);
        $cell = $row->addCell(15 * 50);
        $cell = $row->addCell(70 * 50, ['alignment' => Jc::CENTER]);
        $cell->addText($title, ['alignment' => Jc::CENTER, 'bold' => true]);
        $cell = $row->addCell(15 * 50, ['alignment' => Jc::CENTER]);
        $cell->addPreserveText('Page {PAGE}/{NUMPAGES}', ['alignment' => Jc::CENTER]);
        $header->addTextBreak();

        // Add page footer
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

    /**
     * Generate the response
     * - This response parse PhpWord to a Word document
     * - Prepare it in a BinaryFileResponse.
     *
     * @param string $documentName The document name to use
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return BinaryFileResponse The response
     */
    public function generateResponse(PhpWord $document, string $documentName): BinaryFileResponse
    {
        Settings::setOutputEscapingEnabled(true);
        $objWriter = IOFactory::createWriter($document, 'Word2007');

        $currentDate = (new \DateTimeImmutable())->format('Ymd');
        $fileName    = "{$currentDate}-{$documentName}.doc";
        $tempFile    = \tempnam(\sys_get_temp_dir(), $fileName);

        $objWriter->save($tempFile);

        // Create response and return it
        $response = new BinaryFileResponse($tempFile);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

    protected function addTable(Section $section, array $data = [], bool $header = false, string $orientation = self::TABLE_ORIENTATION_HORIZONTAL): void
    {
        $table = $section->addTable($this->tableStyle);
        $headersTable = $data[0];
        $table->addRow(null, array('tblHeader' => true));
        foreach ($headersTable as $element){
            $cell = $table->addCell(2500, $this->cellHeadStyle);
            $cell->addText($element, $this->textHeadStyle);
        }
        unset($data[0]);

        foreach ($data as $nbLine => $line) {
            $table->addRow();
            $lineData  = $line['data'] ?? $line;
            $lineStyle = $line['style'] ?? null;
            foreach ($lineData as $nbCol => $col) {
                $col = \is_array($col) ? $col : [$col];

                if ($header && self::TABLE_ORIENTATION_HORIZONTAL === $orientation && 0 === $nbLine
                    || $header && self::TABLE_ORIENTATION_VERTICAL === $orientation && 0 === $nbCol) {
                    $cell = $table->addCell(2500, $this->cellHeadStyle);
                    foreach ($col as $item) {
                        $cell->addText($item, $this->textHeadStyle);
                    }
                } else {
                    /* If a style for the cell is specified, it bypass the line style */
                    $cell    = $table->addCell(5000 / \count($lineData), $col['style'] ?? $lineStyle);
                    $textrun = $cell->addTextRun();
                    foreach ($col['content'] ?? $col as $key => $item) {
                        if (0 != $key) {
                            $textrun->addTextBreak();
                        }

                        // If item is simple text, there is no other configuration
                        if (!\is_array($item)) {
                            $textrun->addText($item);
                            continue;
                        }

                        /* If item is array, there is 2 possibility :
                            - this is an array of item to display in a single cell
                            - there is additionnal configuration */
                        if (isset($item['array']) && null !== $item['array']) {
                            foreach ($item['array'] as $subItemKey => $subItem) {
                                $textrun->addText($subItem);
                                if ($subItemKey !== count($item['array']) - 1) {
                                    $textrun->addTextBreak(2);
                                }
                            }
                        } else {
                            $textrun->addText(
                                $item['text'] ?? '',
                                $item['style'] ?? []
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Format a date for Word document.
     *
     * @param \DateTimeInterface|null $dateTime The date to parse
     *
     * @throws \Exception
     *
     * @return string The parsed date with the good timezone
     */
    protected function getDate(?\DateTimeInterface $dateTime, string $format = null): string
    {
        if (\is_null($dateTime)) {
            return '';
        }

        $format = $format ?? self::DATE_TIME_FORMAT;

        $parsedDateTime = new \DateTimeImmutable();
        $parsedDateTime = $parsedDateTime->setTimestamp($dateTime->getTimestamp());
        $parsedDateTime = $parsedDateTime->setTimezone(new \DateTimeZone(self::DATE_TIME_ZONE));

        return $parsedDateTime->format($format);
    }
}
