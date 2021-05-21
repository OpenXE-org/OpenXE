<?php

use Sabre\DAV\Exception\NotFound;
use \Sabre\DAV\ICollection;

class DocscanRoot implements ICollection
{
    /** @var array $children */
    var $children = ['adresse', 'bestellung', 'kassenbuch', 'reisekosten', 'verbindlichkeit'];

    /** @var ApplicationCore $app */
    var $app;

    /** @var string $tableName */
    var $tableName = 'docscan';

    /**
     * @param ApplicationCore $app
     */
    function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @inheritdoc
     */
    function createFile($name, $data = null, $kategorie = '')
    {
        $name = basename($name);

        $user = $this->app->DB->Select(
            "SELECT a.name 
            FROM `user` AS u
            INNER JOIN adresse AS a ON u.adresse = a.id 
            WHERE u.username = '{$this->app->DB->real_escape_string($_SERVER['PHP_AUTH_USER'])}' 
            AND u.activ = 1"
        );

        $this->app->DB->Insert("INSERT INTO $this->tableName (kategorie) VALUES ('$kategorie');");
        $id = $this->app->DB->GetInsertID();

        //function CreateDatei($name,$titel,$beschreibung,$nummer,$datei,$ersteller,$without_log=false,$path="")
        $fileId = $this->app->erp->CreateDatei($name, $name, 'Hochgeladen von Scanbot', '', '', $user);
        $this->app->erp->AddDateiStichwort($fileId, 'Bild', 'DocScan', $id);
        $this->app->DB->Update("UPDATE $this->tableName SET datei = '$fileId' WHERE id = '$id';");

        if ($data != null) {
            $path = $this->app->erp->GetDateiPfad($fileId);
            $this->app->DB->Query('UPDATE datei_version SET size=' . file_put_contents($path, $data) . " WHERE datei=$fileId;");
        }
    }

    /**
     * @throws LogicException
     */
    function createDirectory($name)
    {
        throw new LogicException('Keine Ordner erstellbar');
    }

    /**
     * @inheritdoc
     *
     * @throws NotFound
     */
    function getChild($name)
    {
        if (!$this->childExists($name)) {
            throw new NotFound('Not found');
        }

        return new DocscanDir($name, $this->app, $this);
    }

    /**
     * @inheritdoc
     */
    function getChildren()
    {
        $childFiles = [];
        foreach ($this->children as $child) {
            $childFiles[] = new DocscanDir($child, $this->app, $this);
        }

        return $childFiles;
    }

    /**
     * @inheritdoc
     */
    function childExists($name)
    {
        return in_array($name, $this->children, true);
    }

    /**
     * @inheritdoc
     */
    function delete()
    {
        throw new LogicException('Wurzelverzeichnis kann nicht gelöscht werden');
    }

    /**
     * @inheritdoc
     */
    function getName()
    {
        return "Xentral";
    }

    /**
     * @param string $name
     *
     * @throws LogicException
     */
    function setName($name)
    {
        throw new LogicException('Wurzelverzeichnis kann nicht umbenannt werden');
    }

    /**
     * @inheritdoc
     */
    function getLastModified()
    {
        // TODO: Implement getLastModified() method.
        return null;
    }
}
