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

namespace Framework\Classes\PhpWord\Writer;

use Framework\Classes\PhpWord\PhpWord;

/**
 * HTML writer
 *
 * Not supported: PreserveText, PageBreak, Object
 * @since 0.10.0
 */
class HTML extends AbstractWriter implements WriterInterface
{
    /**
     * Is the current writer creating PDF?
     *
     * @var boolean
     */
    protected $isPdf = FALSE;

    /**
     * Footnotes and endnotes collection
     *
     * @var array
     */
    protected $notes = array();

    /**
     * Create new instance
     */
    public function __construct(PhpWord $phpWord = null)
    {
        $this->setPhpWord($phpWord);

        $this->parts = array('Head', 'Body');
        foreach ($this->parts as $partName) {
            $partClass = 'Framework\Classes\\PhpWord\\Writer\\HTML\\Part\\' . $partName;
            if (class_exists($partClass)) {
                /** @var \Framework\Classes\PhpWord\Writer\HTML\Part\AbstractPart $part Type hint */
                $part = new $partClass();
                $part->setParentWriter($this);
                $this->writerParts[strtolower($partName)] = $part;
            }
        }
    }

    /**
     * Save PhpWord to file.
     *
     * @param string $filename
     * @return void
     * @throws \Framework\Classes\PhpWord\Exception\Exception
     */
    public function save($filename = null)
    {
        $this->writeFile($this->openFile($filename), $this->getContent());
    }

    /**
     * Get content
     *
     * @return string
     * @since 0.11.0
     */
    public function getContent()
    {
        $content = '';

        $content .= '<!DOCTYPE html>' . PHP_EOL;
        $content .= '<!-- Generated by PHPWord -->' . PHP_EOL;
        $content .= '<html>' . PHP_EOL;
        $content .= $this->getWriterPart('Head')->write();
        $content .= $this->getWriterPart('Body')->write();
        $content .= '</html>' . PHP_EOL;

        return $content;
    }

    /**
     * Get is PDF
     *
     * @return bool
     */
    public function isPdf()
    {
        return $this->isPdf;
    }

    /**
     * Get notes
     *
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add note.
     *
     * @param int $noteId
     * @param string $noteMark
     * @return void
     */
    public function addNote($noteId, $noteMark)
    {
        $this->notes[$noteId] = $noteMark;
    }

    /**
     * Write document
     *
     * @return string
     * @deprecated 0.11.0
     * @codeCoverageIgnore
     */
    public function writeDocument()
    {
        return $this->getContent();
    }
}
