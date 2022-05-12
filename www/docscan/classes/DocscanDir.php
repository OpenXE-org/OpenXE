<?php

use Sabre\DAV\ICollection;

class DocscanDir implements ICollection
{
    /** @var string $dirType */
    var $dirType;

    /** @var ApplicationCore $app */
    var $app;

    /** @var array $children */
    var $children = [];

    /**
     * @var DocscanRoot $root
     */
    var $root;

    /**
     * @param string          $type
     * @param ApplicationCore $app
     */
    function __construct($type, $app, $root)
    {
        $this->dirType = $type;
        $this->app = $app;
        $this->root = $root;

        $ar = $app->DB->SelectArr(
            "SELECT d.titel, d.id
            FROM docscan as s 
            INNER JOIN datei AS d 
            ON d.id = s.datei 
            INNER JOIN datei_stichwoerter AS ds 
            ON ds.datei = d.id 
            WHERE ds.objekt LIKE '$type%'
            ;"
        );

        if ($ar) {
            foreach ($ar as $file) {
                $this->children[] = new DocscanFile($file['titel'], $file['id'], $app);
            }
        }
    }

    /**
     * @param string          $name Name of the file
     * @param resource|string $data Initial payload
     *
     * @throws LogicException
     */
    function createFile($name, $data = null)
    {
        $this->root->createFile($name, $data, $this->dirType);
    }

    /**
     * @param string $name
     *
     * @throws LogicException
     */
    function createDirectory($name)
    {
        throw new LogicException('Keine Ordner erstellbar');
    }

    /**
     * @inheritdoc
     */
    function getChild($name)
    {
        foreach ($this->children as $file) {
            if ($file->name === $name) {
                return $file;
            }
        }

        throw new \Sabre\DAV\Exception\NotFound();
    }

    /**
     * @inheritdoc
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    function childExists($name)
    {
        foreach ($this->children as $file) {
            if ($file->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws LogicException
     */
    function delete()
    {
        throw new LogicException('Kategorienordner können nicht gelöscht werden');
    }

    /**
     * @inheritdoc
     */
    function getName()
    {
        return $this->dirType;
    }

    /**
     * @param string $name
     *
     * @throws LogicException
     */
    function setName($name)
    {
        throw new LogicException('Kategorienordner kann nicht umbenannt werden');
    }

    /**
     * @inheritdoc
     */
    function getLastModified()
    {
        return null;
    }
}
