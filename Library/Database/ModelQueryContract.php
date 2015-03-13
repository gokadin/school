<?php namespace Library\Database;

interface ModelQueryContract
{
    public function save();

    public function delete();

    public function touch();

    public static function create(array $values);

    public static function exists($var, $value);

    public static function all(); // base model not supported

    public static function where($var, $operator, $value, $link = 'AND'); // base model not supported

    public static function find($id);

    public function hasOne($modelName, $foreignKey = null);

    public function belongsTo($modelName, $foreignKey = null);

    public function hasMany($modelName, $foreignKey = null);

    public function hasManyThrough($modelName, $throughModelName, $foreignKey = null, $throughForeignKey = null);

    public function belongsToMany($modelName, $pivotName = null, $thisForeignKey = null, $targetForeignKey = null);

    public function morphTo($metaIdField = Table::META_ID, $metaTypeField = Table::META_TYPE);

    public function morphOne($modelName, $metaIdField = Table::META_ID, $metaTypeField = Table::META_TYPE, $typeName = null);
}
