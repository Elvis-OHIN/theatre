<?php
require __DIR__ . "/../../src/Booking.php";
require __DIR__ . "/../../src/BookingController.php";

use PHPUnit\Framework\TestCase;
use App\Booking;

class BookingTest extends TestCase
{
    public function testCanBookSeat()
    {
        $booking = new Booking(5);
        $this->assertTrue($booking->bookSeat('A1'));
        $this->assertContains('A1', $booking->getBookedSeats());
    }

    public function testCannotBookSameSeatTwice()
    {
        $this->expectException(Exception::class);
        $booking = new Booking(5);
        $booking->bookSeat('A1');
        $booking->bookSeat('A1');
    }

    public function testCannotBookMoreThanCapacity()
    {
        $this->expectException(Exception::class);
        $booking = new Booking(2);
        $booking->bookSeat('A1');
        $booking->bookSeat('A2');
        $booking->bookSeat('A3');
    }
}
