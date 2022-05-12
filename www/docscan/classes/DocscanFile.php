<?php

use Sabre\DAV\IFile;

class DocscanFile implements IFile
{
    /** @var string $name */
    var $name;

    /** @var int $id */
    var $id;

    /** @var ApplicationCore $app */
    var $app;

    /**
     * @param string          $name
     * @param int             $id
     * @param ApplicationCore $app
     */
    public function __construct($name, $id, $app)
    {
        $this->name = $name;
        $this->id = $id;
        $this->app = $app;
    }

    /**
     * @inheritdoc
     */
    function put($data)
    {
        $path = $this->app->erp->GetDateiPfad($this->id);
        file_put_contents($path, $data);
    }

    /**
     * @inheritdoc
     */
    function get()
    {
        $path = $this->app->erp->GetDateiPfad($this->id);

        return file_get_contents($path);
    }

    /**
     * @inheritdoc
     */
    function getContentType()
    {
        $path = $this->app->erp->GetDateiPfad($this->id);

        return mime_content_type($path);
    }

    /**
     * @inheritdoc
     */
    function getETag()
    {
        // TODO: Implement getETag() method.
    }

    /**
     * @inheritdoc
     */
    function getSize()
    {
        $path = $this->app->erp->GetDateiPfad($this->id);

        return filesize($path);
    }

    /**
     * @throws LogicException
     */
    function delete()
    {
        throw new LogicException('Bitte nur über Xentral löschen');
        //$this->app->erp->DeleteDatei($this->id);
    }

    /**
     * @inheritdoc
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    function setName($name)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    function getLastModified()
    {
        return null;
    }
}
