<?php
use PHPUnit\Framework\TestCase;
use TheatreReservation\Reservation;

class ReservationTest extends TestCase
{
    public function testCanCreateReservationWithValidConfiguration()
    {
        $reservation = new Reservation(127);
        $this->assertEquals(127, $reservation->getConfiguration());
    }

    public function testCannotCreateReservationWithInvalidConfiguration()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Reservation(100); // Configuration invalide
    }

    public function testCanReserveSeat()
    {
        $reservation = new Reservation(127);
        $result = $reservation->reserveSeat('A', 1);
        $this->assertEquals('Seat A1 reserved.', $result);
    }
}
