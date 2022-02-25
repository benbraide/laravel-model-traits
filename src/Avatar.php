<?php

namespace Benbraide\LaravelTraits;

use Illuminate\Support\Str;

trait Avatar{
    public function getAvatarAttribute($value){
        $field = $this->getAvatarTargetColumnName();
        if ($field !== 'avatar'){
            $value = $this->$field;
        }

        if (!$value){
            return $this->getAvatarDefault($value);
        }

        if (Str::startsWith($value, 'http://') || Str::startsWith($value, 'https://') || Str::startsWith($value, 'www.') || Str::startsWith($value, 'ftp://')){
            return $value;
        }

        if ($value){
            $value = Str::start($value, '/');
        }

        $prefix = $this->getAvatarPrefix();
        if ($prefix){
            $value = Str::start("{$prefix}{$value}", '/');
        }

        return $value;
    }

    public function getAvatarTargetColumnName(){
        return 'avatar';
    }

    public function getAvatarPrefix(){
        return null;
    }

    public function getAvatarDefault($value){
        return $value;
    }
}
