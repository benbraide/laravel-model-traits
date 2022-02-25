<?php

namespace Benbraide\LaravelTraits;

use Illuminate\Support\Str;

trait Slug{
    public static function bootSlug(){
        static::saving(function ($model){
            $targetColumnName = $model->getSlugTargetColumnName();
            $sourceColumnName = $model->getSlugSourceColumnName();
            $pass = null;

            do{
                if ($pass === null){
                    $slug = Str::slug($model->$sourceColumnName);
                    $pass = 0;
                }
                else{
                    $slug = (Str::slug($model->$sourceColumnName) . $pass);
                    $pass++;
                }

                $query = $model->constrainSlugQuery(static::where($targetColumnName, $slug));
            } while ($query && $query->count() > 0);

            $model->$targetColumnName = $slug;
        });
    }

    public function constrainSlugQuery($query){
        if (method_exists($this, 'isUniqueSlug') && $this->isUniqueSlug()){
            return null;
        }

        if (!$query){
            return $query;
        }

        $field = $this->getSlugConstraintField();
        if ($field){
            return $query->where($field, $this->$field);
        }

        return $query;
    }

    public function getSlugConstraintField(){
        return null;
    }

    public function getSlugTargetColumnName(){
        return 'slug';
    }

    public function getSlugSourceColumnName(){
        return 'title';
    }
}
