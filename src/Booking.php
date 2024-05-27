<?php
namespace App;

class Booking
{
    private $capacity;
    private $bookedSeats = [];

    public function __construct($capacity)
    {
        $this->capacity = $capacity;
    }

    public function bookSeat($seatNumber)
    {
        if (in_array($seatNumber, $this->bookedSeats)) {
            throw new \Exception("Seat already booked.");
        }

        if (count($this->bookedSeats) >= $this->capacity) {
            throw new \Exception("No more seats available.");
        }

        $this->bookedSeats[] = $seatNumber;
        return true;
    }

    public function getBookedSeats()
    {
        return $this->bookedSeats;
    }
}
