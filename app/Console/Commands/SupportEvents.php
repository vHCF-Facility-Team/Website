<?php

namespace App\Console\Commands;

use App\Event;
use App\EventDenylist;
use App\EventPosition;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class SupportEvents extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Events:UpdateSupportEvents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates any neighbor support events that aren\'t already in the DB.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // Pulled from the vatSpy datafile - update this list if airports are addded/removed to KZID, KZDC, KZJX, KZHU, KZME, KZMA, or KZNY
    protected array $event_pull_lut = [
        'all_events' => [
            
        ],
        'fno_or_live_only' => [
            
        ]
    ];

    protected array $event_position_preset = [
        "STBY | Standby",
        "HCF | HCF Center",
        "HNL | Honolulu TRACON",
        "HNL | Honolulu ATCT",
        "CIC/TMU",
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info("-- Updating support events - this may take a while --");

        $this->info("Building ARTCC LUTs");

        $all_events_lut = [];
        $fno_or_live_only_lut = [];
        $inverse_lut = [];

        foreach ($this->event_pull_lut["all_events"] as $artcc => $airports) {
            foreach ($airports as $airport) {
                $all_events_lut[] = $airport;
                $inverse_lut[$airport] = $artcc;
            }
        }
        foreach ($this->event_pull_lut["fno_or_live_only"] as $artcc => $airports) {
            foreach ($airports as $airport) {
                $fno_or_live_only_lut[] = $airport;
                $inverse_lut[$airport] = $artcc;
            }
        }


        $client = new Client();

        $this->info('Downloading event info from VATSIM');

        $res = $client->get('https://my.vatsim.net/api/v1/events/all');

        $this->info('Parsing event info JSON');

        $result = json_decode($res->getBody());
        if (is_null($result)) {
            return;
        }

        foreach ($result->data as $event) {
            $pull_this_event = false;
            $organizer = null;

            // parse times
            $start_time = Carbon::parse($event->start_time);

            if (Carbon::now()->diffInMonths($start_time) > 4) {
                continue; // skip events that are too far out or too far in the past
            }

            $end_time = Carbon::parse($event->end_time);

            // Airport check: is this a facility we care about?
            // Checked based off the list of airports pulled from vatSpy datafile.
            foreach ($event->airports as $airport) {
                if (in_array($airport->icao, $all_events_lut)) {
                    $pull_this_event = true;
                    $organizer = $inverse_lut[$airport->icao];
                    break;
                }
                if (in_array($airport->icao, $fno_or_live_only_lut)) {
                    // is it a live event?
                    if (str_contains(strtolower($event->name), 'live')) {
                        // live event - pull this event too
                        $pull_this_event = true;
                        $organizer = $inverse_lut[$airport->icao];
                        break;
                    }
                    // is it on a friday? (FNO)
                    if ($start_time->dayOfWeek == 5) { // 05 (not 06): Friday
                        // FNO - pull this event too
                        $pull_this_event = true;
                        $organizer = $inverse_lut[$airport->icao];
                        break;
                    }
                }
            }

            // Check if in the denylist
            if (EventDenylist::where('vatsim_id', $event->id)->exists()) {
                $this->info('Event with ID ' . $event->id . ' found in denylist. Skipping...');
                $pull_this_event = false;
            }

            if (!$pull_this_event) {
                continue;
            }

            $existing = Event::where('vatsim_id', $event->id)->first();
            if ($existing !== null) {
                if ($existing->type == Event::$TYPES["UNVERIFIED_SUPPORT"]) {
                    $this->info("Updating support event with vatsim id ".$event->id);
                    $this->updateEvent($existing, $event, $organizer, $start_time, $end_time);
                    $existing->save();
                } else {
                    $this->info("Skipping support event with vatsim id ".$event->id);
                }

                continue;
            }

            $this->info('Creating support event with vatsim id '.$event->id);

            // create the event in our database

            $this->info($event->id.': Saving to database');

            $new_event = new Event;
            $this->updateEvent($new_event, $event, $organizer, $start_time, $end_time);

            $new_event->status = Event::$STATUSES["HIDDEN"];
            $new_event->reg = Event::$REGS["CLOSED"];
            $new_event->type = Event::$TYPES["UNVERIFIED_SUPPORT"];
            $new_event->vatsim_id = $event->id;
            $new_event->save();

            $new_event_id = Event::where('vatsim_id', $event->id)->first()->id;

            foreach ($this->event_position_preset as $position_name) {
                $new_event_position = new EventPosition;
                $new_event_position->event_id = $new_event_id;
                $new_event_position->name = $position_name;
                $new_event_position->save();
            }

            $this->info('Created ' . $event->id);
        }
    }

    private function updateEvent($event, $vatsim_data, $organizer, $start_time, $end_time) {
        $event->name = $vatsim_data->name;
        $event->host = $organizer;
        $event->description = $vatsim_data->description;
        $event->date = $start_time->format("m/d/Y");
        $event->start_time = $start_time->toTimeString('minute');
        $event->end_time = $end_time->toTimeString('minute');
        $event->banner_path = $vatsim_data->banner;
    }
}
