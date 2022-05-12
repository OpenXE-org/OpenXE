<?php

namespace Xentral\Widgets\DataTable;

use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Widgets\DataTable\Request\DataTableRequest;
use Xentral\Widgets\DataTable\Service\DataTableBuilder;
use Xentral\Widgets\DataTable\Service\DataTableFetcher;
use Xentral\Widgets\DataTable\Service\DataTableRenderer;
use Xentral\Widgets\DataTable\Service\DataTableRequestHandler;
use Xentral\Widgets\DataTable\Service\DataTableService;

final class DataTableFactory
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return DataTableRequestHandler
     */
    public function createDataTableRequestHandler()
    {
        return new DataTableRequestHandler($this->createDataTableService(), $this->createDataTableRequest());
    }

    /**
     * @return DataTableService
     */
    public function createDataTableService()
    {
        return new DataTableService(
            $this->createDataTableBuilder(),
            $this->createDataTableRenderer(),
            $this->createDataTableFetcher()
        );
    }

    /**
     * @return DataTableBuilder
     */
    private function createDataTableBuilder()
    {
        return new DataTableBuilder($this->container->get('Database'));
    }

    /**
     * @return DataTableRenderer
     */
    private function createDataTableRenderer()
    {
        return new DataTableRenderer();
    }

    /**
     * @return DataTableFetcher
     */
    private function createDataTableFetcher()
    {
        return new DataTableFetcher($this->container->get('Database'), $this->createDataTableRequest());
    }

    /**
     * @return DataTableRequest
     */
    private function createDataTableRequest()
    {
        return DataTableRequest::fromRequest($this->container->get('Request'));
    }
}
