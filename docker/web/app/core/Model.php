<?php

class ApiClient
{
    public function choose(array $query): array
    {
        
    }
}

class User extends Model
{
    
}

abstract class Model
{
    protected static $client;

    public function __construct()
    {
    }

    public function __destruct()
    {   
    }

    protected static function choose(array $query): Model
    {
        $a_model_source = parent::$client->choose($query);
        return self($a_model_source);
    }

    protected static function chooseAll(array $query): array //<Model>
    {
        $model_sources = parent::$client->choose($query);
        $model_list = array();
        foreach ($model_sources as $a_model_source) {
            $model_list[] = self($a_model_source);
        }
        return $model_list;
    }

    protected function delete(): bool
    {
        return self::$source->delete($this);
    }
}