# ChunkedUpload-Widget

## Einrichtung

### Im Modul

```php
/** @var \Xentral\Components\Http\Request $request */
$request = $this->app->Container->get('Request');

/** @var \Xentral\Widgets\ChunkedUpload\ChunkedUploadRequestHandler $handler */
$handler = $this->app->Container->get('ChunkedUploadRequestHandler');

if ($handler->canHandleRequest($request)) {
    $tempDir = $this->app->erp->GetTMP(); // alternativ sys_get_temp_dir();
    $saveDir = __DIR__ . '/uploads';
    $response = $handler->handleRequest($request, $tempDir, $saveDir);
    $response->send();
    $this->app->erp->ExitWawi();
}
```

Zum Laden des benötigten jQuery-Plugins reicht folgende Zeile im Modul: 

```php
$this->app->ModuleScriptCache->IncludeWidgetNew('ChunkedUpload');
```

### Im Template

```html
<input type="file" id="chunkyfile" multiple="multiple">
```

### In Javascript

```javascript
$(document).ready(function () {
    $('#chunkyfile').chunkedUpload({
        upload: {
            url: 'index.php?module=meinmodul&action=meineaction&cmd=upload'
        }
    });
});
```
