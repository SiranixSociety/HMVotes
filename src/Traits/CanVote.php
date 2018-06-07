<?php

namespace SiranixSociety\HMVotes\Traits;

use SiranixSociety\HMVotes\Models\Vote;

trait CanVote
{
    /*
     * Relationships
     */
    public function MyVotes()
    {
        return $this->morphMany(
            config('HelperModels.Structure.Models.Vote'),
            'Voter'
        );
    }
    /*
     * Additional Functions
     */
    public function HasVoted($Model = null){
        if(empty($Model)){
            if($this->MyVotes()->count() > 0){
                return true;
            }
            return false;
        }
        $KeyName = $this->getKeyName();
        return $Model->Votes()->where('Voter_type', get_class())->where('Voter_id', $this->$KeyName)->exists();
    }
    public function GetVote($Model = null){
        if(empty($Model)){
            return $this->MyVotes()->orderBy('CreatedAt', 'DESC')->first();
        }
        $KeyName = $this->getKeyName();
        return $Model->Votes()->where('Voter_type', get_class())->where('Voter_id', $this->$KeyName)->orderBy('CreatedAt', 'DESC')->first();
    }
    public function GetVotes($Model = null){
        if(empty($Model)){
            return $this->MyVotes()->get();
        }
        $KeyName = $this->getKeyName();
        return $Model->Votes()->where('Voter_type', get_class())->where('Voter_id', $this->$KeyName)->get();
    }

    /*
     * Helper Functions
     */
    public function CanVote($Model = null){
        // TODO: Alpha, behaviour might be weird
        if(!$this->HMVoteIsEnabled() || empty($Model)){
            return $this->HMVoteIsEnabled();
        }
        if($this->HMVoteIsLimited() || $Model->HMVoteIsLimited()){
            $Limitation = $this->HMVoteGetLimitation($Model);
            if(isset($Limitation['Enabled'])){
                return $Limitation['Enabled'];
            }
            return false;
        }
        return true;
    }
    public function CanVoteNow($Model = null){
        if(!$this->HMVoteCanBeLimited()){
            return true;
        }
        $Limitation = $this->HMVoteGetLimitation($Model);
        $LimitationMode = $this->HMGetLimitationMode($Limitation);
        $LimitationAmount = $this->HMGetLimitationAmount($Limitation);
        if($this->HMLimitationUsesTime($Limitation)){
            $LimitationTime = $this->HMGetLimitationTime($Limitation);
        }
        $ModelKeyName = $Model->getKeyName();

        if($this->MyVotes()->count() < $LimitationAmount || $LimitationAmount === 0){
            return true;
        }
        if(!empty($Model)) {
            if (!$this->CanVote($Model)) {
                return false;
            }
        }
        if($LimitationMode === 0){
            if(!$this->HMLimitationUsesTime($Limitation)){
                if($this->MyVotes()->count() < $LimitationAmount){
                    return true;
                }
                return false;
            }
            if($this->MyVotes()->where('CreatedAt', '>', $LimitationTime)->count() < $LimitationAmount){
                return true;
            }
        } elseif ($LimitationMode === 1){
            $Votes = $this->MyVotes()->where('Voteable_type', get_class($Model))->where('Voteable_id', $Model->$ModelKeyName);

            if(!$this->HMLimitationUsesTime($Limitation)){
                if($Votes->count() < $LimitationAmount){
                    return true;
                }
                return false;
            }
            if($Votes->where('CreatedAt', '<', $LimitationTime)->count() < $LimitationAmount){
                return true;
            }
        }
        return false;
    }

    /*
     * Actual Function
     */
    public function Vote($Voteable, $Vote = null){
        if(!$this->CanVoteNow($Voteable)){
            return false;
        }
        if(!$this->CanVote($Voteable)){
            return false;
        }
        $KeyName = $this->getKeyName();

        $NewVote = new Vote();
        if(is_null($Vote) && !$Voteable->HMVoteHasAutoFillDefault()){
            return false;
        } elseif(is_null($Vote)){
            $NewVote->Vote = $Voteable->HMVoteGetDefault();
        }else {
            $NewVote->Vote = $Vote;
        }
        $NewVote->Voter_type = get_class();
        $NewVote->Voter_id = $this->$KeyName;


        $Voteable->Votes()->save($NewVote);
        return true;
    }

}