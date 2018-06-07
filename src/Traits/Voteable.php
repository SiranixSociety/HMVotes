<?php

namespace SiranixSociety\HMVotes\Traits;

trait Voteable {
    /*
     * Relationships
     */
    public function Votes(){
        return $this->morphMany(
            config('HelperModels.Structure.Models.Vote'),
            'Voteable'
        );
    }

}