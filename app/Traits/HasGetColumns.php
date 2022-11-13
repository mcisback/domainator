<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasGetColumns
{
    public static function getColumnsNamesTypeMapStatic() {
        $columns = [];
//        collect(Schema::getColumnListing(with(new static)->getTable()))->each(function ($col) use(&$columns) {
//            $columns[$col] = DB::getSchemaBuilder()->getColumnType(with(new static)->getTable(), $col);
//        });

        $tableName = with(new static)->getTable();

        $dbConnection = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if($dbConnection === 'mysql') {
            // Works only for sqlite
            collect(DB::select("describe $tableName"))->each(function ($col) use(&$columns) {
//                $col = (array) $col;
                $colType = $col->Type;

                if(str_contains($col->Type, "bigint")) {
                    $colType = 'integer';
                } elseif(str_contains($col->Type, "varchar")) {
                    $colType = 'string';
                } elseif(str_contains($col->Type, "timestamp")) {
                    $colType = 'datetime';
                } elseif(str_contains($col->Type, "tinyint(1)")) {
                    $colType = 'boolean';
                } else if(str_contains($col->Type, "tinyint")) {
                    $colType = 'integer';
                } else if(str_contains($col->Type, "text")) {
                    $colType = 'text';
                }

                $columns[$col->Field] = $colType;
            });
        } elseif($dbConnection === 'sqlite') {
            // Works only for sqlite
            collect(DB::select("pragma table_info('$tableName')"))->each(function ($col) use(&$columns) {
                $columns[$col->name] = $col->type;
            });
        }



        return $columns;
    }

}
