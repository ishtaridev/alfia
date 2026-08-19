<?php

namespace App\Enums;

enum RoomType: string
{
    case COLLECTIF = 'collectif';
    case ROOM_OF_FOUR = 'room_of_four';
    case ROOM_OF_THREE = 'room_of_three';
    case ROOM_OF_TWO = 'room_of_two';

    public function label(): string
    {
        return match ($this) {
            self::COLLECTIF => ' غرفة جماعية',
            self::ROOM_OF_FOUR => ' غرفة رباعية',
            self::ROOM_OF_THREE => ' غرفة ثلاثية',
            self::ROOM_OF_TWO => ' غرفة ثنائية',
        };
    }
}
