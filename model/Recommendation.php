<?php

class Recommendation
{
    private $id_recommendation;
    private $id_service;
    private $id_user;
    private $discounted_price;
    // Removed recommendation_score

    public function __construct($id_recommendation = null, $id_service = null, $id_user = null, $discounted_price = null)
    {
        $this->id_recommendation = $id_recommendation;
        $this->id_service = $id_service;
        $this->id_user = $id_user;
        $this->discounted_price = $discounted_price;
    }

    // Getters
    public function getIdRecommendation() { return $this->id_recommendation; }
    public function getIdService() { return $this->id_service; }
    public function getIdUser() { return $this->id_user; }
    public function getDiscountedPrice() { return $this->discounted_price; }
    // Removed getRecommendationScore

    // Setters
    public function setIdService($id_service) { 
        $this->id_service = $id_service; 
        return $this;
    }

    public function setIdUser($id_user) { 
        $this->id_user = $id_user; 
        return $this;
    }

    public function setDiscountedPrice($discounted_price) { 
        $this->discounted_price = $discounted_price; 
        return $this;
    }

    // Removed setRecommendationScore
}
?>
