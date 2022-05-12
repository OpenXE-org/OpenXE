<?php

namespace Xentral\Modules\SystemHealth\Service;

interface SystemHealthServiceInterface
{


    /**
     * Create a SystemHealth
     *
     * @param int         $systemHealthCategoryId
     * @param string      $name
     * @param string      $description
     *
     * @return int Created SystemHealth-ID
     */
    public function create($systemHealthCategoryId, $name, $description = '');

    /**
     * Create a SystemHealth Category
     *
     * @param string      $name
     * @param string      $description
     *
     * @return int Created SystemHealthCategory-ID
     */
    public function createCategory($name, $description = '');


    /**
     * Delete SystemHealth by ID
     *
     * @param int $systemHealthId
     *
     * @return bool
     */
    public function delete($systemHealthId);

    /**
     * Delete SystemHealthCategory by ID
     *
     * @param int $systemHealthCategoryId
     *
     * @return bool
     */
    public function deleteCategory($systemHealthCategoryId);

}
