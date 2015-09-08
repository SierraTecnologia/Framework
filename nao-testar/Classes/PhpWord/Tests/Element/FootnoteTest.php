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

namespace Framework\Classes\PhpWord\Tests\Element;

use Framework\Classes\PhpWord\Element\Footnote;

/**
 * Test class for Framework\Classes\PhpWord\Element\Footnote
 *
 * @runTestsInSeparateProcesses
 */
class FootnoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * New instance without parameter
     */
    public function testConstruct()
    {
        $oFootnote = new Footnote();

        $this->assertInstanceOf('Framework\Classes\\PhpWord\\Element\\Footnote', $oFootnote);
        $this->assertCount(0, $oFootnote->getElements());
        $this->assertEquals($oFootnote->getParagraphStyle(), null);
    }

    /**
     * New instance with string parameter
     */
    public function testConstructString()
    {
        $oFootnote = new Footnote('pStyle');

        $this->assertEquals($oFootnote->getParagraphStyle(), 'pStyle');
    }

    /**
     * New instance with array parameter
     */
    public function testConstructArray()
    {
        $oFootnote = new Footnote(array('spacing' => 100));

        $this->assertInstanceOf(
            'Framework\Classes\\PhpWord\\Style\\Paragraph',
            $oFootnote->getParagraphStyle()
        );
    }

    /**
     * Add text element
     */
    public function testAddText()
    {
        $oFootnote = new Footnote();
        $element = $oFootnote->addText('text');

        $this->assertCount(1, $oFootnote->getElements());
        $this->assertInstanceOf('Framework\Classes\\PhpWord\\Element\\Text', $element);
    }

    /**
     * Add text break element
     */
    public function testAddTextBreak()
    {
        $oFootnote = new Footnote();
        $oFootnote->addTextBreak(2);

        $this->assertCount(2, $oFootnote->getElements());
    }

    /**
     * Add link element
     */
    public function testAddLink()
    {
        $oFootnote = new Footnote();
        $element = $oFootnote->addLink('http://www.google.fr');

        $this->assertCount(1, $oFootnote->getElements());
        $this->assertInstanceOf('Framework\Classes\\PhpWord\\Element\\Link', $element);
    }

    /**
     * Set/get reference Id
     */
    public function testReferenceId()
    {
        $oFootnote = new Footnote();

        $iVal = rand(1, 1000);
        $oFootnote->setRelationId($iVal);
        $this->assertEquals($oFootnote->getRelationId(), $iVal);
    }

    /**
     * Get elements
     */
    public function testGetElements()
    {
        $oFootnote = new Footnote();
        $this->assertInternalType('array', $oFootnote->getElements());
    }
}