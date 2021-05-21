# DataTables

## Annahmen

* Nur falls Zeilen selektiert werden sollen:
	* In jeder SQL-Abfrage muss die erste Spalte eine eindeutige ID zurückliefern. 
		* Diese Spalte muss den Namen `id` bekommen.
		* Diese Spalte muss nicht als `Column` definiert werden.   

* Jede Tabellenspalte benötigt einen eindeutigen Namen; für die Zuordnung von Filtern. 
	* Dieser Name korrespondiert mit einem SQL-Spaltennamen bzw. dem Alias. Beispiel:
  	`SELECT projekt.abkuerzung AS projekt_name ... ` dann muss der Spaltenname `projekt_name` und nicht `abkuerzung` heißen.

* Jede DataTable benötigt einen eindeutigen Namen.
	* Für die Zuordnung von Filtern.
	* Eindeutiger Name wird aus Klassenname generiert, wenn kein Name in der BuildConfig angegeben wird.

## Spaltenarten

Siehe `\Xentral\Widgets\DataTable\Column\Column` Klasse.

##### `Column::visible($name, $title, $align = 'left', $width = null)`
* Sichtbar
* Nicht sortierbar
* Nicht durchsuchbar

##### `Column::sortable($name, $title, $align = 'left', $width = null)`
* Sichtbar
* Sortierbar
* Nicht durchsuchbar

##### `Column::searchable($name, $title, $align = 'left', $width = null)`
* Sichtbar
* Sortierbar
* Durchsuchbar

##### `Column::fixed($name, $title, $align = 'left', $width = null)`
* Sichtbar
* Nicht sortierbar
* Nicht durchsuchbar
* Für Menü-Spalten und Zeilen-Selektion

##### `Column::hidden($name, $title, $align = 'left', $width = null)`
* Initial ausgeblendet; kann eingeblendet werden (Feature noch nicht implementiert)
* Nicht sortierbar
* Nicht durchsuchbar


## Aufbau

```


```

## Verwendung

### Vorlage

```php

```
