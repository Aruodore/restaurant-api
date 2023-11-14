<?php

class Restaurant extends Model{

    public $fillable = ['name', 'description', 'telephone_number', 'rating', 'brand_id'];

    public function locations($id)
    {
        return $this->getSub($id, 'location', 'restaurant_locations');
    }

    public function categories($id)
    {
        return $this->getSub($id, 'category', 'restaurant_categories');
    }

    public function images()
    {
        
    }



}