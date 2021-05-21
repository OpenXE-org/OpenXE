<?php

namespace Xentral\Modules\Company\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Company\Exception\InvalidArgumentException;

final class DocumentCustomizationService
{
    /** @var Database */
    private $db;

    /** @var DocumentCustomizationBlockParser */
    private $parser;

    /**
     * DocumentCustomizationService constructor.
     *
     * @param Database                         $db
     * @param DocumentCustomizationBlockParser $parser
     */
    public function __construct(Database $db, DocumentCustomizationBlockParser $parser)
    {
        $this->db = $db;
        $this->parser = $parser;
    }

    /**
     * @param string $keyword
     * @param string $doctype
     * @param string $content
     * @param string $fontStyle
     * @param bool   $active
     * @param string $alignment
     */
    public function createBlock($keyword, $doctype, $content, $fontStyle, $projecId, $active, $alignment)
    {
        $this->db->perform(
            'INSERT INTO document_customization_infoblock 
            (keyword, doctype, fontstyle, content, active, project_id, alignment)
            VALUES (:keyword, :doctype, :fontstyle, :content, :active, :project_id, :alignment)',
            [
                'keyword'    => $keyword,
                'doctype'    => $doctype,
                'fontstyle'  => $fontStyle,
                'content'    => $content,
                'active'     => (int)$active,
                'project_id' => $projecId,
                'alignment'  => $alignment
            ]
        );
    }

    /**
     * @param int    $documentCustomizationInfoBlockId
     * @param string $doctype
     * @param string $content
     * @param string $fontStyle
     * @param int    $projectId
     * @param bool   $active
     * @param string $alignment
     */
    public function updateInfoBlock(
        $documentCustomizationInfoBlockId,
        $doctype,
        $content,
        $fontStyle,
        $projectId,
        $active,
        $alignment
    ) {
        $documentCustomizationBlockId = $this->getBlock($documentCustomizationInfoBlockId);
        if ($documentCustomizationBlockId === false) {
            throw new InvalidArgumentException('id not found: ' . $documentCustomizationInfoBlockId);
        }
        $this->db->perform(
            'UPDATE document_customization_infoblock 
                SET content = :content, fontstyle = :fontstyle, active = :active, project_id = :project_id, 
                    doctype = :doctype, alignment = :alignment
                WHERE id = :id',
            [
                'content'    => $content,
                'fontstyle'  => $fontStyle,
                'project_id' => (int)$projectId,
                'doctype'    => $doctype,
                'active'     => (int)$active,
                'id'         => (int)$documentCustomizationInfoBlockId,
                'alignment'  => $alignment
            ]
        );
    }

    /**
     * @param string $languageCode
     * @param int    $documentCustomizationBlockId
     * @param string $content
     * @param bool   $active
     * @param string $fontstyle
     * @param string $alignment
     */
    public function saveTranslation($documentCustomizationBlockId, $languageCode, $content, $active, $fontstyle, $alignment)
    {
        if ($documentCustomizationBlockId === false) {
            throw new $documentCustomizationBlockId('block not found');
        }
        $documentCustomizationInfoBlockTranslationId = $this->getTranslationBlock(
            $documentCustomizationBlockId,
            $languageCode
        );

        if ($documentCustomizationInfoBlockTranslationId === false) {
            $this->db->perform(
                'INSERT INTO document_customization_infoblock_translation 
                (document_customization_infoblock_id, language_code, content, active, fontstyle, alignment)
                VALUES (:document_customization_infoblock_id, :language_code, :content, :active, :fontstyle, :alignment)',
                [
                    'document_customization_infoblock_id' => (int)$documentCustomizationBlockId,
                    'language_code'                       => $languageCode,
                    'content'                             => $content,
                    'active'                              => (int)$active,
                    'fontstyle'                           => $fontstyle,
                    'alignment'                           => $alignment
                ]
            );

            return;
        }

        $this->db->perform(
            'UPDATE document_customization_infoblock_translation 
            SET content = :content, active = :active, fontstyle = :fontstyle, alignment = :alignment
            WHERE id = :id',
            [
                'id'        => (int)$documentCustomizationInfoBlockTranslationId,
                'content'   => $content,
                'active'    => (int)$active,
                'fontstyle' => $fontstyle,
                'alignment' => $alignment
            ]
        );
    }

    /**
     * @param int $documentCustomizationInfoBlockId
     *
     * @throws InvalidArgumentException
     */
    public function delete($documentCustomizationInfoBlockId)
    {
        if (!$this->getBlock($documentCustomizationInfoBlockId)) {
            throw new InvalidArgumentException('customaziationblock not found id ' . $documentCustomizationInfoBlockId);
        }

        $this->db->perform(
            'DELETE FROM document_customization_infoblock_translation WHERE document_customization_infoblock_id = :id',
            ['id' => (int)$documentCustomizationInfoBlockId]
        );

        $this->db->perform(
            'DELETE FROM document_customization_infoblock WHERE id = :id',
            ['id' => (int)$documentCustomizationInfoBlockId]
        );
    }

    /**
     * @param int $documentCustomizationInfoBlockId
     */
    public function copy($documentCustomizationInfoBlockId)
    {
        if (!$this->getBlock($documentCustomizationInfoBlockId)) {
            throw new InvalidArgumentException('customaziationblock not found id ' . $documentCustomizationInfoBlockId);
        }

        $this->db->perform(
            'INSERT INTO document_customization_infoblock (keyword, doctype, fontstyle, content, active, project_id, alignment) 
            SELECT keyword, doctype, fontstyle, content, active, project_id, alignment FROM document_customization_infoblock 
            WHERE id = :id',
            ['id' => (int)$documentCustomizationInfoBlockId]
        );

        $newDocumentCustomizationInfoBlockId = $this->db->lastInsertId();

        $this->db->perform('INSERT INTO document_customization_infoblock_translation 
            (language_code, content, active, document_customization_infoblock_id, alignment, fontstyle)
            SELECT  language_code, content, active, :new_id , alignment, fontstyle
            FROM document_customization_infoblock_translation WHERE document_customization_infoblock_id = :id',
            [
                'new_id' => (int)$newDocumentCustomizationInfoBlockId,
                'id'     => (int)$documentCustomizationInfoBlockId,
            ]
        );
    }

    /**
     * @param int $documentCustomizationInfoBlockId
     *
     * @return false|array
     */
    public function getBlock($documentCustomizationInfoBlockId)
    {
        return $this->db->fetchRow(
            'SELECT id, keyword, doctype, fontstyle, content, active, project_id, alignment
            FROM document_customization_infoblock 
            WHERE id = :id',
            ['id' => (int)$documentCustomizationInfoBlockId]
        );
    }

    /**
     * @param string $languageCode
     * @param int    $documentCustomizationBlockId
     *
     * @return array
     */
    public function getTranslationByCustomizationInfoBlockId($languageCode, $documentCustomizationBlockId)
    {
        return $this->db->fetchRow(
            'SELECT id, content, active, language_code, document_customization_infoblock_id, fontstyle, alignment
            FROM document_customization_infoblock_translation 
            WHERE document_customization_infoblock_id = :blockid AND language_code = :language_code',
            ['blockid' => (int)$documentCustomizationBlockId, 'language_code' => $languageCode]
        );
    }

    /**
     * @param string $languageCode
     * @param string $keyword
     * @param string $doctype
     * @param array  $variables
     * @param int    $projectId
     *
     * @return string
     */
    public function parseBlock($languageCode, $keyword, $doctype, $variables, $projectId = 0)
    {
        $block = $this->findTranslation($languageCode, $keyword, $doctype, $projectId);
        if (empty($block)) {
            return '';
        }

        return $this->parser->parse($block['content'], $variables);
    }

    /**
     * @param string $languageCode
     * @param string $keyword
     * @param string $doctype
     * @param int    $projectId
     *
     * @return array|bool
     */
    public function findTranslation($languageCode, $keyword, $doctype, $projectId = 0)
    {
        $block = $this->findActiveBlock($keyword, $doctype, $projectId);
        if (empty($block)) {
            return false;
        }

        $translation = $this->db->fetchRow(
            'SELECT * FROM document_customization_infoblock_translation
            WHERE document_customization_infoblock_id = :block_id AND language_code = :language_code',
            ['block_id' => $block['id'], 'language_code' => $languageCode]
        );
        if (!empty($translation)) {
            return $translation;
        }

        return $block;
    }

    /**
     * @param string $languageCode
     * @param string $keyword
     * @param string $doctype
     * @param array  $variables
     * @param int    $projectId
     *
     * @return array
     */
    public function parseBlockAsArray($languageCode, $keyword, $doctype, $variables, $projectId = 0)
    {
        $string = $this->parseBlock($languageCode, $keyword, $doctype, $variables, $projectId);
        $sCD = [];
        $elements = explode("\n", $string);
        foreach ($elements as $key => $el) {
            if (!empty($elements[$key])) {
                $row = explode('|', $elements[$key], 2);
                $sCD[trim(rtrim(trim($row[0]), ':'))] = !empty($row[1]) ? $row[1] : '';
            }
        }

        return $sCD;
    }

    /**
     * @param string $keyword
     * @param string $doctype
     * @param string $projectId
     *
     * @return bool
     */
    public function isBlockActive($keyword, $doctype, $projectId = 0)
    {
        $infoBlocks = $this->findActiveBlock($keyword, $doctype, $projectId);

        return !empty($infoBlocks);
    }

    /**
     * @param string $keyword
     * @param string $doctype
     * @param string $projectId
     *
     * @return array
     */
    public function findActiveBlock($keyword, $doctype, $projectId)
    {
        return $this->db->fetchRow(
            'SELECT * 
            FROM document_customization_infoblock
            WHERE active = 1 AND keyword = :keyword AND doctype = :doctype
            AND (project_id = 0 OR project_id = :project_id)
            LIMIT 1',
            ['keyword' => $keyword, 'doctype' => $doctype, 'project_id' => $projectId]
        );
    }

    /**
     * @param int    $blockId
     * @param string $languageCode
     *
     * @return false|int
     */
    private function getTranslationBlock($blockId, $languageCode)
    {
        return $this->db->fetchValue(
            'SELECT id 
            FROM document_customization_infoblock_translation 
            WHERE document_customization_infoblock_id = :blockid AND language_code = :language_code',
            ['blockid' => (int)$blockId, 'language_code' => $languageCode]
        );
    }
}