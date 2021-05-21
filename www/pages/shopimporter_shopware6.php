<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php

use Xentral\Components\Http\JsonResponse;
use Xentral\Modules\Shopware6\Client\Shopware6Client;
use Xentral\Modules\Shopware6\Data\PriceData;

class Shopimporter_Shopware6 extends ShopimporterBase
{
    public $intern = false;
    public $shopid;
    public $data;
    public $UserName;
    public $Password;
    public $ShopUrl;
    public $createManufacturerAllowed;
    public $defaultManufacturer;
    public $defaultRuleName;
    public $statesToFetch;
    public $deliveryStatesToFetch;
    public $transactionStatesToFetch;
    public $salesChannelToFetch;
    public $orderSearchLimit;
    public $freeFieldOption;
    public $propertyOption;
    public $shopwareDefaultSalesChannel;
    public $shopwareMediaFolder;
    public $protocol;

    /** @var bool  */
    protected $exportCategories = false;
    /** @var Shopware6Client */
    protected $client;

    /**
     * @var Application
     */
    protected $app;
    protected $accessToken;
    /** @var array $currencyMapping available currency Iso Codes mapped to shopware IDs */
    protected $currencyMapping;
    /** @var array $knownShopLanguageIds */
    protected $knownShopLanguageIds = [];
    /** @var array $knownPropertyGroupIds */
    protected $knownPropertyGroupIds = [];
    /** @var array $knownManufacturerIds */
    protected $knownManufacturerIds = [];
    /** @var array $taxesInShop */
    protected $taxesInShop = [];


    /** @var bool $taxationByDestinationCountry */
    protected $taxationByDestinationCountry;

    /**
     * Shopimporter_Shopwaree6 constructor.
     *
     * @param      $app
     * @param bool $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        $this->intern = true;
        if ($intern) {
            return;
        }
        $this->app->ActionHandlerInit($this);

        $this->app->ActionHandler('list', 'Shopimporter_Shopware6List');
        $this->app->ActionHandler('auth', 'ImportAuth');
        $this->app->ActionHandler('sendlistlager', 'ImportSendListLager');
        $this->app->ActionHandler('getauftraegeanzahl', 'ImportGetAuftraegeAnzahl');
        $this->app->ActionHandler('getauftrag', 'ImportGetAuftrag');
        $this->app->ActionHandler('deleteauftrag', 'ImportDeleteAuftrag');
        $this->app->ActionHandler('updateauftrag', 'ImportUpdateAuftrag');
        $this->app->ActionHandler('storniereauftrag','ImportStorniereAuftrag');
        $this->app->ActionHandler('getarticle','ImportGetArticle');
        $this->app->ActionHandler('getarticlelist','ImportGetArticleList');
        $this->app->ActionHandler("updatezahlungsstatus","ImportUpdateZahlungsstatus");
        $this->app->DefaultActionHandler('list');

        $this->app->ActionHandlerListen($app);
    }

  /**
   * @param string $productId
   *
   * @return mixed
   */
    public function addSyncCustomFieldToProduct(string $productId)
    {
      $customField = [
        'customFields' => [
          'wawision_shopimporter_syncstate' => 1
        ]
      ];

      return $this->shopwareRequest('PATCH', "product/{$productId}", $customField);
    }

  /**
   * @param string $orderId
   *
   * @return mixed
   */
    public function addCustomFieldToOrder(string $orderId)
    {
      $customField = [
        'customFields' => [
          'wawision_shopimporter_syncstate' => 1
        ]
      ];

      return $this->shopwareRequest('PATCH', "order/{$orderId}", $customField);
    }

    public function ImportGetArticleList()
    {
        $page = 1;
        $limit = 500;

        do {
            $productIdsToAdd = [];
            $searchdata = [
                'limit' => $limit,
                'page' => $page,
                'filter' => [
                    [
                        'field' => 'product.parentId',
                        'type' => 'equals',
                        'value' => null
                    ]
                ]
            ];

            $productsInShop = $this->shopwareRequest('POST', 'search/product', $searchdata);
            if (!empty($productsInShop['data'])) {
                foreach ($productsInShop['data'] as $productInShop) {
                    $productIdsToAdd[] = $productInShop['id'];
                }
            }

            foreach ($productIdsToAdd as $productId) {
                $this->app->DB->Insert("INSERT INTO shopexport_getarticles (shop, nummer) VALUES ('$this->shopid', '" . $this->app->DB->real_escape_string($productId) . "')");
            }
            $page++;
        } while (count($productsInShop['data']) === $limit);


        $anzahl = $this->app->DB->Select("SELECT COUNT(id) FROM shopexport_getarticles WHERE shop=$this->shopid");
        $this->app->erp->SetKonfigurationValue('artikelimportanzahl_' . $this->shopid, $anzahl);

    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param string $data
     *
     * @param array $headerInformation
     * @return mixed
     */
    public function shopwareRequest($method, $endpoint, $data = '', $headerInformation = [])
    {
        $accessToken = $this->shopwareToken();
        $url = $this->ShopUrl;
        $url .= 'v2/' . $endpoint;

        $ch = curl_init();
        $headerInformation[] = 'Content-Type:application/json';
        $headerInformation[] = 'Authorization:Bearer ' . $accessToken['token'];
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerInformation);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_error($ch)) {
            $this->error[] = curl_error($ch);
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * @return array
     */
    protected function shopwareToken()
    {
        $result = [];

        $result['success'] = true;
        $result['token'] = $this->accessToken;
        $result['message'] = 'Keine Antwort von API erhalten.';

        if (!empty($result['token'])) {
            return $result;
        }

        $result['success'] = false;

        $data = [
            'username' => $this->UserName,
            'password' => $this->Password,
            'grant_type' => 'password',
            'scopes' => 'write',
            'client_id' => 'administration',
        ];

        $ch = curl_init($this->ShopUrl . 'oauth/token');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Cache-Control: no-cache',
            ]
        );
        $response = json_decode(curl_exec($ch), true);

        if (!empty((string)$response['title'])) {
            $result['message'] = $response['title'];
        }

        if (!empty($response['access_token'])) {
            $result['success'] = true;
            $this->accessToken = $response['access_token'];
            $result['token'] = $response['access_token'];
        }

        return $result;
    }

    public function ImportGetArticle()
    {
        $tmp = $this->CatchRemoteCommand('data');

        if (isset($tmp['nummerintern'])) {
            $nummer = $tmp['nummerintern'];
            $response = $this->shopwareRequest('GET', 'product/' . $nummer);
            if (empty($response['data'])) {
                $this->error[] = 'Artikel in der Shop Datenbank nicht gefunden!';
                return;
            }
            $nummer = $response['data']['attributes']['productNumber'];
        } else {
            $nummer = $tmp['nummer'];
        }
        $articleInfo = $this->shopwareRequest('GET', 'product?filter[product.productNumber]=' . $nummer .
            '&associations[manufacturer][]&associations[properties][]');
        if (empty($articleInfo['data'][0])) {
            $this->error[] = 'Artikel in der Shop Datenbank nicht gefunden!';
            return;
        }
        $articleIdInShop = $articleInfo['data'][0]['id'];
        if(empty($articleInfo['data'][0]['customFields'])
          || empty($articleInfo['data'][0]['customFields']['wawision_shopimporter_syncstate'])){
          $this->addSyncCustomFieldToProduct((string)$articleIdInShop);
        }

        $articleInfo = $this->shopwareRequest('GET', 'product?filter[product.productNumber]=' . $nummer .
        '&associations[manufacturer][]&associations[properties][]');
        $associatedInformation = [];
        $properties = [];
        foreach ($articleInfo['included'] as $includedInformation) {
            if ($includedInformation['type'] === 'property_group_option') {
                $properties[$includedInformation['id']] = $includedInformation['attributes'];
            } else {
                $associatedInformation[$includedInformation['id']] = $includedInformation['attributes'];
            }
        }
        $groups = [];
        if (!empty($properties)) {
            $groupsInShop = $this->shopwareRequest('GET', 'property-group');
            foreach ($groupsInShop['data'] as $groupInShop) {
                $groups[$groupInShop['id']] = $groupInShop['attributes']['name'];
            }
        }
        $media = $this->shopwareRequest('GET', 'product/' . $articleIdInShop . '/media');
        $imagesToAdd = [];
        if (!empty($media['included'])) {
            foreach ($media['included'] as $mediaInfo) {
                if ($mediaInfo['type'] === 'media') {
                    $imagesToAdd[] = [
                        'content' => base64_encode(@file_get_contents($mediaInfo['attributes']['url'])),
                        'path' => $mediaInfo['attributes']['url'],
                        'id' => $mediaInfo['id']
                    ];
                }
            }
        }
        $articleInfo = $articleInfo['data'][0]['attributes'];

        $data = [];
        $data['name'] = $articleInfo['name'];
        if (isset($tmp['nummerintern'])) {
            $data['nummer'] = $articleInfo['productNumber'];
        }


        $data['artikelnummerausshop'] = $articleInfo['productNumber'];
        $data['restmenge'] = $articleInfo['stock'];
        $data['uebersicht_de'] = $articleInfo['description'];
        $data['preis_netto'] = $articleInfo['price'][0]['net'];
        if (!empty($articleInfo['price'][0]['listPrice'])) {
            $data['pseudopreis'] = $articleInfo['price'][0]['listPrice'];
        }
        $data['aktiv'] = $articleInfo['active'];
        if (!empty($articleInfo['weight'])) {
            $data['gewicht'] = $articleInfo['weight'];
        }
        if (!empty($articleInfo['manufacturerNumber'])) {
            $data['herstellernummer'] = $articleInfo['manufacturerNumber'];
        }
        if (!empty($articleInfo['ean'])) {
            $data['ean'] = $articleInfo['ean'];
        }
        if (!empty($articleInfo['manufacturerId'])) {
            $data['hersteller'] = $associatedInformation[$articleInfo['manufacturerId']]['name'];
        }
        if (!empty($articleInfo['taxId'])) {
            $data['umsatzsteuer'] = $associatedInformation[$articleInfo['taxId']]['taxRate'];
        }
        if (!empty($properties)) {
            foreach ($properties as $property) {
                if ($this->propertyOption === 'toProperties') {
                    $data['eigenschaften'][] = [
                        'name' => $groups[$property['groupId']],
                        'values' => $property['name'],
                    ];
                }
                if ($this->propertyOption === 'toCustomFields') {
                    $data['freifeld_' . $groups[$property['groupId']]] = $property['name'];
                }
            }
        }
        if (!empty($articleInfo['customFields'])) {
            foreach ($articleInfo['customFields'] as $customFieldName => $customFieldValue) {
                if ($this->freeFieldOption === 'toProperties') {
                    $data['eigenschaften'][] = [
                        'name' => $customFieldName,
                        'values' => $customFieldValue
                    ];
                }
                if ($this->freeFieldOption === 'toCustomFields') {
                    $data['freifeld_' . $customFieldName] = $customFieldValue;
                }
            }
        }
        if (!empty($imagesToAdd)) {
            $data['bilder'] = $imagesToAdd;
        }


        if ($articleInfo['childCount'] > 0) {
            $data = [$data];

            $limit = 50;
            $page = 1;
            $optionInfo = [];
            $optionGroupInfo = [];
            do {

                $searchdata = [
                    'limit' => $limit,
                    'page' => $page,
                    'filter' => [
                        [
                            'field' => 'product.parentId',
                            'type' => 'equals',
                            'value' => $articleIdInShop
                        ]
                    ],
                    'sort' => [
                        [
                            'field' => 'product.options.groupId',
                            'naturalSorting' => false,
                            'order' => 'ASC'
                        ],
                        [
                            'field' => 'product.options.id',
                            'naturalSorting' => false,
                            'order' => 'ASC'
                        ]
                    ],
                    'associations' => [
                        'options' => [
                            'sort' => [
                                [
                                    'field' => 'groupId',
                                    'naturalSorting' => false,
                                    'order' => 'ASC'
                                ],
                                [
                                    'field' => 'id',
                                    'naturalSorting' => false,
                                    'order' => 'ASC'
                                ]
                            ]
                        ]
                    ]
                ];
                $variantsInShop = $this->shopwareRequest('POST', 'search/product', $searchdata);
                foreach ($variantsInShop['included'] as $includedInfo) {
                    if ($includedInfo['type'] === 'property_group_option') {
                        $optionInfo[$includedInfo['id']] = $includedInfo['attributes'];
                        if (empty($optionGroupInfo[$includedInfo['attributes']['groupId']])) {
                            $optionGroupInfo[$includedInfo['attributes']['groupId']] = count($optionGroupInfo) + 1;
                        }
                    }
                }

                foreach ($variantsInShop['data'] as $variantInShop) {
                    $variantData = [];
                    $variantName = $data[0]['name'];
                    foreach ($variantInShop['attributes']['optionIds'] as $optionId) {
                        $variantData['matrixprodukt_wert' . $optionGroupInfo[$optionInfo[$optionId]['groupId']]] =
                            $optionInfo[$optionId]['name'];
                        $variantName .= ' - ' . $optionInfo[$optionId]['name'];
                    }

                    $variantData['name'] = $variantName;
                    $variantData['nummer'] = $variantInShop['attributes']['productNumber'];
                    $variantData['artikelnummerausshop'] = $variantInShop['attributes']['productNumber'];
                    $variantData['restmenge'] = $variantInShop['attributes']['stock'];
                    $variantData['uebersicht_de'] = $variantInShop['attributes']['description'];
                    if (empty($variantInShop['attributes']['price'][0]['net'])) {
                        $variantData['preis_netto'] = $data[0]['preis_netto'];
                    } else {
                        $variantData['preis_netto'] = $variantInShop['attributes']['price'][0]['net'];
                    }
                    if (!empty($variantInShop['attributes']['price'][0]['listPrice'])) {
                        $variantData['pseudopreis'] = $variantInShop['attributes']['price'][0]['listPrice'];
                    }
                    $variantData['aktiv'] = $variantInShop['attributes']['active'];
                    if (!empty($variantInShop['attributes']['weight'])) {
                        $variantData['gewicht'] = $variantInShop['attributes']['weight'];
                    }
                    if (!empty($variantInShop['attributes']['manufacturerNumber'])) {
                        $variantData['herstellernummer'] = $variantInShop['attributes']['manufacturerNumber'];
                    }
                    if (!empty($variantInShop['attributes']['ean'])) {
                        $variantData['ean'] = $variantInShop['attributes']['ean'];
                    }
                    if (!empty($data[0]['umsatzsteuer'])) {
                        $variantData['umsatzsteuer'] = $data[0]['umsatzsteuer'];
                    }

                    $data[] = $variantData;
                }

                $page++;
            } while (count($variantsInShop['data']) > $limit);

            foreach ($optionGroupInfo as $groupId => $sorting) {
                $data[0]['matrixprodukt_gruppe' . $sorting] = $groups[$groupId];
            }
            foreach ($optionInfo as $optionData) {
                $data[0]['matrixprodukt_optionen' . $optionGroupInfo[$optionData['groupId']]][] = $optionData['name'];
            }
        }

        //TODO Staffelpreise
        //TODO Kategorien
        //TODO Freifelder
        //TODO Crossselling

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function checkApiApp($data)
    {
        foreach (['shopwareUserName', 'shopwarePassword', 'shopwareUrl'] as $field) {
            if (empty($data['data'][$field])) {
                return ['success' => false, 'error' => sprintf('%s is empty', $field)];
            }
        }

        $shops = $this->app->DB->SelectArr(
            sprintf(
                "SELECT  `einstellungen_json`, `bezeichnung`,`id` 
          FROM `shopexport` 
          WHERE `modulename` = 'shopimporter_shopware6' 
            AND `einstellungen_json` IS NOT NULL AND `einstellungen_json` <> ''"
            )
        );
        if (empty($shops)) {
            return [
                'info' => [
                    'Shop' => 'Shopware',
                    'info' => 'Url ' . $data['data']['shopwareUrl'],
                ]
            ];
        }
        foreach ($shops as $shop) {
            if (empty($shop['einstellungen_json'])) {
                continue;
            }
            $json = @json_decode($shop['einstellungen_json'], true);
            if (empty($json['felder']) || empty($json['felder']['shopwareUrl'])) {
                continue;
            }
            if ($json['felder']['shopwareUrl'] === $data['data']['shopwareUrl']) {
                return [
                    'success' => false,
                    'error' => sprintf('Shop with url %s allready exists', $data['data']['shopwareUrl'])
                ];
            }
        }

        return [
            'info' => [
                'Shop' => 'Shopware',
                'info' => 'Url ' . $data['data']['shopwareUrl'],
            ]
        ];
    }

    /**
     *
     */
    public function Shopimporter_Shopware6List()
    {
        $msg = $this->app->erp->base64_url_encode('<div class="info">Sie k&ouml;nnen hier die Shops einstellen</div>');
        header('Location: index.php?module=onlineshops&action=list&msg=' . $msg);
        exit;
    }

    /**
     * @param $shopid
     * @param $data
     */
    public function getKonfig($shopid, $data)
    {
        $this->shopid = $shopid;
        $this->data = $data;
        $importerSettings = $this->app->DB->SelectArr("SELECT `einstellungen_json`, `kategorienuebertragen` FROM `shopexport` WHERE `id` = '$shopid' LIMIT 1");
        $importerSettings = reset($importerSettings);

        $this->exportCategories = (bool) $importerSettings['kategorienuebertragen'];

        $einstellungen = [];
        if (!empty($importerSettings['einstellungen_json'])) {
            $einstellungen = json_decode($importerSettings['einstellungen_json'], true);
        }
        $this->protocol = $einstellungen['felder']['protocol'];
        $this->UserName = $einstellungen['felder']['shopwareUserName'];
        $this->Password = $einstellungen['felder']['shopwarePassword'];
        $this->ShopUrl = rtrim($einstellungen['felder']['shopwareUrl'], '/') . '/';
        $this->createManufacturerAllowed = false;
        if ($einstellungen['felder']['shopwareAllowCreateManufacturer'] === '1') {
            $this->createManufacturerAllowed = true;
        }
        $this->defaultManufacturer = $einstellungen['felder']['shopwareDefaultManufacturer'];
        $this->defaultRuleName = $einstellungen['felder']['shopwareDefaultRuleName'];
        $this->statesToFetch = $einstellungen['felder']['statesToFetch'];
        $this->deliveryStatesToFetch = $einstellungen['felder']['deliveryStatesToFetch'];
        $this->transactionStatesToFetch = $einstellungen['felder']['transactionStatesToFetch'];
        $this->salesChannelToFetch = $einstellungen['felder']['salesChannelToFetch'];
        $this->orderSearchLimit = $einstellungen['felder']['orderSearchLimit'];
        $this->freeFieldOption = $einstellungen['felder']['shopwareFreeFieldOption'];
        $this->propertyOption = $einstellungen['felder']['shopwarePropertyOption'];
        $this->shopwareDefaultSalesChannel = $einstellungen['felder']['shopwareDefaultSalesChannel'];
        $this->shopwareMediaFolder = $einstellungen['felder']['shopwareMediaFolder'];
        $query = sprintf('SELECT `steuerfreilieferlandexport` FROM `shopexport`  WHERE `id` = %d', $this->shopid);
        $this->taxationByDestinationCountry = !empty($this->app->DB->Select($query));

      $this->client = $this->app->Container->get('Shopware6Client');
      $this->client->setCredentials(
        $this->UserName,
        $this->Password,
        $this->ShopUrl
      );
    }

    /**
     * @return array
     */
    public function EinstellungenStruktur()
    {
        return
            [
                'ausblenden' => ['abholmodus' => ['ab_nummer']],
                'functions' =>  ['exportartikelbaum','getarticlelist','updatezahlungsstatus'],
                'felder'     => [
                    'protocol'                        => [
                        'typ'         => 'checkbox',
                        'bezeichnung' => '{|Protokollierung im Logfile|}:',
                    ],
                    'shopwareUserName' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Benutzername|}:',
                        'size' => 40,
                    ],
                    'shopwarePassword' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Passwort|}:',
                        'size' => 40,
                    ],
                    'shopwareUrl' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Shop API URL|}:',
                        'size' => 40,
                    ],
                    'shopwareDefaultManufacturer' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Standard Hersteller|}:',
                        'size' => 40,
                        'default' => 'Keine Herstellerinformation',
                    ],
                    'shopwareAllowCreateManufacturer' => [
                        'typ' => 'checkbox',
                        'bezeichnung' => '{|Bei Artikelexport Hersteller anlegen|}:',
                    ],
                    'shopwareDefaultRuleName' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Name der Standardpreisgruppe|}:',
                        'size' => 40,
                        'default' => 'All customers',
                    ],
                    'shopwarePropertyOption' => [
                        'heading' => '{|Eigenschaften / Freifeld Zuordnung|}',
                        'typ' => 'select',
                        'bezeichnung' => '{|Xentral Artikel Eigenschaften|}:',
                        'size' => 40,
                        'default' => 'toProperties',
                        'optionen' => ['toProperties' => '{|Shopware Eigenschaften|}', 'toCustomFields' => '{|Shopware Zusatzfelder|}', 'doNotExport' => '{|Nicht übertragen|}']
                    ],
                    'shopwareFreeFieldOption' => [
                        'typ' => 'select',
                        'bezeichnung' => '{|Xentral Artikel Freifelder|}:',
                        'size' => 40,
                        'default' => 'toCustomFields',
                        'optionen' => ['toProperties' => '{|Shopware Eigenschaften|}', 'toCustomFields' => '{|Shopware Zusatzfelder|}', 'doNotExport' => '{|Nicht übertragen|}']
                    ],
                    'shopwareDefaultSalesChannel' => [
                        'heading' => '{|Artikelexport Standardeinstellungen|}',
                        'typ' => 'text',
                        'bezeichnung' => '{|Standard Sichtbarkeit|}:',
                        'size' => 40
                    ],
                        'shopwareMediaFolder' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Media Folder für Artikelbilder|}:',
                        'size' => 40,
                        'default' => 'Product Media'
                    ],
                    'statesToFetch' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Abzuholender Bestellstatus|}:',
                        'size' => 40,
                        'default' => 'open',
                        'col' => 2,
                        'info' => '<br />Erlaubte Werte: open;in_progress;completed;cancelled'
                    ],
                    'deliveryStatesToFetch' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Eingrenzen auf Lieferstatus|}:',
                        'size' => 40,
                        'default' => '',
                        'col' => 2,
                        'info' => '<br />Erlaubte Werte: open;shipped_partially;shipped;returned;returned_partially;cancelled'
                    ],
                    'transactionStatesToFetch' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Eingrenzen auf Bezahlstatus|}:',
                        'size' => 40,
                        'default' => '',
                        'col' => 2,
                        'info' => '<br />Erlaubte Werte: open;paid;authorized;paid_partially;refunded;refunded_partially;reminded;cancelled'
                    ],
                    'salesChannelToFetch' => [
                        'typ' => 'text',
                        'bezeichnung' => '{|Eingrenzen auf Sales Channel|}:',
                        'size' => 40,
                        'default' => '',
                        'col' => 2,
                        'info' => '<br />Klicke auf "Verbindung prüfen" um die verfügbaren Channels (bitte die Id verwenden) anzuzeigen.'
                    ],
                    'orderSearchLimit' => [
                        'typ' => 'select',
                        'bezeichnung' => '{|Anzahl Aufträge abholen|}:',
                        'optionen' => [
                            '25' => '25',
                            '50' => '50',
                            '75' => '75',
                            '100' => '100',
                        ],
                        'default' => '25',
                        'col' => 2
                    ],
                ],
            ];
    }

    public function ImportUpdateZahlungsstatus()
    {
        $tmp = $this->CatchRemoteCommand('data');
        $auftrag = $tmp['auftrag'];

        $transactions = $this->shopwareRequest('GET', 'order/'.$auftrag.'/transactions');
        $transactionId = $transactions['data'][0]['id'];

        if(empty($transactionId)){
            return;
        }

        $response = $this->shopwareRequest('POST', '_action/order_transaction/'.$transactionId.'/state/paid');
        if (!empty($response['id'])) {
            return 'ok';
        }
    }

  public function ImportSendArtikelbaum(){
    $xentralCategoryTree = [];
    $this->app->erp->GetKategorienbaum($xentralCategoryTree, 0, 0, $this->shopid);

        $xentralCategoryIdToParentId = [];
        foreach ($xentralCategoryTree as $key => $value) {
            $xentralCategoryTree[$key]['erledigt'] = false;
            $xentralCategoryTree[$key]['shopid'] = '';
            $xentralCategoryTree[$key]['aktiv'] = false;
            $xentralCategoryIdToParentId[$value['id']] = $key;
        }

        $parentCategoryId = null;
        foreach ($xentralCategoryTree as $index => $categoryData) {
            $this->createCategoryTree($index, $xentralCategoryTree, $xentralCategoryIdToParentId, $parentCategoryId);
        }
    }

    protected function createCategoryTree($id, &$xentralCategoryTree, $xentralCategoryIdToParentId, $parentCategoryId)
    {
        $parentId = $parentCategoryId;
        if ($xentralCategoryTree[$id]['parent']) {
            $parentId = $xentralCategoryTree[$xentralCategoryIdToParentId[$xentralCategoryTree[$id]['parent']]]['shopid'];
        }
        if ($xentralCategoryTree[$id]['parent'] && !$xentralCategoryTree[$xentralCategoryIdToParentId[$xentralCategoryTree[$id]['parent']]]['erledigt']) {
            $this->createCategoryTree($xentralCategoryIdToParentId[$xentralCategoryTree[$id]['parent']], $xentralCategoryTree, $xentralCategoryIdToParentId, $parentCategoryId);
        }
        $xentralCategoryTree[$id]['erledigt'] = true;

        $categoryName = $xentralCategoryTree[$id]['bezeichnung'];
        $searchdata = [
            'limit' => 25,
            'filter' => [
                [
                    'field' => 'category.name',
                    'type' => 'equals',
                    'value' => $categoryName
                ],
                [
                    'field' => 'category.parentId',
                    'type' => 'equals',
                    'value' => $parentId
                ]
            ]
        ];

        $categoriesInShop = $this->shopwareRequest('POST', 'search/category', $searchdata);

        $categoryId = '';
        if (!empty($categoriesInShop['data'])) {
            $categoryId = $categoriesInShop['data'][0]['id'];
        }

        if (!$categoryId) {
            $categoryData = [
                'parentId' => $parentId,
                'name' => $categoryName
            ];
            $result = $this->shopwareRequest('POST', 'category?_response=true', $categoryData);
            if ($result['data']['id']) {
                $categoryId = $result['data']['id'];
            }
        }

        if ($categoryId) {
            $xentralCategoryTree[$id]['shopid'] = $categoryId;
        }
    }

    /**
     * @return int
     */
    public function ImportSendListLager()
    {
        $tmp = $this->CatchRemoteCommand('data');

        $count = 0;
        foreach ($tmp as $article) {
            $artikel = $article['artikel'];
            if ($artikel === 'ignore') {
                continue;
            }
            $nummer = $article['nummer'];
            $fremdnummer = $article['fremdnummer'];
            if (!empty($fremdnummer)) {
                $nummer = $fremdnummer;
            }
            $articleInfo = $this->shopwareRequest('GET', 'product?filter[product.productNumber]=' . $nummer);

            if (empty($articleInfo['data'][0]['id'])) {
                $this->Shopware6Log('Artikel wurde nicht im Shop gefunden: ' . $nummer, $articleInfo);
                continue;
            }
            if(empty($articleInfo['data'][0]['customFields'])
              || empty($articleInfo['data'][0]['customFields']['wawision_shopimporter_syncstate'])){
              $this->addSyncCustomFieldToProduct((string)$articleInfo['data'][0]['id']);
            }

            $active = true;
            if ($article['inaktiv']) {
                $active = false;
            }

            $stock = $article['anzahl_lager'];
            if (!empty($article['pseudolager'])) {
                $stock = $article['pseudolager'];
            }
            $stock = $this->getCorrectedStockFromAvailable($active, (int)$stock, $articleInfo);
            $data = [
                'stock' => $stock,
                'active' => $active,
            ];
            $response = $this->shopwareRequest('PATCH', 'product/' . $articleInfo['data'][0]['id'], $data);
            $this->Shopware6Log('Lagerbestand konnte nicht uebertragen werden fuer Artikel: ' . $nummer, $response);
            $count++;
        }

        return $count;
    }

  /**
   * @param bool       $isStockActive
   * @param int        $stock
   * @param array|null $articleInfo
   *
   * @return int
   */
    public function getCorrectedStockFromAvailable(bool $isStockActive, int $stock, ?array $articleInfo): int
    {
      if(!$isStockActive) {
        return $stock;
      }
      if(empty($articleInfo)) {
        return $stock;
      }
      if(!isset($articleInfo['data'][0]['attributes']['availableStock'])) {
        return $stock;
      }
      if(!isset($articleInfo['data'][0]['attributes']['availableStock'])) {
        return $stock;
      }
      $reserved = (int)$articleInfo['data'][0]['attributes']['stock']
        - (int)$articleInfo['data'][0]['attributes']['availableStock'];
      if($reserved <= 0) {
        return $stock;
      }

      return $stock + $reserved;
    }

    /**
     * @param string $message
     * @param mixed $dump
     */
    public function Shopware6Log($message, $dump = '')
    {
        if ($this->protocol) {
            $this->app->erp->Logfile($message, print_r($dump, true));
        }
    }

    /**
     * @return int
     */
    public function ImportSendList()
    {
        $articleList = $this->CatchRemoteCommand('data');

        $successCounter = 0;
        foreach ($articleList as $article) {
            $number = $article['nummer'];
            $articleInfo = $this->shopwareRequest(
                'GET',
                sprintf('product?filter[product.productNumber]=%s', $number)
            );
            $articleIdShopware = '';
            if (!empty($articleInfo['data'][0]['id'])) {
                $articleIdShopware = $articleInfo['data'][0]['id'];
            }

            $quantity = $article['anzahl_lager'];
            if (!empty($article['pseudolager'])) {
                $quantity = $article['pseudolager'];
            }
            $inaktiv = $article['inaktiv'];
            $active = true;
            if (!empty($inaktiv)) {
                $active = false;
            }
            $quantity = $this->getCorrectedStockFromAvailable($active, (int)$quantity, $articleInfo);
            $taxRate = (float)$article['steuersatz'];

            $taxId = $this->getTaxIdByRate($taxRate);

            $mediaToAdd = $this->mediaToExport($article, $articleIdShopware);

            $categoriesToAdd = [];
            if($this->exportCategories){
              $categoriesToAdd = $this->categoriesToExport($article, $articleIdShopware);
            }

            $propertiesToAdd = $this->propertiesToExport($article, $articleIdShopware);

            $crosselingToAdd = $this->crosssellingToExport($article, $articleIdShopware);

            $systemFieldsToAdd = $this->systemFieldsToExport($article, $articleIdShopware);

            $deliveryTimeId = null;
            if(!empty($article['lieferzeitmanuell'])){
              $deliveryTimeId = $this->getDeliveryTimeId($article['lieferzeitmanuell']);
            }

            if (empty($systemFieldsToAdd['visibilities']) && !empty($this->shopwareDefaultSalesChannel)) {
                $systemFieldsToAdd['visibilities'] = $this->modifySalesChannel(explode(',', $this->shopwareDefaultSalesChannel), $articleIdShopware);
            }

            if(empty($systemFieldsToAdd['unitId']) && !empty($article['einheit']) ){
                $systemFieldsToAdd['unitId'] = $this->unitToAdd($article['einheit']);
            }


            //Hersteller in Shopware suchen bzw. Anlegen
            $manufacturerName = $article['hersteller'];
            $manufacturerId = $this->getManufacturerIdByName($manufacturerName);

            if ($manufacturerId === null && $this->createManufacturerAllowed === true) {
                $manufacturerId = $this->createManufacturer($manufacturerName);
            }

            if (empty($manufacturerId)) {
                return 'error: Für den Artikelexport ist die Herstellerinformation zwingend erforderlich';
            }

            $isCloseOut = false;
            if(!empty($article['restmenge'])){
                $isCloseOut = true;
            }

            $description = $this->prepareDescription($article['uebersicht_de']);
            $ean = $article['ean'];
            $metaTitle = $article['metatitle_de'];
            $metaDescription = $article['metadescription_de'];
            $metaKeywords = $article['metakeywords_de'];

            $manufacturerNumber = $article['herstellernummer'];
            if (empty($manufacturerNumber)) {
                $manufacturerNumber = '';
            }

            $weight = (float)$article['gewicht'];
            $length = (float)$article['laenge'] * 10;
            $height = (float)$article['hoehe'] * 10;
            $width = (float)$article['breite'] * 10;

            $purchasePrice = (float)$article['einkaufspreis'];

            $currencyId = $this->findCurrencyId($article['waehrung']);
            $price = [
                'net' => $article['preis'],
                'gross' => $article['bruttopreis'],
                'currencyId' => $currencyId,
                'linked' => true];

            if (!empty($article['pseudopreis'])) {
                $price['listPrice'] = [
                    'currencyId' => $currencyId,
                    'gross' => $article['pseudopreis'],
                    'linked' => true,
                    'net' => $article['pseudopreis']/(1+$taxRate/100)
                ];
            }

            $data = [
                'name' => $article['name_de'],
                'isCloseout' => $isCloseOut,
                'productNumber' => $number,
                'manufacturerId' => $manufacturerId,
                'stock' => (int)$quantity,
                'taxId' => $taxId,
                'active' => $active,
                'description' => $description,
                'ean' => $ean,
                'metaTitle' => $metaTitle,
                'metaDescription' => $metaDescription,
                'keywords' => $metaKeywords,
                'manufacturerNumber' => $manufacturerNumber,
                'length' => $length,
                'width' => $width,
                'height' => $height,
                'weight' => $weight,
                'purchasePrice' => $purchasePrice,
                'price' => [$price],
                'categories' => $categoriesToAdd,
                'properties' => $propertiesToAdd,
                'crossSellings' => $crosselingToAdd,
                'media' => $mediaToAdd,
                'deliveryTimeId' => $deliveryTimeId
            ];

            $data = array_merge($data, $systemFieldsToAdd);
            if(empty($data['customFields'])
              || empty($data['customFields']['wawision_shopimporter_syncstate'])){
              $data['customFields']['wawision_shopimporter_syncstate'] = 1;
            }

            if (empty($articleIdShopware)) {
                $result = $this->shopwareRequest('POST',
                    'product?_response=true', $data);
                if (!empty($result['data']['id'])) {
                    $articleIdShopware = $result['data']['id'];
                    $articleInfo['data'][0] = $result['data'];
                }
            } else {
                $headerInformation = [];
                $languageId = $this->getLanguageIdByCountryIso('DE');
                if (!empty($languageId)) {
                    $headerInformation[] = 'sw-language-id: ' . $languageId;
                }
                $result = $this->shopwareRequest('PATCH',
                    sprintf('product/%s?_response=true', $articleIdShopware), $data, $headerInformation);
            }

            if(!empty($articleIdShopware)){
                $this->exportTranslationsForArticle($article, $articleIdShopware);
            }

            $this->addCoverImage($article, $articleIdShopware);

            if (empty($result['data']) || is_array($result['errors'])) {
                $this->Shopware6Log('Artikelexport fehlgeschlagen', ['data:' => $data, 'response' => $result]);
                continue;
            }

            $this->exportSeoUrls($article, $articleIdShopware);

            $this->exportVariants($article, $articleIdShopware, $currencyId);

            if (empty($result['data']) || is_array($result['errors'])) {
                $this->Shopware6Log('Artikelexport bei Bild&uuml;bertragung fehlgeschlagen', ['data:' => $data, 'response' => $result]);
                continue;
            }

          $defaultPrices = $this->getPricesFromArray($article['staffelpreise_standard'] ?? []);
          $groupPrices = $this->getPricesFromArray($article['staffelpreise_gruppen'] ?? []);

          if (!empty($defaultPrices) || !empty($groupPrices)) {
            $this->deleteOldBulkPrices($articleIdShopware);
          }
          if (!empty($defaultPrices)) {
            foreach ($defaultPrices as $priceData) {
              $this->exportBulkPriceForGroup($articleIdShopware, $this->defaultRuleName, $priceData);
            }
          }
          if (!empty($groupPrices)) {
            foreach ($groupPrices as $priceData) {
              $this->exportBulkPriceForGroup($articleIdShopware, $priceData->getGroupName(), $priceData);
            }
          }

          $successCounter++;
        }

        return $successCounter;
    }

  protected function exportBulkPriceForGroup(string $productId, string $groupName, PriceData $priceData): void
  {
    $currencyId = $this->findCurrencyId($priceData->getCurrency());

    $groupRuleId = $this->client->getGroupRuleId($groupName);
    if (empty($groupRuleId)) {
      $this->Shopware6Log("Fehler: Gruppe {$groupName} konnte im Shop nicht gefunden werden");
      return;
    }

    $result = $this->client->saveBulkPrice($productId, $groupRuleId, $currencyId, $priceData);
    if (empty($result['data'])) {
        $this->Shopware6Log("Fehler: Staffelpreis für Gruppe {$groupName} konnte nicht exportiert werden", $result);
    }
  }

  /**
   * @param string $deliveryTimeText
   *
   * @return string|null
   */
  protected function getDeliveryTimeId(string $deliveryTimeText): ?string
  {
    $searchCommand = [
      'limit' => 5,
      'filter' => [
        [
          'field' => 'name',
          'type' => 'equals',
          'value' => $deliveryTimeText
        ]
      ]
    ];
    $result = $this->shopwareRequest('POST', 'search/delivery-time', $searchCommand);

    if (empty($result['data'][0]['id'])) {
      return null;
    }

    return $result['data'][0]['id'];
  }

  /**
     * @param string $description
     * @return string
     */
    protected function prepareDescription($description): string
    {
        $markupSubstitute = [
            '/&quot;/' => '"',
            '/&lt;([^&]+)&gt;/' => '<\1>',
            '/\\<strong>/' => '<b>',
            '/\\<\/strong>/' => '</b>',
            '/\\<em>/' => '<i>',
            '/\\<\/em>/' => '</i>',
            '/&amp;/' => '&',
        ];

        return (string)preg_replace(array_keys($markupSubstitute), array_values($markupSubstitute), $description);
    }

    /**
     * @param array $article
     * @param string $articleIdShopware
     */
    protected function exportTranslationsForArticle(array $article, string $articleIdShopware): void
    {
        $customFieldsToAdd = $this->customFieldsToExport($article, $articleIdShopware);

        $preparedTranslations = [];
        $preparedTranslations['DE'] = [
            'name' => $article['name_de'],
            'description' => $this->prepareDescription($article['uebersicht_de']),
            'metaTitle' => $article['metatitle_de'],
            'metaDescription' => $article['metadescription_de'],
            'keywords' => $article['metakeywords_de'],
            'customFields' => []
        ];
        if(!empty($customFieldsToAdd['DE'])){
            $preparedTranslations['DE']['customFields'] = $customFieldsToAdd['DE'];
        }
        $preparedTranslations['GB'] = [
            'name' => $article['name_en'],
            'description' => $this->prepareDescription($article['uebersicht_en']),
            'metaTitle' => $article['metatitle_en'],
            'metaDescription' => $article['metadescription_en'],
            'keywords' => $article['metakeywords_en'],
            'customFields' => [],
        ];
        if(!empty($customFieldsToAdd['GB'])){
            $preparedTranslations['GB']['customFields'] = $customFieldsToAdd['GB'];
        }
        foreach ($article['texte'] as $translation) {
            if ($translation['sprache'] === 'EN') {
                $translation['sprache'] = 'GB';
            }
            $preparedTranslations[$translation['sprache']] = [
                'name' => $translation['name'],
                'description' => $this->prepareDescription($translation['beschreibung_online']),
                'metaTitle' => $translation['meta_title'],
                'metaDescription' => $translation['meta_description'],
                'keywords' => $translation['meta_keywords'],
            ];
            if(!empty($customFieldsToAdd[$translation['sprache']])){
                $preparedTranslations[$translation['sprache']]['customFields'] = $customFieldsToAdd[$translation['sprache']];
            }
        }

        foreach ($preparedTranslations as $countryIsoCode => $translation) {
            $languageId = $this->getLanguageIdByCountryIso($countryIsoCode);
            if (empty($languageId)) {
                $this->Shopware6Log('Language Id not found for country: ' . $countryIsoCode);
                continue;
            }

            $headerInformation = ['sw-language-id: ' . $languageId];
            $this->shopwareRequest(
                'PATCH',
                sprintf('product/%s', $articleIdShopware),
                $translation, $headerInformation
            );
        }
    }

    /**
     * @param string $countryIso
     *
     * @return string|null
     */
    protected function getLanguageIdByCountryIso(string $countryIso): ?string
    {
        if(array_key_exists($countryIso, $this->knownShopLanguageIds)){
            return $this->knownShopLanguageIds[$countryIso];
        }

        $searchCommand = [
            'limit' => 5,
            'filter' => [
                [
                    'field' => 'country.iso',
                    'type' => 'equals',
                    'value' => $countryIso
                ]
            ]
        ];
        $countryInformation = $this->shopwareRequest('POST', 'search/country', $searchCommand);

        foreach ($countryInformation['data'] as $country){
            $searchCommand = [
                'limit' => 5,
                'filter' => [
                    [
                        'field' => 'locale.territory',
                        'type' => 'equals',
                        'value' => $country['attributes']['name']
                    ]
                ]
            ];
            $localeInformation = $this->shopwareRequest('POST', 'search/locale', $searchCommand);
            foreach ($localeInformation['data'] as $locale) {
                $searchCommand = [
                    'limit' => 5,
                    'filter' => [
                        [
                            'field' => 'language.localeId',
                            'type' => 'equals',
                            'value' => $locale['id']
                        ]
                    ]
                ];
                $languageInformation = $this->shopwareRequest('POST', 'search/language', $searchCommand);
                if (!empty($languageInformation['data'][0]['id'])) {
                    $this->knownShopLanguageIds[$countryIso] = $languageInformation['data'][0]['id'];
                    return $languageInformation['data'][0]['id'];
                }
            }
        }
        $this->knownShopLanguageIds[$countryIso] = null;

        return null;
    }

    /**
     * @param string $manufacturerName
     *
     * @return null|string
     */
    protected function createManufacturer(string $manufacturerName): ?string
    {
        $data = ['name' => $manufacturerName];
        $response = $this->shopwareRequest('POST', 'product-manufacturer?_response=true', $data);

        $manufacturerId = null;
        if(!empty($response['data']['id'])){
            $manufacturerId = $response['data']['id'];
            $this->knownManufacturerIds[$manufacturerName] = $manufacturerId;
        }

        return $manufacturerId;
    }

    /**
     * @param string $manufacturerName
     *
     * @return null|string
     */
    protected function getManufacturerIdByName(string $manufacturerName): ?string
    {
        if (!empty($this->knownManufacturerIds[$manufacturerName])) {
            return $this->knownManufacturerIds[$manufacturerName];
        }

        $manufacturerId = null;
        if (empty($manufacturerName)) {
            $manufacturerName = $this->defaultManufacturer;
        }
        $manufacturer = $this->shopwareRequest(
            'GET',
            'product-manufacturer?filter[product_manufacturer.name]=' . urlencode($manufacturerName)
        );
        $manufacturerId = $manufacturer['data'][0]['id'];
        $this->knownManufacturerIds[$manufacturerName] = $manufacturerId;

        return $manufacturerId;
    }

    /**
     * @param float $taxRate
     *
     * @return string
     */
    protected function getTaxIdByRate(float $taxRate): string{
        if(empty($this->taxesInShop)){
            $this->taxesInShop = $this->shopwareRequest('GET', 'tax');
        }
        foreach ($this->taxesInShop['data'] as $taxData) {
            if (abs(($taxData['attributes']['taxRate']-$taxRate)) < 0.0001 ) {
                return $taxData['id'];
            }
        }

        return $this->taxesInShop['data'][0]['id'];
    }

    /**
     * @param array $internalArticleData
     * @param string $articleIdShopware
     *
     * @return array
     */
    protected function mediaToExport($internalArticleData, $articleIdShopware)
    {
        $mediaToAdd = [
        ];

        if (empty($internalArticleData['Dateien'])) {
            return $mediaToAdd;
        }
        $internalMediaIds = [];

        $searchdata = [
            'limit' => 1,
            'filter' => [
                [
                    'field' => 'name',
                    'type' => 'equals',
                    'value' => $this->shopwareMediaFolder
                ]
            ]
        ];
        $mediaFolderData = $this->shopwareRequest('POST', 'search/media-folder', $searchdata);
        if(empty($mediaFolderData['data'][0]['id'])){
          $this->Shopware6ErrorLog('Kein Media Folder gefunden für: ', $this->shopwareMediaFolder);
          return [];
        }

        $mediaFolderId = $mediaFolderData['data'][0]['id'];

        foreach ($internalArticleData['Dateien'] as $internalFile) {
            $filename = explode('.', $internalFile['filename']);
            unset($filename[count($filename) - 1]);
            $filename = $internalFile['id'].'_'.implode($filename);
            $extension = $internalFile['extension'];
            $imageTitle = (string)$internalFile['titel'];
            $imageAltText = (string)$internalFile['beschreibung'];
            $accessToken = $this->shopwareToken();

            $searchdata = [
                'limit' => 5,
                'filter' => [
                    [
                        'field' => 'media.fileName',
                        'type' => 'equals',
                        'value' => $filename
                    ]
                ]
            ];
            $mediaData = $this->shopwareRequest('POST', 'search/media', $searchdata);
            if (!empty($mediaData['data'][0]['id'])) {
                $internalMediaIds[] = $mediaData['data'][0]['id'];
                if($mediaData['data'][0]['attributes']['title'] !== $imageTitle
                  || $mediaData['data'][0]['attributes']['alt'] !== $imageAltText){
                  $this->setMediaTitleAndAltText($mediaData['data'][0]['id'], $imageTitle, $imageAltText);
                }
                continue;
            }

            $mediaData = $this->shopwareRequest('POST', 'media?_response=true', []);
            if(empty($mediaData['data']['id'])){
              $this->Shopware6Log('Error when creating media for sku: ' . $internalArticleData['nummer'],
                ['mediaData' => $mediaData, 'title' => $imageTitle, 'text' => $imageAltText]);
              continue;
            }
            $mediaId = $mediaData['data']['id'];
            $this->setMediaTitleAndAltText($mediaId, $imageTitle, $imageAltText);

            $mediaAssociationData = [
                [
                    'action' => 'upsert',
                    'entity' => 'media',
                    'payload' => [
                        [
                            'id' => $mediaId,
                            'mediaFolderId' => $mediaFolderId
                        ]
                    ]
                ]
            ];
            $this->shopwareRequest('POST', '_action/sync?_response=true', $mediaAssociationData);

            $url = $this->ShopUrl . 'v2/_action/media/' . $mediaId . '/upload?extension=' . $extension . '&fileName=' . $filename;
            $ch = curl_init();
            $setHeaders = [
                'Content-Type:image/' . $extension,
                'Authorization:Bearer ' . $accessToken['token']
            ];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, base64_decode($internalFile['datei']));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);

            $internalMediaIds[] = $mediaId;
        }

        $existingMediaConnection = [];
        if (!empty($articleIdShopware)) {
            $existingMediaConnection = $this->shopwareRequest('GET', 'product/' . $articleIdShopware . '/media?limit=100');
            foreach ($existingMediaConnection['data'] as $existingConnection) {
                if (!in_array($existingConnection['attributes']['mediaId'], $internalMediaIds, false)) {
                    $this->shopwareRequest('DELETE', 'product/' . $articleIdShopware . '/media/' . $existingConnection['id']);
                }
            }
        }

        $alreadyAddedMediaIDs = [];
        if (!empty($existingMediaConnection)) {
            foreach ($existingMediaConnection['data'] as $existingConnection) {
                $alreadyAddedMediaIDs[$existingConnection['attributes']['mediaId']] = $existingConnection['id'];
            }
        }
        $position = 0;
        foreach ($internalMediaIds as $mediaId) {
            $mediaDataSet = [
                'mediaId' => $mediaId,
                'position' => $position
            ];
            if (array_key_exists($mediaId, $alreadyAddedMediaIDs)) {
                $mediaDataSet['id'] = $alreadyAddedMediaIDs[$mediaId];
            }
            $mediaToAdd[] = $mediaDataSet;
            $position++;
        }

        return $mediaToAdd;
    }

  /**
   * @param string $mediaId
   * @param string $title
   * @param string $altText
   */
  protected function setMediaTitleAndAltText(string $mediaId, string $title, string $altText): void
  {
    $this->shopwareRequest('PATCH', 'media/' . $mediaId,
      ['title' => $title,
        'alt' => $altText
      ]
    );
  }

    /**
     * @param array $articleInXentral
     * @param string $articleIdShopware
     */
    protected function addCoverImage($articleInXentral, $articleIdShopware){
        if(empty($articleIdShopware)){
            return;
        }
        if(empty($articleInXentral['Dateien'])){
            return;
        }
        $existingMediaConnection = $this->shopwareRequest('GET', 'product/' . $articleIdShopware . '/media?limit=100');
        if(empty($existingMediaConnection['data'])){
            return;
        }
        foreach ($articleInXentral['Dateien'] as $xentralFile) {
            $filename = explode('.', $xentralFile['filename']);
            unset($filename[count($filename) - 1]);
            $filename = $xentralFile['id'].'_'.implode($filename);

            $searchdata = [
                'limit' => 5,
                'filter' => [
                    [
                        'field' => 'media.fileName',
                        'type' => 'equals',
                        'value' => $filename
                    ]
                ]
            ];
            $mediaData = $this->shopwareRequest('POST', 'search/media', $searchdata);
            $mediaId = $mediaData['data'][0]['id'];

            foreach ($existingMediaConnection['data'] as $mediaConnection){
                if($mediaId === $mediaConnection['attributes']['mediaId']){

                    $this->shopwareRequest('PATCH',
                        sprintf('product/%s?_response=true', $articleIdShopware),['coverId' => $mediaConnection['id']]);
                    return;
                }
            }
        }
    }

    /**
     * @param array $articleInXentral
     * @param string $articleIdShopware
     * @return array
     */
    protected function categoriesToExport($articleInXentral, $articleIdShopware)
    {
        $categoryName = $articleInXentral['kategoriename'];
        $categoryTree = $articleInXentral['kategorien'];

        $categoriesToAdd = [];
        if (empty($categoryName) && empty($categoryTree)) {
            return $categoriesToAdd;
        }

        $categoriesInXentral = [];
        if (!empty($categoryTree)) {
            $rootcategory = null;
            $categoryTreeid = [];
            foreach ($categoryTree as $categoryData) {
                $categoryData['shopwareparent'] = 0;
                if (!$categoryData['parent']) {
                    $categoryData['shopwareid'] = $rootcategory;
                }
                $categoryTreeid[$categoryData['id']] = $categoryData;
            }

            foreach ($categoryTree as $categoryData) {
                $parentid = $rootcategory;
                if (!empty($categoryData['parent'])) {
                    $parentid = $this->getCategoryParentId($categoryData, $categoryTreeid);
                }

                $searchdata = [
                    'limit' => 25,
                    'filter' => [
                        [
                            'field' => 'category.name',
                            'type' => 'equals',
                            'value' => $categoryData['name']
                        ]
                    ]
                ];
                if (!empty($parentid)) {
                    $searchdata['filter'][] = [
                        'field' => 'category.parentId',
                        'type' => 'equals',
                        'value' => $parentid
                    ];
                }
                $result = $this->shopwareRequest('POST', 'search/category', $searchdata);


                if (!empty($result['data'][0]['id'])) {
                    $categoryTreeid[$categoryData['id']]['shopwareid'] = $result['data'][0]['id'];
                    $categoriesInXentral[] = $result['data'][0]['id'];
                }
            }
        } else if (!empty($categoryName)) {
            $searchdata = [
                'limit' => 25,
                'filter' => [
                    [
                        'field' => 'category.name',
                        'type' => 'equals',
                        'value' => $categoryName
                    ]
                ]
            ];

            $result = $this->shopwareRequest('POST', 'search/category', $searchdata);

            if (!empty($result['data'][0]['id'])) {
                $categoriesInXentral[] = $result['data'][0]['id'];
            }
        }

        if (!empty($articleIdShopware)) {
            $existingCategories = $this->shopwareRequest('GET', 'product/' . $articleIdShopware . '/categories?limit=50');
            foreach ($existingCategories['data'] as $existingCategory) {
                if (!in_array($existingCategory['id'], $categoriesInXentral, false)) {
                    $this->shopwareRequest('DELETE', 'product/' . $articleIdShopware . '/categories/' . $existingCategory['id']);
                }
            }
        }
        foreach ($categoriesInXentral as $categoryId) {
            $categoriesToAdd[] = ['id' => $categoryId];
        }


        return $categoriesToAdd;
    }

    /**
     * @param $categoryData
     * @param $categoryTreeId
     * @return string|null
     */
    protected function getCategoryParentId($categoryData, &$categoryTreeId)
    {
        $parentId = $categoryTreeId[$categoryData['parent']]['shopwareid'];
        if (!empty($parentId)) {
            return $parentId;
        }

        $parentCategoryData = $this->app->DB->SelectRow("SELECT id,parent,bezeichnung AS name FROM artikelkategorien WHERE id<>'' AND id<>'0' AND id='" . $categoryData['parent'] . "' LIMIT 1");
        if (empty($parentCategoryData)) {
            return null;
        }

        $searchData = [
            'limit' => 25,
            'filter' => [
                [
                    'field' => 'category.name',
                    'type' => 'equals',
                    'value' => $parentCategoryData['name']
                ]
            ]
        ];
        $result = $this->shopwareRequest('POST', 'search/category', $searchData);

        if (count($result['data']) < 1) {
            return null;
        }

        if (count($result['data']) === 1) {
            $parentCategoryData['shopwareid'] = $result['data'][0]['id'];
            $categoryTreeId[$parentCategoryData['id']] = $parentCategoryData;
            return $result['data'][0]['id'];
        }

        $grandparentId = $this->getCategoryParentId($parentCategoryData, $categoryTreeId);

        $searchData = [
            'limit' => 25,
            'filter' => [
                [
                    'field' => 'category.name',
                    'type' => 'equals',
                    'value' => $parentCategoryData['name']
                ],
                [
                    'field' => 'category.parentId',
                    'type' => 'equals',
                    'value' => $grandparentId
                ]
            ]
        ];
        $result = $this->shopwareRequest('POST', 'search/category', $searchData);


        if (count($result['data']) === 1) {
            $parentCategoryData['shopwareid'] = $result['data'][0]['id'];
            $categoryTreeId[$parentCategoryData['id']] = $parentCategoryData;
            return $result['data'][0]['id'];
        }
        return null;
    }

    /**
     * @param string $propertyName
     *
     * @return string|null
     */
    protected function getPropertyGroupId($propertyName): ?string
    {
        if(array_key_exists($propertyName, $this->knownPropertyGroupIds)){
            return $this->knownPropertyGroupIds[$propertyName];
        }

        $searchData = [
            'limit' => 25,
            'filter' => [
                [
                    'field' => 'property_group.name',
                    'type' => 'equals',
                    'value' => $propertyName
                ]
            ]
        ];

        $germanLanguageId = $this->getLanguageIdByCountryIso('DE');
        $headerInformation = ['sw-language-id: ' . $germanLanguageId];
        $propertyData = $this->shopwareRequest(
            'POST',
            'search/property-group',
            $searchData,
            $headerInformation);
        if (empty($propertyData['data'][0]['id'])) {
            return null;
        }

        $this->knownPropertyGroupIds[$propertyName] = $propertyData['data'][0]['id'];

        return $propertyData['data'][0]['id'];
    }

    /**
     * @param string $propertyName
     * @return null|string
     */
    protected function createPropertyGroup($propertyName): ?string
    {
        $propertyGroupData = [
            'displayType' => 'text',
            'name' => $propertyName,
            'sortingType' => 'alphanumeric'
        ];
        $propertyGroup = $this->shopwareRequest(
            'POST',
            'property-group?_response=true',
            $propertyGroupData);

        $this->knownPropertyGroupIds[$propertyName] = $propertyGroup['data']['id'];

        if (empty($propertyGroup['data']['id'])) {
            return null;
        }

        return $propertyGroup['data']['id'];
    }

    /**
     * @param string $propertyGroupId
     * @param string $propertyName
     * @param string $countryIsoCode
     */
    protected function createTranslationForPropertyGroup($propertyGroupId, $propertyName, $countryIsoCode): void
    {
        $languageId = $this->getLanguageIdByCountryIso($countryIsoCode);
        if (empty($languageId)) {
            return;
        }

        $headerInformation = ['sw-language-id: ' . $languageId];

        $translation = [
            'name' => $propertyName,
        ];

        $this->shopwareRequest(
            'PATCH',
            sprintf('property-group/%s', $propertyGroupId),
            $translation,
            $headerInformation);
    }

    /**
     * @param string $propertyGroupId
     * @param string $propertyOptionName
     * @param string $countryIsoCode
     * @return mixed|null
     */
    protected function getPropertyOptionId($propertyGroupId, $propertyOptionName, $countryIsoCode = 'DE'): ?string
    {
        $searchData = [
            'limit' => 25,
            'filter' => [
                [
                    'field' => 'property_group_option.name',
                    'type' => 'equals',
                    'value' => $propertyOptionName
                ]
            ]
        ];
        $languageId = $this->getLanguageIdByCountryIso($countryIsoCode);
        $headerInformation = ['sw-language-id: ' . $languageId];
        $optionData = $this->shopwareRequest(
            'POST',
            'search/property-group/' . $propertyGroupId . '/options',
            $searchData,
            $headerInformation);

        if (empty($optionData['data'][0]['id'])) {
            return null;
        }

        return $optionData['data'][0]['id'];
    }

    /**
     * @param string $propertyGroupId
     * @param string $propertyOptionName
     * @return null|string
     */
    protected function createPropertyOption($propertyGroupId, $propertyOptionName): ?string
    {
        $propertyOptionData = [
            'id' => '',
            'name' => $propertyOptionName
        ];
        $createdPropertyOption = $this->shopwareRequest(
            'POST',
            'property-group/' . $propertyGroupId . '/options?_response=true',
            $propertyOptionData);

        if (empty($createdPropertyOption['data']['id'])) {
            return null;
        }

        return $createdPropertyOption['data']['id'];
    }

    /**
     * @param string $optionId
     * @param string $optionName
     * @param string $countryIsoCode
     */
    protected function createTranslationForPropertyOption($optionId, $optionName, $countryIsoCode): void
    {
        $languageId = $this->getLanguageIdByCountryIso($countryIsoCode);
        if (empty($languageId)) {
            return;
        }
        $headerInformation = ['sw-language-id: ' . $languageId];
        $translation = [
            'name' => $optionName,
        ];

        $this->shopwareRequest(
            'PATCH',
            sprintf('property-group-option/%s', $optionId),
            $translation,
            $headerInformation);
    }

    /**
     * @param array $internalArticle
     * @param string $articleIdShopware
     * @return array
     */
    protected function propertiesToExport($internalArticle, $articleIdShopware): array
    {
        $propertiesToAdd = $this->getPropertiesFromArticle($internalArticle);
        if (empty($propertiesToAdd)) {
            return [];
        }
        $assignedProperties = [];

        foreach ($propertiesToAdd as $propertyDefaultName => $countryIsoToPropertyTranslation) {
            if (empty($countryIsoToPropertyTranslation['DE'])) {
                continue;
            }
            $propertyGroupId = '';
            if (array_key_exists($propertyDefaultName, $this->knownPropertyGroupIds)) {
                $propertyGroupId = $this->knownPropertyGroupIds[$propertyDefaultName];
            }
            if (empty($propertyGroupId)) {
                $propertyGroupId = $this->getPropertyGroupId($propertyDefaultName);
            }
            if (empty($propertyGroupId)) {
                $propertyGroupId = $this->createPropertyGroup($propertyDefaultName);
            }
            if (empty($propertyGroupId)) {
                $this->Shopware6Log('PropertyGroup kann nicht erstellt werden: ' . $propertyDefaultName);
                continue;
            }

            foreach ($countryIsoToPropertyTranslation as $countryIsoCode => $translation) {
                $this->createTranslationForPropertyGroup($propertyGroupId, $translation['name'], $countryIsoCode);
            }


            $optionId = $this->getPropertyOptionId($propertyGroupId, $countryIsoToPropertyTranslation['DE']['value'], 'DE');
            if (empty($optionId)) {
                $optionId = $this->createPropertyOption($propertyGroupId, $countryIsoToPropertyTranslation['DE']['value']);
            }
            if (empty($optionId)) {
                $this->Shopware6Log('Option kann nicht erstellt werden: ' . $countryIsoToPropertyTranslation['DE']['value']);
                continue;
            }

            $assignedProperties[] = $optionId;

            foreach ($countryIsoToPropertyTranslation as $countryIsoCode => $translation) {
                $this->createTranslationForPropertyOption($optionId, $translation['value'], $countryIsoCode);
            }
        }

        if (!empty($articleIdShopware)) {
            $existingProperties = $this->shopwareRequest('GET', 'product/' . $articleIdShopware . '/properties?limit=100');
            foreach ($existingProperties['data'] as $existingProperty) {
                if (!in_array($existingProperty['id'], $assignedProperties, false)) {
                    $this->shopwareRequest('DELETE', 'product/' . $articleIdShopware . '/properties/' . $existingProperty['id']);
                }
            }
        }

        $propertiesToAdd = [];
        foreach ($assignedProperties as $propertyOptionId) {
            $propertiesToAdd[] = ['id' => $propertyOptionId];
        }

        return $propertiesToAdd;
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    protected function propertyMustBeIgnored(string $name, string $value): bool
    {
        return empty($value) ||
            strpos($name, 'customField_') === 0 ||
            stripos($name, 'shopware6_') !== false;
    }

    /**
     * @param array $internalArticleData
     * @return array
     */
    protected function getPropertiesFromArticle($internalArticleData): array
    {
        //'Farbe' => [['DE' => ['name' => 'Farbe, 'value' => 'Gelb']],
        //           ['EN' => ['name' => 'Colour, 'value' => 'Yellow']]]
        $propertiesToAdd = [];
        if (!empty($internalArticleData['eigenschaften'])) {
            foreach ($internalArticleData['eigenschaften'] as $property) {
                if ($this->propertyMustBeIgnored($property['name'], $property['values'])) {
                    continue;
                }
                if (strpos($property['name'], 'property_') === 0) {
                    $propertyName = substr($property['name'], 9);
                    $propertiesToAdd[$propertyName]['DE'] = [
                        'name' => $propertyName,
                        'value' => $property['values']];
                    continue;
                }
                if ($this->propertyOption === 'toProperties') {
                    $propertiesToAdd[$property['name']]['DE'] = [
                        'name' => $property['name'],
                        'value' => $property['values']];
                }
            }
        }

        if (!empty($internalArticleData['eigenschaftenuebersetzungen'])) {
            foreach ($internalArticleData['eigenschaftenuebersetzungen'] as $translatedProperty) {
                if ($translatedProperty['language_to'] === 'EN') {
                    $translatedProperty['language_to'] = 'GB';
                }
                if ($this->propertyMustBeIgnored($translatedProperty['property_to'], $translatedProperty['property_value_to'])) {
                    continue;
                }
                if (strpos($translatedProperty['property_to'], 'property_') === 0) {
                    $propertiesToAdd[$translatedProperty['property_from']][$translatedProperty['language_to']] = [
                        'name' => substr($translatedProperty['property_to'], 9),
                        'value' => $translatedProperty['property_value_to']];
                    continue;
                }
                if ($this->propertyOption === 'toProperties') {
                    $propertiesToAdd[$translatedProperty['property_from']][$translatedProperty['language_to']] = [
                        'name' => $translatedProperty['property_to'],
                        'value' => $translatedProperty['property_value_to']];
                }
            }
        }

        if (!empty($internalArticleData['freifelder'])) {
            foreach ($internalArticleData['freifelder']['DE'] as $freeFieldKey => $freeFieldValue) {
                if ($this->propertyMustBeIgnored($freeFieldKey, $freeFieldValue)) {
                    continue;
                }
                if (strpos($freeFieldKey, 'property_') === 0) {
                    $propertyName = substr($freeFieldKey, 9);
                    $propertiesToAdd[$propertyName]['DE'] = [
                        'name' => $propertyName,
                        'value' => $freeFieldValue
                    ];
                    continue;
                }
                if ($this->freeFieldOption === 'toProperties') {
                    $propertiesToAdd[$freeFieldKey]['DE'] = [
                        'name' => $freeFieldKey,
                        'value' => $freeFieldValue
                    ];
                }
            }

            foreach ($internalArticleData['freifelder'] as $languageIso => $freeFields) {
                if ($languageIso === 'DE') {
                    continue;
                }
                if ($languageIso === 'EN') {
                    $languageIso = 'GB';
                }
                foreach ($freeFields as $freeFieldData) {
                    if ($this->propertyMustBeIgnored($freeFieldData['mapping'], $freeFieldData['wert'])) {
                        continue;
                    }
                    if (strpos($freeFieldData['mapping'], 'property_') === 0) {
                        $propertyName = substr($freeFieldData['mapping'], 9);
                        $propertiesToAdd[$propertyName][$languageIso] = [
                            'name' => $propertyName,
                            'value' => $freeFieldData['wert']
                        ];
                        continue;
                    }
                    if ($this->freeFieldOption === 'toProperties') {
                        $propertiesToAdd[$freeFieldData['mapping']][$languageIso] = [
                            'name' => $freeFieldData['mapping'],
                            'value' => $freeFieldData['wert']
                        ];
                    }
                }
            }
        }

        return $propertiesToAdd;
    }

    /**
     * @param array $articleInXentral
     * @param string $articleIdShopware
     *
     * @return array
     */
    protected function customFieldsToExport($articleInXentral, $articleIdShopware): array
    {
        $customFieldsToAdd = $this->getCustomFieldsFromArticle($articleInXentral);
        if (empty($customFieldsToAdd)) {
            return [];
        }
        $languageId = $this->getLanguageIdByCountryIso('DE');
        $headerInformation = ['sw-language-id: ' . $languageId];

        $customFields = [];
        if (!empty($articleIdShopware)) {
            $articleInfo = $this->shopwareRequest(
                'GET', 'product/' . $articleIdShopware,
                [],
                $headerInformation);
            $customFields['DE'] = $articleInfo['data'][0]['attributes']['customFields'];
            if ($customFields === null) {
                $customFields = [];
            }
        }

        foreach ($customFieldsToAdd as $defaultFieldName => $countryIsoCodeToCustomFieldData) {
            $customFieldDefinition = $this->shopwareRequest(
                'GET',
                sprintf('custom-field?filter[custom_field.name]=%s', $defaultFieldName),
                [],
                $headerInformation
            );
            if (empty($customFieldDefinition)) {
                $this->Shopware6Log('Freifeld entspricht keinem shopware Freifeld', $defaultFieldName);
                continue;
            }

            foreach ($countryIsoCodeToCustomFieldData as $countryIsoCode => $customFieldData) {
                $name = $customFieldData['name'];
                $value = $customFieldData['value'];
                if ($value === '') {
                    continue;
                }
                if($countryIsoCode === 'EN'){
                    $countryIsoCode = 'GB';
                }
                $fieldType = $customFieldDefinition['data'][0]['attributes']['type'];
                $controlType = $customFieldDefinition['data'][0]['attributes']['config']['componentName'];

                switch ($fieldType) {
                    case 'text':
                    case 'html':
                        if ($controlType === 'sw-media-field') {
                            $this->Shopware6Log(
                                'Warnung: Freifelder vom Type "medium" werden nicht unterstützt.'
                            );
                        } else {
                            $customFields[$countryIsoCode][$name] = (string)$value;
                        }
                        break;
                    case 'bool':
                        $customFields[$countryIsoCode][$name] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'int':
                        $customFields[$countryIsoCode][$name] = (int)$value;
                        break;
                    case 'float':
                        $customFields[$countryIsoCode][$name] = (float)$value;
                        break;
                    case 'select':
                        $options = $customFieldDefinition['data'][0]['attributes']['config']['options'];
                        $allowedValues = [];
                        foreach ($options as $option) {
                            $allowedValues[] = $option['value'];
                        }
                        if ($controlType === 'sw-single-select') {
                            if (in_array($value, $allowedValues, true)) {
                                $customFields[$countryIsoCode][$name] = $value;
                            } else {
                                $this->Shopware6Log(
                                    sprintf('Warnung: Freifeld "%s"="%s"; ungültiger Wert', $name, $value),
                                    ['allowed values' => $allowedValues]
                                );
                            }
                        }
                        if ($controlType === 'sw-multi-select') {
                            $value = explode(',', $value);
                            foreach ($value as &$item) {
                                $item = trim($item);
                            }
                            unset($item);
                            if (array_intersect($value, $allowedValues) === $value) {
                                $customFields[$countryIsoCode][$name] = $value;
                            } else {
                                $this->Shopware6Log(
                                    sprintf('Warnung: Freifeld "%s"; ungültiger Wert', $name),
                                    ['values' => $value, 'allowed values' => $allowedValues]
                                );
                            }
                        }
                        break;
                    default:
                        $this->Shopware6Log(
                            'Warnung: Freifeld enthält falschen Typ.',
                            ['freifeld' => $name, 'wert' => $value]
                        );
                        continue 2;
                }
            }
        }


        return $customFields;
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    protected function customFieldMustBeIgnored(string $name, string $value): bool
    {
        return empty($value) ||
            strpos($name, 'property_') === 0 ||
            stripos($name, 'shopware6_') !== false;
    }

    /**
     * @param array $articleInXentral
     * @return array
     */
    protected function getCustomFieldsFromArticle($articleInXentral): array
    {
        $customFieldsToAdd = [];
        if (!empty($articleInXentral['eigenschaften'])) {
            foreach ($articleInXentral['eigenschaften'] as $propertyInXentral) {
                if ($this->customFieldMustBeIgnored($propertyInXentral['name'], $propertyInXentral['values'])) {
                    continue;
                }
                if (strpos($propertyInXentral['name'], 'customField_') === 0) {
                    $customFieldName = substr($propertyInXentral['name'], 12);
                    $customFieldsToAdd[$customFieldName]['DE'] = [
                        'name' => $customFieldName,
                        'value' => $propertyInXentral['values']
                    ];
                    continue;
                }
                if ($this->propertyOption === 'toCustomFields') {
                    $customFieldsToAdd[$propertyInXentral['name']]['DE'] = [
                        'name' => $propertyInXentral['name'],
                        'value' => $propertyInXentral['values']
                    ];
                }
            }
        }
        if (!empty($articleInXentral['eigenschaftenuebersetzungen'])) {
            foreach ($articleInXentral['eigenschaftenuebersetzungen'] as $translatedProperty) {
                if ($this->customFieldMustBeIgnored($translatedProperty['property_to'], $translatedProperty['property_value_to'])) {
                    continue;
                }
                if (strpos($translatedProperty['property_to'], 'customField_') === 0) {
                    $customFieldName = substr($translatedProperty['property_to'], 12);
                    $customFieldsToAdd[$customFieldName][$translatedProperty['language_to']] = [
                        'name' => $customFieldName,
                        'value' => $translatedProperty['property_value_to']
                    ];
                    continue;
                }
                if ($this->propertyOption === 'toCustomFields') {
                    $customFieldsToAdd[$translatedProperty['property_to']][$translatedProperty['language_to']] = [
                        'name' => $translatedProperty['property_to'],
                        'value' => $translatedProperty['property_value_to']
                    ];
                }
            }
        }

        if (!empty($articleInXentral['freifelder'])) {
            foreach ($articleInXentral['freifelder']['DE'] as $freeFieldKey => $freeFieldValue) {
                if ($this->customFieldMustBeIgnored($freeFieldKey, $freeFieldValue)) {
                    continue;
                }
                if (strpos($freeFieldKey, 'customField_') === 0) {
                    $customFieldName = substr($freeFieldKey, 12);
                    $customFieldsToAdd[$customFieldName]['DE'] = [
                        'name' => $customFieldName,
                        'value' => $freeFieldValue
                    ];
                    continue;
                }
                if ($this->freeFieldOption === 'toCustomFields') {
                    $customFieldsToAdd[$freeFieldKey]['DE'] = [
                        'name' => $freeFieldKey,
                        'value' => $freeFieldValue
                    ];
                }
            }

            foreach ($articleInXentral['freifelder'] as $countryIsoCode => $freeFieldTranslations) {
                if ($countryIsoCode === 'DE') {
                    continue;
                }
                foreach ($freeFieldTranslations as $freeFieldTranslation){
                    if ($this->customFieldMustBeIgnored($freeFieldTranslation['mapping'], $freeFieldTranslation['wert'])) {
                        continue;
                    }
                    if ($countryIsoCode === 'EN') {
                        $countryIsoCode = 'GB';
                    }
                    if (strpos($freeFieldTranslation['mapping'], 'customField_') === 0) {
                        $customFieldName = substr($freeFieldTranslation['mapping'], 12);
                        $customFieldsToAdd[$customFieldName][$countryIsoCode] = [
                            'name' => $customFieldName,
                            'value' => $freeFieldTranslation['wert']
                        ];
                        continue;
                    }
                    if ($this->freeFieldOption === 'toCustomFields') {
                        $customFieldsToAdd[$freeFieldTranslation['mapping']][$countryIsoCode] = [
                            'name' => $freeFieldTranslation['mapping'],
                            'value' => $freeFieldTranslation['wert']
                        ];
                    }
                }
            }
        }

        return $customFieldsToAdd;
    }

    /**
     * @param array $articleInXentral
     * @param int $articleIdShopware
     *
     * @return array
     */
    protected function crosssellingToExport($articleInXentral, $articleIdShopware){
        if (empty($articleInXentral['crosssellingartikel'])) {
            return [];
        }

        $crosssellingArticles = [];
        foreach ($articleInXentral['crosssellingartikel'] as $crosssellingArticle){
            $type = 'Ähnlich';
            if($crosssellingArticle['art'] == 2){
                $type = 'Zubehör';
            }
            $crosssellingArticles[$type][] = $crosssellingArticle['nummer'];
        }
        $crossselingInformation = [];
        foreach ($crosssellingArticles as $type => $articles){
            if(!empty($articleIdShopware)){
                $existingCrossSellings = $this->shopwareRequest('GET', sprintf('product/%s/cross-sellings/',
                    $articleIdShopware));
                if(!empty($existingCrossSellings['data'])){
                    foreach ($existingCrossSellings['data'] as $existingCrossSelling){
                        if($existingCrossSelling['attributes']['name'] === $type){
                            $this->shopwareRequest('DELETE', sprintf('product/%s/cross-sellings/%s/',
                                $articleIdShopware, $existingCrossSelling['id']));
                        }
                    }
                }
            }

            $crosselingToAdd = [];
            foreach ($articles as $articleNumber) {
                $articleInfo = $this->shopwareRequest(
                    'GET',
                    sprintf('product?filter[product.productNumber]=%s', $articleNumber)
                );

                if(empty($articleInfo['data'][0]['id'])){
                    continue;
                }
                $crosselingToAdd[] = $articleInfo['data'][0]['id'];
            }
            if(empty($crosselingToAdd)){
                continue;
            }
            $crossselingInformationForType = [
                'active' => true,
                'name' => $type,
                'assignedProducts' => [],
                'type' => 'productList',
                'sortBy' => 'name',
                'limit' => 24,
                'position' => 1
            ];
            $position = 1;
            foreach ($crosselingToAdd as $articleId){
                $crossselingInformationForType['assignedProducts'][] = [
                    'productId' => $articleId,
                    'position' => $position,
                ];
                $position++;
            }
            $crossselingInformation[] = $crossselingInformationForType;
        }


        return $crossselingInformation;
    }

    /**
     * @param string $unitShortCode
     *
     * @return string
     */
    protected function unitToAdd(string $unitShortCode): string{
        $searchData = [
            'limit' => 25,
            'source' => [
                'id'
            ],
            'filter' => [
                [
                    'field' => 'unit.shortCode',
                    'type' => 'equals',
                    'value' => $unitShortCode
                ]
            ]
        ];
        $unitInShopware = $this->shopwareRequest(
            'POST',
            'search/unit',
            $searchData);

        if(!empty($unitInShopware['data'][0]['id'])){
            return $unitInShopware['data'][0]['id'];
        }

        $query = sprintf("SELECT `internebemerkung` FROM `artikeleinheit` WHERE `einheit_de` = '%s' LIMIT 1",
            $unitShortCode);
        $unitName = $this->app->DB->Select($query);
        if(empty($unitName)){
            $unitName = $unitShortCode;
        }

        $unitInformation = [
            'name' => $unitName,
            'shortCode' => $unitShortCode
        ];
        $result = $this->shopwareRequest('POST', 'unit?_response=true', $unitInformation);

        if(empty($result['data']['id'])){
            return '';
        }

        return $result['data']['id'];
    }

    /**
     * @param array $internArticle
     * @param int $articleIdShopware
     *
     * @return array
     */
    protected function systemFieldsToExport($internArticle, $articleIdShopware): array
    {
        $internalSpecialFields = [];
        foreach ($internArticle['freifelder']['DE'] as $freeFieldName => $freeFieldValue) {
            if (stripos($freeFieldName, 'shopware6_') !== false) {
                $internalSpecialFields[$freeFieldName] = $freeFieldValue;
            }
        }
        foreach ($internArticle['eigenschaften'] as $property) {
            if (stripos($property['name'], 'shopware6_') !== false) {
                $internalSpecialFields[$property['name']] = $property['values'];
            }
        }

        $systemFields = [];
        foreach ($internalSpecialFields as $fieldName => $fieldValue) {
            switch (strtolower($fieldName)) {
                case 'shopware6_sales_channel':
                    $systemFields['visibilities'] = $this->modifySalesChannel(explode(',', $fieldValue), $articleIdShopware);
                    break;
                case 'shopware6_purchase_unit':
                    $systemFields['purchaseUnit'] = (float)str_replace(',', '.', $fieldValue);
                    break;
                case 'shopware6_reference_unit':
                    $systemFields['referenceUnit'] = (float)str_replace(',', '.', $fieldValue);
                    break;
                case 'shopware6_unit':
                    $systemFields['unitId'] = $this->unitToAdd($fieldValue);
                    break;
                case 'shopware6_pack_unit':
                    $systemFields['packUnit'] = (string)$fieldValue;
                    break;
                case 'shopware6_restock_time':
                  $systemFields['restockTime'] = (int)$fieldValue;
                  break;
                case 'shopware6_pack_unit_plural':
                    $systemFields['packUnitPlural'] = (string)$fieldValue;
                    break;
            }
        }

        return $systemFields;
    }

    /**
     * @param array $salesChannelNames
     * @param string $articleIdInShopware
     *
     * @return array
     */
    protected function modifySalesChannel($salesChannelNames, $articleIdInShopware)
    {
        $salesChannelInXentralIds = [];
        foreach ($salesChannelNames as $salesChannelName) {
            $salesChannelInfo = $this->shopwareRequest('GET',
                sprintf('sales-channel?filter[sales_channel.name]=%s', urlencode(trim($salesChannelName)))
            );
            if (!empty($salesChannelInfo['data'][0]['id'])) {
                $salesChannelInXentralIds[] = $salesChannelInfo['data'][0]['id'];
            }
        }

        $existingVisibilities = $this->shopwareRequest(
            'GET',
            sprintf('product/%s/visibilities', $articleIdInShopware)
        );

        $existingSalesChannelIds = [];
        if (!empty($existingVisibilities['data'])) {
            foreach ($existingVisibilities['data'] as $visibility) {
                $existingSalesChannelIds[$visibility['id']] = $visibility['attributes']['salesChannelId'];
            }
        }

        foreach ($existingSalesChannelIds as $associationId => $existingSalesChannelId){
            if (!in_array($existingSalesChannelId, $salesChannelInXentralIds,true)) {
                $this->shopwareRequest('DELETE', sprintf('product/%s/visibilities/%s/',
                    $articleIdInShopware, $associationId));
            }
        }

        $salesChannelsToAdd = [];
        foreach ($salesChannelInXentralIds as $salesChannelInXentralId){
            if (!in_array($salesChannelInXentralId, $existingSalesChannelIds,true)) {
                $salesChannelsToAdd[] = $salesChannelInXentralId;
            }
        }

        $visibilities = [];
        foreach ($salesChannelsToAdd as $salesChannelIdToAdd) {
            $visibilities[] = [
                'salesChannelId' => $salesChannelIdToAdd,
                'visibility' => 30
            ];
        }

        return $visibilities;
    }

    /**
     * @param string $isoCode
     *
     * @return string
     */
    protected function findCurrencyId($isoCode)
    {

        $this->requestCurrencyMappingLazy();
        if (isset($this->currencyMapping[strtoupper($isoCode)])) {
            return $this->currencyMapping[strtoupper($isoCode)];
        }
        $this->Shopware6Log(
            sprintf('Warnung: Kein Mapping für Waehrung "%s" gefunden.', $isoCode),
            $this->currencyMapping
        );

        return null;
    }

    /**
     * request currency mapping only once
     */
    protected function requestCurrencyMappingLazy()
    {

        if ($this->currencyMapping !== null) {
            return;
        }
        $currencies = $this->shopwareRequest('GET', 'currency');
        if (!isset($currencies['data'])) {
            $this->Shopware6Log('Kann Währungsmapping nicht abrufen', $currencies);
        }
        foreach ($currencies['data'] as $currency) {
            $isoCode = strtoupper($currency['attributes']['isoCode']);
            $this->currencyMapping[$isoCode] = $currency['id'];
        }
    }

    /**
     * @param array $internalArticleData
     * @param string $articleIdInShopware
     * @return bool
     */
    public function exportSeoUrls(array $internalArticleData, string $articleIdInShopware): bool
    {
        if (empty($articleIdInShopware)) {
            return false;
        }

        $preparedSeoInformation = [];
        foreach ($internalArticleData['freifelder'] as $countryIsoCode => $freeFieldInformation) {
            if($countryIsoCode === 'EN'){
                $countryIsoCode = 'GB';
            }
            if($countryIsoCode === 'DE'){
                foreach ($freeFieldInformation as $freeFieldName => $freeFieldValue) {
                    if (stripos($freeFieldName, 'shopware6_seo_url') !== false) {
                        $preparedSeoInformation[$countryIsoCode][$freeFieldName] = $freeFieldValue;
                    }
                }
            }else{
                foreach ($freeFieldInformation as $freeFieldData) {
                    if (stripos($freeFieldData['mapping'], 'shopware6_seo_url') !== false) {
                        $preparedSeoInformation[$countryIsoCode][$freeFieldData['mapping']] = $freeFieldData['wert'];
                    }
                }
            }
        }
        foreach ($internalArticleData['eigenschaften'] as $property) {
            if (stripos($property['name'], 'shopware6_seo_url') !== false) {
                $preparedSeoInformation['DE'][$property['name']] = $property['values'];
            }
        }
        foreach ($internalArticleData['eigenschaftenuebersetzungen'] as $propertyTranslation) {
            if($propertyTranslation['language_to'] === 'EN'){
                $propertyTranslation['language_to'] = 'GB';
            }
            if (stripos($propertyTranslation['property_to'], 'shopware6_seo_url') !== false) {
                $preparedSeoInformation[$propertyTranslation['language_to']][$propertyTranslation['property_to']] = $propertyTranslation['property_value_to'];
            }
        }

        $specificSalesChannelSeoUrls = [];
        $defaultSeoUrls = [];
        foreach ($preparedSeoInformation as $countryIsoCode => $channelAssociations) {
            foreach ($channelAssociations as $fieldName => $fieldValue){
                if(strtolower($fieldName) === 'shopware6_seo_url'){
                    $defaultSeoUrls[$countryIsoCode] = $fieldValue;
                }else{
                    $seoInformation = explode('|', $fieldName);
                    $specificSalesChannelSeoUrls[$countryIsoCode][array_pop($seoInformation)] = $fieldValue;
                }
            }
        }

        if (empty($specificSalesChannelSeoUrls) && empty($defaultSeoUrls)) {
             return false;
        }

        $salesChannelsIdToName = [];
        $salesChannels = $this->shopwareRequest('GET','sales-channel');
        foreach ($salesChannels['data'] as $salesChannel) {
            $salesChannelsIdToName[$salesChannel['id']] = $salesChannel['attributes']['name'];
        }

        foreach ($preparedSeoInformation as $countryIsoCode => $x){
            $languageId = $this->getLanguageIdByCountryIso($countryIsoCode);
            if (empty($languageId)) {
                $this->Shopware6Log('Language Id not found for country: ' . $countryIsoCode);
                continue;
            }

            $headerInformation = ['sw-language-id: ' . $languageId];
            foreach ($salesChannelsIdToName as $salesChannelId => $salesChannelName) {
                $seoUrlToUse = $defaultSeoUrls[$countryIsoCode];
                if (!empty($specificSalesChannelSeoUrls[$countryIsoCode][$salesChannelName])) {
                    $seoUrlToUse = $specificSalesChannelSeoUrls[$countryIsoCode][$salesChannelsIdToName[$salesChannelName]];
                }
                if (empty($seoUrlToUse)) {
                    continue;
                }
                $seoDataToSend = [
                    'seoPathInfo' => $seoUrlToUse,
                    '_isNew' => true,
                    'isModified' => true,
                    'isCanonical' => true,
                    'isDeleted' => false,
                    'routeName' => 'frontend.detail.page',
                    'foreignKey' => $articleIdInShopware,
                    'pathInfo' => '/detail/'.$articleIdInShopware,
                    'languageId' => $languageId,
                    'salesChannelId' => $salesChannelId];
                $this->shopwareRequest('PATCH', '_action/seo-url/canonical', $seoDataToSend, $headerInformation);
            }
        }

        return true;
    }

    /**
     * @param array $article
     * @param string $articleIdShopware
     * @param string $currencyId
     *
     * @return bool
     */
    protected function exportVariants($article, $articleIdShopware, $currencyId): bool
    {
        $languageId = $this->getLanguageIdByCountryIso('DE');
        if (empty($languageId)) {
            return false;
        }
        if (empty($article['matrix_varianten']) || empty($articleIdShopware)) {
            return false;
        }
        $internalGroupPropertiesToShopwareId = [];
        foreach ($article['matrix_varianten']['gruppen'] as $propertyGroupName => $internalPropertyGroupValues) {
            $propertyGroupId = '';
            if (array_key_exists($propertyGroupName, $this->knownPropertyGroupIds)) {
                $propertyGroupId = $this->knownPropertyGroupIds[$propertyGroupName];
            }
            if (empty($propertyGroupId)) {
                $propertyGroupId = $this->getPropertyGroupId($propertyGroupName);
            }
            if (empty($propertyGroupId)) {
                $propertyGroupId = $this->createPropertyGroup($propertyGroupName);
            }
            if (empty($propertyGroupId)) {
                $this->Shopware6Log('PropertyGroup kann nicht erstellt werden: ' . $propertyGroupName);
                return false;
            }

            if (!empty($article['matrix_varianten']['texte'])) {
                $this->createTranslationForPropertyGroup($propertyGroupId, $propertyGroupName, 'DE');

                foreach ($article['matrix_varianten']['texte']['gruppen'] as $countryIsoCode => $matrixGroupTranslation) {
                    if ($countryIsoCode === 'EN') {
                        $countryIsoCode = 'GB';
                    }

                    $this->createTranslationForPropertyGroup($propertyGroupId, $matrixGroupTranslation[$propertyGroupName], $countryIsoCode);
                }
            }

            $languageId = $this->getLanguageIdByCountryIso('DE');
            $headerInformation = ['sw-language-id: ' . $languageId];
            $shopwarePropertyGroupOptions = $this->shopwareRequest(
                'GET',
                'property-group/' . $propertyGroupId . '/options?limit=100',
                $headerInformation);
            foreach ($shopwarePropertyGroupOptions['data'] as $shopwarePropertyGroupOption) {
                $propertyValue = $shopwarePropertyGroupOption['attributes']['name'];
                $internalGroupPropertiesToShopwareId[$propertyGroupName][$propertyValue] = $shopwarePropertyGroupOption['id'];
            }

            foreach ($internalPropertyGroupValues as $internalPropertyGroupValue => $valueNotNeeded) {
                if (!array_key_exists($internalPropertyGroupValue, $internalGroupPropertiesToShopwareId[$propertyGroupName])) {
                    $newOptionData = [
                        'name' => (string)$internalPropertyGroupValue
                    ];
                    $optionData = $this->shopwareRequest(
                        'POST',
                        'property-group/' . $propertyGroupId . '/options?_response=true',
                        $newOptionData);
                    $internalGroupPropertiesToShopwareId[$propertyGroupName][$internalPropertyGroupValue] = $optionData['data']['id'];
                }
            }

            if (!empty($article['matrix_varianten']['texte'])) {
                foreach ($internalPropertyGroupValues as $optionValue => $valueNotNeeded) {
                    $optionId = $internalGroupPropertiesToShopwareId[$propertyGroupName][$optionValue];
                    $this->createTranslationForPropertyOption(
                        $optionId,
                        $optionValue,
                        'DE');
                    foreach ($article['matrix_varianten']['texte']['werte'] as $countryIsoCode => $matrixOptionTranslations) {
                        if ($countryIsoCode === 'EN') {
                            $countryIsoCode = 'GB';
                        }
                        if (array_key_exists($optionValue, $matrixOptionTranslations)) {
                            $this->createTranslationForPropertyOption(
                                $optionId,
                                $matrixOptionTranslations[$optionValue],
                                $countryIsoCode);
                        }
                    }
                }
            }
        }

        $existingCombinations = $this->shopwareRequest(
            'GET',
            '_action/product/' . $articleIdShopware . '/combinations');
        $existingCombinationsByNumber = [];

        foreach ($existingCombinations as $combinationId => $combinationInfo) {
            $existingCombinationsByNumber[$combinationInfo['productNumber']] = [
                'id' => $combinationId,
                'options' => [],
            ];
            foreach ($combinationInfo['options'] as $combinationOption) {
                $existingCombinationsByNumber[$combinationInfo['productNumber']]['options'][$combinationOption] = $combinationOption;
            }
        }


        foreach ($article['artikel_varianten'] as $variant) {
            $internalVariantMatrixData = $article['matrix_varianten']['artikel'][$variant['artikel']];
            $productNumber = $internalVariantMatrixData[0]['nummer'];
            $name = $variant['name_de'];
            $stock = $variant['lag'];
            $ean = $variant['ean'];
            $weight = (float)$variant['gewicht'];
            $pseudoPrice = $variant['pseudopreis'];
            if (empty($pseudoPrice)) {
                $pseudoPrice = 0;
            }
            if (!empty($variant['pseudolager'])) {
                $stock = $variant['pseudolager'];
            }
            $active = true;
            if (!empty($variant['inaktiv'])) {
                $active = false;
            }
            $isCloseOut = false;
            if (!empty($variant['restmenge'])) {
                $isCloseOut = true;
            }

            $variantProductData = [
                'active' => $active,
                'isCloseout' => $isCloseOut,
                'name' => $name,
                'description' => null,
                'weight' => null,
                'price' => [
                    [
                        'currencyId' => $currencyId,
                        'gross' => $variant['bruttopreis'],
                        'net' => $variant['preis'],
                        'linked' => true,
                        'listPrice' => [
                            'currencyId' => $currencyId,
                            'gross' => $pseudoPrice,
                            'linked' => true,
                            'net' => $pseudoPrice / (1 + $variant['steuersatz'] / 100)
                        ]
                    ]
                ],
                'stock' => (int)$stock,
                'ean' => null,
                'taxId' => $this->getTaxIdByRate($variant['steuersatz']),
            ];
            if(!empty($weight)){
                $variantProductData['weight'] = $weight;
            }
            if(!empty($ean)){
                $variantProductData['ean'] = $ean;
            }
            if (!empty($variant['uebersicht_de'])) {
                $variantProductData['description'] = $variant['uebersicht_de'];
            }

            $renewVariant = false;
            $options = [];
            foreach ($internalVariantMatrixData as $expression) {
                if (!in_array(
                    $internalGroupPropertiesToShopwareId[$expression['name']][$expression['values']],
                    $existingCombinationsByNumber[$productNumber]['options'],
                    false)) {
                    $renewVariant = true;
                } else {
                    unset($existingCombinationsByNumber[$productNumber]['options'][$internalGroupPropertiesToShopwareId[$expression['name']][$expression['values']]]);
                }
                $options[] = ['id' => $internalGroupPropertiesToShopwareId[$expression['name']][$expression['values']]];
            }

            if (!empty($existingCombinationsByNumber[$productNumber]['options'])) {
                $renewVariant = true;
            }

            $variantImageData = [
                'Dateien' => []
            ];
            $variantProductId = '';
            if (!empty($existingCombinationsByNumber[$productNumber]['id']) && !$renewVariant) {
                $variantProductId = $existingCombinationsByNumber[$productNumber]['id'];
            }
            if (!empty($variant['Dateien']['id'])) {
                foreach ($variant['Dateien']['id'] as $index => $fileId) {
                    $variantImageData['Dateien'][] = [
                        'filename' => $variant['Dateien']['filename'][$index],
                        'extension' => $variant['Dateien']['extension'][$index],
                        'datei' => $variant['Dateien']['datei'][$index],
                        'beschreibung' => $variant['Dateien']['beschreibung'][$index],
                        'titel' => $variant['Dateien']['titel'][$index],
                        'id' => $fileId,
                    ];
                }
            }
            $mediaToAdd = $this->mediaToExport($variantImageData, $variantProductId);
            $variantProductData['media'] = $mediaToAdd;

            if ($renewVariant) {
                if (!empty($existingCombinationsByNumber[$productNumber]['id'])) {
                    $this->shopwareRequest('DELETE', 'product/' . $existingCombinationsByNumber[$productNumber]['id']);
                }
                $variantProductData['productNumber'] = $productNumber;
                $variantProductData['parentId'] = $articleIdShopware;
                $variantProductData['options'] = $options;

                $result = $this->shopwareRequest('POST', 'product?_response=true', $variantProductData);
                $variantProductId = $result['data']['id'];
            } else {
                $variantProductId = $existingCombinationsByNumber[$productNumber]['id'];
                $this->shopwareRequest('PATCH', 'product/' . $variantProductId, $variantProductData);
            }

            $defaultPrices = $this->getPricesFromArray($variant['staffelpreise_standard'] ?? []);
            $groupPrices = $this->getPricesFromArray($variant['staffelpreise_gruppen'] ?? []);

            $this->deleteOldBulkPrices($variantProductId);
            if (!empty($defaultPrices)) {
              foreach ($defaultPrices as $priceData) {
                $this->exportBulkPriceForGroup($variantProductId, $this->defaultRuleName, $priceData);
              }
            }
            if (!empty($groupPrices)) {
              foreach ($groupPrices as $priceData) {
                $this->exportBulkPriceForGroup($variantProductId, $priceData->getGroupName(), $priceData);
              }
            }

            $this->addCoverImage($variantImageData, $variantProductId);
        }

        $existingConfigurations = $this->shopwareRequest(
            'GET', 'product/' . $articleIdShopware . '/configuratorSettings');
        $optionIdsToAdd = [];
        foreach ($article['artikel_varianten'] as $variant) {
            foreach ($article['matrix_varianten']['artikel'][$variant['artikel']] as $matrixInfo) {
                $configurationExists = false;
                foreach ($existingConfigurations['data'] as $configuration) {
                    if ($configuration['attributes']['optionId'] === $internalGroupPropertiesToShopwareId[$matrixInfo['name']][$matrixInfo['values']]) {
                        $configurationExists = true;
                        break;
                    }
                }
                if (!$configurationExists) {
                    $optionIdsToAdd[] = $internalGroupPropertiesToShopwareId[$matrixInfo['name']][$matrixInfo['values']];
                }
            }
        }
        if (!empty($optionIdsToAdd)) {
            $optionIdsToAdd = array_flip(array_flip($optionIdsToAdd));
            $configurationData = [
                'configuratorSettings' => []
            ];
            foreach ($optionIdsToAdd as $id) {
                $configurationData['configuratorSettings'][] = ['optionId' => $id];
            }

            $this->shopwareRequest(
                'PATCH',
                sprintf('product/%s', $articleIdShopware),
                $configurationData
            );

            $existingConfigurations = $this->shopwareRequest(
                'GET', 'product/' . $articleIdShopware . '/configuratorSettings');
            $optionsToSort = [];
            foreach ($article['artikel_varianten'] as $variant) {
                foreach ($article['matrix_varianten']['artikel'][$variant['artikel']] as $matrixInfo) {
                    foreach ($existingConfigurations['data'] as $configuration) {
                        if ($configuration['attributes']['optionId'] === $internalGroupPropertiesToShopwareId[$matrixInfo['name']][$matrixInfo['values']]) {
                            $optionsToSort[] = $configuration['id'];
                            break;
                        }
                    }
                }
            }
            if (!empty($optionsToSort)) {
                $optionsToSort = array_flip(array_flip($optionsToSort));
                $configurationData = [
                    'configuratorSettings' => []
                ];
                $position = 1;

                foreach ($optionsToSort as $id) {
                    $configurationData['configuratorSettings'][] = [
                        'id' => $id,
                        'position' => $position];
                    $position++;
                }

                $this->shopwareRequest(
                    'PATCH',
                    sprintf('product/%s', $articleIdShopware),
                    $configurationData
                );
            }
        }

        return true;
    }

  /**
   * @param $priceArray
   * @return PriceData[]
   */
    protected function getPricesFromArray($priceArray): array{
      return array_map(static function($price){
        return new PriceData(
          (int)$price['ab_menge'],
          (float)$price['preis'],
          (float)$price['bruttopreis'],
          $price['waehrung'],
          $price['gruppeextern'] ?? '') ;
      },$priceArray);
    }

    /**
     * delete all old price entries for a product
     *
     * @param string $productId
     */
    protected function deleteOldBulkPrices($productId)
    {
      //TODO Instead of deleting all old prices we should rather check first whether they are still in order
        $oldPrices = $this->shopwareRequest(
            'GET',
            sprintf('product-price?filter[product_price.productId]=%s', $productId)
        );
        if (is_array($oldPrices)) {
            foreach ($oldPrices['data'] as $deletePrice) {
                $this->shopwareRequest('DELETE', 'product-price/' . $deletePrice['id']);
            }
        } else {
            $this->Shopware6Log('Fehler: Alte Preise wurden nicht gelöscht', $productId);
        }
    }

    /**
     * @return int
     */
    public function getOrderSearchLimit(): int
    {
      if(in_array($this->orderSearchLimit, ['50', '75', '100'])) {
        return (int)$this->orderSearchLimit;
      }

      return 25;
    }

    /**
     * @return int
     */
    public function ImportGetAuftraegeAnzahl()
    {
        $order = null;
        $dataToGet = $this->CatchRemoteCommand('data');

        if (empty($this->statesToFetch)) {
          return false;
        }

        $ordersToProcess = $this->getOrdersToProcess($this->getOrderSearchLimit());

        return count($ordersToProcess['data']);
    }

    /**
     * @param string $parameter1
     * @param string $parameter2
     */
    public function Shopware6ErrorLog($parameter1, $parameter2 = '')
    {
        $this->app->DB->Insert(
            sprintf(
                "INSERT INTO `shopexport_log` 
             (shopid, typ, parameter1, parameter2, bearbeiter, zeitstempel) 
            VALUES (%d, 'fehler', '%s','%s','%s',NOW())",
                $this->shopid,
                $this->app->DB->real_escape_string($parameter1),
                $this->app->DB->real_escape_string($parameter2),
                $this->app->DB->real_escape_string($this->app->User->GetName())
            )
        );
    }

    /**
     * @param array $stateMachinesIds
     * @return array
     */
    protected function getTransactionStateIdsToFetch($stateMachinesIds): array
    {
        $transactionStateIdsToFetch = [];
        if (!empty($this->transactionStatesToFetch)) {
            $transactionStatesToFetch = explode(';', $this->transactionStatesToFetch);
            foreach ($transactionStatesToFetch as $transactionStateToFetch) {
                $stateInformation = $this->shopwareRequest('GET', 'state-machine-state?filter[technicalName]=' .
                    trim($transactionStateToFetch) . '&filter[stateMachineId]=' . $stateMachinesIds['order_transaction.state']);
                if (empty($stateInformation['data'])) {
                    $this->Shopware6ErrorLog('Zahlungsstatus für Abholung nicht gefunden', $transactionStateToFetch);
                    return false;
                }
                foreach ($stateInformation['data'] as $state) {
                    $transactionStateIdsToFetch[] = $state['id'];
                }
            }
        }

        return $transactionStateIdsToFetch;
    }

    /**
     * @param int $limit
     *
     * @return mixed
     */
    protected function getOrdersToProcess(int $limit)
    {
        $searchData = [
            'limit' => $limit,
            'includes' => [
                'order' => ['id']
            ],
            'sort' => [
                [
                    'field' => 'order.createdAt',
                    'direction' => 'DESC'
                ]
            ],
            'filter' => []
        ];

        $searchData['filter'][] = [
            'field' => 'stateMachineState.technicalName',
            'type' => 'equalsAny',
            'value' => explode(';', $this->statesToFetch)
        ];

        if (!empty($this->deliveryStatesToFetch)) {
            $searchData['filter'][] = [
                'field' => 'deliveries.stateMachineState.technicalName',
                'type' => 'equalsAny',
                'value' => explode(';', $this->deliveryStatesToFetch)
            ];
        }
        if (!empty($this->transactionStatesToFetch)) {
            $searchData['filter'][] = [
                'field' => 'transactions.stateMachineState.technicalName',
                'type' => 'equalsAny',
                'value' => explode(';', $this->transactionStatesToFetch)
            ];
        }

        if (!empty($this->salesChannelToFetch)) {
            $searchData['filter'][] = [
                'field' => 'order.salesChannelId',
                'type' => 'equals',
                'value' => $this->salesChannelToFetch
            ];
        }

        return $this->shopwareRequest('POST', 'search/order', $searchData);
    }

    /**
     * @return int|mixed
     */
    public function ImportGetAuftrag()
    {
        $voucherArticleId = $this->app->DB->Select("SELECT s.artikelrabatt FROM `shopexport` AS `s` WHERE s.id='$this->shopid' LIMIT 1");
        $voucherArticleNumber = $this->app->DB->Select("SELECT a.nummer FROM `artikel` AS `a` WHERE a.id='$voucherArticleId' LIMIT 1");

        $dataToGet = $this->CatchRemoteCommand('data');
        if (empty($this->statesToFetch)) {
            return false;
        }
        $expectOrderArray = !empty($dataToGet['anzgleichzeitig']) && (int)$dataToGet['anzgleichzeitig'] > 1;
        $expectNumber = !empty($dataToGet['nummer']);
        $order = null;
        if($expectNumber) {
            $order = $this->shopwareRequest('GET', 'order/' . $dataToGet['nummer'] . '?associations[currency][]');
            if(empty($order['data'])) {
                return false;
            }
            $ordersToProcess = ['data' => [ ['id' => $dataToGet['nummer']] ]];
            $orderIncludedData = $order['included'];
            $order = $order['data'];
        }
        elseif(!$expectOrderArray) {
          $ordersToProcess = $this->getOrdersToProcess(1);
        }
        elseif(!$expectNumber) {
          $ordersToProcess = $this->getOrdersToProcess($this->getOrderSearchLimit());
        }
        if (empty($ordersToProcess['data'])) {
            return false;
        }

        $fetchedOrders = [];
        if (isset($ordersToFetch['data']['id']) && !isset($ordersToFetch['data'][0])) {
            $ordersToFetch['data'] = [$ordersToFetch['data']];
        }
        foreach ($ordersToProcess['data'] as $currentlyOpenOrder) {
            $orderIdToFetch = $currentlyOpenOrder['id'];

            if (empty($dataToGet['nummer']) || empty($order)) {
                $order = $this->shopwareRequest('GET', 'order/' . $orderIdToFetch.'?associations[currency][]');
                $orderIncludedData = $order['included'];
                $order = $order['data'];
            }
            $cart = [];
            try {
                $timestamp = date_create_from_format('Y-m-d\TH:i:s+', $order['attributes']['createdAt']);
                $cart['zeitstempel'] = $timestamp->format('Y-m-d H:i:s');
            } catch (Exception $ex) {

            }
            $cart['auftrag'] = $order['id'];
            $cart['subshop'] = $order['attributes']['salesChannelId'];
            $cart['order'] = $order;
            $cart['onlinebestellnummer'] = $order['attributes']['orderNumber'];
            $cart['gesamtsumme'] = $order['attributes']['amountTotal'];
            $cart['versandkostenbrutto'] = $order['attributes']['shippingTotal'];
            $cart['bestelldatum'] = substr($order['attributes']['orderDate'], 0, 10);
            if (!empty($order['attributes']['customerComment'])) {
                $cart['freitext'] = $order['attributes']['customerComment'];
            }

            foreach ($orderIncludedData as $includedDataSet){
                if($includedDataSet['type'] === 'currency'){
                    $cart['waehrung'] = $includedDataSet['attributes']['isoCode'];
                }
            }

            $deliveryInfo = $this->shopwareRequest('GET', 'order/' . $order['id'] . '/deliveries');
            $shippingMethod = $this->shopwareRequest('GET',
                'order-delivery/' . $deliveryInfo['data'][0]['id'] . '/shipping-method');
            $order['shippingMethod'] = $shippingMethod;
            $cart['lieferung'] = $shippingMethod['data'][0]['attributes']['name'];

            $customer = $this->shopwareRequest('GET', 'order/' . $order['id'] . '/order-customer');
            $order['customer'] = $customer;
            $cart['email'] = $customer['data']['0']['attributes']['email'];

            $addresses = $this->shopwareRequest('GET', 'order/' . $order['id'] . '/addresses?associations[salutation][]&associations[country][]');
            $order['addresses'] = $addresses;
            $deliveryCountryId = '';
            $billingCountryId = '';
            $billingSalutationId = '';
            foreach ($addresses['data'] as $address) {
                if ($address['id'] === $order['attributes']['billingAddressId']) {
                    if (!empty($address['attributes']['vatId'])) {
                        $cart['ustid'] = $address['attributes']['vatId'];
                    }
                    $cart['name'] = $address['attributes']['firstName'] . ' ' . $address['attributes']['lastName'];
                    if (!empty($address['attributes']['company'])) {
                        $cart['ansprechpartner'] = $cart['name'];
                        $cart['name'] = $address['attributes']['company'];
                    }
                    $cart['strasse'] = $address['attributes']['street'];
                    $cart['abteilung'] = $address['attributes']['department'];
                    $cart['adresszusatz'] = trim($address['attributes']['additionalAddressLine1'].' '.
                      $address['attributes']['additionalAddressLine2']);
                    $cart['telefon'] = $address['attributes']['phoneNumber'];
                    $cart['plz'] = $address['attributes']['zipcode'];
                    $cart['ort'] = $address['attributes']['city'];
                    $billingCountryId = $address['attributes']['countryId'];
                    $billingSalutationId = $address['attributes']['salutationId'];
                }
                if ($address['id'] !== $order['attributes']['billingAddressId']) {
                    $cart['abweichendelieferadresse'] = 1;
                    if (!empty($address['attributes']['vatId'])) {
                        $cart['lieferadresse_ustid'] = $address['attributes']['vatId'];
                    }
                    $cart['lieferadresse_name'] = $address['attributes']['firstName'] . ' ' . $address['attributes']['lastName'];
                    if (!empty($address['attributes']['company'])) {
                        $cart['lieferadresse_ansprechpartner'] = $cart['lieferadresse_name'];
                        $cart['lieferadresse_name'] = $address['attributes']['company'];
                    }
                    $cart['lieferadresse_strasse'] = $address['attributes']['street'];
                    $cart['lieferadresse_abteilung'] = $address['attributes']['department'];
                    $cart['lieferadresse_adresszusatz'] = trim($address['attributes']['additionalAddressLine1'].' '.
                    $address['attributes']['additionalAddressLine2']);
                    $cart['lieferadresse_plz'] = $address['attributes']['zipcode'];
                    $cart['lieferadresse_ort'] = $address['attributes']['city'];
                    $deliveryCountryId = $address['attributes']['countryId'];
                }
            }

            $anrede = 'herr';
            $land = 'DE';
            $lieferadresseLand = 'DE';
            foreach ($addresses['included'] as $includedInfo) {
                if ($includedInfo['id'] === $billingCountryId) {
                    $land = $includedInfo['attributes']['iso'];
                }
                if ($includedInfo['id'] === $deliveryCountryId) {
                    $lieferadresseLand = $includedInfo['attributes']['iso'];
                }
                if ($includedInfo['id'] === $billingSalutationId) {
                    $salutation = $includedInfo['attributes']['salutationKey'];
                    if ($salutation === 'ms' || $salutation === 'mrs') {
                        $anrede = 'frau';
                    }
                }
            }

            $cart['anrede'] = $anrede;
            $cart['land'] = $land;
            if (!empty($cart['abweichendelieferadresse'])) {
                $cart['lieferadresse_land'] = $lieferadresseLand;
            }

            $transactionData = $this->shopwareRequest('GET', 'order/' . $order['id'] . '/transactions');
            $cart['transacion_data'] = $transactionData;
            if (!empty($transactionData['data'][0]['attributes']['customFields']['swag_paypal_pui_payment_instruction']['reference_number'])) {
                $cart['transaktionsnummer'] = $transactionData['data'][0]['attributes']['customFields']['swag_paypal_pui_payment_instruction']['reference_number'];
            }
            if (empty($cart['transaktionsnummer'] && !empty($transactionData['data'][0]['attributes']['customFields']['swag_paypal_order_id']))) {
                $cart['transaktionsnummer'] = (string)$transactionData['data'][0]['attributes']['customFields']['swag_paypal_order_id'];
            }
            if (empty($cart['transaktionsnummer'] && !empty($transactionData['data'][0]['attributes']['customFields']['swag_paypal_transaction_id']))) {
                $livePayPalData = $this->shopwareRequest('GET', 'paypal/payment-details/' . $order['id'] . '/' . $transactionData['data'][0]['attributes']['customFields']['swag_paypal_transaction_id']);
                if (!empty($livePayPalData['transactions'])) {
                    foreach ($livePayPalData['transactions'] as $payPalData) {
                        foreach ($payPalData['related_resources'] as $ressources) {
                            if ($ressources['sale']['state'] === 'completed') {
                                $cart['transaktionsnummer'] = $ressources['sale']['id'];
                                break 2;
                            }
                        }
                    }
                }
            }
            if(
                empty($cart['transaktionsnummer'])
                && isset($transactionData['data'][0]['attributes']['customFields']['stripe_payment_context']['payment']['payment_intent_id'])
            ){
                $cart['transaktionsnummer'] = $transactionData['data'][0]['attributes']['customFields']['stripe_payment_context']['payment']['payment_intent_id'];
            }

            $paymentMethodId = $transactionData['data'][0]['attributes']['paymentMethodId'];
            $paymentMethod = $this->shopwareRequest('GET', 'payment-method/' . $paymentMethodId);
            $cart['zahlungsweise'] = $paymentMethod['data']['attributes']['name'];

            $taxedCountry = $land;
            if($this->taxationByDestinationCountry){
                $taxedCountry = $lieferadresseLand;
            }
            if($order['attributes']['amountTotal'] === $order['attributes']['amountNet']){
                if($this->app->erp->IstEU($taxedCountry)){
                    $cart['ust_befreit'] = 1;
                }elseif($this->app->erp->Export($taxedCountry)){
                    $cart['ust_befreit'] = 2;
                }else{
                    $cart['ust_befreit'] = 3;
                }
            }

            $lineItems = $this->shopwareRequest('GET', 'order/' . $order['id'] . '/line-items');
            $order['lineItems'] = $lineItems;
            $cart['articlelist'] = [];

            $taxRate = 0;
            foreach ($lineItems['data'] as $lineItem) {
                if ($lineItem['attributes']['price']['calculatedTaxes'][0]['taxRate'] > $taxRate) {
                    $taxRate = $lineItem['attributes']['price']['calculatedTaxes'][0]['taxRate'];
                }
            }

            $orderPriceType = 'price';
            if(in_array($order['attributes']['taxStatus'], ['net', 'tax-free'])) {
                $orderPriceType = 'price_netto';
                $cart['versandkostennetto'] = $cart['versandkostenbrutto'];
                unset($cart['versandkostenbrutto']);
            }

            foreach ($lineItems['data'] as $lineItem) {
                $productPriceType = $orderPriceType;
                if(empty($lineItem['attributes']['price']['calculatedTaxes'][0]['taxRate'])){
                    $productPriceType = 'price_netto';
                }
                $articleId = null;
                if($lineItem['attributes']['price']['unitPrice'] < 0) {
                  $articleId = $voucherArticleNumber;
                }
                elseif(isset($lineItem['attributes']['payload']['productNumber'])){
                  $articleId = $lineItem['attributes']['payload']['productNumber'];
                }
                $product = [
                    'articleid' => $articleId,
                    'name' => $lineItem['attributes']['label'],
                    'quantity' => $lineItem['attributes']['quantity'],
                    $productPriceType => $lineItem['attributes']['price']['unitPrice'],
                    'steuersatz' => $lineItem['attributes']['price']['calculatedTaxes'][0]['taxRate'],
                ];
                $cart['articlelist'][] = $product;
            }

            $cart['order'] = $order;
            $fetchedOrders[] = [
                'id' => $cart['auftrag'],
                'sessionid' => '',
                'logdatei' => '',
                'warenkorb' => base64_encode(serialize($cart)),
                'warenkorbjson' => base64_encode(json_encode($cart)),
            ];
            $this->Shopware6Log('Ergebnis: Auftrag', $order);
            $this->Shopware6Log('Ergebnis: Adresse', $addresses);
            $this->Shopware6Log('Ergebnis: Positionen', $lineItems);
        }

        return $fetchedOrders;
    }

    /**
     * @return void
     */
    public function ImportDeleteAuftrag()
    {
        $tmp = $this->CatchRemoteCommand('data');
        $auftrag = $tmp['auftrag'];

        $this->shopwareRequest('POST', '_action/order/'.$auftrag.'/state/process');
        $this->addCustomFieldToOrder((string)$auftrag);
    }

    /**
     * @return void
     */
    public function ImportUpdateAuftrag()
    {
        $tmp = $this->CatchRemoteCommand('data');
        $auftrag = $tmp['auftrag'];
        $tracking = $tmp['tracking'];

        $this->shopwareRequest('POST', '_action/order/'.$auftrag.'/state/complete');

        $deliveries = $this->shopwareRequest('GET', 'order/'.$auftrag.'/deliveries');
        $deliveryId = $deliveries['data'][0]['id'];

        if(!empty($deliveryId)){
            $this->shopwareRequest('POST', '_action/order_delivery/'.$deliveryId.'/state/ship');

            $deliveryData = [
                'trackingCodes' => [$tracking]
            ];
            $this->shopwareRequest('PATCH', 'order-delivery/'.$deliveryId,$deliveryData);
        }

        $this->sendInvoce($auftrag);
        $this->addCustomFieldToOrder((string)$auftrag);
        if(empty($tmp['orderId'])) {
            return;
        }
        $this->updateStorageForOrderIntId((int)$tmp['orderId']);
    }

    public function ImportStorniereAuftrag()
    {
        $tmp = $this->CatchRemoteCommand('data');
        $auftrag = $tmp['auftrag'];

        $this->shopwareRequest('POST', '_action/order/'.$auftrag.'/state/cancel');
        $this->addCustomFieldToOrder((string)$auftrag);
    }

    /**
     * @param string $extOrderId
     */
    protected function sendInvoce($extOrderId)
    {
        $order = $this->app->DB->SelectRow(
            sprintf(
                "SELECT `rechnungid`, `id` FROM `auftrag` WHERE shopextid='%s'",
                $extOrderId
            )
        );
        $invoiceId = 0;
        if (!empty($order['rechnungid'])) {
            $invoiceId = $order['rechnungid'];
            $sql = sprintf("SELECT projekt, belegnr FROM rechnung WHERE id='%s'", $invoiceId);
            $invoiceData = $this->app->DB->SelectRow($sql);
        }
        if (empty($invoiceId) && !empty($order['id'])) {
            $invoiceData = $this->app->DB->SelectRow(
                sprintf(
                    "SELECT `id`, `projekt`, `belegnr` 
                      FROM `rechnung` 
                      WHERE `auftragid` = %d AND `status` <> 'storniert' AND `status` <> 'angelegt' 
                      LIMIT 1",
                    $order['id']
                )
            );
            if (!empty($invoiceData)) {
                $invoiceId = $invoiceData['id'];
            }
        }

        if (!empty($invoiceData['belegnr'])) {
            $projekt = $invoiceData['projekt'];
            if (class_exists('RechnungPDFCustom')) {
                $Brief = new RechnungPDFCustom($this->app, $projekt);
            } else {
                $Brief = new RechnungPDF($this->app, $projekt);
            }

            $Brief->GetRechnung($invoiceId);
            $filePath = $Brief->displayTMP(true);

            $documentNumber = $invoiceData['belegnr'];
            $invoiceDocumentData = [
                'config' => [
                    'custom' => [
                        'invoiceNumber' => $documentNumber,
                    ],
                    'documentComment' => 'Aus Xentral heraus erstellte Rechnung',
                    'documentNumber' => $documentNumber,
                ],
                'referenced_document_id' => null,
                'static' => true
            ];

            $documentData = $this->shopwareRequest('POST', '_action/order/' . $extOrderId . '/document/invoice', $invoiceDocumentData);
            $documentId = $documentData['documentId'];

            $accessToken = $this->shopwareToken();
            $url = $this->ShopUrl . 'v2/_action/document/' . $documentId . '/upload?_response=true&extension=pdf&fileName=' . $documentNumber;

            $ch = curl_init();
            $setHeaders = [
                'Content-Type:application/pdf',
                'Authorization:Bearer ' . $accessToken['token']
            ];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($filePath));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);
            if (!empty($response['errors'])) {
                $this->Shopware6Log(
                    'Fehler bei Rechnugnsübertragung für ' . $documentNumber, $response['errors']
                );
            }
        }
    }

    /**
     * @return string
     */
    public function ImportAuth()
    {
        $tokeninfo = $this->shopwareToken();


        if (!$tokeninfo['success']) {
            return 'failed: ' . $tokeninfo['message'];
        }
        if($this->data === 'info'){
            $salesChannelsInShopware = $this->client->getAllSalesChannels();
            $salesChannelsToShow = ['subshops' => []];
            foreach ($salesChannelsInShopware['data'] as $salesChannelInShopware){
                $salesChannelsToShow['subshops'][] = [
                    'id'=>$salesChannelInShopware['id'],
                    'name'=>$salesChannelInShopware['name'],
                    'aktiv'=>$salesChannelInShopware['active']
                ];
            }
            return $salesChannelsToShow;
        }

        return 'success';
    }

    /**
     * Build category tree as displayed in article info
     * May be useful for setting category in the future
     * but probably obsolete
     *
     * @param string $categoryName
     * @param array $categoryTree
     *
     * @return array
     */
    protected function appendCategoryTree($categoryName, $categoryTree = [])
    {
        $shopwareCategory = $this->shopwareRequest(
            'GET',
            'category?filter[category.name]=' . urlencode($categoryName)
        );
        if (!isset($shopwareCategory['data'][0]['id'])) {
            return $categoryTree;
        }
        $categoryInfo = $shopwareCategory['data'][0]['attributes'];
        $categories[] = [(int)$categoryInfo['level'], $shopwareCategory['data'][0]['id']];
        $path = $categoryInfo['path'];
        if (!empty($path)) {
            $pathArray = explode('|', $path);
            foreach ($pathArray as $nodeId) {
                if ($nodeId === '') {
                    continue;
                }
                $nodeCategory = $this->shopwareRequest('GET', 'category/' . $nodeId);
                if (isset($nodeCategory['data']['id'])) {
                    $categories[] = [(int)$nodeCategory['data']['attributes']['level'], $nodeId];
                    unset($nodeCategory);
                }
            }
        }
        foreach ($categories as $category) {
            $level = $category[0];
            if (!isset($categoryTree[$level])) {
                $categoryTree[$level] = [];
            }
            if (!in_array($category, $categoryTree[$level], true)) {
                $categoryTree[$level][] = $category[1];
            }
        }
        ksort($categoryTree);

        return $categoryTree;
    }

  /**
   * @param array $postData
   *
   * @return array
   */
    public function updatePostDataForAssistent($postData)
    {
      if(!empty($this->ShopUrl)) {
        $postData['shopwareUrl'] = $this->ShopUrl;
      }
      return $postData;
    }

  /**
   * @param array $shopArr
   * @param array $postData
   *
   * @return array
   */
    public function updateShopexportArr($shopArr, $postData)
    {
      $shopArr['stornoabgleich'] = 1;
      $shopArr['demomodus'] = 0;

      return $shopArr;
    }

  /**
   * @return JsonResponse|null
   */
    public function AuthByAssistent()
    {
      $shopwareUrl = $this->app->Secure->GetPOST('shopwareUrl');
      $shopwareUserName = $this->app->Secure->GetPOST('shopwareUserName');
      $shopwarePassword = $this->app->Secure->GetPOST('shopwarePassword');
      $step = (int)$this->app->Secure->GetPOST('step');

      if($step <= 1){
        if(empty($shopwareUrl)){
          return new JsonResponse(['error' => 'Bitte die URL des Shops angeben.'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if(empty($shopwareUserName)){
          return new JsonResponse(['error' => 'Bitte den Benutzernamen angeben'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if(empty($shopwarePassword)){
          return new JsonResponse(['error' => 'Bitte das Passwort angeben'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->UserName = $shopwareUserName;
        $this->Password = $shopwarePassword;
        $shopwareUrl = rtrim($shopwareUrl, '/') . '/';
        $testUrls = [];
        $hasNoHttp = strpos($shopwareUrl,'http') !== 0;
        if(substr($shopwareUrl, -5) !== '/api/') {
          if($hasNoHttp) {
            $testUrls[] = 'https://'.$shopwareUrl.'api/';
            $testUrls[] = 'http://'.$shopwareUrl.'api/';
          }
          $testUrls[] = $shopwareUrl.'api/';
        }
        elseif($hasNoHttp) {
          $testUrls[] = 'https://'.$shopwareUrl;
          $testUrls[] = 'http://'.$shopwareUrl;
        }
        else {
          $testUrls[] = $shopwareUrl;
        }
        foreach($testUrls as $testUrl) {
          $this->ShopUrl = $testUrl;
          $tokeninfo = $this->shopwareToken();
          if(!empty($tokeninfo['success'])) {
            break;
          }
        }

        if(!$tokeninfo['success']){
          return new JsonResponse(['error' => $tokeninfo['message']], JsonResponse::HTTP_BAD_REQUEST);
        }
      }

      return null;
    }

  /**
   * @return string
   */
    public function getClickByClickHeadline()
    {
      return 'Bitte im Shopware Backend einen eigenen Benutzer für Xentral anlegen und diese 
      Zugangsdaten hier eintragen.';
    }

  /**
   * @return array
   */
    public function getStructureDataForClickByClickSave()
    {
      return [
        'shopwareAllowCreateManufacturer' => 1,
      ];
    }

  /**
   * @return array[]
   */
    public function getCreateForm()
    {
      return [
        [
          'id' => 0,
          'name' => 'urls',
          'inputs' => [
            [
              'label' => 'URL des Shops',
              'type' => 'text',
              'name' => 'shopwareUrl',
              'validation' => true,
            ],
          ],
        ],
        [
          'id' => 1,
          'name' => 'username',
          'inputs' => [
            [
              'label' => 'Benutzername aus Shopware',
              'type' => 'text',
              'name' => 'shopwareUserName',
              'validation' => true,
            ],
          ],
        ],
        [
          'id' => 2,
          'name' => 'password',
          'inputs' => [
            [
              'label' => 'Passwort aus Shopware',
              'type' => 'password',
              'name' => 'shopwarePassword',
              'validation' => true,
            ],
          ],
        ],
      ];
    }

    public function getBoosterHeadline(): string
    {
      return 'Shopware 6 Business Booster App';
    }

    public function getBoosterSubHeadline(): string
    {
      return 'Bitte gehe auf Shopware 6 und installiere dort das Plugin Xentral Business Booster App.
      Dort kann man sich dann mit ein paar Klicks mit Xentral verbinden.';
    }

  /**
   * @param int $intOrderId
   *
   * @return array
   */
    protected function getArticleShopLinks(int $intOrderId): array
    {
      return $this->app->DB->SelectPairs(
        "SELECT DISTINCT ao.artikel, a.nummer
        FROM `auftrag_position` AS `ap`
        INNER JOIN `auftrag` AS `ab` ON ap.auftrag = ab.id
        INNER JOIN `artikel` AS `a` ON ap.artikel = a.id 
        INNER JOIN `artikel_onlineshops` AS `ao` ON ab.shop = ao.shop AND a.id = ao.artikel 
        WHERE ab.id = {$intOrderId} AND ao.aktiv = 1"
      );
    }

    /**
     * @param array $articleIds
     */
    protected function updateArticleCacheToSync(array $articleIds): void
    {
      if(empty($articleIds)) {
        return;
      }
      $articleIdsString = implode(', ', $articleIds);
      $this->app->DB->Update(
        "UPDATE `artikel` 
        SET `laststorage_changed` = DATE_ADD(NOW(), INTERVAL 1 SECOND) 
        WHERE `id` IN ({$articleIdsString})"
      );
    }

    /**
     * @param array $articleIds
     */
    protected function updateArticleOnlineShopCache(array $articleIds): void
    {
      if(empty($articleIds)) {
        return;
      }
      $articleIdsString = implode(', ', $articleIds);
      $this->app->DB->Update(
        "UPDATE `artikel_onlineshops` 
        SET `storage_cache` = -999, `pseudostorage_cache` = -999 
        WHERE `artikel` IN ({$articleIdsString}) AND `aktiv` = 1 AND `shop` = {$this->shopid}"
      );
    }

    /**
     * @param int $intOrderId
     */
    protected function updateStorageForOrderIntId(int $intOrderId): void
    {
      $articles = $this->getArticleShopLinks($intOrderId);
      if(empty($articles)) {
        return;
      }
      $articleIds = array_keys($articles);
      $this->updateArticleCacheToSync($articleIds);
      $this->updateArticleOnlineShopCache($articleIds);

      $isStorageSyncCronjobActive = (int)$this->app->DB->Select(
          "SELECT COUNT(`id`) FROM `prozessstarter` WHERE `aktiv` = 1 AND `parameter` = 'lagerzahlen'"
        ) > 0;
      if(!$isStorageSyncCronjobActive) {
        return;
      }
      foreach($articleIds as $articleId) {
        try {
          $this->app->erp->LagerSync($articleId, false, [$this->shopid]);
        }
        catch (Exception $e) {
          $articleNumber = $articles[$articleId];
          $this->Shopware6ErrorLog('LagerSync konnte nicht ausgeführt werden', $articleNumber);
        }
      }

      $this->updateArticleCacheToSync($articleIds);
    }
}
