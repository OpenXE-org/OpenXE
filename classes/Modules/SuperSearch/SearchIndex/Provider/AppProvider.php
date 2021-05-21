<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use Appstore;
use Exception;
use Xentral\Modules\SuperSearch\SearchIndex\Collection\ItemFormatterCollection;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class AppProvider implements FullIndexProviderInterface, ItemIndexProviderInterface
{
    /** @var Appstore $appstore */
    private $appstore;

    /**
     * @param Appstore $appstore
     */
    public function __construct(Appstore $appstore)
    {
        $this->appstore = $appstore;
    }

    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
       return 'appstore';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'apps';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Apps';
    }

    /**
     * @inheritDoc
     */
    public function getItem(IndexIdentifier $identifier)
    {
        $moduleKey = $identifier->getId();
        $modules = $this->appstore->BuildModuleList();
        if (!isset($modules[$moduleKey])) {
            return null;
        }

        $formatter = $this->getRowFormatter();

        return $formatter($modules[$moduleKey]);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function getAllItems()
    {
        $callback = $this->getRowFormatter();

        $modules = $this->appstore->BuildModuleList();
        unset($modules['appstore_extern']);

        // Module ohne Link entfernen
        foreach ($modules as $moduleName => $moduleData) {
            if (empty($moduleData['module_link'])) {
                unset($modules[$moduleName]);
            }
        }

        return new ItemFormatterCollection($modules, $callback);
    }

    /**
     * @inheritDoc
     */
    protected function getRowFormatter()
    {
        return static function (array $module) {
            $projectId = 0;
            $data = new IndexData($module['title'], $module['module_link'], $projectId);
            $data->addSearchWord($module['title']);
            $data->addSearchWord(html_entity_decode($module['title']));
            $data->addSearchWord($module['description']);
            $data->addSearchWord($module['category']);
            $identifier = new IndexIdentifier('apps', $module['key']);

            return new IndexItem($identifier, $data);
        };
    }
}
