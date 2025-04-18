<?php

namespace App\Console\Commands;

use App\TrainingTicket;
use Config;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class UploadTrainingTickets extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'VATUSATrainingTickets:UploadPending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload pending training tickets to VATUSA';

    /**
     * Execute the console command.
     */
    public function handle() {
        $tickets = TrainingTicket::where('vatusa_upload_status', TrainingTicket::$VATUSA_UPLOAD_STATUS["PENDING"])
            ->where('draft', false)
            ->get();

        foreach ($tickets as $ticket) {
            $vatusa_position = $this->vatusaizePosition($ticket);

            $req_params = [
                'form_params' =>
                [
                    'instructor_id' => $ticket->trainer_id,
                    'session_date' => date_format(date_create($ticket->date), "Y-m-d") . ' ' . $ticket->start_time,
                    'position' => $vatusa_position,
                    'duration' => $ticket->duration,
                    'notes' => $ticket->comments,
                    'movements' => $ticket->movements,
                    'score' => $ticket->score,
                    'location' => 1
                ],
                'http_errors' => false
            ];

            if (is_null($ticket->position)) {
                continue;
            }

            $res = (new Client())->request(
                'POST',
                Config::get('vatusa.base').'/v2/user/' . $ticket->controller_id . '/training/record?apikey=' . Config::get('vatusa.api_key'),
                $req_params
            );

            if ($res->getStatusCode() == Response::HTTP_OK) {
                $ticket->vatusa_upload_status = TrainingTicket::$VATUSA_UPLOAD_STATUS["UPLOADED"];
                $ticket->save();
            }
        }
    }

    // Position type must match regex /^([A-Z]{2,3})(_([A-Z]{1,3}))?_(DEL|GND|TWR|APP|DEP|CTR)$/ to be accepted by VATUSA
    private function vatusaizePosition($ticket) {
        switch ($ticket->position) {
            case 100:
            case 101: return 'HCF_DEL';
            case 105: return 'HCF_GND';
            case 109: return 'HCF_TWR';
            case 115: return 'HCF_APP';
            case 104: return 'HNL_DEL';
            case 108: return 'HNL_GND';
            case 113: return 'HNL_TWR';
            case 117:
            case 118:
            case 119: return 'HNL_APP';
            case 121: return 'HNL_CTR';
            default: return null;
        }
    }
}
