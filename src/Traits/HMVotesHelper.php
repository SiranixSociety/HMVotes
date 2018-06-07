<?php

namespace SiranixSociety\HMVotes\Traits;

trait HMVotesHelper {
    /*
     * Setting Functions
     */
    public function HMVoteHasSettings()
    {
        return isset($this->HMVoteSettings);
    }

    public function HMVoteGetAllSettings()
    {
        if ($this->HMVoteHasSettings()) {
            $Settings = $this->HMVoteSettings;
            while ($this->HMHasReferences($Settings)) {
                $Settings = $this->HMFillReferences($Settings);
            }
            return $Settings;
        }
        return null;
    }

    public function HMVoteHasSetting($Setting = null)
    {
        return array_has($this->HMVoteGetAllSettings(), $Setting);
    }

    public function HMVoteGetSetting($Setting = null)
    {
        return array_get($this->HMVoteGetAllSettings(), $Setting);
    }

    /*
     * Default Setting Functions
     */
    public function HMVoteHasAutoFillDefault(){
        if($this->HMVoteHasSetting('DefaultSettings.AutoFillDefault')){
            return $this->HMVoteGetSetting('DefaultSettings.AutoFillDefault');
        }
        if ($this->HMHasConfigSetting('Votes.DefaultSettings.AutoFillDefault')) {
            return $this->HMGetConfigSetting('Votes.DefaultSettings.AutoFillDefault');
        }
        return false;
    }
    public function HMVoteGetDefault(){
        if($this->HMVoteHasSetting('DefaultSettings.Default')){
            return $this->HMVoteGetSetting('DefaultSettings.Default');
        }
        if ($this->HMHasConfigSetting('Votes.DefaultSettings.Default')) {
            return $this->HMGetConfigSetting('Votes.DefaultSettings.Default');
        }
        return true;
    }

    /*
     * Limitation Settings
     */
    public function HMVoteIsLimited()
    {
        if ($this->HMVoteHasSetting('Limitations.Enabled')) {
            return $this->HMVoteGetSetting('Limitations.Enabled');
        }
        if ($this->HMHasConfigSetting('Votes.Limitations.Enabled')) {
            return $this->HMGetConfigSetting('Votes.Limitations.Enabled');
        }
        return false;
    }
    public function HMVoteCanBeLimited(){
        if ($this->HMHasConfigSetting('Votes.Limitations.Enabled')) {
            return $this->HMGetConfigSetting('Votes.Limitations.Enabled');
        }
        return false;
    }
    public function HMVoteHasModelLimitation($Model)
    {
        if (is_string($Model)) {
            $Model = new $Model;
        }
        if ($this->HMVoteHasSetting('Limitations.Models.' . get_class($Model))) {
            return true;
        }
        if ($Model->HMVoteHasSetting('Limitations.Model.' . get_class($this))) {
            return true;
        }
        return false;
    }
    public function HMVoteGetLimitation($Model = null){
        //TODO: Alpha, behaviour might be weird
        if (is_string($Model) && !empty($Model)) {
            $Model = new $Model;
        }
        $Limitation = [];
        if($this->HMVoteHasSetting('Limitations.Models.'.get_class($Model))){
            $Limitation = array_merge($this->HMVoteGetSetting('Limitations.Models.'.get_class($Model)), $Limitation);
        }
        if(!empty($Model)){
            if($Model->HMVoteHasSetting('Limitations.Models.'.get_class($this))){
                $Limitation = array_merge($Model->HMVoteGetSetting('Limitations.Models.'.get_class($this)), $Limitation);
            }
            if($Model->HMVoteHasSetting('Limitations.Default')){
                $Limitation = array_merge($Model->HMVoteGetSetting('Limitations.Default'), $Limitation);
            }
        }
        if($this->HMVoteHasSetting('Limitations.Default')){
            $Limitation = array_merge($this->HMVoteGetSetting('Limitations.Default'), $Limitation);
        }
        if($this->HMHasConfigSetting('Votes.Limitations.Default')){
            $Limitation = array_merge($this->HMGetConfigSetting('Votes.Limitations.Default'), $Limitation);
        }
        return $Limitation;
    }

    /*
     * Functions
     */
    public function HMVoteIsEnabled($Model = null)
    {
        if (is_string($Model)) {
            $Model = new $Model;
        }
        if ($this->HMHasConfigSetting('Votes.Enabled')) {
            if (!$this->HMGetConfigSetting('Votes.Enabled')) {
                return false;
            }
        }
        if ($this->HMVoteHasSetting('Enabled')) {
            if (!$this->HMVoteGetSetting('Enabled')) {
                return false;
            }
        }
        if (!empty($Model)) {
            if ($this->HMVoteHasSetting('Limitations.Models.' . get_class($Model) . '.Enabled')) {
                if (!$this->HMVoteGetSetting('Limitations.Models.' . get_class($Model) . '.Enabled')) {
                    return false;
                }
            }
            if ($Model->HMVoteHasSetting('Enabled')) {
                if (!$Model->HMVoteGetSetting('Enabled')) {
                    return false;
                }
            }
            if ($Model->HMVoteHasSetting('Limitations.Models.' . get_class($this) . '.Enabled')) {
                if (!$Model->HMVoteGetSetting('Limitations.Models.' . get_class($this) . '.Enabled')) {
                    return false;
                }
            }
        }
        return true;
    }
}