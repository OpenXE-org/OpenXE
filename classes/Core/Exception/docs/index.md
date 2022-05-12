# Exceptions

Alle Exceptions im `classes`-Bereich implementieren das `XentralExceptionInterface`.

Jeder der vier Bereich hat ein eigenes `ExceptionInterface`. Jedes der Interfaces ist vom `XentralExceptionInterface` 
abgeleitet.

* Core > `CoreExceptionInterface`
* Components > `ComponentExceptionInterface`
* Modules > `ModuleExceptionInterface`
* Widgets > `WidgetExceptionInterface`

Jedes Modul, jede Komponente und jedes Widget hat wiederum ein eigenes `ExceptionInterface`, 
z.b. das `HttpExceptionInterface` der Http-Komponente. Dieses Interface extended das entsprechende `ExceptionInterface` 
aus seinem Bereich.

Alle Exceptions in einem Modul/Komponente/Widget implementieren das `ExceptionInterface` des Moduls/Komponente/Widget.

Alle Exceptions sind von einer `SplException` abgeleitet, z.B.: `RuntimeException`


###### Beispiel Exception-Baum

```
Xentral\Core\Exception\XentralExceptionInterface
 └─ Xentral\Core\Exception\ComponentExceptionInterface
     └─ Xentral\Components\Http\Exception\HttpExceptionInterface
         └─ Xentral\Components\Http\Exception\MethodNotAllowedException
             └─ RuntimeException
                 └─ Exception
```
