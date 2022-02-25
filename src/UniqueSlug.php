<?php

namespace Benbraide\LaravelTraits;

trait UniqueSlug{
    public static function bootUniqueSlug(){
        static::saving(function ($model){
            $targetColumnName = $model->getUniqueSlugTargetColumnName();

            $query = static::where($targetColumnName, $model->$targetColumnName);
            $query = $model->constrainUniqueSlugQuery($query);

            if (!$query){
                return true;
            }

            $existing = $query->first();
            if (!$existing){
                return true;
            }

            foreach ($model->getUniqueSlugFillColumns() as $column){
                $model->$column = $existing->$column;
            }

            return false;
        });
    }

    public function isUniqueSlug(){
        return true;
    }

    public function constrainUniqueSlugQuery($query){
        if (!$query){
            return $query;
        }

        $field = $this->getUniqueSlugConstraintField();
        if ($field){
            return $query->where($field, $this->$field);
        }

        return $query;
    }

    public function getUniqueSlugConstraintField(){
        return null;
    }

    public function getUniqueSlugTargetColumnName(){
        return 'slug';
    }

    public function getUniqueSlugFillColumns(){
        return ['id'];
    }
}
