<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class ParticipantExport implements FromCollection, WithHeadings, WithMapping{
  public $participant;
  public function __construct($participant){
    $this->participant = $participant;
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection(){
    return $this->participant;
  }

  public function map($participant): array{
    return [
      $participant->event_name,
      $participant->fname,
      $participant->competition_type,
      $participant->title,
      $participant->category,
      $participant->delegation,
      $participant->status
    ];
  }

  public function headings(): array{
    return [
      'Event Title', 'Participant Name', 'Type', 'Category', 'Delegation', 'Delegation Name', 'Status'
    ];
  }
}
