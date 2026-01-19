#!/usr/bin/env php
<?php
/**
 * OpenXE API Documentation Generator
 * 
 * Generiert eine RAML-Datei aus den API-Routen und Resource-Klassen.
 * 
 * Verwendung:
 *   php tools/generate-api-docs.php              # Generiert docs.raml
 *   php tools/generate-api-docs.php --check      # Prüft ob docs.raml aktuell ist
 *   php tools/generate-api-docs.php --format=openapi  # Generiert OpenAPI 3.0 (JSON)
 * 
 * @author OpenXE Team
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

// CLI-Argumente parsen
$options = getopt('', ['check', 'format:', 'output:', 'help']);

if (isset($options['help'])) {
    echo <<<HELP
OpenXE API Documentation Generator

Usage:
  php tools/generate-api-docs.php [options]

Options:
  --check       Check if docs.raml is up-to-date (exit code 1 if not)
  --format=FMT  Output format: raml (default) or openapi
  --output=FILE Custom output file path
  --help        Show this help

Examples:
  php tools/generate-api-docs.php
  php tools/generate-api-docs.php --check
  php tools/generate-api-docs.php --format=openapi --output=api/openapi.json

HELP;
    exit(0);
}

$checkOnly = isset($options['check']);
$format = $options['format'] ?? 'raml';
$outputFile = $options['output'] ?? null;

// Autoloader laden
require_once ROOT_PATH . '/vendor/autoload.php';

/**
 * Klasse zum Extrahieren von API-Metadaten aus dem Code
 */
class ApiDocumentationExtractor
{
    private string $classesPath;
    private array $routes = [];
    private array $resources = [];
    
    public function __construct(string $classesPath)
    {
        $this->classesPath = $classesPath;
    }
    
    /**
     * Extrahiert alle Routen aus ApiApplication.php
     */
    public function extractRoutes(): array
    {
        $apiAppPath = $this->classesPath . '/Modules/Api/Engine/ApiApplication.php';
        $content = file_get_contents($apiAppPath);
        
        // Regex um addRoute-Aufrufe zu finden
        $pattern = '/\$collection->addRoute\(\s*(\[?[\'"][A-Z,\s\'\"]+[\'\"]?\]?)\s*,\s*[\'"]([^\'\"]+)[\'"]\s*,\s*\[([^\]]+)\]\s*\)/';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        $routes = [];
        foreach ($matches as $match) {
            $methods = $this->parseMethods($match[1]);
            $path = $match[2];
            $handler = $this->parseHandler($match[3]);
            
            // Nur v1/v2 REST-API Routen (keine Legacy)
            if (strpos($path, '/v1/') === 0 || strpos($path, '/v2/') === 0) {
                if ($handler['version'] !== 'Legacy') {
                    $routes[] = [
                        'methods' => $methods,
                        'path' => $path,
                        'handler' => $handler,
                    ];
                }
            }
        }
        
        $this->routes = $routes;
        return $routes;
    }
    
    /**
     * Extrahiert Metadaten aus Resource-Klassen
     */
    public function extractResources(): array
    {
        $resourcePath = $this->classesPath . '/Modules/Api/Resource';
        $files = glob($resourcePath . '/*Resource.php');
        
        $resources = [];
        foreach ($files as $file) {
            $className = basename($file, '.php');
            if ($className === 'AbstractResource') {
                continue;
            }
            
            $content = file_get_contents($file);
            $resourceData = $this->parseResourceClass($content, $className);
            if ($resourceData) {
                $resources[$className] = $resourceData;
            }
        }
        
        $this->resources = $resources;
        return $resources;
    }
    
    /**
     * Parst die Methoden aus einem Route-Aufruf
     */
    private function parseMethods(string $methodStr): array
    {
        $methodStr = trim($methodStr, "[] \t\n\r");
        preg_match_all('/[\'"]([A-Z]+)[\'"]/', $methodStr, $matches);
        return $matches[1] ?? ['GET'];
    }
    
    /**
     * Parst den Handler aus einem Route-Aufruf
     */
    private function parseHandler(string $handlerStr): array
    {
        $parts = array_map(function($p) {
            return trim($p, " \t\n\r'\"");
        }, explode(',', $handlerStr));
        
        return [
            'version' => $parts[0] ?? '',
            'resource' => $parts[1] ?? null,
            'controller' => $parts[2] ?? '',
            'action' => $parts[3] ?? '',
            'permission' => $parts[4] ?? null,
        ];
    }
    
    /**
     * Parst eine Resource-Klasse
     */
    private function parseResourceClass(string $content, string $className): ?array
    {
        $data = [
            'className' => $className,
            'tableName' => null,
            'filterParams' => [],
            'sortingParams' => [],
        ];
        
        // TABLE_NAME extrahieren
        if (preg_match('/const\s+TABLE_NAME\s*=\s*[\'"]([^\'\"]+)[\'"]/', $content, $match)) {
            $data['tableName'] = $match[1];
        }
        
        // registerFilterParams extrahieren
        if (preg_match('/registerFilterParams\s*\(\s*\[(.*?)\]\s*\)/s', $content, $match)) {
            $data['filterParams'] = $this->parseArrayContent($match[1]);
        }
        
        // registerSortingParams extrahieren
        if (preg_match('/registerSortingParams\s*\(\s*\[(.*?)\]\s*\)/s', $content, $match)) {
            $data['sortingParams'] = $this->parseArrayContent($match[1]);
        }
        
        return $data;
    }
    
    /**
     * Parst Array-Inhalt aus PHP-Code
     */
    private function parseArrayContent(string $content): array
    {
        $params = [];
        // Einfaches Pattern für 'key' => 'value'
        preg_match_all('/[\'"]([^\'\"]+)[\'"]\s*=>\s*[\'"]([^\'\"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $params[$match[1]] = $match[2];
        }
        
        return $params;
    }
    
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    public function getResources(): array
    {
        return $this->resources;
    }
}

/**
 * RAML-Generator
 */
class RamlGenerator
{
    private array $routes;
    private array $resources;
    private string $existingDocsPath;
    
    public function __construct(array $routes, array $resources, string $existingDocsPath)
    {
        $this->routes = $routes;
        $this->resources = $resources;
        $this->existingDocsPath = $existingDocsPath;
    }
    
    /**
     * Generiert die komplette RAML-Dokumentation
     */
    public function generate(): string
    {
        $raml = $this->generateHeader();
        $raml .= $this->generateSecuritySchemes();
        $raml .= $this->generateDocumentation();
        $raml .= $this->generateEndpoints();
        
        return $raml;
    }
    
    private function generateHeader(): string
    {
        return <<<RAML
#%RAML 1.0
title: OpenXE REST-API
description: Die API befindet sich in Ihrer OpenXE-Installation im Unterordner `/www/api/`.
version: v1, v2
baseUri: http://www.example.com/api/{version}/
mediaType: application/json
securedBy: [ Digest ]

RAML;
    }
    
    private function generateSecuritySchemes(): string
    {
        return <<<RAML
securitySchemes:
  Digest:
    type: Digest Authentication
    displayName: Digest Authentifizierung
    description: |
      Die API unterstützt nur die Digest Authentifizierung.
      Grundsätzlich empfehlen wir aber die zusätzliche Absicherung mit HTTPS-Verschlüsselung.
    describedBy:
      responses:
        401:
          description: |
            Fehler bei der Authentifizierung werden immer mit dem HTTP-Status `401 Unauthorized` ausgeliefert.

RAML;
    }
    
    private function generateDocumentation(): string
    {
        // Die allgemeine Dokumentation wird aus der bestehenden docs.raml übernommen
        // da diese manuell gepflegte Texte enthält
        if (file_exists($this->existingDocsPath)) {
            $existing = file_get_contents($this->existingDocsPath);
            
            // Extrahiere den documentation-Block
            if (preg_match('/^documentation:\s*\n(.*?)(?=^\/v\d|^types:|$)/ms', $existing, $match)) {
                return "\ndocumentation:\n" . $match[1];
            }
        }
        
        // Fallback: Minimale Dokumentation
        return <<<RAML

documentation:
  - title: Authentifizierung
    content: |
      Die REST-API unterstützt die Digest Authentifizierung.
      
      ## API-Account anlegen
      In OpenXE unter *Administration > Einstellungen > API-Account*.

RAML;
    }
    
    private function generateEndpoints(): string
    {
        $output = "\n";
        
        // Routen nach Pfad gruppieren
        $grouped = [];
        foreach ($this->routes as $route) {
            $basePath = $this->getBasePath($route['path']);
            if (!isset($grouped[$basePath])) {
                $grouped[$basePath] = [];
            }
            $grouped[$basePath][] = $route;
        }
        
        // Sortieren
        ksort($grouped);
        
        foreach ($grouped as $basePath => $routes) {
            $output .= $this->generateEndpointGroup($basePath, $routes);
        }
        
        return $output;
    }
    
    private function getBasePath(string $path): string
    {
        // /v1/adressen/{id} -> /v1/adressen
        return preg_replace('/\/\{[^}]+\}.*$/', '', $path);
    }
    
    private function generateEndpointGroup(string $basePath, array $routes): string
    {
        $output = "{$basePath}:\n";
        
        // Finde die zugehörige Resource
        $resourceName = $this->findResourceForPath($basePath);
        $resource = $resourceName ? ($this->resources[$resourceName] ?? null) : null;
        
        foreach ($routes as $route) {
            $isDetailRoute = strpos($route['path'], '{id}') !== false;
            
            if ($isDetailRoute) {
                // Detail-Route unter /{id}
                continue; // Wird separat behandelt
            }
            
            foreach ($route['methods'] as $method) {
                $output .= $this->generateMethodBlock(strtolower($method), $route, $resource);
            }
        }
        
        // Detail-Routen
        $detailRoutes = array_filter($routes, fn($r) => strpos($r['path'], '{id}') !== false);
        if (!empty($detailRoutes)) {
            $output .= "  /{id}:\n";
            foreach ($detailRoutes as $route) {
                foreach ($route['methods'] as $method) {
                    $output .= $this->generateMethodBlock(strtolower($method), $route, $resource, 4);
                }
            }
        }
        
        return $output;
    }
    
    private function generateMethodBlock(string $method, array $route, ?array $resource, int $indent = 2): string
    {
        $spaces = str_repeat(' ', $indent);
        $permission = $route['handler']['permission'] ?? '';
        $action = $route['handler']['action'] ?? '';
        
        $displayName = $this->getDisplayName($method, $route['path']);
        $description = $this->getDescription($method, $permission);
        
        $output = "{$spaces}{$method}:\n";
        $output .= "{$spaces}  displayName: {$displayName}\n";
        
        if ($description) {
            $output .= "{$spaces}  description: |\n";
            $output .= "{$spaces}    {$description}\n";
            if ($permission) {
                $output .= "{$spaces}    \n";
                $output .= "{$spaces}    Permission: `{$permission}`\n";
            }
        }
        
        // Query-Parameter für GET-List-Routen
        if ($method === 'get' && strpos($route['path'], '{id}') === false && $resource) {
            $queryParams = $this->generateQueryParameters($resource, $indent);
            if ($queryParams) {
                $output .= "{$spaces}  queryParameters:\n{$queryParams}";
            }
        }
        
        return $output;
    }
    
    private function generateQueryParameters(array $resource, int $indent): string
    {
        $spaces = str_repeat(' ', $indent + 4);
        $output = '';
        
        foreach ($resource['filterParams'] as $param => $definition) {
            $output .= "{$spaces}{$param}:\n";
            $output .= "{$spaces}  description: Filter nach {$param}\n";
            $output .= "{$spaces}  type: string\n";
            $output .= "{$spaces}  required: false\n";
        }
        
        return $output;
    }
    
    private function getDisplayName(string $method, string $path): string
    {
        $resourceName = $this->extractResourceName($path);
        
        $actions = [
            'get' => strpos($path, '{id}') !== false ? 'Einzelnen Eintrag abrufen' : 'Liste abrufen',
            'post' => 'Erstellen',
            'put' => 'Bearbeiten',
            'delete' => 'Löschen',
        ];
        
        return ucfirst($resourceName) . ' ' . ($actions[$method] ?? $method);
    }
    
    private function getDescription(string $method, string $permission): string
    {
        $descriptions = [
            'get' => 'Endpunkt zum Abrufen von Daten.',
            'post' => 'Endpunkt zum Erstellen eines neuen Eintrags.',
            'put' => 'Endpunkt zum Bearbeiten eines vorhandenen Eintrags.',
            'delete' => 'Endpunkt zum Löschen eines Eintrags.',
        ];
        
        return $descriptions[$method] ?? '';
    }
    
    private function extractResourceName(string $path): string
    {
        // /v1/adressen -> adressen
        preg_match('/\/v\d\/([^\/\{]+)/', $path, $match);
        return $match[1] ?? 'resource';
    }
    
    private function findResourceForPath(string $path): ?string
    {
        $resourceMap = [
            'adressen' => 'AddressResource',
            'artikel' => 'ArticleResource',
            'artikelkategorien' => 'ArticleCategoryResource',
            'aboartikel' => 'ArticleSubscriptionResource',
            'abogruppen' => 'ArticleSubscriptionGroupResource',
            'lieferadressen' => 'DeliveryAddressResource',
            'dateien' => 'FileResource',
            'gruppen' => 'GroupResource',
            'laender' => 'CountryResource',
            'steuersaetze' => 'TaxRateResource',
            'versandarten' => 'ShippingMethodResource',
            'zahlungsweisen' => 'PaymentMethodResource',
            'eigenschaften' => 'PropertyResource',
            'eigenschaftenwerte' => 'PropertyValueResource',
            'trackingnummern' => 'TrackingNumberResource',
            'wiedervorlagen' => 'ResubmissionResource',
            'adresstyp' => 'AddressTypeResource',
            'crmdokumente' => 'CrmDocumentResource',
            'docscan' => 'DocumentScannerResource',
        ];
        
        $resourceName = $this->extractResourceName($path);
        return $resourceMap[$resourceName] ?? null;
    }
}

/**
 * OpenAPI 3.0 Generator
 */
class OpenApiGenerator
{
    private array $routes;
    private array $resources;
    
    public function __construct(array $routes, array $resources)
    {
        $this->routes = $routes;
        $this->resources = $resources;
    }
    
    public function generate(): string
    {
        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'OpenXE REST-API',
                'description' => 'Die API befindet sich in Ihrer OpenXE-Installation im Unterordner `/www/api/`.',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => 'http://localhost/api', 'description' => 'Lokale Installation'],
            ],
            'security' => [
                ['digestAuth' => []],
            ],
            'paths' => $this->generatePaths(),
            'components' => [
                'securitySchemes' => [
                    'digestAuth' => [
                        'type' => 'http',
                        'scheme' => 'digest',
                    ],
                ],
            ],
        ];
        
        return json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    private function generatePaths(): array
    {
        $paths = [];
        
        foreach ($this->routes as $route) {
            $path = $this->convertPath($route['path']);
            
            if (!isset($paths[$path])) {
                $paths[$path] = [];
            }
            
            foreach ($route['methods'] as $method) {
                $paths[$path][strtolower($method)] = $this->generateOperation($route, $method);
            }
        }
        
        ksort($paths);
        return $paths;
    }
    
    private function convertPath(string $path): string
    {
        // /v1/adressen/{id:\d+} -> /v1/adressen/{id}
        return preg_replace('/\{([^:}]+):[^}]+\}/', '{$1}', $path);
    }
    
    private function generateOperation(array $route, string $method): array
    {
        $resourceName = $this->extractResourceName($route['path']);
        $pathParams = $this->extractPathParameters($route['path']);
        $isDetail = !empty($pathParams);
        $permission = $route['handler']['permission'] ?? null;
        
        $operation = [
            'tags' => [ucfirst($resourceName)],
            'summary' => $this->getSummary($method, $resourceName, $isDetail),
            'operationId' => $this->getOperationId($method, $route['path'], $isDetail),
        ];
        
        if ($permission) {
            $operation['description'] = "Permission: `{$permission}`";
        }
        
        // Parameter initialisieren
        $parameters = [];
        
        // Path-Parameter hinzufügen (für alle Routen mit Variablen im Pfad)
        if (!empty($pathParams)) {
            $parameters = array_merge($parameters, $pathParams);
        }
        
        // Query-Parameter für GET List-Routen (ohne Path-Parameter)
        if (strtoupper($method) === 'GET' && empty($pathParams)) {
            $parameters = array_merge($parameters, [
                [
                    'name' => 'page',
                    'in' => 'query',
                    'schema' => ['type' => 'integer', 'default' => 1, 'minimum' => 1, 'maximum' => 1000],
                    'description' => 'Seite der Ergebnisliste',
                ],
                [
                    'name' => 'items',
                    'in' => 'query',
                    'schema' => ['type' => 'integer', 'default' => 20, 'minimum' => 1, 'maximum' => 1000],
                    'description' => 'Anzahl der Ergebnisse pro Seite',
                ],
                [
                    'name' => 'sort',
                    'in' => 'query',
                    'schema' => ['type' => 'string'],
                    'description' => 'Sortierung (z.B. "name" oder "-name" für absteigend)',
                ],
            ]);
        }
        
        // Parameter nur hinzufügen wenn vorhanden
        if (!empty($parameters)) {
            $operation['parameters'] = $parameters;
        }
        
        // Request Body für POST/PUT - generisches Schema
        if (in_array(strtoupper($method), ['POST', 'PUT'])) {
            $operation['requestBody'] = [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => ['type' => 'object', 'additionalProperties' => true],
                    ],
                    'application/xml' => [
                        'schema' => ['type' => 'object', 'additionalProperties' => true],
                    ],
                ],
            ];
        }
        
        // Response-Schemas
        $responses = [];
        
        if (strtoupper($method) === 'GET') {
            if ($isDetail) {
                $responses['200'] = [
                    'description' => 'Erfolgreiche Anfrage',
                    'content' => [
                        'application/json' => [
                            'schema' => ['type' => 'object', 'properties' => ['data' => ['type' => 'object']]],
                        ],
                    ],
                ];
            } else {
                $responses['200'] = [
                    'description' => 'Erfolgreiche Anfrage',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'data' => ['type' => 'array', 'items' => ['type' => 'object']],
                                    'pagination' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'items_total' => ['type' => 'integer'],
                                            'items_current' => ['type' => 'integer'],
                                            'items_per_page' => ['type' => 'integer'],
                                            'page_current' => ['type' => 'integer'],
                                            'page_last' => ['type' => 'integer'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }
        } elseif (in_array(strtoupper($method), ['POST', 'PUT'])) {
            $responses['200'] = [
                'description' => strtoupper($method) === 'POST' ? 'Erfolgreich erstellt' : 'Erfolgreich aktualisiert',
                'content' => [
                    'application/json' => [
                        'schema' => ['type' => 'object', 'properties' => ['data' => ['type' => 'object']]],
                    ],
                ],
            ];
        } elseif (strtoupper($method) === 'DELETE') {
            $responses['200'] = [
                'description' => 'Erfolgreich gelöscht',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer'],
                                    ],
                                ],
                                'success' => ['type' => 'boolean'],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $responses['200'] = ['description' => 'Erfolgreiche Anfrage'];
        }
        
        $responses['401'] = [
            'description' => 'Nicht authentifiziert',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'error' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'integer'],
                                    'http_code' => ['type' => 'integer'],
                                    'message' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $responses['404'] = [
            'description' => 'Ressource nicht gefunden',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'error' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'integer'],
                                    'http_code' => ['type' => 'integer'],
                                    'message' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $operation['responses'] = $responses;
        
        return $operation;
    }
    
    /**
     * Extrahiert den Resource-Pfad (z.B. belege/angebote)
     */
    private function extractResourcePath(string $path): string
    {
        // /v1/belege/angebote/{id} -> belege/angebote
        if (preg_match('/\/v\d\/(.+?)(?:\/\{|$)/', $path, $match)) {
            return rtrim($match[1], '/');
        }
        return $this->extractResourceName($path);
    }
    
    private function extractResourceName(string $path): string
    {
        preg_match('/\/v\d\/([^\/\{]+)/', $path, $match);
        return $match[1] ?? 'resource';
    }
    
    private function getSummary(string $method, string $resource, bool $isDetail): string
    {
        $actions = [
            'GET' => $isDetail ? 'Einzelnen Eintrag abrufen' : 'Liste abrufen',
            'POST' => 'Erstellen',
            'PUT' => 'Bearbeiten',
            'DELETE' => 'Löschen',
        ];
        
        return ucfirst($resource) . ': ' . ($actions[strtoupper($method)] ?? $method);
    }
    
    private function getOperationId(string $method, string $fullPath, bool $isDetail): string
    {
        $actions = [
            'GET' => $isDetail ? 'get' : 'list',
            'POST' => 'create',
            'PUT' => 'update',
            'DELETE' => 'delete',
        ];
        
        $action = $actions[strtoupper($method)] ?? strtolower($method);
        
        // Extrahiere Version und kompletten Resource-Pfad (auch nach {id})
        // Beispiel: /v1/dateien/{id}/base64 -> V1DateienBase64
        if (preg_match('/\/(v\d+)\/(.+)$/', $fullPath, $match)) {
            $version = ucfirst($match[1]); // V1, V2
            $resourcePath = $match[2];
            
            // Entferne Parameter wie {id}, {param} etc.
            $resourcePath = preg_replace('/\{[^}]+\}/', '', $resourcePath);
            // Entferne mehrfache/trailing slashes
            $resourcePath = trim(preg_replace('#/+#', '/', $resourcePath), '/');
            
            $parts = explode('/', $resourcePath);
            $camelCaseName = implode('', array_map('ucfirst', $parts));
            return $action . $version . $camelCaseName;
        }
        
        // Fallback
        return $action . 'Resource';
    }
    
    /**
     * Extrahiert alle Path-Parameter aus einem Pfad
     */
    private function extractPathParameters(string $path): array
    {
        $params = [];
        
        // Finde alle {param} oder {param:regex} Muster
        if (preg_match_all('/\{([^:}]+)(?::[^}]+)?\}/', $path, $matches)) {
            foreach ($matches[1] as $paramName) {
                $params[] = [
                    'name' => $paramName,
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => $paramName === 'id' ? 'integer' : 'string'],
                    'description' => $paramName === 'id' ? 'ID des Eintrags' : ucfirst($paramName),
                ];
            }
        }
        
        return $params;
    }
}

// =============================================================================
// MAIN
// =============================================================================

echo "OpenXE API Documentation Generator\n";
echo "===================================\n\n";

$extractor = new ApiDocumentationExtractor(ROOT_PATH . '/classes');

echo "📖 Extrahiere Routen aus ApiApplication.php...\n";
$routes = $extractor->extractRoutes();
echo "   Gefunden: " . count($routes) . " REST-API Routen\n";

echo "📦 Extrahiere Resource-Metadaten...\n";
$resources = $extractor->extractResources();
echo "   Gefunden: " . count($resources) . " Resource-Klassen\n\n";

$existingDocsPath = ROOT_PATH . '/www/api/docs.raml';

if ($format === 'openapi') {
    echo "🔧 Generiere OpenAPI 3.0 Spezifikation...\n";
    $generator = new OpenApiGenerator($routes, $resources);
    $output = $generator->generate();
    $defaultOutput = ROOT_PATH . '/www/api/openapi.json';
} else {
    echo "🔧 Generiere RAML-Dokumentation...\n";
    $generator = new RamlGenerator($routes, $resources, $existingDocsPath);
    $output = $generator->generate();
    $defaultOutput = ROOT_PATH . '/www/api/docs.generated.raml';
}

$targetFile = $outputFile ?? $defaultOutput;

if ($checkOnly) {
    // Prüfmodus: Vergleiche mit bestehender Datei
    if (!file_exists($targetFile)) {
        echo "❌ Zieldatei existiert nicht: {$targetFile}\n";
        exit(1);
    }
    
    $existing = file_get_contents($targetFile);
    if ($existing === $output) {
        echo "✅ Dokumentation ist aktuell.\n";
        exit(0);
    } else {
        echo "❌ Dokumentation ist nicht aktuell. Bitte `php tools/generate-api-docs.php` ausführen.\n";
        exit(1);
    }
}

// Schreibe Ausgabe
file_put_contents($targetFile, $output);
echo "✅ Dokumentation geschrieben: {$targetFile}\n";

// Statistik
echo "\n📊 Statistik:\n";
echo "   - REST-API Endpunkte: " . count($routes) . "\n";
echo "   - Resource-Klassen: " . count($resources) . "\n";

// Hinweis für raml2html
if ($format === 'raml') {
    echo "\n💡 Tipp: HTML-Dokumentation generieren mit:\n";
    echo "   npx raml2html www/api/docs.generated.raml > www/api/docs.html\n";
}

if ($format === 'openapi') {
    echo "\n💡 Tipp: Swagger UI starten mit:\n";
    echo "   npx @redocly/cli preview-docs www/api/openapi.json\n";
}

echo "\n✨ Fertig!\n";
