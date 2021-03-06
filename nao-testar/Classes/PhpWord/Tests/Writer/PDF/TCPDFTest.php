<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */
namespace Framework\Classes\PhpWord\Tests\Writer\PDF;

use Framework\Classes\PhpWord\PhpWord;
use Framework\Classes\PhpWord\Settings;
use Framework\Classes\PhpWord\Writer\PDF;

/**
 * Test class for Framework\Classes\PhpWord\Writer\PDF\TCPDF
 *
 * @runTestsInSeparateProcesses
 */
class TCPDFTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test construct
     */
    public function testConstruct()
    {
        $file = __DIR__ . "/../../_files/tcpdf.pdf";

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Test 1');

        $rendererName = Settings::PDF_RENDERER_TCPDF;
        $rendererLibraryPath = realpath(PHPWORD_TESTS_BASE_DIR . '/../vendor/tecnick.com/tcpdf');
        Settings::setPdfRenderer($rendererName, $rendererLibraryPath);
        $writer = new PDF($phpWord);
        $writer->save($file);

        $this->assertTrue(file_exists($file));

        unlink($file);
    }
}
