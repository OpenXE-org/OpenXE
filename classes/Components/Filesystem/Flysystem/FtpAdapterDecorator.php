<?php

namespace Xentral\Components\Filesystem\Flysystem;

use League\Flysystem\Adapter\Ftp;

final class FtpAdapterDecorator extends Ftp
{
    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        $metadata = parent::getMetadata($path);
        if ($metadata === false) {
            return false; // File does not exist
        }

        if ($metadata['type'] === 'dir') {
            $metadata['timestamp'] = null; // ftp_mdtm() does not work with directories.
            return $metadata;
        }

        if ($metadata['timestamp'] === null) {
            $data = $this->getTimestamp($path);
            $metadata['timestamp'] = $data !== false && isset($data['timestamp']) ? $data['timestamp'] : null;
        }

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function normalizeListing(array $listing, $prefix = '')
    {
        $result = parent::normalizeListing($listing, $prefix);

        foreach ($result as &$item) {
            if ($item['type'] === 'dir') {
                $item['timestamp'] = null; // ftp_mdtm() does not work with directories.
                continue;
            }
            if (!isset($item['timestamp'])) {
                $data = $this->getTimestamp($item['path']);
                $item['timestamp'] = $data !== false && isset($data['timestamp']) ? $data['timestamp'] : null;
            }
        }

        return $result;
    }
}
