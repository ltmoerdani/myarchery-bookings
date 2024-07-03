<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event\Ticket;
use App\Models\Event\Booking;
use App\Models\ParticipantCompetitions;

class MapppingOrignalTicketAvailableInTicketsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $getTicket = Ticket::get();

    if (!empty($getTicket)) {
      foreach ($getTicket as $ticket) {
        $ticket_available = $ticket->ticket_available;
        $getParticipant = ParticipantCompetitions::where('ticket_id', $ticket->id)->get();
        if (!empty($getParticipant)) {
          foreach ($getParticipant as $participant) {
            $getBooking = Booking::where('id', $participant->booking_id)->get();
            if (!empty($getBooking)) {
              foreach ($getBooking as $booking) {
                $ticket_available = !empty($booking->quantity) ? $ticket_available + $booking->quantity : $ticket_available;
              }
            }
          }
        }

        $dataTicket = Ticket::find($ticket->id);
        $dataTicket->original_ticket_available = $ticket_available;
        $dataTicket->save();
      }
    }
  }
}
