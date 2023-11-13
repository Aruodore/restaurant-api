<?php

class Location extends Model
{
    public function restaurants($id)
    {
        return $this->getSub($id, 'restaurant');
    }
    
}