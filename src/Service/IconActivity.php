<?php

namespace App\Service;

class IconActivity
{
    public function getIconForActivity($activityName)
    {
        $activitiesArray = [
            "Musculation" => "musculation.png",
            "Volley-ball" => "volley-ball.png",
            "Zumba" => "zumba.png",
            "Tennis" => "tennis.png",
            "Ping-pong" => "ping-pong.png",
            "Pilates" => "pilates.png",
            "Plongée" => "plongée.png",
            "Natation" => "natation.png",
            "Football" => "football.png",
            "Running" => "running.png",
            "Badminton" => "badminton.png",
            "Basket-ball" => "basket-ball.png",
            "Boxing" => "boxing.png",
            "Fitness" => "fitness.png"
        ];

        if (array_key_exists($activityName, $activitiesArray)) {
            $icon = $activitiesArray[$activityName];
        } else {
            $icon = null;
        }
        return $icon;
    }
}
