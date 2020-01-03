[![Build Status](https://travis-ci.org/mahmudkuet11/sheet.svg?branch=master)](https://travis-ci.org/mahmudkuet11/sheet)
[![Latest Stable Version](https://poser.pugx.org/mahmud/sheet/v/stable)](https://packagist.org/packages/mahmud/sheet)
[![License](https://poser.pugx.org/mahmud/sheet/license)](https://packagist.org/packages/mahmud/sheet)
[![composer.lock](https://poser.pugx.org/mahmud/sheet/composerlock)](https://packagist.org/packages/mahmud/sheet)

A clean and beautiful API to read Excel/CSV sheet. This is a wrapper around [box/spout](https://github.com/box/spout) package.

# Installation

```bash
composer require mahmud/sheet
```

# Requirements

- php: ^7.1.3
- box/spout: ^3.0

# Usage

## Simple Example

Let's assume we have a csv file like this.


| ID | Name   | Age |
|----|--------|-----|
| 1  | Mahmud | 27  |
| 2  | Mohor  | 26  |
| 3  | Ayman  | 1   |


```php
use Mahmud\Sheet\SheetReader;

SheetReader::makeFromCsv('/path-to-csv-file/example-file.csv')
            ->ignoreRow(0)                          // Optional: Skip the header row
            ->columns(['id', 'name', 'age'])        // Arbitary column name that will be mapped sequentially for each row
            ->onEachRow(function($row, $index){
                // This callback will be executed for each row
                var_dump($row);     // Current row in associative array
                var_dump($index);   // Current index of the row
            })->read();
```

## Middleware

You can modify data of each row with middleware. See the following example

```php
use Mahmud\Sheet\SheetReader;

SheetReader::makeFromCsv('/path-to-csv-file/example-file.csv')
            ->ignoreRow(0)
            ->columns(['id', 'name', 'age'])
            ->applyMiddleware(function($row, $index){
                $row['age'] = $row['age'] . " Years";
                
                return $row;
            })
            ->onEachRow(function($row, $index){
                var_dump($row);
            })->read();

```

Another example using class as middleware

```php
class AgeMiddleware{
    public function handle($row, $index) {
        $row['age'] = $row['age'] . " Years";
    
        return $row;
    }
}

SheetReader::makeFromCsv('/path-to-csv-file/example-file.csv')
            ->ignoreRow(0)
            ->columns(['id', 'name', 'age'])
            ->applyMiddleware(new AgeMiddleware)
            ->onEachRow(function($row, $index){
                var_dump($row);
            })->read();

```

Also you can pass array of middlewares

```php
SheetReader::makeFromCsv('/path-to-csv-file/example-file.csv')
            ->ignoreRow(0)
            ->columns(['id', 'name', 'age'])
            ->applyMiddleware([
                new AgeMiddleware,
                new AnotherMiddleware,
            ])
            ->onEachRow(function($row, $index){
                var_dump($row);
            })->read();
```

If you return `null` from middleware, That row will be skipped and won't pass to `onEachRow` handler.

```php
SheetReader::makeFromCsv('/path-to-csv-file/example-file.csv')
            ->ignoreRow(0)
            ->columns(['id', 'name', 'age'])
            ->applyMiddleware(function($row){
                if($row['id'] == 1){
                    return null;
                }
                
                return $row;
            })
            ->onEachRow(function($row, $index){
                var_dump($row);
            })->read();
```

