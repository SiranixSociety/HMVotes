<?php

namespace SiranixSociety\HMVotes\Models;

use Illuminate\Database\Eloquent\Model;
use SiranixSociety\HMFramework\Traits\HMSettingsHelper;

class Vote extends Model {

    /*
     * Traits and other things it uses
     */
    use HMSettingsHelper;

    public function __construct()
    {
        $this->setTable(config('HMVotes.TableNames.Votes'));
    }
    /*
     * Basic information
     */
    protected $primaryKey = 'ID';
    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';

    protected $fillable = [
        'Vote', 'Voter_id', 'Voter_type'
    ];

    protected $casts = [
        'Vote' => 'boolean'
    ];

    /*
     * Relationships
     */
    public function Voteable(){
        return $this->morphTo();
    }
    public function Voter(){
        return $this->morphTo();
    }

    /*
     * Scopes
     */
    // Vote Scopes
    public function scopeVotes($query){
        return $query->where('Vote', true);
    }
    public function scopeVoteCount($query){
        return $this->scopeVotes($query)->count();
    }

    // DisVote Scopes
    public function scopeNegativeVotes($query){
        return $query->where('Vote', false);
    }
    public function scopeNegativeVoteCount($query){
        return $this->scopeNegativeVotes($query)->count();
    }

    // Score Scopes
    public function scopeVoteScore($query){
        $DisVotes = $this->scopeNegativeVotes($query);
        $Votes = $this->scopeVoteCount($query);
        return $Votes - $DisVotes;
    }

}