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
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class Generator
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
            'unit'        => Style\Table::WIDTH_PERCENT,
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
        $document->addTitleStyle(
            2,
            [
                'bold'  => true,
                'color' => '3c8dbc',
                'size'  => 20,
            ],
            [
                'alignment'  => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                'spaceAfter' => 500,
            ]
        );
        $document->addTitleStyle(
            3,
            [
                'bold' => true,
                'size' => 16,
            ],
            [
                'spaceAfter' => 250,
            ]
        );
    }

    public function generateHeader(PhpWord $document, string $title): void
    {
        $this->defineStyle($document);

        $currentDateTime = new \DateTimeImmutable('now', new \DateTimeZone(self::DATE_TIME_ZONE));
        $section         = $document->addSection();
        $section->addText(
            $title,
            [
                'bold'  => true,
                'color' => '3c8dbc',
                'size'  => 36,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 5000,
            ]
        );
        $section->addText(
            $this->userProvider->getAuthenticatedUser()->getCollectivity(),
            [
                'bold'   => true,
                'italic' => true,
                'size'   => 20,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 100,
            ]
        );
        $section->addText(
            "Édition du {$currentDateTime->format(self::DATE_TIME_FORMAT)}",
            [
                'size' => 15,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 3000,
            ]
        );
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

    abstract public function generateOverview(PhpWord $document, array $data): void;

    abstract public function generateDetails(PhpWord $document, array $data): void;

    protected function addTable(Section $section, array $data = [], bool $header = false, string $orientation = self::TABLE_ORIENTATION_HORIZONTAL): void
    {
        $table = $section->addTable($this->tableStyle);

        foreach ($data as $nbLine => $line) {
            $table->addRow();
            foreach ($line as $nbCol => $col) {
                if ($header && self::TABLE_ORIENTATION_HORIZONTAL === $orientation && 0 === $nbLine
                    || $header && self::TABLE_ORIENTATION_VERTICAL === $orientation && 0 === $nbCol) {
                    $cell = $table->addCell(2500, $this->cellHeadStyle);
                    $cell->addText($col, $this->textHeadStyle);
                } else {
                    $cell = $table->addCell();
                    $cell->addText($col);
                }
            }
        }

        $section->addTextBreak(2);
    }
}
