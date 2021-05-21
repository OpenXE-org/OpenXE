<?php

namespace Xentral\Modules\RetailPriceTemplate\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\RetailPriceTemplate\Exception\InvalidArgumentException;
use Xentral\Modules\RetailPriceTemplate\Exception\NotFoundException;

final class RetailPriceTemplateService
{
    /** @var Database $database */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param int $templateId
     *
     * @throws NotFoundException
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function deleteTemplate($templateId)
    {
        $this->ensureId($templateId, 'TemplateID');

        $affectedRows = (int)$this->db->fetchAffected(
            'DELETE FROM retail_price_template WHERE id = :template_id LIMIT 1',
            ['template_id' => $templateId]
        );

        if ($affectedRows === 0) {
            throw new NotFoundException('Template not found: ID ' . $templateId);
        }

        $this->db->perform(
            'DELETE FROM retail_price_template_assignment WHERE retail_price_template_id = :template_id',
            ['template_id' => $templateId]
        );

        $this->db->perform(
            'DELETE FROM retail_price_template_price WHERE template_id = :template_id',
            ['template_id' => $templateId]
        );
    }

    /**
     * @param int $templateId
     *
     * @throws NotFoundException
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getTemplate($templateId)
    {
        $this->ensureId($templateId, 'TemplateID');

        $template = $this->db->fetchRow(
            'SELECT rpt.id, rpt.description, rpt.active, CONCAT(a.nummer,\' \',a.name_de) AS articleNoAndName
            FROM retail_price_template AS rpt 
            JOIN artikel a ON a.id = rpt.article_id
            WHERE rpt.id = :template_id',
            ['template_id' => $templateId]
        );

        if (empty($template)) {
            throw new NotFoundException('Template not found: ID ' . $templateId);
        }

        return $template;
    }


    /**
     * @param int    $templateId
     * @param string $description
     * @param bool   $active
     * @param string $articleNo
     *
     * @return void
     */
    public function saveTemplate($templateId, $description, $active, $articleNo)
    {
        $articleId = $this->db->fetchValue(
            'SELECT id FROM artikel WHERE nummer <> \'\' AND nummer = :articleNo',
            ['articleNo' => $articleNo]
        );

        $this->ensureId($articleId, 'ArticleId');
        $this->ensureId($templateId, 'TemplateID');
        $this->ensureActive($active);

        $this->db->perform(
            'UPDATE retail_price_template AS rpt 
             SET rpt.description = :description, rpt.active = :active, rpt.article_id = :articleId
             WHERE rpt.id = :templateId',
            [
                'description' => $description,
                'active'      => $active,
                'articleId'   => $articleId,
                'templateId'  => $templateId,
            ]
        );
    }

    /***
     * @param string $description
     * @param bool   $active
     * @param string $articleNo
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    public function createTemplate($description, $active, $articleNo)
    {
        $articleId = $this->db->fetchValue(
            'SELECT id FROM artikel WHERE nummer <> \'\' AND nummer = :articleNo',
            ['articleNo' => $articleNo]
        );

        $this->ensureId($articleId, 'ArticleId');
        $this->ensureActive($active);

        $this->db->perform(
            'INSERT INTO retail_price_template  (description, active, article_id) 
             VALUES (:description, :active, :articleId)',
            [
                'description' => $description,
                'active'      => $active,
                'articleId'   => $articleId,
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param int    $templateId
     * @param string $name
     *
     * @return void
     */
    private function ensureId($templateId, $name = '')
    {
        if (empty($templateId) || (int)$templateId < 0) {
            throw new InvalidArgumentException('Required argument "' . $name . '" is empty or invalid.');
        }
    }

    /**
     * @param bool $active
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function ensureActive($active)
    {
        if (!is_bool($active)) {
            throw new InvalidArgumentException('Required argument "active" is invalid.');
        }
    }
}
