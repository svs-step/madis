<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var UserProvider
     */
    protected $userProvider;

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

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;

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

    public function initializeDocument(PhpWord $document): void
    {
        // CONFIGURATION
        $document->getSettings()->setThemeFontLang(new Style\Language(Style\Language::FR_FR));
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
            1,
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
     * @param PhpWord $document
     * @param string  $title
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
        $hasDpo = $collectivity->isDifferentDpo();
        $section->addText(
            $hasDpo ? $collectivity->getDpo()->getFullName() : 'SOLURIS',
            ['size' => 12]
        );
        $section->addText(
            $hasDpo ? $collectivity->getAddress()->getLineOne() : '2 rue des Rochers',
            ['size' => 12]
        );
        $section->addText(
            $hasDpo ? "{$collectivity->getAddress()->getZipCode()} {$collectivity->getAddress()->getCity()}" : '17100 Saintes',
            ['size' => 12]
        );

        $section->addText(
            "{$this->getDate(new \DateTimeImmutable(), 'd/m/Y')}",
            ['italic'    => true],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 1000]
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
        $section->addTOC(null, null, 1, $maxDepth);
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
        $objWriter = IOFactory::createWriter($document, 'Word2007');

        $currentDate = (new \DateTimeImmutable())->format('Ymd');
        $fileName    = "{$currentDate}-{$documentName}.doc";
        $temp_file   = \tempnam(\sys_get_temp_dir(), $fileName);

        $objWriter->save($temp_file);

        // Create response and return it
        $response = new BinaryFileResponse($temp_file);
        $response->headers->set('Content-Type', 'application/msword');
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
                    $cell    = $table->addCell();
                    $textrun = $cell->addTextRun();
                    foreach ($col as $key => $item) {
                        if (0 != $key) {
                            $textrun->addTextBreak();
                        }
                        $textrun->addText($item);
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
