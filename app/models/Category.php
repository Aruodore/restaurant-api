<?php

class Category extends Model
{
    public $fillable = ['category_id', 'name'];

    public function __construct(){
        parent::__construct();
        $this->table = 'categories';
    }

    public function restaurants($id)
    {
        return $this->getSub($id, 'restaurant');
    }
    
}