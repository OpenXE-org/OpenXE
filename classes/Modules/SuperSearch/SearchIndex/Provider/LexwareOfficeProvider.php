<?php

declare(strict_types=1);

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use Xentral\Modules\SuperSearch\SearchIndex\Collection\ItemFormatterCollection;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class LexwareOfficeProvider implements FullIndexProviderInterface, ItemIndexProviderInterface
{
    public function getModuleName(): ?string
    {
        return 'lexwareoffice';
    }

    public function getIndexName()
    {
        return 'lexwareoffice';
    }

    public function getIndexTitle()
    {
        return 'Lexware Office';
    }

    public function getItem(IndexIdentifier $identifier)
    {
        if ($identifier->getId() !== 'settings') {
            return null;
        }

        return $this->buildItem();
    }

    public function getAllItems()
    {
        $callback = function () {
            return $this->buildItem();
        };

        return new ItemFormatterCollection(['settings'], $callback);
    }

    private function buildItem(): IndexItem
    {
        $title = 'Lexware Office Einstellungen';
        $link = 'index.php?module=lexwareoffice&action=edit';
        $data = new IndexData($title, $link, 0);
        $data->addSearchWord('lexware');
        $data->addSearchWord('lexware office');
        $data->addSearchWord('lexware api');
        $data->addSearchWord('lexware schluessel');
        $data->addSearchWord('lexware einstellungen');
        $identifier = new IndexIdentifier($this->getIndexName(), 'settings');

        return new IndexItem($identifier, $data);
    }
}
