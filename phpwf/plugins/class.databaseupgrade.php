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

final class DatabaseUpgrade
{
  /** @var Application $app */
  private $app;

  /** @var array $CheckColumnTableCache */
  private $CheckColumnTableCache;

  /** @var bool $check_column_missing_run */
  private $check_column_missing_run=false;

  /** @var array $check_column_missing */
  private $check_column_missing=array();

  /** @var array $check_index_missing */
  private $check_index_missing=array();

  /** @var array */
  private $allTables = [];

  /** @var array */
  private $indexe = [];

  /**
   * @param Application $app
   */
  public function __construct($app)
  {
    $this->app = $app;
  }

  public function emptyTableCache(){
    $this->CheckColumnTableCache = [];
    $this->allTables = [];
    $this->indexe = [];
  }

  /**
   * @var bool $force
   *
   * @return array
   */
  public function getAllTables($force = false)
  {
    if($force || empty($this->allTables)) {
      $this->allTables = $this->app->DB->SelectFirstCols('SHOW TABLES');
    }

    return $this->allTables;
  }

  /**
   * @param string $table
   * @param string $pk
   */
  public function createTable($table, $pk = 'id')
  {
    $sql = "CREATE TABLE `$table` (`".$pk."` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`".$pk."`)) ENGINE = InnoDB DEFAULT CHARSET=utf8";
    $this->app->DB->Query($sql);
    $this->addPrimary($table, $pk);
  }

  /**
   * @param string $table
   * @param string $pk
   */
  public function addPrimary($table, $pk = 'id')
  {
    $this->CheckAlterTable(
      "ALTER TABLE `$table`
      ADD PRIMARY KEY (`".$pk."`)",
      true
    );
    $this->CheckAlterTable(
      "ALTER TABLE `$table`
      MODIFY `".$pk."` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1",
      true
    );
  }

  /**
   * @param string $table
   * @param bool   $force
   *
   * @return array
   */
  public function getIndexeCached($table, $force = false)
  {
    if($force || !isset($this->indexe[$table])){
      $this->indexe[$table] = $this->app->DB->SelectArr(sprintf('SHOW INDEX FROM `%s`', $table));
      if($this->indexe[$table] === null) {
        $this->indexe[$table] = [];
      }
    }

    return $this->indexe[$table];
  }

  /**
   * @param string $table
   */
  public function clearIndexCached($table)
  {
    if(!isset($this->indexe[$table])) {
      return;
    }
    unset($this->indexe[$table]);
  }

  /**
   * @param string $table
   * @param string $pk
   */
  public function hasPrimaryKey($table, $pk = 'id')
  {
    $indexe = $this->getIndexeCached($table);
    if(empty($indexe)) {
      return false;
    }
    foreach($indexe as $index) {
      if($index['Column_name'] === $pk
        && $index['Key_name'] === 'PRIMARY'
        && (int)$index['Non_unique'] === 0
      ) {
        return true;
      }
    }

    return false;
  }

  /**
   * @param string $table
   * @param string $pk
   *
   * @return void
   */
  function CheckTable($table, $pk = 'id')
  {
    if($pk === 'id') {
      $tables = $this->getAllTables();
      if(!empty($tables)){
        if(!in_array($table, $tables)){
          $this->createTable($table, $pk);
          return;
        }
        if(!$this->hasPrimaryKey($table, $pk)) {
          $this->addPrimary($table, $pk);
        }
        return;
      }
    }
    $found = false;
    $tables = $this->getAllTables(true);
    if($tables) {
      $found = in_array($table, $tables);
    }
    else{
      $check = $this->app->DB->Select("SELECT $pk FROM `$table` LIMIT 1");
      if($check) {
        $found = true;
      }
    }
    if($found==false)
    {
      $sql = "CREATE TABLE `$table` (`".$pk."` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`".$pk."`)) ENGINE = InnoDB DEFAULT CHARSET=utf8";
      $this->app->DB->Update($sql);
      $this->CheckAlterTable("ALTER TABLE `$table`
      ADD PRIMARY KEY (`".$pk."`)");
      $this->CheckAlterTable("ALTER TABLE `$table`
      MODIFY `".$pk."` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
    }
    if($pk !== 'id') {
      $this->CheckColumn('created_at','timestamp',$table,"DEFAULT CURRENT_TIMESTAMP NOT NULL");
    }
  }

  /**
   * @param string $column
   * @param string $type
   * @param string $table
   * @param string $default
   *
   * @return void
   */
  function UpdateColumn($column,$type,$table,$default="NOT NULL")
  {
    $fields = $this->app->DB->SelectArr("show columns from `".$table."`");
    if($fields)
    {
      foreach($fields as $val)
      {
        $field_array[] = $val['Field'];
      }
    }
    if (in_array($column, $field_array))
    {
      $this->app->DB->Query('ALTER TABLE `'.$table.'` CHANGE `'.$column.'` `'.$column.'` '.$type.' '.$default.';');
    }
  }

  /**
   * @param string $column
   * @param string $table
   *
   * @return void
   */
  public function DeleteColumn($column,$table)
  {
    $this->app->DB->Query('ALTER TABLE `'.$table.'` DROP `'.$column.'`;');
  }

  /**
   * @param string $column
   * @param string $type
   * @param string $table
   * @param string $default
   *
   * @return void
   */
  public function CheckColumn($column,$type,$table,$default="")
  {
    if($table === 'firmendaten')
    {
      if($this->app->DB->Select("SELECT `id` FROM `firmendaten_werte` WHERE `name` = '$column' LIMIT 1"))return;
    }
    if(!isset($this->CheckColumnTableCache[$table]))
    {
      $tmp=$this->app->DB->SelectArr("show columns from `".$table."`");
      if($tmp)
      {
        foreach($tmp as $val)
        {
          $this->CheckColumnTableCache[$table][] = $val['Field'];
          //$types[$val['Field']] = strtolower($val['Type']);
        }
      }
    }
    if (isset($this->CheckColumnTableCache[$table]) && !in_array($column, $this->CheckColumnTableCache[$table]))
    {
      if($this->check_column_missing_run)
      {
        //$result = mysqli_query($this->app->DB->connection,'ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$type.' '.$default.';');
        $this->check_column_missing[$table][]=$column;
      } else {
        $result = $this->app->DB->Query('ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$type.' '.$default.';');
        if($table === 'firmendaten' && $this->app->DB->error())
        {
          if((method_exists($this->app->DB, 'errno2') && $this->app->DB->errno() == '1118')
            || strpos($this->app->DB->error(),'Row size too large') !== false
          )
          {
            $this->ChangeFirmendatenToMyIsam();
            $this->app->DB->Query('ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$type.' '.$default.';');
          }
        }
      }
    }
  }

  /**
   * @param array $indexe
   *
   * @return array
   */
  protected function getGroupedIndexe($indexe)
  {
    if(empty($indexe)) {
      return $indexe;
    }
    $return = [];
    foreach($indexe as $index) {
      $keyName = $index['Key_name'];
      $isUnique = $index['Non_unique'] == '0';
      $seq = $index['Seq_in_index'];
      $columnName = $index['Column_name'];
      $return[$isUnique?'unique':'index'][$keyName][(int)$seq - 1] = $columnName;
    }

    return $return;
  }

  /**
   * @param array $indexe
   *
   * @return array
   */
  protected function getDoubleIndexeFromGroupedIndexe($indexe)
  {
    if(empty($indexe)) {
      return [];
    }

    $ret = [];
    foreach($indexe as $type => $indexArrs) {
      $columnStrings = [];
      foreach($indexArrs as $indexKey => $columns) {
        $columnString = implode('|', $columns);
        if(in_array($columnString, $columnStrings)) {
          $ret[$type][] = $indexKey;
          continue;
        }
        $columnStrings[] = $columnString;
      }
    }

    return $ret;
  }

  /**
   * @param string $table
   * @param array  $indexe
   * @param bool   $noCache
   *
   * @return array|null
   */
  public function CheckDoubleIndex($table, $indexe, $noCache = false)
  {
    $query = $noCache?null:$this->CheckAlterTable("SHOW INDEX FROM `$table`");
    if(!$query) {
      $indexeGrouped = $this->getGroupedIndexe($indexe);
      $doubleIndexe = $this->getDoubleIndexeFromGroupedIndexe($indexeGrouped);
      if(!empty($doubleIndexe)) {
        $indexe = $this->getIndexeCached($table, true);
        $indexeGrouped = $this->getGroupedIndexe($indexe);
        $doubleIndexe = $this->getDoubleIndexeFromGroupedIndexe($indexeGrouped);
        if(empty($doubleIndexe)) {
          return $indexe;
        }

        foreach($doubleIndexe as $type => $doubleIndex) {
          foreach($doubleIndex as $indexName) {
            $this->app->DB->Query("ALTER TABLE `".$table."` DROP INDEX `".$indexName."`");
          }
        }
      }
      elseif($noCache) {
        return $indexe;
      }
      $this->CheckAlterTable("SHOW INDEX FROM `$table`", true);

      return $this->getIndexeCached($table, true);
    }
    if(empty($indexe) || count($indexe) == 1){
      return $indexe;
    }
    $uniquearr = array();
    $indexarr = array();
    foreach($indexe as $index)
    {
      if($index['Key_name'] !== 'PRIMARY' && !empty($index['Column_name']))
      {
        if($index['Non_unique'])
        {
          $indexarr[$index['Key_name']][] = $index['Column_name'];
        }else{
          $uniquearr[$index['Key_name']][] = $index['Column_name'];
        }
      }
    }
    $cindex = count($indexarr);
    $cuniqe = count($uniquearr);
    $changed = false;
    if($cindex > 1)
    {
      $check  = array();
      foreach($indexarr as $key => $value)
      {
        if(empty($value))
        {
          continue;
        }
        if(count($value) > 1){
          sort($value);
        }
        $vstr = implode(',', $value);
        if(in_array($vstr, $check))
        {
          $this->app->DB->Query("DROP INDEX `".$key."` ON `".$table."`");
          $changed = true;
        }else{
          $check[] = $vstr;
        }
      }
    }
    if($cuniqe > 1)
    {
      $check  = array();
      foreach($uniquearr as $key => $value)
      {
        if(empty($value))
        {
          continue;
        }
        if(count($value) > 1){
          sort($value);
        }
        $vstr = implode(',', $value);
        if(in_array($vstr, $check))
        {
          $this->app->DB->Query("DROP UNIQUE `".$key."` ON `".$table."`");
          $changed = true;
        }else{
          $check[] = $vstr;
        }
      }
    }
    if($changed) {
      return $this->getIndexeCached($table, true);
    }
    return $indexe;
  }

  /**
   * @param string $table
   * @param string|array $column
   *
   * @return bool
   */
  public function CheckFulltextIndex($table,$column)
  {
    if(empty($table) || empty($column))
    {
      return false;
    }
    if(!is_array($column))
    {
      $column = [$column];
    }
    $columnmasked = [];
    foreach($column as $keyColumn => $valueColumn)
    {
      if(!empty($valueColumn))
      {
        $columnmasked[] = "`$valueColumn`";
      }else{
        unset($column[$keyColumn]);
      }
    }
    if(empty($column))
    {
      return false;
    }
    $columnsFound = [];
    $indexe = $this->getIndexeCached($table, true);
    $indexeFound = [];
    if(!empty($indexe))
    {
      foreach($indexe as $index)
      {
        if($index['Index_type'] === 'FULLTEXT')
        {
          $indexeFound[] = $index['Column_name'];
          if(!in_array($index['Column_name'], $columnsFound))
          {
            $columnsFound[] = $index['Column_name'];
          }
        }
      }
      $cindexeFound = count($indexeFound);
      $column = count($column);
      if(($column === $cindexeFound) && (count($columnsFound) === $column))
      {
        return true;
      }
      if($cindexeFound > 0)
      {
        return false;
      }

    }
    $this->app->DB->Query(
      "ALTER TABLE `$table`
      ADD FULLTEXT INDEX `FullText` 
      (".implode(',',$columnmasked).");"
    );
    $error = $this->app->DB->error();

    return empty($error);
  }

  /**
   * @param string $table
   * @param string $column
   * @param bool   $unique
   *
   * @return void
   */
  function CheckIndex($table, $column, $unique = false)
  {
    $indexex = null;
    $indexexother = null;
    $indexe = $this->getIndexeCached($table);
    if($indexe)
    {
      $indexe = $this->CheckDoubleIndex($table, $indexe, true);
      foreach($indexe as $index)
      {
        if(is_array($column) && $index['Key_name'] !== 'PRIMARY')
        {
          if($unique && !$index['Non_unique'])
          {
            if(in_array($index['Column_name'], $column))
            {
              $indexex[$index['Key_name']][$index['Column_name']] = true;
            }else{
              $indexexother[$index['Key_name']][$index['Column_name']] = true;
            }
          }
          elseif(!$unique){
            if(in_array($index['Column_name'], $column)) {
              $indexex[$index['Key_name']][$index['Column_name']] = true;
            }
          }
        }
        elseif(!is_array($column)){
          if($index['Column_name'] == $column)
          {
            return;
          }
        }
      }
      if($this->check_column_missing_run)
      {
        $this->check_index_missing[$table][] = $column;
      }
      if(!$unique)
      {
        if(is_array($column))
        {
          if($indexex)
          {
            foreach($indexex as $k => $v) {
              if(count($v) === 1 && count($column) > 1) {
                $this->app->DB->Query("DROP INDEX `".$k."` ON `".$table."`");
                $this->clearIndexCached($table);
                unset($indexex[$k]);
              }
            }
            foreach($indexex as $k => $v)
            {
              if(count($v) == count($column)){
                return;
              }
            }
            foreach($indexex as $k => $v)
            {
              if(!isset($indexexother[$k]))
              {
                $this->app->DB->Query("DROP INDEX `".$k."` ON `".$table."`");
                $cols = null;
                foreach($column as $c) {
                  $cols[] = "`$c`";
                }
                $this->CheckAlterTable("ALTER TABLE `$table` ADD INDEX(".implode(', ',$cols)."); ",true);
                $this->clearIndexCached($table);
                return;
              }
            }
          }
          $cols = null;
          foreach($column as $c) {
            $cols[] = "`$c`";
          }
          $this->CheckAlterTable("ALTER TABLE `$table` ADD INDEX(".implode(', ',$cols)."); ", true);
          $this->clearIndexCached($table);
        }
        else{
          $this->CheckAlterTable("ALTER TABLE `$table` ADD INDEX(`$column`); ", true);
          $this->clearIndexCached($table);
        }
      }
      else{
        if(is_array($column))
        {
          if($indexex)
          {
            foreach($indexex as $k => $v)
            {
              if(count($v) == count($column))
              {
                return;
              }
            }
            foreach($indexex as $k => $v)
            {
              if(!isset($indexexother[$k]))
              {
                $this->app->DB->Query("DROP INDEX `".$k."` ON `".$table."`");
                $cols = null;
                foreach($column as $c) {
                  $cols[] = "`$c`";
                }
                $this->CheckAlterTable("ALTER TABLE `$table` ADD UNIQUE(".implode(', ',$cols)."); ", true);
                $this->clearIndexCached($table);
                return;
              }
            }
          }
          $cols = null;
          foreach($column as $c) {
            $cols[] = "`$c`";
          }
          $this->CheckAlterTable("ALTER TABLE `$table` ADD UNIQUE(".implode(', ',$cols)."); ", true);
          $this->clearIndexCached($table);
        }else{
          $this->CheckAlterTable("ALTER TABLE `$table` ADD UNIQUE(`$column`); ", true);
          $this->clearIndexCached($table);
        }
      }
    }
    elseif(!is_array($column))
    {
      if(!$unique)
      {
        $this->CheckAlterTable("ALTER TABLE `$table` ADD INDEX(`$column`); ");
      }else{
        $this->CheckAlterTable("ALTER TABLE `$table` ADD UNIQUE(`$column`); ");
      }
      $this->clearIndexCached($table);
    }
    elseif(is_array($column))
    {
      $cols = null;
      foreach($column as $c) {
        $cols[] = "`$c`";
      }
      $this->CheckAlterTable("ALTER TABLE `$table` ADD UNIQUE(".implode(', ',$cols)."); ");
      $this->clearIndexCached($table);
    }
  }

  /**
   * @param string $sql
   * @param bool   $force
   *
   * @return mysqli_result|bool
   */
  function CheckAlterTable($sql, $force = false)
  {
    $sqlmd5 = md5($sql);
    $check  = $this->app->DB->Select("SELECT id FROM checkaltertable WHERE checksum='$sqlmd5' LIMIT 1");
    if($check > 0 && !$force) return;
    $query = $this->app->DB->Query($sql);
    if($query && empty($check) && !$this->app->DB->error()){
      $this->app->DB->Insert("INSERT INTO checkaltertable (id,checksum) VALUES ('','$sqlmd5')");
    }
    return $query;
  }

  /**
   * @return void
   */
  public function ChangeFirmendatenToMyIsam()
  {
    $this->app->DB->Query("ALTER TABLE firmendaten ENGINE = MyISAM;");
  }

  /**
   * @param string $table
   *
   * @return array
   */
  public function getSortedIndexColumnsByIndexName($table): array
  {
    $indexesByName = [];
    $indexes = $this->app->DB->SelectArr(sprintf('SHOW INDEX FROM `%s`', $table));
    if(empty($indexes)) {
      return $indexesByName;
    }
    foreach($indexes as $index) {
      $indexesByName[$index['Key_name']][] = $index['Column_name'];
    }
    foreach($indexesByName as $indexName => $columns) {
      $columns = array_unique($columns);
      sort($columns);
      $indexesByName[$indexName] = $columns;
    }

    return $indexesByName;
  }

  /**
   * @deprecated will be removed in 21.4
   *
   * @param string $table
   * @param array  $columns
   */
  public function dropIndex($table, $columns): void
  {
    if(empty($table) || empty($columns)) {
      return;
    }
    $columns = array_unique($columns);
    sort($columns);
    $countColumns = count($columns);
    $indexes = $this->getSortedIndexColumnsByIndexName($table);
    if(empty($indexes)) {
      return;
    }
    foreach($indexes as $indexName => $indexColumns) {
      if(count($indexColumns) !== $countColumns) {
        continue;
      }
      if(count(array_intersect($indexColumns, $columns)) === $countColumns) {
        $this->app->DB->Query(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $indexName));
      }
    }
  }
}
