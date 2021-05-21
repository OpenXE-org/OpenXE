<?php

namespace Xentral\Modules\Api\Engine;

use ReflectionClass;
use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Modules\Api\Auth\DigestAuth;
use Xentral\Modules\Api\Controller\Version1\AbstractController;
use Xentral\Modules\Api\Converter\Converter;
use Xentral\Modules\Api\Converter\JsonConverter;
use Xentral\Modules\Api\Converter\OpenTransConverter;
use Xentral\Modules\Api\Converter\XmlConverter;
use Xentral\Modules\Api\LegacyBridge\LegacyApiLazyProxy;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;
use Xentral\Modules\Api\Resource\AbstractResource as AbstractApiResource;
use Xentral\Modules\Api\Resource\ResourceManager;
use Xentral\Modules\Api\Router\Router as ApiRouter;
use Xentral\Modules\Api\Validator\Rule\BooleanRule;
use Xentral\Modules\Api\Validator\Rule\DbValueRule;
use Xentral\Modules\Api\Validator\Rule\DecimalRule;
use Xentral\Modules\Api\Validator\Rule\LengthRule;
use Xentral\Modules\Api\Validator\Rule\LowerRule;
use Xentral\Modules\Api\Validator\Rule\NotPresentRule;
use Xentral\Modules\Api\Validator\Rule\TimeRule;
use Xentral\Modules\Api\Validator\Rule\UniqueRule;
use Xentral\Modules\Api\Validator\Rule\UpperRule;
use Xentral\Modules\Api\Validator\Validator;

final class ApiContainer
{
    /** @var array $services Speicher für Service-Instanzen */
    private $services = array();

    /**
     * Service-Instanz von außen injizieren
     *
     * @param string $name
     * @param object $instance
     */
    public function add($name, $instance)
    {
        if (isset($this->services['name'])) {
            throw new \RuntimeException(
                sprintf('Service "%s" is already registered.', $name)
            );
        }

        $this->services[$name] = $instance;
    }

    /**
     * @param string $name Service-Name oder FQCN
     *
     * @return object
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->services[$name];
        }

        return $this->createService($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param string $name
     *
     * @return object
     */
    private function createService($name)
    {
        $createServiceMethod = 'create' . $name . 'Service';
        if (!method_exists($this, $createServiceMethod)) {
            throw new \RuntimeException(
                sprintf(
                    'Service "%s" could not be created. Container method "%s" is missing.',
                    $name, $createServiceMethod
                )
            );
        }

        $this->services[$name] = $this->$createServiceMethod();

        return $this->services[$name];
    }

    /**
     * @param string        $contollerClass
     * @param Request|null $request
     *
     * @return AbstractController
     */
    public function getApiController($contollerClass, Request $request = null)
    {
        // @todo
        /*$interfaces = class_implements($contollerClass, true);
        if (!in_array('Xentral\Modules\Api\Version1\Controller\ControllerInterface', $interfaces, true)) {
            throw new \CountryInvalidArgumentException(sprintf(
                '"%s" must implement "%s"',
                $contollerClass, 'Xentral\Modules\Api\Version1\Controller\ControllerInterface'
            ));
        }*/
        $parents = class_parents($contollerClass, true);
        if (!in_array(AbstractController::class, $parents, true)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" must implement "%s"',
                $contollerClass, AbstractController::class
            ));
        }

        // Controller nicht sharen!
        // Resourcen können sich Controller teilen
        return new $contollerClass(
            $this->get('LegacyApi'),
            $this->get('Database'),
            $this->get('Converter'),
            $request ?: $this->get('Request'),
            $this->get('ResourceManager')
        );
    }

    /**
     * @param string $resourceClass
     *
     * @return AbstractApiResource
     *
     * @throws \ReflectionException
     */
    public function getApiResource($resourceClass)
    {
        $resourceReflection = new ReflectionClass($resourceClass);
        $resourceName = $resourceReflection->getShortName();
        if ($this->has($resourceName)) {
            return $this->services[$resourceName];
        }

        $parents = class_parents($resourceClass, true);
        if (!in_array(AbstractApiResource::class, $parents, true)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" must extend "%s"',
                $resourceClass, AbstractApiResource::class
            ));
        }

        // @todo
        /*$interfaces = class_implements($resourceClass, false);
        if (!in_array(ApiResourceInterface::class, $interfaces, true)) {
            throw new \CountryInvalidArgumentException(sprintf(
                '"%s" must implement "%s"',
                $resourceClass, ApiResourceInterface::class
            ));
        }*/

        // Resource erzeugen
        $resource = new $resourceClass(
            $this->get('Database'),
            $this->get('Validator')
        );

        // Resource sharen
        $this->add($resourceName, $resource);

        return $resource;
    }

    /**
     * @return ResourceManager
     */
    private function createResourceManagerService()
    {
        return new ResourceManager(
            $this->get('Database'),
            $this->get('Validator'),
            $this->get('LegacyApi')
        );
    }

    /**
     * @return LegacyApiLazyProxy
     */
    private function createLegacyApiService()
    {
        return new LegacyApiLazyProxy();
    }

    /**
     * @return LegacyApplication
     */
    private function createLegacyApplicationService()
    {
        return new LegacyApplication();
    }

    /**
     * @return DigestAuth
     */
    private function createDigestAuthService()
    {
        return new DigestAuth($this->get('Database'), $this->get('Request'));
    }

    /**
     * @return Database
     */
    private function createDatabaseService()
    {
        /** @var LegacyApplication $legacyApp */
        $legacyApp = $this->get('LegacyApplication');

        return $legacyApp->Container->get('Database');
    }

    /**
     * @return Validator
     */
    private function createValidatorService()
    {
        $validator = new Validator();
        $validator->addValidator('db_value', new DbValueRule($this->get('Database')));
        $validator->addValidator('boolean', new BooleanRule());
        $validator->addValidator('decimal', new DecimalRule());
        $validator->addValidator('length', new LengthRule());
        $validator->addValidator('lower', new LowerRule());
        $validator->addValidator('not_present', new NotPresentRule());
        $validator->addValidator('time', new TimeRule());
        $validator->addValidator('unique', new UniqueRule($this->get('Database')));
        $validator->addValidator('upper', new UpperRule());

        return $validator;
    }

    /**
     * @return Request
     */
    private function createRequestService()
    {
        /** @var LegacyApplication $legacyApp */
        $legacyApp = $this->get('LegacyApplication');

        return $legacyApp->Container->get('Request');
    }

    /**
     * @return ApiRouter
     */
    private function createApiRouterService()
    {
        return new ApiRouter();
    }

    /**
     * @return Converter
     */
    private function createConverterService()
    {
        return new Converter($this->get('XmlConverter'), $this->get('JsonConverter'));
    }

    /**
     * @return OpenTransConverter
     */
    private function createOpenTransConverterService()
    {
        return new OpenTransConverter();
    }

    /**
     * @return XmlConverter
     */
    private function createXmlConverterService()
    {
        return new XmlConverter();
    }

    /**
     * @return JsonConverter
     */
    private function createJsonConverterService()
    {
        return new JsonConverter();
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }
}
