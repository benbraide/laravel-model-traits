<?php

namespace Benbraide\LaravelTraits;

trait UniqueId{
    public static function bootUniqueId(){
        static::creating(function ($model) {
            $columnName = $model->getUniqueIdColumnName();
            if (!$model->getUniqueIdAlwaysCreateState() && $model->$columnName != null){
                return;
            }

            $model->$columnName = static::generateRandomDigits($model->getUniqueIdDigitLength(), function($value, $pass) use ($model, $columnName) {
                $query = $model->constrainUniqueIdQuery(static::where($columnName, $value));
                return (!$query || $query->count() == 0);
            }, $model->getUniqueIdPrefix(), $model->getUniqueIdSuffix());
        });

        static::saving(function ($model){
            $columnName = $model->getUniqueIdColumnName();
            if ($model->$columnName != null){
                return;
            }

            $model->$columnName = static::generateRandomDigits($model->getUniqueIdDigitLength(), function($value, $pass) use ($model, $columnName) {
                $query = $model->constrainUniqueIdQuery(static::where($columnName, $value));
                return (!$query || $query->count() == 0);
            }, $model->getUniqueIdPrefix(), $model->getUniqueIdSuffix());
        });
    }

    public function constrainUniqueIdQuery($query){
        if (!$query){
            return $query;
        }

        $field = $this->getUniqueIdConstraintField();
        if ($field){
            return $query->where($field, $this->$field);
        }

        return $query;
    }

    public function getUniqueIdConstraintField(){
        return null;
    }

    public function getUniqueIdColumnName(){
        return 'uid';
    }

    public function getUniqueIdDigitLength(){
        return 16;
    }

    public function getUniqueIdPrefix(){
        return '';
    }

    public function getUniqueIdSuffix(){
        return '';
    }

    public function getUniqueIdAlwaysCreateState(){
        return false;
    }

    public static function generateRandomDigits($length = 16, $validator = null, $prefix = '', $suffix = ''){
        $pass = 0;
        do{
            $digits = '';
            for ($i = 0; $i < $length; $i++){
                if (strlen($digits) == 0){
                    $digits .= mt_rand(1, 9);
                }
                else{
                    $digits .= mt_rand(0, 9);
                }
            }

            $digits = ($prefix . $digits . $suffix);
            $pass++;
        } while ($validator && $validator($digits, $pass) === false);

        return $digits;
    }
}
