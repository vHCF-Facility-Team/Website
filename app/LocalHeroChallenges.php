<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocalHeroChallenges extends Model {
    protected $table = 'local_hero_challenges';
    private static $enroute = ['HNL','GUM'];
    private static $approach = ['HNL','OGG','ITO','NGF','GUM'];
    private static $local = ['BKH','BSF','HHI','JRF','KOA','LIH','MKK','GSN','UAM'];

    public static function getLocalHeroChallengePositions() {
        $control_positions = [];
        foreach (static::$enroute as $facility) {
            $control_positions[] = $facility . '_CTR';
        }
        foreach (static::$approach as $facility) {
            $control_positions[] = $facility . '_APP';
        }
        $locals = array_merge(static::$approach, static::$local);
        foreach ($locals as $facility) {
            $control_positions[] = $facility . '_TWR';
            $control_positions[] = $facility . '_GND';
            $control_positions[] = $facility . '_DEL';
        }
        asort($control_positions);
        return $control_positions;
    }
}
