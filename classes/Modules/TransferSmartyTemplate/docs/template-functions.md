# Template Funktionen

## CSV-Funktionen

### `quoteCsv` – CSV-Wert mit Anführungszeichen umschließen und escapen

##### Beispiele

* `{"Inhalt"|quoteCsv}` > `"Inhalt"`
* `{"Inh"alt"|quoteCsv}` > `"Inh""alt"`
* `{"Inhalt"|quoteCsv:"'"}` > `'Inhalt'`
* `{"Inh'alt"|quoteCsv:"'"}` > `'Inh''alt'`

#### Parameter

| Pos. | Type     | Pflicht  | Default | Beschreibung |
|------|----------|----------|---------|--------------|
| 1    | `string` | Nein     | `"`     | Umschließendes Zeichen und Escaping-Zeichen |


## XML-Funktionen

### `escapeXml` – Wert für Ausgabe in XML escapen

Nachfolgende Zeichen werden umgewandelt. Außerdem werden alle Steuerzeichen (außer Zeilenumbrüche) entfernt. 
Sollen Zeilenumbrüche zusätzlich entfernt werden, kann der Modifier `stripLineBreaks` verwendet werden. 

| Zeichen | Ersetzung |
|---------|-----------|
| `"`     | `&quot;`  |
| `'`     | `&apos;`  |
| `<`     | `&lt;`    |
| `>`     | `&gt;`    |
| `&`     | `&amp;`   |


##### Beispiel als Modifier

```smarty
{$value|escapeXml}
```

##### Beispiel als Block-Funktion

```smarty
{escapeXml}{$value}{/escapeXml}
{escapeXml charset="UTF-32"}{$value}{/escapeXml}
```

### `cdata` – CData-Abschnitt erstellen

##### Beispiel als Modifier

```smarty
{assign var="variable" value="Hello<br>World"}
{$variable|cdata}
```

##### Beispiel als Block-Funktion

```smarty
{cdata}Hello<br>World{/cdata}
```

##### Ausgabe

```
<![CDATA[Hello<br>World]]>
```


## HTML-Funktionen

### `br2nl` – BR-Tags in Zeilenumbrüche umwandeln

##### Beispiel

```smarty
{assign var="variable" value="Hello<br>World"}
{$variable|br2nl}
```

##### Ausgabe

```text
Hello
World
```

### `decodeHtmlSpecialChars` – HTML-Specialchars dekodieren

Ersetzt die HTML-Entities für die fünf HTML-Specialchars ( `"`, `'`, `<`, `>`, `&`) durch das ursprüngliche Zeichen.  

### `decodeHtmlEntities` – HTML-Entities dekodieren

Wandelt alle HTML-Entities (inklusive der HTML-Specialchars) in das ursprüngliche Zeichen zurück.


## URL-Funktionen

### `encodeUrl` 

Kodiert einen String für die Verwendung in einer URL

### `decodeUrl`

Dekodiert einen URL-kodierten String 


## Sonstige Funktionen

### `replaceLineBreaks` – Zeilenumbrüche ersetzen

##### Beispiel

```smarty
{$variable|replaceLineBreaks}
{$variable|replaceLineBreaks:" ZEILENUMBRUCH "}
```

##### Ausgabe

```text
Hello World
Hello ZEILENUMBRUCH World
```

### `dump` – Variablen-Inhalt ausgeben

_Methode ist nur zum Debuggen gedacht; nicht für den Einsatz in Produktiv-Umgebungen!_

##### Beispiel

```smarty
{$article|dump}
```

##### Ausgabe

```text
(object) array(
   'id' => '15',
   'typ' => '2_kat',
   'nummer' => '1000001',
   'projekt' => '1',
   ...
)
```
_Ausgabe wurde gekürzt._
