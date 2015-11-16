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

namespace Framework\Classes\PhpWord\Writer\Word2007\Part;

use Framework\Classes\PhpWord\Shared\XMLWriter;

/**
 * Word2007 contenttypes part writer: [Content_Types].xml
 */
class ContentTypes extends AbstractPart
{
    /**
     * Write part
     *
     * @return string
     */
    public function write()
    {
        /** @var \Framework\Classes\PhpWord\Writer\Word2007 $parentWriter Type hint */
        $parentWriter = $this->getParentWriter();
        $contentTypes = $parentWriter->getContentTypes();

        $openXMLPrefix = 'application/vnd.openxmlformats-';
        $wordMLPrefix  = $openXMLPrefix . 'officedocument.wordprocessingml.';
        $drawingMLPrefix  = $openXMLPrefix . 'officedocument.drawingml.';
        $overrides = array(
            '/docProps/core.xml'     => $openXMLPrefix . 'package.core-properties+xml',
            '/docProps/app.xml'      => $openXMLPrefix . 'officedocument.extended-properties+xml',
            '/docProps/custom.xml'   => $openXMLPrefix . 'officedocument.custom-properties+xml',
            '/word/document.xml'     => $wordMLPrefix  . 'document.main+xml',
            '/word/styles.xml'       => $wordMLPrefix  . 'styles+xml',
            '/word/numbering.xml'    => $wordMLPrefix  . 'numbering+xml',
            '/word/settings.xml'     => $wordMLPrefix  . 'settings+xml',
            '/word/theme/theme1.xml' => $openXMLPrefix . 'officedocument.theme+xml',
            '/word/webSettings.xml'  => $wordMLPrefix  . 'webSettings+xml',
            '/word/fontTable.xml'    => $wordMLPrefix  . 'fontTable+xml',
        );

        $defaults = $contentTypes['default'];
        if (!empty($contentTypes['override'])) {
            foreach ($contentTypes['override'] as $key => $val) {
                if ($val == 'chart') {
                    $overrides[$key] = $drawingMLPrefix . $val . '+xml';
                } else {
                    $overrides[$key] = $wordMLPrefix . $val . '+xml';
                }
            }
        }

        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('Types');
        $xmlWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/content-types');

        $this->writeContentType($xmlWriter, $defaults, TRUE);
        $this->writeContentType($xmlWriter, $overrides, FALSE);

        $xmlWriter->endElement(); // Types

        return $xmlWriter->getData();
    }

    /**
     * Write content types element
     *
     * @param \Framework\Classes\PhpWord\Shared\XMLWriter $xmlWriter XML Writer
     * @param array $parts
     * @param boolean $isDefault
     * @return void
     */
    private function writeContentType(XMLWriter $xmlWriter, $parts, $isDefault)
    {
        foreach ($parts as $partName => $contentType) {
            $partType = $isDefault ? 'Default' : 'Override';
            $partAttribute = $isDefault ? 'Extension' : 'PartName';
            $xmlWriter->startElement($partType);
            $xmlWriter->writeAttribute($partAttribute, $partName);
            $xmlWriter->writeAttribute('ContentType', $contentType);
            $xmlWriter->endElement();
        }
    }
}
