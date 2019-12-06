<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmudkuet11@gmail.com>.
 */

namespace Mahmud\Sheet;

use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderFactory;

class SheetReader {
    protected $filePath;
    
    protected $ignoredRowsIndex = [];
    
    protected $rowCallback;
    
    protected $columns = [];
    
    protected $fileType = null;
    
    protected $delimiter = null;
    
    public function __construct($filePath, $fileType = Type::XLSX, $delimiter = ',') {
        $this->filePath = $filePath;
        $this->fileType = $fileType;
        $this->delimiter = $delimiter;
    }
    
    public static function makeFromXlsx($filePath) {
        return static::make($filePath);
    }
    
    public static function make($filePath, $fileType = Type::XLSX) {
        return new static($filePath, $fileType);
    }
    
    public static function makeFromCsv($filePath) {
        return static::make($filePath, Type::CSV);
    }
    
    public function ignoreRow($index) {
        $this->ignoredRowsIndex[] = $index;
        
        return $this;
    }
    
    public function columns($columns) {
        $this->columns = $columns;
        
        return $this;
    }
    
    public function delimiter($delimiter) {
        $this->delimiter = $delimiter;
        
        return $this;
    }
    
    public function totalRows() {
        $count = 0;
        static::make($this->filePath)
            ->onEachRow(function () use (&$count) {
                $count++;
            })
            ->read();
        return $count;
    }
    
    public function read() {
        $reader = ReaderFactory::createFromType($this->fileType);
        if ($this->fileType === Type::CSV) {
            $reader->setFieldDelimiter($this->delimiter);
        }
        $reader->open($this->filePath);
        
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row_number => $row) {
                $index = $row_number - 1;
                if ($this->isIgnored($index)) continue;
                
                call_user_func($this->rowCallback, $this->mapData($row->toArray()), $index);
            }
        }
        
        $reader->close();
        
        return $this;
    }
    
    private function isIgnored($index) {
        return array_search($index, $this->ignoredRowsIndex) !== false;
    }
    
    protected function mapData($row) {
        if (count($this->columns) === 0) return $row;
        
        $data = [];
        foreach ($this->columns as $index => $column) {
            $data[$column] = isset($row[$index]) ? $row[$index] : null;
        }
        
        return $data;
    }
    
    public function onEachRow($callback) {
        $this->rowCallback = $callback;
        
        return $this;
    }
}
