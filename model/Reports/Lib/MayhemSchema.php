<?php namespace Reports\Lib;

/**
 * 
 * @author itay
 *
 */
class MayhemSchema{

    /**
     */
    public function __construct(string $report_namespace){
        $path = CORE_PATH . '/model' . str_replace('\\','/',$report_namespace) . '/schema.php';
        $this->schema = require($path);
    }
    
    /**
     * 
     * @var array<string,mixed>
     */
    private array $schema;
    
    /**
     * Checks the filters in the schema, any that 
     * is a preloaded list, fill.
     * 
     * loop and look for 
     *    UI_FILTER_TYPE__LIST_STATIC_SINGLE
     *    UI_FILTER_TYPE__LIST_STATIC_MULTIPLE
     */
    public function prefillFilters():MayhemSchema{
        //TODO do we need this?
        return $this;
    }
    
    /**
     * @return array<string,mixed>
     */
    public function schema():array{
        return $this->schema;        
    }
    
    /**
     * @return array<mixed>
     */
    public function filters():array{
        return $this->schema()[\Reports\SCHEMA_SECTION__FILTERS];
    }
    
    /**
     * @return array<mixed>
     */
    public function fields():array{
        return $this->schema()[\Reports\SCHEMA_SECTION__FIELDS];
    }
    
    /**
     * @return string
     */
    public function default_sort_by():string{
        return $this->schema()[\Reports\SCHEMA_DEFAULT_SORT_BY];
    }

    /**
     * @return string
     */
    public function default_sort_dir():string{
        return $this->schema()[\Reports\SCHEMA_DEFAULT_SORT_DIR];
    }

    /**
     * Returns the fields formatted for sql query
     * 
     * @return string
     */
    public function fields_as_string():string{
        return '';
    }
    
    /**
     * Formated landing page entry
     * @return array<string,mixed>
     */
    public function formatMenu():array{
        return [
            \Reports\SCHEMA_REPORT_NAME        => $this->schema[\Reports\SCHEMA_REPORT_NAME],
            \Reports\SCHEMA_REPORT_DESCRIPTION => $this->schema[\Reports\SCHEMA_REPORT_DESCRIPTION],
            \Reports\SCHEMA_REPORT_ID          => $this->schema[\Reports\SCHEMA_REPORT_ID],
            \Reports\SCHEMA_SECTION__PERSPECTIVES => $this->schema[\Reports\SCHEMA_SECTION__PERSPECTIVES]
        ];
    }
}


