<?php

namespace App\Service;

class IconActivity
{
    public function getIconForActivity($activityName)
    {
        $iconsArray = [
            "Musculation" => "fitness_center",
            "Volley-ball" => "sports_volleyball",
            "Zumba" => "music_note",
            "Tennis" => "sports_tennis",
            "Ping-pong" => "sports_tennis",
            "Pilates" => "self_improvement",
            "Plongée" => "water",
            "Natation" => "pool",
            "Football" => "sports_soccer",
            "Running" => "directions_run",
            "Badminton" => "sports_tennis",
            "Basket-ball" => "sports_basketball",
            "Boxing" => "sports_mma",
            "Fitness" => "fitness_center",
            "Evenement sportif" => "emoji_events",
            "Evenement autre" => "people"
        ];

        if (array_key_exists($activityName, $iconsArray)) {
            $icon = $iconsArray[$activityName];
        } else {
            $icon = null;
        }
        return $icon;
    }
}
