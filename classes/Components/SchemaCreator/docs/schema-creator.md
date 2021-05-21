# Synopsis
* Create defined Table Schema if it does not exist
* Ensure that existing table, and the target schema are the same


# Usage

Example:
```php

    use Xentral\Components\SchemaCreator\Option\TableOption;
    use Xentral\Components\SchemaCreator\SchemaCreator;
    use Xentral\Components\SchemaCreator\Schema\TableSchema;
    use Xentral\Components\SchemaCreator\Type;

     /** @var SchemaCreator $tableCreator */
     $tableCreator = $this->app->Container->get('SchemaCreator');

     $fooTable = new TableSchema('foo_table');
     $fooTable->addColumn(Type\Integer::asAutoIncrement('id'));
     $fooTable->addColumn(Type\Integer::asUnsigned('user_id'));
     $fooTable->addColumn(new Type\Varchar('name'));
     $fooTable->addColumn(new Type\Varchar('language', 5));
     $fooTable->addColumn(new Type\Varchar('first_name', 100));
     $fooTable->addColumn(new Type\Varchar('last_name', 200, '', false));
     $fooTable->addColumn(new Type\Tinyint('active',1,false));
     $fooTable->addColumn(new Type\Time('timed_at','00:00:00'));
     $fooTable->addColumn(new Type\Year('year_example'));
     $fooTable->addOption(TableOption::fromEngine('InnoDB'));
     $fooTable->addOption(TableOption::fromCharset('utf8'));
     $fooTable->addOption(TableOption::fromComment('This is a comment'));
     $fooTable->addOption(TableOption::fromCollation('utf8_unicode_ci'));

     $tableCreator->ensureSchema($fooTable);
     // display table's structure
     var_export($tableCreator->getSqlSchema($fooTable));
```

Example with Index
```php
     use Xentral\Components\SchemaCreator\SchemaCreator;
     use Xentral\Components\SchemaCreator\Schema\TableSchema;
     use Xentral\Components\SchemaCreator\Type;
     use Xentral\Components\SchemaCreator\Index;

    /** @var SchemaCreator $tableCreator */
    $tableCreator = $this->app->Container->get('SchemaCreator');

    $barTable = new TableSchema('bar_table');
    $barTable->addColumn(new Type\Integer('id',10, true,null,false, ['extra' => 'ai']));
    $barTable->addColumn(new Type\Integer('foo_table_id',10, true));
    $barTable->addColumn(new Type\Varchar('langue'));
    $barTable->addColumn(new Type\Varchar('prenom'));
    $barTable->addColumn(new Type\Varchar('nom_de_famille'));
    $barTable->addColumn(new Type\Tinyint('actif', 1));
    $barTable->addIndex(new Index\Primary(['id']));
    $barTable->addIndex(new Index\Unique(['prenom'], 'unique_first_name'));
    $barTable->addIndex(new Index\Unique(['langue', 'actif']));
    $barTable->addIndex(new Index\Index(['nom_de_famille'], 'custom_name'));

    $tableCreator->ensureSchema($barTable);
```

Generate Schema from existing table
```php
     use Xentral\Components\SchemaCreator\SchemaCreator;

    /** @var SchemaCreator $tableCreator */
    $tableCreator = $this->app->Container->get('SchemaCreator');
    $currentSchema = $tableCreator->createFromExistingTable('foo_table');
    // get table's definition
    $tableDefinition = $tableCreator->getSqlSchema($currentSchema);
    var_export($tableDefinition);
```

# Defines Table Schema in your module

```php
  declare(strict_types=1);

  namespace Xentral\Modules\Foo;

  use Xentral\Components\SchemaCreator\Collection\SchemaCollection;
  use Xentral\Components\SchemaCreator\Schema\TableSchema;
  use Xentral\Components\SchemaCreator\Type;
  use Xentral\Components\SchemaCreator\Index;
  use Xentral\Components\SchemaCreator\Option\TableOption;

  final class Bootstrap
  {

    /**
     * @param SchemaCollection $collection
     *
     * @return void
     */
    public static function registerTableSchemas(SchemaCollection $collection): void
    {

        $fooTable = new TableSchema('foo_table');
        $fooTable->addColumn(Type\Integer::asAutoIncrement('id'));
        $fooTable->addColumn(Type\Integer::asUnsigned('user_id'));
        $fooTable->addColumn(new Type\Varchar('name'));
        $fooTable->addColumn(new Type\Varchar('language', 5));
        $fooTable->addColumn(new Type\Varchar('first_name', 100));
        $fooTable->addColumn(new Type\Varchar('last_name', 200, '', false));
        $fooTable->addColumn(new Type\Tinyint('active',1,false));
        $fooTable->addColumn(new Type\Time('timed_at',null, false));
        $fooTable->addColumn(new Type\Year('year_example'));
        $fooTable->addOption(TableOption::fromEngine('InnoDB'));
        $fooTable->addOption(TableOption::fromCharset('utf8'));
        $fooTable->addOption(TableOption::fromComment('This is a comment'));
        $fooTable->addOption(TableOption::fromCollation('utf8_unicode_ci'));
      
        $barTable = new TableSchema('bar_table');
        $barTable->addColumn(new Type\Integer('id',10, true,null,false, ['extra' => 'ai']));
        $barTable->addColumn(new Type\Integer('foo_table_id',10, true));
        $barTable->addColumn(new Type\Varchar('langue'));
        $barTable->addColumn(new Type\Varchar('prenom'));
        $barTable->addColumn(new Type\Varchar('nom_de_famille'));
        $barTable->addColumn(new Type\Tinyint('actif', 1));
        $barTable->addIndex(new Index\Primary(['id']));
        $barTable->addIndex(new Index\Unique(['prenom'], 'unique_first_name'));
        $barTable->addIndex(new Index\Unique(['langue', 'actif']));
        $barTable->addIndex(new Index\Index(['nom_de_famille'], 'custom_name'));

        // ADD TableSchema
        $collection->add($fooTable);
        $collection->add($barTable);
  }
  # ...
}

    
```
