<?php

namespace Xentral\Modules\Api\Engine;

use Xentral\Components\Http\Collection\ReadonlyParameterCollection;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Auth\DigestAuth;
use Xentral\Modules\Api\Auth\PermissionGuard;
use Xentral\Modules\Api\Controller\Legacy\DefaultController;
use Xentral\Modules\Api\Controller\Legacy\GobNavConnectController;
use Xentral\Modules\Api\Controller\Legacy\MobileApiController;
use Xentral\Modules\Api\Controller\Legacy\OpenTransConnectController;
use Xentral\Modules\Api\Controller\Legacy\ShopimportController;
use Xentral\Modules\Api\Controller\Version1\AbstractController;
use Xentral\Modules\Api\Controller\Version1\ReportsController;
use Xentral\Modules\Api\Converter\Converter;
use Xentral\Modules\Api\Http\Exception\HttpException as ApiHttpException;
use Xentral\Modules\Api\Http\PathInfoDetector;
use Xentral\Modules\Api\Router\Router;
use Xentral\Modules\Api\Router\RouterResult;

class ApiApplication
{
    /** @var ApiContainer $container */
    protected $container;

    /** @var Converter $converter */
    protected $converter;

    /** @var Request $request */
    protected $request;

    /** @var Response $response */
    protected $response;

    /** @var DigestAuth $auth */
    protected $auth;

    /** @var RouterResult|null $routerResult */
    protected $routerResult;

    /**
     * @param ApiContainer $container
     */
    public function __construct(ApiContainer $container)
    {
        $this->converter = $container->get('Converter');
        $this->container = $container;
    }

    /**
     * @param Request|null $request
     *
     * @return Response
     */
    public function handle(Request $request = null)
    {
        $this->request = $request ?: Request::createFromGlobals();
        $this->container->add('Request', $this->request);

        $method = $this->request->getMethod();
        $uri = $this->request->getPathInfo();

        /**
         * Failsafe; falls Webserver-Konfiguration Probleme bereitet.
         * Dann kann der Pfad zur Ressource im Parameter "path" übergeben werden.
         *
         * @example /api/index.php?path=/v1/artikelkategorien&sort=bezeichnung
         */
        if ($uri === '' && $this->request->get->has('path')) {
            $uri = $this->request->get->get('path');
            $queryParams = $this->request->get->all();
            unset($queryParams['path']);
            $this->request->get = new ReadonlyParameterCollection($queryParams);
        }

        try {
            $this->auth = $this->get('DigestAuth');
            $this->auth->checkLogin();

            $this->response = $this->handleApiRequest($method, $uri);
        } catch (ApiHttpException $e) {
            $this->response = $this->createErrorResponse($e);
        }

        return $this->response;
    }

    /**
     * @param string $serviceName
     *
     * @return object
     */
    protected function get($serviceName)
    {
        return $this->container->get($serviceName);
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return Response
     */
    protected function handleApiRequest($method, $uri)
    {
        /** @var Router $apiRouter */
        /** @var RouterResult $routeInfo */
        $apiRouter = $this->get('ApiRouter');

        /*
         * Routen zusammenstellen
         */

        $collection = $apiRouter->createCollection();
        $collection->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/', ['Version1', null, 'Start', 'indexAction']);
        $collection->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/v1', ['Version1', null, 'Start', 'indexAction']);

        /*
         * Dokumentation
         *
         * Routen greifen nur wenn Webserver falsch konfiguriert ist. Webserver sollte existierende Dateien direkt ausliefern.
         * Zugriff auf Dokumentation erfordert API-Authentifizierung wenn Routen greifen.
         */
        $collection->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/docs.html', ['Version1', null, 'Start', 'docsAction']);
        $collection->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/assets/{assetfile}', ['Version1', null, 'Start', 'docsAssetsAction', 'handle_assets']);

        /**
         * Legacy-API
         *
         * @example POST /www/api/legacy/AdresseGet
         */

        $collection->addRoute('POST', '/v1/gobnavconnect', ['Legacy', null, 'GobNavConnect', 'exampleAction', 'handle_navision']);
        $collection->addRoute('POST', '/v1/gobnavconnect/', ['Legacy', null, 'GobNavConnect', 'exampleAction', 'handle_navision']);
        $collection->addRoute('POST', '/{action}', ['Legacy', null, 'Default', 'postAction']);
        $collection->addRoute('GET', '/{action}', ['Legacy', null, 'Default', 'postAction']);

        $collection->addRoute('GET', '/v1/mobileapi/dashboard', ['Legacy', null, 'MobileApi', 'dashboardAction', 'mobile_app_communication']);

        $collection->addRoute('GET','/opentrans/dispatchnotification/{id:\d+}',
            ['Legacy',null,'OpenTransConnect','readDispatchnotification', 'handle_opentrans']
        );
        $collection->addRoute('GET','/opentrans/dispatchnotification/orderid/{orderid:\d+}',
            ['Legacy',null,'OpenTransConnect','readDispatchnotification', 'handle_opentrans']
        );
        $collection->addRoute('GET','/opentrans/dispatchnotification/ordernumber/{ordernumber:\w+}',
            ['Legacy',null,'OpenTransConnect','readDispatchnotification', 'handle_opentrans']
        );
        $collection->addRoute('GET','/opentrans/dispatchnotification/extorder/{extorder:\w+}',
            ['Legacy',null,'OpenTransConnect','readDispatchnotification', 'handle_opentrans']
        );
        /*$collection->addRoute('POST', '/opentrans/dispatchnotification',
            ['Legacy', null, 'OpenTransConnect',  'createDispatchnotification']
        );*/

        $collection->addRoute('PUT', '/opentrans/dispatchnotification/{id:\d+}',
            ['Legacy', null, 'OpenTransConnect',  'updateDispatchnotification', 'handle_opentrans']
        );
        $collection->addRoute('PUT', '/opentrans/dispatchnotification/orderid/{orderid:\d+}',
            ['Legacy', null, 'OpenTransConnect',  'updateDispatchnotification', 'handle_opentrans']
        );
        $collection->addRoute('PUT', '/opentrans/dispatchnotification/ordernumber/{ordernumber:\w+}',
            ['Legacy', null, 'OpenTransConnect',  'updateDispatchnotification', 'handle_opentrans']
        );
        $collection->addRoute('PUT', '/opentrans/dispatchnotification/extorder/{extorder:\w+}',
            ['Legacy', null, 'OpenTransConnect',  'updateDispatchnotification', 'handle_opentrans']
        );

        $collection->addRoute('GET','/opentrans/order/{id:\d+}',['Legacy',null,'OpenTransConnect','readOrder', 'handle_opentrans']);
        $collection->addRoute('GET','/opentrans/order/ordernumber/{ordernumber:\w+}',['Legacy',null,'OpenTransConnect','readOrder', 'handle_opentrans']);
        $collection->addRoute('GET','/opentrans/order/extorder/{extorder:\w+}',['Legacy',null,'OpenTransConnect','readOrder', 'handle_opentrans']);
        $collection->addRoute('POST', '/opentrans/order',
            ['Legacy', null, 'OpenTransConnect',  'createOrder', 'handle_opentrans']
        );

        $collection->addRoute('DELETE','/opentrans/order/{id:\d+}',['Legacy',null,'OpenTransConnect','deleteOrder', 'handle_opentrans']);
        $collection->addRoute('DELETE','/opentrans/order/ordernumber/{ordernumber:\w+}',['Legacy',null,'OpenTransConnect','deleteOrder', 'handle_opentrans']);
        $collection->addRoute('DELETE','/opentrans/order/extorder/{extorder:\w+}',['Legacy',null,'OpenTransConnect','deleteOrder', 'handle_opentrans']);

        /*$collection->addRoute('PUT', '/opentrans/order/{id:\d+}',
            ['Legacy', null, 'OpenTransConnect',  'updateOrder']
        );*/

        $collection->addRoute('GET','/opentrans/invoice/{id:\d+}',['Legacy',null,'OpenTransConnect','readInvoice', 'handle_opentrans']);
        $collection->addRoute('GET','/opentrans/invoice/orderid/{orderid:\d+}',['Legacy',null,'OpenTransConnect','readInvoice', 'handle_opentrans']);
        $collection->addRoute('GET','/opentrans/invoice/ordernumber/{ordernumber:\w+}',['Legacy',null,'OpenTransConnect','readInvoice', 'handle_opentrans']);
        $collection->addRoute('GET','/opentrans/invoice/extorder/{extorder:\w+}',['Legacy',null,'OpenTransConnect','readInvoice', 'handle_opentrans']);

        $collection->addRoute('POST', '/shopimport/auth',
                              ['Legacy', null, 'Shopimport',  'auth', 'communicate_with_shop']
        );
        $collection->addRoute('POST', '/shopimport/syncstorage/{articlenumber:.+}',
                              ['Legacy', null, 'Shopimport',  'syncStorage', 'communicate_with_shop']
        );

        $collection->addRoute('POST', '/shopimport/articletoxentral/{articlenumber:.+}',
                              ['Legacy', null, 'Shopimport',  'putArticleToXentral', 'communicate_with_shop']
        );
        $collection->addRoute('POST', '/shopimport/articletoshop/{articlenumber:.+}',
                              ['Legacy', null, 'Shopimport',  'putArticleToShop', 'communicate_with_shop']
        );

        $collection->addRoute('POST', '/shopimport/ordertoxentral/{ordernumber:.+}',
                              ['Legacy', null, 'Shopimport',  'putOrderToXentral', 'communicate_with_shop']
        );
        $collection->addRoute('GET', '/shopimport/articlesyncstate',
                              ['Legacy', null, 'Shopimport',  'getArticleSyncState', 'communicate_with_shop']
        );
        $collection->addRoute('GET', '/shopimport/statistics',
                              ['Legacy', null, 'Shopimport',  'getStatistics', 'communicate_with_shop']
        );

        $collection->addRoute('GET', '/shopimport/modulelinks',
                              ['Legacy', null, 'Shopimport',  'getModulelinks', 'communicate_with_shop']
        );

        $collection->addRoute('POST', '/shopimport/disconnect',
                              ['Legacy', null, 'Shopimport',  'postDisconnect', 'communicate_with_shop']
        );
        $collection->addRoute('POST', '/shopimport/reconnect',
                              ['Legacy', null, 'Shopimport',  'postReconnect', 'communicate_with_shop']
        );
        $collection->addRoute('GET', '/shopimport/status',
                              ['Legacy', null, 'Shopimport',  'getStatus', 'communicate_with_shop']
        );
        $collection->addRoute('POST', '/shopimport/refund',
                              ['Legacy', null, 'Shopimport',  'postRefund', 'communicate_with_shop']
        );
        /**
         * REST-API (v1)
         *
         * @example GET /www/api/v1/adressen
         */


        // Abo-Artikel
        $collection->addRoute('POST', '/v1/aboartikel',
            ['Version1', 'ArticleSubscription', 'ArticleSubscription', 'createAction', 'create_subscription'] // Achtung: Eigener Controller
        );
        $collection->addRoute('GET', '/v1/aboartikel',
            ['Version1', 'ArticleSubscription', 'Generic', 'listAction', 'list_subscriptions']
        );
        $collection->addRoute('GET', '/v1/aboartikel/{id:\d+}',
            ['Version1', 'ArticleSubscription', 'Generic', 'readAction', 'view_subscription']
        );
        $collection->addRoute('PUT', '/v1/aboartikel/{id:\d+}',
            ['Version1', 'ArticleSubscription', 'ArticleSubscription', 'updateAction', 'edit_subscription'] // Achtung: Eigener Controller
        );

        $collection->addRoute('DELETE', '/v1/aboartikel/{id:\d+}',
            ['Version1', 'ArticleSubscription', 'Generic', 'deleteAction', 'delete_subscription']
        );

        // Abo-Artikel-Gruppen
        $collection->addRoute('POST', '/v1/abogruppen',
            ['Version1', 'ArticleSubscriptionGroup', 'Generic', 'createAction', 'create_subscription_group']
        );
        $collection->addRoute('GET', '/v1/abogruppen',
            ['Version1', 'ArticleSubscriptionGroup', 'Generic', 'listAction', 'list_subscription_groups']
        );
        $collection->addRoute('GET', '/v1/abogruppen/{id:\d+}',
            ['Version1', 'ArticleSubscriptionGroup', 'Generic', 'readAction', 'view_subscription_group']
        );
        $collection->addRoute('PUT', '/v1/abogruppen/{id:\d+}',
            ['Version1', 'ArticleSubscriptionGroup', 'Generic', 'updateAction', 'edit_subscription_group']
        );

        // Adressen
        /** @see AddressController::createAction */
        $collection->addRoute('POST', '/v1/adressen', ['Version1', null, 'Address', 'createAction', 'create_address']);
        /** @see AddressController::listAction */
        $collection->addRoute('GET', '/v1/adressen', ['Version1', null, 'Address', 'listAction', 'list_addresses']);
        /** @see AddressController::readAction */
        $collection->addRoute('GET', '/v1/adressen/{id:\d+}', ['Version1', null, 'Address', 'readAction', 'view_address']);
        /** @see AddressController::updateAction */
        $collection->addRoute('PUT', '/v1/adressen/{id:\d+}', ['Version1', null, 'Address', 'updateAction', 'edit_address']);

        // Addressen
        /*$collection->addRoute('POST', '/v2/adressen',
            array('Version1', 'Address', 'Generic', 'createAction')
        );*/
        $collection->addRoute('GET', '/v2/adressen',
            ['Version1', 'Address', 'Generic', 'listAction','list_addresses']
        );
        $collection->addRoute('GET', '/v2/adressen/{id:\d+}',
            ['Version1', 'Address', 'Generic', 'readAction','view_address']
        );
        /*$collection->addRoute('PUT', '/v2/adressen/{id:\d+}',
            array('Version1', 'Address', 'Generic', 'updateAction')
        );*/

        // Addressen-Typ (herr, frau, firma)
        $collection->addRoute('POST', '/v1/adresstyp',
            ['Version1', 'AddressType', 'Generic', 'createAction', 'create_address_type']
        );
        $collection->addRoute('GET', '/v1/adresstyp',
            ['Version1', 'AddressType', 'Generic', 'listAction', 'list_address_types']
        );
        $collection->addRoute('GET', '/v1/adresstyp/{id:\d+}',
            ['Version1', 'AddressType', 'Generic', 'readAction', 'view_address_type']
        );
        $collection->addRoute('PUT', '/v1/adresstyp/{id:\d+}',
            ['Version1', 'AddressType', 'Generic', 'updateAction', 'edit_address_type']
        );

        // Artikel
        /*$collection->addRoute('POST', '/v1/artikel',
            array('Version1', 'Article', 'Generic', 'createAction')
        );*/
        $collection->addRoute('GET', '/v1/artikel',
            ['Version1', 'Article', 'Generic', 'listAction', 'list_articles']
        );
        $collection->addRoute('GET', '/v1/artikel/{id:\d+}',
            ['Version1', 'Article', 'Generic', 'readAction', 'view_article']
        );
        /*$collection->addRoute('PUT', '/v1/artikel/{id:\d+}',
            array('Version1', 'Article', 'Generic', 'updateAction')
        );*/

        // Eigenschaften
        $collection->addRoute('GET', '/v1/eigenschaften',
            ['Version1', 'Property', 'Generic', 'listAction', 'list_property']
        );
        $collection->addRoute('GET', '/v1/eigenschaften/{id:\d+}',
            ['Version1', 'Property', 'Generic', 'readAction', 'view_property']
        );
        $collection->addRoute('DELETE', '/v1/eigenschaften/{id:\d+}',
            ['Version1', 'Property', 'Generic', 'deleteAction', 'delete_property']
        );
        $collection->addRoute('PUT', '/v1/eigenschaften/{id:\d+}',
            ['Version1', 'Property', 'Generic', 'updateAction', 'edit_property']
        );
        $collection->addRoute('POST', '/v1/eigenschaften',
            ['Version1', 'Property', 'Generic', 'createAction', 'create_property']
        );

        // Eigenschaftenwerte
        $collection->addRoute('GET', '/v1/eigenschaftenwerte',
            ['Version1', 'PropertyValue', 'Generic', 'listAction', 'list_property_value']
        );
        $collection->addRoute('GET', '/v1/eigenschaftenwerte/{id:\d+}',
            ['Version1', 'PropertyValue', 'Generic', 'readAction', 'view_property_value']
        );
        $collection->addRoute('DELETE', '/v1/eigenschaftenwerte/{id:\d+}',
            ['Version1', 'PropertyValue', 'Generic', 'deleteAction', 'delete_property_value']
        );
        $collection->addRoute('PUT', '/v1/eigenschaftenwerte/{id:\d+}',
            ['Version1', 'PropertyValue', 'Generic', 'updateAction', 'edit_property_value']
        );
        $collection->addRoute('POST', '/v1/eigenschaftenwerte',
            ['Version1', 'PropertyValue', 'Generic', 'createAction', 'create_property_value']
        );

        //
        // BELEGE
        //

        // /v1/belege => Nothing here
        $collection->addRoute('GET', '/v1/belege', ['Version1', null, 'Start', 'indexAction']);

        // Angebote
        $collection->addRoute('GET', '/v1/belege/angebote',
            ['Version1', 'DocumentOffer', 'Generic', 'listAction', 'list_quotes']
        );
        $collection->addRoute('GET', '/v1/belege/angebote/{id:\d+}',
            ['Version1', 'DocumentOffer', 'Generic', 'readAction', 'view_quote']
        );

        // Aufträge
        $collection->addRoute('GET', '/v1/belege/auftraege',
            ['Version1', 'DocumentSalesOrder', 'Generic', 'listAction', 'list_orders']
        );
        $collection->addRoute('GET', '/v1/belege/auftraege/{id:\d+}',
            ['Version1', 'DocumentSalesOrder', 'Generic', 'readAction', 'view_order']
        );

        // Lieferscheine
        $collection->addRoute('GET', '/v1/belege/lieferscheine',
            ['Version1', 'DocumentDeliveryNote', 'Generic', 'listAction', 'list_delivery_notes']
        );
        $collection->addRoute('GET', '/v1/belege/lieferscheine/{id:\d+}',
            ['Version1', 'DocumentDeliveryNote', 'Generic', 'readAction', 'view_delivery_note']
        );

        // Rechnungen
        $collection->addRoute('GET', '/v1/belege/rechnungen',
            ['Version1', 'DocumentInvoice', 'Generic', 'listAction', 'list_invoices']
        );
        $collection->addRoute('GET', '/v1/belege/rechnungen/{id:\d+}',
            ['Version1', 'DocumentInvoice', 'Generic', 'readAction', 'view_invoice']
        );
        $collection->addRoute('DELETE', '/v1/belege/rechnungen/{id:\d+}',
            ['Version1', 'DocumentInvoice', 'Generic', 'deleteAction', 'delete_invoice']
        );

        // Gutschriften/Stornorechnungen
        $collection->addRoute('GET', '/v1/belege/gutschriften',
            ['Version1', 'DocumentCreditNote', 'Generic', 'listAction', 'list_credit_memos']
        );
        $collection->addRoute('GET', '/v1/belege/gutschriften/{id:\d+}',
            ['Version1', 'DocumentCreditNote', 'Generic', 'readAction', 'view_credit_memo']
        );

        //
        // ENDE: BELEGE
        //

        $collection->addRoute('GET', '/v1/reports/{id:\d+}/download',
            ['Version1', null, 'Reports', 'downloadAction', 'view_report']
        );

        // Dateien
        $collection->addRoute('POST', '/v1/dateien',
            ['Version1', 'File', 'File', 'createAction', 'create_file']
        );
        $collection->addRoute('GET', '/v1/dateien',
            ['Version1', 'File', 'File', 'listAction', 'list_files']
        );
        $collection->addRoute('GET', '/v1/dateien/{id:\d+}',
            ['Version1', 'File', 'File', 'readAction', 'view_file']
        );
        $collection->addRoute('GET', '/v1/dateien/{id:\d+}/download',
            ['Version1', 'File', 'File', 'downloadAction', 'view_file']
        );
        $collection->addRoute('GET', '/v1/dateien/{id:\d+}/base64',
            ['Version1', 'File', 'File', 'base64Action', 'view_file']
        );
        /*$collection->addRoute('PUT', '/v1/dateien/{id:\d+}',
            array('Version1', 'File', 'File', 'updateAction')
        );*/

        // Dokumenten-Scanner (DocScan)
        $collection->addRoute('POST', '/v1/docscan',
            ['Version1', 'DocumentScanner', 'DocumentScanner', 'createAction', 'create_scanned_document']
        );
        $collection->addRoute('GET', '/v1/docscan',
            ['Version1', 'DocumentScanner', 'DocumentScanner', 'listAction', 'list_scanned_documents']
        );
        $collection->addRoute('GET', '/v1/docscan/{id:\d+}',
            ['Version1', 'DocumentScanner', 'DocumentScanner', 'readAction', 'view_scanned_document']
        );

        // Artikelkategorien
        $collection->addRoute('POST', '/v1/artikelkategorien',
            ['Version1', 'ArticleCategory', 'Generic', 'createAction', 'create_article_category']
        );
        $collection->addRoute('GET', '/v1/artikelkategorien',
            ['Version1', 'ArticleCategory', 'Generic', 'listAction', 'list_article_categories']
        );
        $collection->addRoute('GET', '/v1/artikelkategorien/{id:\d+}',
            ['Version1', 'ArticleCategory', 'Generic', 'readAction', 'view_article_category']
        );
        $collection->addRoute('PUT', '/v1/artikelkategorien/{id:\d+}',
            ['Version1', 'ArticleCategory', 'Generic', 'updateAction', 'edit_article_category']
        );

        // Gruppen
        $collection->addRoute('POST', '/v1/gruppen',
            ['Version1', 'Group', 'Generic', 'createAction', 'create_group']
        );
        $collection->addRoute('GET', '/v1/gruppen',
            ['Version1', 'Group', 'Generic', 'listAction', 'list_groups']
        );
        $collection->addRoute('GET', '/v1/gruppen/{id:\d+}',
            ['Version1', 'Group', 'Generic', 'readAction', 'view_group']
        );
        $collection->addRoute('PUT', '/v1/gruppen/{id:\d+}',
            ['Version1', 'Group', 'Generic', 'updateAction', 'edit_group']
        );

        //CrmDokumente
        $collection->addRoute('POST', '/v1/crmdokumente',
            ['Version1', 'CrmDocument', 'Generic', 'createAction', 'create_crm_document']
        );
        $collection->addRoute('GET', '/v1/crmdokumente',
            ['Version1', 'CrmDocument', 'Generic', 'listAction', 'list_crm_documents']
        );
        $collection->addRoute('GET', '/v1/crmdokumente/{id:\d+}',
            ['Version1', 'CrmDocument', 'Generic', 'readAction', 'view_crm_document']
        );
        $collection->addRoute('PUT', '/v1/crmdokumente/{id:\d+}',
            ['Version1', 'CrmDocument', 'Generic', 'updateAction', 'edit_crm_document']
        );
        $collection->addRoute('DELETE', '/v1/crmdokumente/{id:\d+}',
            ['Version1', 'CrmDocument', 'Generic', 'deleteAction', 'delete_crm_document']
        );

        // Länder
        $collection->addRoute('POST', '/v1/laender',
            ['Version1', 'Country', 'Generic', 'createAction', 'create_country']
        );
        $collection->addRoute('GET', '/v1/laender',
            ['Version1', 'Country', 'Generic', 'listAction', 'list_countries']
        );
        $collection->addRoute('GET', '/v1/laender/{id:\d+}',
            ['Version1', 'Country', 'Generic', 'readAction', 'view_country']
        );
        $collection->addRoute('PUT', '/v1/laender/{id:\d+}',
            ['Version1', 'Country', 'Generic', 'updateAction', 'edit_country']
        );

        // Lager-Charge
        $collection->addRoute('GET', '/v1/lagercharge',
            ['Version1', 'StorageBatch', 'Generic', 'listAction', 'view_storage_batch']
        );

        // Lager-Mindesthaltbarkeitsdatum (MHD)
        $collection->addRoute('GET', '/v1/lagermhd',
            ['Version1', 'StorageBestBeforeDate', 'Generic', 'listAction', 'view_storage_best_before']
        );

        // Lieferadressen
        $collection->addRoute('POST', '/v1/lieferadressen',
            ['Version1', 'DeliveryAddress', 'Generic', 'createAction', 'create_delivery_address']
        );
        $collection->addRoute('GET', '/v1/lieferadressen',
            ['Version1', 'DeliveryAddress', 'Generic', 'listAction', 'list_delivery_addresses']
        );
        $collection->addRoute('GET', '/v1/lieferadressen/{id:\d+}',
            ['Version1', 'DeliveryAddress', 'Generic', 'readAction', 'view_delivery_address']
        );
        $collection->addRoute('PUT', '/v1/lieferadressen/{id:\d+}',
            ['Version1', 'DeliveryAddress', 'Generic', 'updateAction', 'edit_delivery_address']
        );
        $collection->addRoute('DELETE', '/v1/lieferadressen/{id:\d+}',
            ['Version1', 'DeliveryAddress', 'Generic', 'deleteAction', 'delete_delivery_address']
        );

        // Steuersätze
        $collection->addRoute('POST', '/v1/steuersaetze',
            ['Version1', 'TaxRate', 'Generic', 'createAction', 'create_tax_rate']
        );
        $collection->addRoute('GET', '/v1/steuersaetze',
            ['Version1', 'TaxRate', 'Generic', 'listAction', 'list_tax_rates']
        );
        $collection->addRoute('GET', '/v1/steuersaetze/{id:\d+}',
            ['Version1', 'TaxRate', 'Generic', 'readAction', 'view_tax_rate']
        );
        $collection->addRoute('PUT', '/v1/steuersaetze/{id:\d+}',
            ['Version1', 'TaxRate', 'Generic', 'updateAction', 'edit_tax_rate']
        );

        // Versandarten
        $collection->addRoute('POST', '/v1/versandarten',
            ['Version1', 'ShippingMethod', 'Generic', 'createAction', 'create_shipping_method']
        );
        $collection->addRoute('GET', '/v1/versandarten',
            ['Version1', 'ShippingMethod', 'Generic', 'listAction', 'list_shipping_methods']
        );
        $collection->addRoute('GET', '/v1/versandarten/{id:\d+}',
            ['Version1', 'ShippingMethod', 'Generic', 'readAction', 'view_shipping_method']
        );
        $collection->addRoute('PUT', '/v1/versandarten/{id:\d+}',
            ['Version1', 'ShippingMethod', 'Generic', 'updateAction', 'edit_shipping_method']
        );

        // Wiedervorlagen
        $collection->addRoute('POST', '/v1/wiedervorlagen',
            ['Version1', 'Resubmission', 'Generic', 'createAction', 'create_resubmission']
        );
        $collection->addRoute('GET', '/v1/wiedervorlagen',
            ['Version1', 'Resubmission', 'Generic', 'listAction', 'list_resubmissions']
        );
        $collection->addRoute('GET', '/v1/wiedervorlagen/{id:\d+}',
            ['Version1', 'Resubmission', 'Generic', 'readAction', 'view_resubmission']
        );
        $collection->addRoute('PUT', '/v1/wiedervorlagen/{id:\d+}',
            ['Version1', 'Resubmission', 'Generic', 'updateAction', 'edit_resubmission']
        );

        // Zahlungsweisen
        $collection->addRoute('POST', '/v1/zahlungsweisen',
            ['Version1', 'PaymentMethod', 'Generic', 'createAction', 'create_payment_method']
        );
        $collection->addRoute('GET', '/v1/zahlungsweisen',
            ['Version1', 'PaymentMethod', 'Generic', 'listAction', 'list_payment_methods']
        );
        $collection->addRoute('GET', '/v1/zahlungsweisen/{id:\d+}',
            ['Version1', 'PaymentMethod', 'Generic', 'readAction', 'view_payment_method']
        );
        $collection->addRoute('PUT', '/v1/zahlungsweisen/{id:\d+}',
            ['Version1', 'PaymentMethod', 'Generic', 'updateAction', 'edit_payment_method']
        );

        // Trackingnummern
        $collection->addRoute('POST', '/v1/trackingnummern',
            ['Version1', 'TrackingNumber', 'TrackingNumber', 'createAction', 'create_tracking_number'] // Achtung: Eigener Controller
        );
        $collection->addRoute('GET', '/v1/trackingnummern',
            ['Version1', 'TrackingNumber', 'Generic', 'listAction', 'list_tracking_numbers']
        );
        $collection->addRoute('GET', '/v1/trackingnummern/{id:\d+}',
            ['Version1', 'TrackingNumber', 'Generic', 'readAction', 'view_tracking_number']
        );
        $collection->addRoute('PUT', '/v1/trackingnummern/{id:\d+}',
            ['Version1', 'TrackingNumber', 'TrackingNumber', 'updateAction', 'edit_tracking_number'] // Achtung: Eigener Controller
        );

        // @todo Aufträge
        //$collection->addRoute('GET', '/v1/auftraege', array('Version1', 'Order', 'GetAllOrders'));
        //$collection->addRoute('GET', '/v1/auftraege/{id:\d+}', array('Version1', 'Order', 'GetOrderById'));
        //$collection->addRoute('POST', '/v1/auftraege', array('Version1', 'Order', 'CreateOrder'));

        /*
         * Route ermitteln
         */

        $apiRouter->setCollection($collection);
        $routeInfo = $apiRouter->dispatch($method, $uri);
        $this->routerResult = $routeInfo;

        /*
         * Check permission
         */
        if($routeInfo->getPermission() !== null){
            $guard = New PermissionGuard($this->container->get('Database'), $this->auth->getApiAccountId());
            $guard->check($routeInfo->getPermission());
        }

        /*
         * Controller dispatchen
         */

        $this->request->attributes->add($routeInfo->getRouterParams());

        // Legacy-API-Controller
        if ($routeInfo->getControllerClass() === DefaultController::class) {

            $controller = new DefaultController(
                $this->container->get('LegacyApi'),
                $this->container->get('Request'),
                $this->container->get('DigestAuth')->getApiAccountId()
            );
            $action = $routeInfo->getControllerAction();

            return $controller->$action();

        }

        if ($routeInfo->getControllerClass() === GobNavConnectController::class) {

            $controller = new GobNavConnectController(
                $this->container->get('LegacyApplication'),
                $this->container->get('Request')
            );
            $action = $routeInfo->getControllerAction();

            return $controller->$action();
        }

        if ($routeInfo->getControllerClass() === OpenTransConnectController::class) {

            $controller = new OpenTransConnectController(
                $this->container->get('LegacyApplication'),
                $this->container->get('OpenTransConverter'),
                $this->container->get('Request'),
                $this->container->get('DigestAuth')->getApiAccountId()
            );
            $action = $routeInfo->getControllerAction();

            return $controller->$action();
        }

        if ($routeInfo->getControllerClass() === ShopimportController::class) {

            $controller = new ShopimportController(
                $this->container->get('LegacyApplication'),
                $this->container->get('Request'),
                $this->container->get('DigestAuth')->getApiAccountId()
            );
            $action = $routeInfo->getControllerAction();

            return $controller->$action();
        }

        if ($routeInfo->getControllerClass() === MobileApiController::class) {

            $controller = new MobileApiController(
                $this->container->get('LegacyApplication'),
                $this->container->get('Converter'),
                $this->container->get('Database'),
                $this->container->get('Request')
            );
            $action = $routeInfo->getControllerAction();

            return $controller->$action();
        }

        if ($routeInfo->getControllerClass() === ReportsController::class) {
            $controller = new ReportsController(
                $this->container->get('LegacyApplication'),
                $this->container->get('Request'),
                $this->container->get('DigestAuth')->getApiAccountId()
            );
            $action = $routeInfo->getControllerAction();

            return $controller->$action();
        }



        /** @var AbstractController $controller */
        $controller = $this->container->getApiController(
            $routeInfo->getControllerClass()
        );
        $controller->setResourceClass($routeInfo->getResourceClass());

        return $controller->dispatch($routeInfo->getControllerAction());
    }

    /**
     * @param int $errorCode
     *
     * @return string
     */
    private function buildErrorLink($errorCode)
    {
        $pathInfo = $this->request->getPathInfo();
        $fullUrl = $this->request->getFullUrl();

        $apiUrl = $fullUrl;
        if ($pos = strrpos($fullUrl, $pathInfo)) {
            $apiUrl = substr($fullUrl, 0, $pos);
        }

        if ($pos = strrpos($apiUrl, '/index.php')) {
            $apiUrl = substr($apiUrl, 0, $pos);
        }

        return $apiUrl . '/docs.html#error-' . $errorCode;
    }

    /**
     * @param ApiHttpException $e
     *
     * @return Response
     */
    private function createErrorResponse($e)
    {
        // Fehler-Informationen zusammenbauen
        $data = [
            'error' => [
                'code'      => $e->getCode(),
                'http_code' => $e->getStatusCode(),
                'message'   => $e->getMessage(),
                'href'      => $this->buildErrorLink($e->getCode()),
            ],
        ];
        if ($e->hasErrors()) {
            // Validierungsfehler anhängen
            $data['error']['details'] = $e->getErrors();
        }

        if ($this->isDebugModeActive()) {
            $data['debug'] = [];

            // Router-Informationen anhängen
            $data['debug']['router'] = $this->routerResult !== null ? $this->routerResult->toArray() : false;

            // Request-Informationen anhängen
            $pathInfoDetector = new PathInfoDetector($this->request);
            $pathInfo = $pathInfoDetector->detect();
            $data['debug']['request'] = [
                'isFailsafe'       => $this->request->isFailsafeUri(),
                'pathInfo'         => [
                    'actual'   => (string)$this->request->server->get('PATH_INFO'),
                    'expected' => $pathInfo,
                ],
                'info'             => [
                    'method'     => $this->request->getMethod(),
                    'requestUri' => $this->request->getRequestUri(),
                    'fullUri'    => $this->request->getFullUri(true),
                ],
                'serverParams'     => $this->request->server->all(),
                'header'           => $this->request->header->all(),
                'getParams'        => $this->request->get->all(),
                'postParams'       => $this->request->post->all(),
                'additionalParams' => $this->request->attributes->all(),
            ];
        }

        // XML oder JSON
        if (in_array('text/html', $this->request->getAcceptableContentTypes(), true)) {
            // Client ist vermutlich ein Browser > JSON ausliefern
            $json = $this->converter->arrayToJson($data);
            $response = new Response(
                $json,
                $e->getStatusCode(),
                ['Content-Type' => 'application/json; charset=UTF-8']
            );
        } else {
            if (in_array('application/xml', $this->request->getAcceptableContentTypes(), true)) {
                $xml = $this->converter->arrayToXml($data['error'], 'error');
                $response = new Response(
                    $xml,
                    $e->getStatusCode(),
                    ['Content-Type' => 'application/xml; charset=UTF-8']
                );
            } else {
                $json = $this->converter->arrayToJson($data);
                $response = new Response(
                    $json,
                    $e->getStatusCode(),
                    ['Content-Type' => 'application/json; charset=UTF-8']
                );
            }
        }

        // Login-Header mitschicken
        $response->setHeader('WWW-Authenticate', $this->auth->generateAuthenticationString());

        return $response;
    }

    /**
     * @return bool
     */
    private function isDebugModeActive()
    {
        return defined('DEBUG_MODE') && (int)DEBUG_MODE === 1;
    }
}
