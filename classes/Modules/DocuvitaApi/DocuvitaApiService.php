<?php

namespace Xentral\Modules\DocuvitaApi;

use Xentral\Modules\DocuvitaApi\Exception\CurlException;
use Xentral\Modules\DocuvitaApi\Exception\FileNotFoundException;
use Xentral\Modules\DocuvitaApi\Exception\UnauthorizedException;

/**
 * Class DocuvitaApiService
 *
 * @package Xentral\Modules\DocuvitaApi
 */
class DocuvitaApiService
{
    /** @var string ENDPOINT_SETOBJECTPROPERTIES */
    const ENDPOINT_SETOBJECTPROPERTIES = 'setobjectproperties';

    /** @var string ENDPOINT_GETOBJECTPROPERTIES */
    const ENDPOINT_GETOBJECTPROPERTIES = 'getobjectproperties';

    /** @var string ENDPOINT_SETVERSION */
    const ENDPOINT_SETVERSION = 'setversion';

    /** @var string ENDPOINT_GETTREE */
    const ENDPOINT_GETTREE = 'gettree';

    /** @var string ENDPOINT_FILEUPLOAD */
    const ENDPOINT_FILEUPLOAD = 'fileupload';

    /** @var int $objectTypeIDReceipt */
    private $objectTypeIDReceipt;

    /** @var int $objectTypeIDFolder */
    private $objectTypeIDFolder;

    /** @var string $folderID */
    private $folderID;

    /** @var string $sessionGUID */
    private $sessionGUID;

    /** @var string $url */
    private $url;

    /**
     * @param string $sessionGUID
     * @param string $endpoint
     * @param string $directoryName
     * @param int    $objectTypeIDFolder
     * @param int    $objectTypeIDReceipt
     */
    public function __construct($sessionGUID, $endpoint, $directoryName, $objectTypeIDFolder, $objectTypeIDReceipt)
    {
        $this->objectTypeIDFolder = $objectTypeIDFolder;
        $this->objectTypeIDReceipt = $objectTypeIDReceipt;

        $this->url = $endpoint;
        $this->sessionGUID = $sessionGUID;
        $this->folderID = $this->checkFolder($directoryName);
    }

    /**
     * @param string $file
     * @param string $voucherNr
     * @param string $voucherDate
     * @param string email
     *
     * @throws FileNotFoundException
     *
     * @return DocuvitaObject
     */
    public function uploadReceipt($file, $voucherNr, $voucherDate, $email)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException("File not found: $file");
        }

        $object = $this->getReceiptObjectTemplate();

        $object->OBJ_DATECREATED = date_format(date_create(), 'Y-m-d\TH:i:s');
        $object->OBJ_NAME = pathinfo($file)['filename'];
        $object->OBJ_PARENTOBJECT = $this->folderID;
        $object->OBJ_VOUCHERNUMBER = $voucherNr;
        $object->OBJ_VOUCHERDATE = $voucherDate;
        $object->OBJ_MAILTO = $email;

        $object->setVersionOriginalFilename(basename($file));

        return $this->uploadFile($object, $file);
    }

    /**
     * @param string $docuvitaFileID
     * @param string $file
     *
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function updateFile($docuvitaFileID, $file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException("File not found: $file");
        }
        $name = basename($file);
        $docuvitaObject = $this->updateDocuvitaFileVersion($docuvitaFileID, $name);
        $this->sendFileContents($docuvitaObject->getDocUploadGuid(), $name, $file);
    }

    /**
     * @return DocuvitaObject
     */
    public function getReceiptObjectTemplate()
    {
        return $this->getObjectTemplate($this->objectTypeIDReceipt);
    }

    /**
     * @return DocuvitaObject
     */
    public function getDirectoryObjectTemplate()
    {
        return $this->getObjectTemplate($this->objectTypeIDFolder);
    }

    /**
     * @param string $directoryName
     *
     * @return string
     */
    private function checkFolder($directoryName)
    {
        try {
            return $this->getFolder($directoryName);
        } catch (FileNotFoundException $e) {
            $this->createFolder($directoryName);

            return $this->getFolder($directoryName);
        }
    }

    /**
     * @param $directoryName
     *
     * @return void
     */
    private function createFolder($directoryName)
    {
        $object = $this->getDirectoryObjectTemplate();
        $object->OBJ_DATECREATED = date_format(date_create(), 'Y-m-d\TH:i:s');
        $object->OBJ_OBJECTTYPE = $this->objectTypeIDFolder;
        $object->OBJ_NAME = $directoryName;
        $object->OBJ_PARENTOBJECT = 0;
        $this->setObject($object);
    }

    /**
     * @param DocuvitaObject $docuvitaObject
     * @param string         $file
     *
     * @return DocuvitaObject
     */
    private function uploadFile($docuvitaObject, $file)
    {
        $docuvitaObject = $this->setObject($docuvitaObject);

        $this->sendFileContents(
            $docuvitaObject->getDocUploadGuid(),
            $docuvitaObject->getVersionOriginalFilename(),
            $file
        );

        return $docuvitaObject;
    }

    /**
     * @param string $duid
     * @param string $name
     * @param string $fullPath
     *
     * @throws CurlException
     *
     * @return void
     */
    private function sendFileContents($duid, $name, $fullPath)
    {
        $curl = curl_init($this->url . '/' . self::ENDPOINT_FILEUPLOAD . "?guid=$duid");

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);


        $data = '-----------------------------98700305420448413741962028704
      Content-Disposition: form-data; name="datei"; filename="' . $name . '"
      Content-Type: text/plain

      ' . file_get_contents($fullPath) . '

      -----------------------------98700305420448413741962028704--';

        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: multipart/form-data; boundary=---------------------------98700305420448413741962028704',
                'Content-Length: ' . strlen($data),
                "Cookie: Sessionguid={$this->sessionGUID};",
            ]
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response);

        if (!$response->success) {
            throw new CurlException($response->msg);
        }
    }

    /**
     * @param string $endpoint
     * @param string $data
     *
     * @throws CurlException|UnauthorizedException
     *
     * @return mixed
     */
    private function sendCurlRequest($endpoint, $data)
    {
        $header = [
            'Content-Type: application/json',
            "Cookie: Sessionguid={$this->sessionGUID}",
        ];
        $curl = curl_init("{$this->url}/{$endpoint}?format=json");
        if ($data !== null) {
            $header[] = 'Content-Length: ' . strlen($data);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $responseRaw = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error !== '') {
            throw new CurlException($error);
        }

        if ($responseRaw === '') {
            if ($code === 401) {
                throw new UnauthorizedException('cURL error: Unauthorized');
            }

            throw new CurlException("cURL error: empty response ({$code})");
        }

        return json_decode($responseRaw);
    }

    /**
     * @param $type
     *
     * @throws CurlException
     *
     * @return DocuvitaObject
     */
    private function getObjectTemplate($type)
    {
        $data = [
            'SessionGuid'  => $this->sessionGUID,
            'ObjectTypeId' => $type,
            'Purpose'      => 'NewObject',
        ];

        $response = $this->sendCurlRequest(self::ENDPOINT_GETOBJECTPROPERTIES, json_encode($data));
        if (isset($response->ResponseStatus)) {
            $responseStatus = $response->ResponseStatus;
            if ($responseStatus->ErrorCode !== '') {
                throw new CurlException($responseStatus->ErrorCode . ':  ' . $responseStatus->Message);
            }
        }


        return $this->createDocuvitaObject($response);
    }

    /**
     * @param string $oldID
     * @param string $name
     *
     * @return DocuvitaObject
     */
    private function updateDocuvitaFileVersion($oldID, $name)
    {
        $data = [
            'ObjectId'                => $oldID,
            'CheckIn'                 => false,
            'VersionOriginalFilename' => $name,
            'VersionComment'          => '',
            'VersionExternalMetadata' => null,
            'VersionNumber'           => [
                'VersionIncrementStyle' => 0,
                'VersionMajor'          => null,
                'VersionMinor'          => null,
                'VersionRevision'       => null,
                'VersionLabel'          => '',
            ],
            'AppendToPrevious'        => false,
        ];
        $response = $this->sendCurlRequest(self::ENDPOINT_SETVERSION . '', json_encode($data));

        return $this->createDocuvitaObject($response);
    }

    /**
     * @param string $directoryName
     *
     * @throws FileNotFoundException|CurlException
     *
     * @return string
     */
    private function getFolder($directoryName)
    {
        $response = $this->sendCurlRequest(self::ENDPOINT_GETTREE, null);

        if (isset($response->ResponseStatus)) {
            $responseStatus = $response->ResponseStatus;
            if ($responseStatus->ErrorCode !== '') {
                throw new CurlException($responseStatus->ErrorCode . ':  ' . $responseStatus->Message);
            }
        }

        foreach ($response->ResultList as $file) {
            if ($file->Name === $directoryName) {
                return $file->Objectid;
            }
        }

        throw new FileNotFoundException(sprintf('File "%s" not found', $directoryName));
    }


    /**
     * @param DocuvitaObject $docuvitaObject
     *
     * @throws CurlException
     *
     * @return DocuvitaObject
     */
    private function setObject($docuvitaObject)
    {
        $response = $this->sendCurlRequest(self::ENDPOINT_SETOBJECTPROPERTIES, $docuvitaObject->toJSON());
        if (isset($response->ResponseStatus)) {
            $responseStatus = $response->ResponseStatus;
            if ($responseStatus->ErrorCode !== '') {
                throw new CurlException($responseStatus->ErrorCode . ':  ' . $responseStatus->Message);
            }
        }
        $createdObject = $this->createDocuvitaObject($response);
        $createdObject->setVersionOriginalFilename($docuvitaObject->getVersionOriginalFilename());

        return $createdObject;
    }

    /**
     * @param $data
     *
     * @return DocuvitaObject
     */
    private function createDocuvitaObject($data)
    {
        return new DocuvitaObject($data, $this->sessionGUID);
    }
}
