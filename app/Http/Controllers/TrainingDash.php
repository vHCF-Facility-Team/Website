<?php

namespace App\Http\Controllers;

use App\Audit;
use App\Mail\OtsAssignment;
use App\Mail\StudentComment;
use App\Mail\TrainingTicketMail;
use App\Ots;
use App\PublicTrainingInfo;
use App\PublicTrainingInfoPdf;
use App\TrainerFeedback;
use App\TrainingInfo;
use App\TrainingTicket;
use App\User;
use Auth;
use Carbon\Carbon;
use Config;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mail;

class TrainingDash extends Controller {
    public static $GRAPH_SESSIONS_PER_MONTH = 1;
    public static $GRAPH_SESSIONS_BY_INSTRUCTOR = 2;
    public static $GRAPH_SESSION_AVERAGE_DURATION = 3;
    public static $GRAPH_STUDENT_TRAINING_PER_RATING = 4;

    public function showATCast() {
        return view('dashboard.training.atcast');
    }

    public function trainingInfo() {
        $info_minor_gnd = TrainingInfo::where('section', 0)->orderBy('number', 'ASC')->get();
        $info_minor_lcl = TrainingInfo::where('section', 1)->orderBy('number', 'ASC')->get();
        $info_minor_app = TrainingInfo::where('section', 2)->orderBy('number', 'ASC')->get();
        $info_major_gnd = TrainingInfo::where('section', 3)->orderBy('number', 'ASC')->get();
        $info_major_lcl = TrainingInfo::where('section', 4)->orderBy('number', 'ASC')->get();
        $info_major_app = TrainingInfo::where('section', 5)->orderBy('number', 'ASC')->get();
        $info_ctr = TrainingInfo::where('section', 6)->orderBy('number', 'ASC')->get();

        $public_sections = PublicTrainingInfo::orderBy('order', 'ASC')->get();
        $public_sections_count = count(PublicTrainingInfo::get());
        $public_sections_order = [];
        $i = 0;
        foreach (range(0, $public_sections_count) as $p) {
            $public_sections_order[$i] = $p + 1;
            $i++;
        }
        $public_section_next = $i - 1;

        return view('dashboard.training.info')->with('info_minor_gnd', $info_minor_gnd)->with('info_minor_lcl', $info_minor_lcl)->with('info_minor_app', $info_minor_app)
            ->with('info_major_gnd', $info_major_gnd)->with('info_major_lcl', $info_major_lcl)->with('info_major_app', $info_major_app)
            ->with('info_ctr', $info_ctr)->with('public_sections', $public_sections)->with('public_section_order', $public_sections_order)
            ->with('public_section_next', $public_section_next);
    }

    public function addInfo(Request $request, $section) {
        $replacing = TrainingInfo::where('number', '>', $request->number)->where('section', $section)->get();
        if ($replacing != null) {
            foreach ($replacing as $r) {
                $new = $r->number + 1;
                $r->number = $new;
                $r->save();
            }
        }
        $info = new TrainingInfo;
        $info->number = $request->number + 1;
        $info->section = $request->section;
        $info->info = $request->info;
        $info->save();
        return redirect()->back()->with('success', 'The information has been added successfully.');
    }

    public function deleteInfo($id) {
        $info = TrainingInfo::find($id);
        $other_info = TrainingInfo::where('number', '>', $info->number)->get();
        foreach ($other_info as $o) {
            $o->number = $o->number - 1;
            $o->save();
        }
        $info->delete();
        return redirect()->back()->with('success', 'The information has been removed successfully.');
    }

    public function newPublicInfoSection(Request $request) {
        $request->validate([
            'name' => 'required',
            'order' => 'required'
        ]);

        if ($request->order < count(PublicTrainingInfo::get())) {
            $change_order = PublicTrainingInfo::where('order', '>=', $request->order)->get();
            foreach ($change_order as $c) {
                $c->order = $c->order + 1;
                $c->save();
            }
        }

        $info = new PublicTrainingInfo;
        $info->name = $request->name;
        $info->order = $request->order;
        $info->save();

        return redirect('/dashboard/training/info')->with('success', 'The section was added successfully.');
    }

    public function editPublicSection(Request $request, $id) {
        $request->validate([
            'name' => 'required'
        ]);

        $section = PublicTrainingInfo::find($id);
        $section->name = $request->name;
        $section->save();

        return redirect('/dashboard/training/info')->with('success', 'The section was updated successfully.');
    }

    public function saveSession() {
        $id = Auth::id();
        $nSessions = MentorAvail::where('trainee_id', $id)->where('slot', '>', Carbon::now())->count();



        $position = $request->input('position');
        $slot_id = $request->input('slot');
        $Slot = MentorAvail::find($slot_id);

        $Slot->trainee_id = $id;
        $Slot->position_id = $position;
        $Slot->trainee_comments = $request->input('comments');
        $Slot->save();

        ActivityLog::create(['note' => 'Accepted Session: ' . $Slot->slot, 'user_id' => Auth::id(), 'log_state' => 1, 'log_type' => 6]);

        $Slot->sendNewSessionEmail();
    }

    public function removePublicInfoSection($id) {
        $section = PublicTrainingInfo::find($id);
        $order = $section->order;
        $section->delete();

        $order_updates = PublicTrainingInfo::where('order', '>', $order)->get();
        foreach ($order_updates as $o) {
            $o->order = $o->order - 1;
            $o->save();
        }

        $pdfs = PublicTrainingInfoPdf::where('section_id', $id)->get();
        foreach ($pdfs as $p) {
            $p->delete();
        }

        return redirect('/dashboard/training/info')->with('success', 'The section was removed successfully.');
    }

    public function addPublicPdf(Request $request, $section_id) {
        $request->validate([
            'pdf' => 'required'
        ]);

        $ext = $request->file('pdf')->getClientOriginalExtension();
        $time = Carbon::now()->timestamp;
        $path = $request->file('pdf')->storeAs(
            'public/training_info',
            $time . '.' . $ext
        );
        $public_url = '/storage/training_info/' . $time . '.' . $ext;

        $pdf = new PublicTrainingInfoPdf;
        $pdf->section_id = $section_id;
        $pdf->pdf_path = $public_url;
        $pdf->save();

        return redirect('/dashboard/training/info')->with('success', 'The PDF was added successfully.');
    }

    public function removePublicPdf($id) {
        $pdf = PublicTrainingInfoPdf::find($id);
        $pdf->delete();

        return redirect('/dashboard/training/info')->with('success', 'The PDF was removed successfully.');
    }

    public function ticketsIndex(Request $request) {
        $controllers_with_tickets = array_flip(TrainingTicket::groupBy('controller_id')->pluck('controller_id')->toArray());
        $controllers = User::where('status', '1')->orderBy('lname', 'ASC')->get()->filter(function ($user) use ($controllers_with_tickets) {
            if (array_key_exists($user->id, $controllers_with_tickets) || $user->visitor == 0) {
                return $user;
            }
        })->pluck('backwards_name', 'id');
        $drafts = false;

        if ($request->id != null) {
            $search_result = User::find($request->id);
        } else {
            $search_result = null;
        }

        $tickets = null;
        $all_drafts = null;
        $exams = null;

        if ($search_result != null) {
            $tickets_sort = TrainingTicket::where('controller_id', $search_result->id)->get()->sortByDesc(function ($t) {
                return strtotime($t->date . ' ' . $t->start_time);
            })->pluck('id');
            $tickets_order = implode(',', array_fill(0, count($tickets_sort), '?'));
            $tickets = TrainingTicket::whereIn('id', $tickets_sort)->orderByRaw("field(id,{$tickets_order})", $tickets_sort)->paginate(25);
            foreach ($tickets as &$t) {
                $t->position = $this->legacyTicketTypes($t->position);
                $t->sort_category = $this->getTicketSortCategory($t->position, $t->draft);

                $drafts = $drafts || $t->draft;
            }
            if ($tickets_sort->isEmpty() && ($search_result->status != 1)) {
                return redirect()->back()->with('error', 'There is no controller that exists with that CID.');
            }
            $exams = User::getAcademyExamTranscriptByCid($request->id);
        } elseif (auth()->user()->hasRole('ata') || auth()->user()->isAbleTo('snrStaff')) {
            $all_drafts = TrainingTicket::where('draft', true)->orderBy('created_at', 'DESC')->paginate(25);
        } elseif (auth()->user()->isAbleTo('train')) {
            $all_drafts = TrainingTicket::where('draft', true)->where('trainer_id', auth()->user()->id)->orderBy('created_at', 'DESC')->paginate(25);
        }

        return view('dashboard.training.tickets')->with('controllers', $controllers)->with('search_result', $search_result)->with('tickets', $tickets)->with('exams', $exams)->with('drafts', $drafts)->with('all_drafts', $all_drafts);
    }

    public function searchTickets(Request $request) {
        $search_result = User::find($request->cid);
        if ($search_result != null) {
            return redirect('/dashboard/training/tickets?id=' . $search_result->id);
        } else {
            return redirect()->back()->with('error', 'There is no controller that exists with that CID.');
        }
    }

    public function imageUpload(Request $request) {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $ext = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $ext;

            $request->file('upload')->storeAs('public/ticket_images', $fileName);

            $public_url = config('app.url') . '/storage/ticket_images/' . $fileName;

            return response()->json([
                'fileName' => $fileName,
                'uploaded' => 1,
                'url' => $public_url
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => ['message' => 'No file uploaded.']
        ], 400);
    }

    public function newTrainingTicket(Request $request) {
        $c = $request->id;
        $ticket = new TrainingTicket;
        $controllers = User::where('status', '1')->orderBy('lname', 'ASC')->get()->pluck('backwards_name', 'id');
        $recent_sessions = [];

        try {
            $res = (new Client)->get(
                Config::get('scheddy.base').'/api/userSessions/trainers/'.Auth::id(),
                ['headers' => [
                    'Authorization' => 'Bearer '.Config::get('scheddy.api_key')
                ],
                'http_errors' => false
                ]
            );

            if ($res->getStatusCode() == "200") {
                $recent_sessions = json_decode($res->getBody());
            } else {
                Log::error('Scheddy trainer session pull resulted in a ' . $res->getStatusCode() . ' status code');
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error($e);
        }

        $max_recent_sessions = 5;
        $recent_sessions = array_reduce($recent_sessions, function ($new_sessions, $session) use ($max_recent_sessions) {
            $date = Carbon::parse($session->session->start)->setTimezone('America/New_York');
            $student = User::find($session->session->student);
            $session_id = TrainingTicket::$scheddy_session_id_map['DEFAULT'];
            $scheddy_id = $session->session->id;

            if (array_key_exists($session->sessionType->id, TrainingTicket::$scheddy_session_id_map)) {
                $session_id = TrainingTicket::$scheddy_session_id_map[$session->sessionType->id];
            }

            if (!TrainingTicket::where('scheddy_id', $scheddy_id)->exists() && isset($student) && count($new_sessions) < $max_recent_sessions) {
                array_push($new_sessions, [
                    "scheddy_id" => $scheddy_id,
                    "date" =>  $date->format('m/d/Y'),
                    "start_time" => $date->format('H:i'),
                    "student_name" => $student->full_name,
                    "student_cid" => $session->session->student,
                    "lesson_name" => $session->sessionType->name,
                    "lesson_type" => $session_id
                ]);
            }

            return $new_sessions;
        }, []);

        return view('dashboard.training.new_ticket')->with('controllers', $controllers)->with('c', $c)
            ->with('positions', $ticket->getPositionSelectAttribute())->with('session_ids', $ticket->getSessionSelectAttribute())
            ->with('progress_types', $ticket->getProgressSelectAttribute())->with('recent_sessions', $recent_sessions);
    }

    public function handleSaveTicket(Request $request, $id = null) {
        if ($request->action == 'new') {
            return $this->saveNewTicket($request, $id);
        }

        if ($request->action == 'save') {
            return $this->saveTicket($request, $id);
        }
        
        if ($request->action == 'draft') {
            return $this->draftNewTicket($request, $id);
        }

        return redirect()->back()->with('error', 'Invalid way to save training tickets. Please report this to the webmaster.');
    }

    public function addStudentComments(Request $request, $id) {
        $validator = $request->validate([
            'student_comments' => 'required'
        ]);

        $ticket = TrainingTicket::find($id);

        if (Auth::id() != $ticket->controller_id) {
            return redirect()->back()->with('error', 'Not your training ticket');
        }

        $ticket->student_comments = $request->student_comments;
        $ticket->save();

        $mailer = Mail::to('hcf-ta@vatusa.net');
        $trainer = User::find($ticket->trainer_id);
        if ($trainer) {
            if ($trainer->user_status[$trainer->status] == 'Active' && $trainer->isAbleTo('train')) {
                $mailer = Mail::to($trainer->email)->cc('hcf-training@vatusa.net');
            }
        }
        $mailer->send(new StudentComment($trainer->full_name, $ticket->id));
        return redirect()->back()->with('success', 'You have successfully added your comments to your training ticket. Please reach out to your mentor or instructor if you have any further questions or concerns');
    }

    public function viewTicket($id) {
        $ticket = TrainingTicket::find($id);
        $ticket->position = $this->legacyTicketTypes($ticket->position);
        return view('dashboard.training.view_ticket')->with('ticket', $ticket);
    }

    public function editTicket($id) {
        $ticket = TrainingTicket::find($id);
        $ticket->position = $this->legacyTicketTypes($ticket->position);
        $positions = $ticket->getPositionSelectAttribute();
        if (!key_exists($ticket->position, $positions)) {
            $positions[$ticket->position] = 'Legacy Category';
        }
        $sessions = $ticket->getSessionSelectAttribute();
        if (!key_exists($ticket->session_id, $sessions)) {
            $sessions[$ticket->session_id] = 'Legacy Session';
        }
        if (Auth::id() == $ticket->trainer_id || Auth::user()->isAbleTo('snrStaff')) {
            $controllers = User::where('status', '1')->where('canTrain', '1')->orderBy('lname', 'ASC')->get()->pluck('backwards_name', 'id');
            return view('dashboard.training.edit_ticket')->with('ticket', $ticket)->with('controllers', $controllers)
            ->with('positions', $positions)->with('session_ids', $sessions)
            ->with('progress_types', $ticket->getProgressSelectAttribute());
        } else {
            return redirect()->back()->with('error', 'You can only edit tickets that you have submitted unless you are the TA.');
        }
    }

    public function deleteTicket($id) {
        $ticket = TrainingTicket::find($id);
        $draft = $ticket->draft;
        if (Auth::user()->isAbleTo('snrStaff') || (Auth::id() == $ticket->trainer_id && $draft)) {
            $controller_id = $ticket->controller_id;
            $ticket->delete();

            if (! $draft) {
                $audit = new Audit;
                $audit->cid = Auth::id();
                $audit->ip = $_SERVER['REMOTE_ADDR'];
                $audit->what = Auth::user()->full_name . ' deleted a training ticket for ' . User::find($controller_id)->full_name . '.';
                $audit->save();
            }

            return redirect('/dashboard/training/tickets?id=' . $controller_id)->with('success', 'The ticket has been deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Only the TA can delete non-draft training tickets.');
        }
    }

    public function otsCenter() {
        $ots_new = Ots::where('status', 0)->orderBy('created_at', 'DESC')->paginate(25);
        $ots_accepted = Ots::where('status', 1)->orderBy('created_at', 'DESC')->paginate(25);
        $ots_complete = Ots::where('status', 2)->orWhere('status', 3)->orderBy('created_at', 'DESC')->paginate(25);
        $instructors = User::orderBy('lname', 'ASC')->get()->filter(function ($user) {
            return $user->hasRole('ins');
        })->pluck('full_name', 'id');
        return view('dashboard.training.ots-center')->with('ots_new', $ots_new)->with('ots_accepted', $ots_accepted)->with('ots_complete', $ots_complete)->with('instructors', $instructors);
    }

    public function acceptRecommendation($id) {
        $ots = Ots::find($id);
        $ots->status = 1;
        $ots->ins_id = Auth::id();
        $ots->save();

        $audit = new Audit;
        $audit->cid = Auth::id();
        $audit->ip = $_SERVER['REMOTE_ADDR'];
        $audit->what = Auth::user()->full_name . ' accepted an OTS for ' . User::find($ots->controller_id)->full_name . '.';
        $audit->save();

        return redirect()->back()->with('success', 'You have sucessfully accepted this OTS. Please email the controller at ' . User::find($ots->controller_id)->email . ' in order to schedule the OTS.');
    }

    public function rejectRecommendation($id) {
        if (!Auth::user()->isAbleTo('snrStaff')) {
            return redirect()->back()->with('error', 'Only the TA can reject OTS recommendations.');
        } else {
            $ots = Ots::find($id);
            $ots->delete();

            return redirect()->back()->with('success', 'The OTS recommendation has been rejected successfully.');
        }
    }

    public function assignRecommendation(Request $request, $id) {
        if (!Auth::user()->isAbleTo('snrStaff')) {
            return redirect()->back()->with('error', 'Only the TA can assign OTS recommendations to instructors.');
        } else {
            $ots = Ots::find($id);
            $ots->status = 1;
            $ots->ins_id = $request->ins;
            $ots->save();

            $ins = User::find($ots->ins_id);
            $controller = User::find($ots->controller_id);

            Mail::to($ins->email)->cc('hcf-ta@vatusa.net')->send(new OtsAssignment($ots, $controller, $ins));

            $audit = new Audit;
            $audit->cid = Auth::id();
            $audit->ip = $_SERVER['REMOTE_ADDR'];
            $audit->what = Auth::user()->full_name . ' assigned an OTS for ' . User::find($ots->controller_id)->full_name . ' to ' . User::find($ots->ins_id)->full_name . '.';
            $audit->save();

            return redirect()->back()->with('success', 'The OTS has been assigned successfully and the instructor has been notified.');
        }
    }

    public function completeOTS(Request $request, $id) {
        $validator = $request->validate([
            'result' => 'required'
        ]);

        $ots = Ots::find($id);

        if ($ots->ins_id == Auth::id() || Auth::user()->isAbleTo('snrStaff')) {
            $ots->status = $request->result;
            $ots->save();

            $audit = new Audit;
            $audit->cid = Auth::id();
            $audit->ip = $_SERVER['REMOTE_ADDR'];
            $audit->what = Auth::user()->full_name . ' updated an OTS for ' . User::find($ots->controller_id)->full_name . '.';
            $audit->save();

            return redirect()->back()->with('success', 'The OTS has been updated successfully!');
        } else {
            return redirect()->back()->with('error', 'This OTS has not been assigned to you.');
        }
    }

    public function otsCancel($id) {
        $ots = Ots::find($id);
        $ots->ins_id = null;
        $ots->status = 0;
        $ots->save();

        $audit = new Audit;
        $audit->cid = Auth::id();
        $audit->ip = $_SERVER['REMOTE_ADDR'];
        $audit->what = Auth::user()->full_name . ' cancelled an OTS for ' . User::find($ots->controller_id)->full_name . '.';
        $audit->save();

        return redirect()->back()->with('success', 'The OTS has been unassigned from you and cancelled successfully.');
    }

    public function getTicketSortCategory($position, $draft) {
        $position_types_by_rating = TrainingTicket::$position_types_by_rating;
        switch (true) {
            case ($draft):
                return 'drafts';
                break;
            case ($position > 6 && $position < 22): // Legacy types
                return 's1';
                break;
            case (in_array($position, $position_types_by_rating['S1'])):
                return 's1';
                break;
            case ($position > 21 && $position < 31): // Legacy types
                return 's2';
                break;
            case (in_array($position, $position_types_by_rating['S2'])):
                return 's2';
                break;
            case ($position > 30 && $position < 42): // Legacy types
                return 's3';
                break;
            case (in_array($position, $position_types_by_rating['S3'])):
                return 's3';
                break;
            case ($position > 41 && $position < 48): // Legacy types
                return 'c1';
                break;
            case (in_array($position, $position_types_by_rating['C1'])):
                return 'c1';
                break;
            default:
                return 'other';
        }
    }

    public function legacyTicketTypes($position) { // Returns modern ticket ids for legacy ticket types
        switch ($position) {
            case 11:
                return 104;
                break;
            case 103:
                return 104;
                break;
            case 18:
                return 108;
                break;
            case 107:
                return 108;
                break;
            case 27:
                return 113;
                break;
            case 112:
                return 113;
                break;
            case 31:
                return 115;
                break;
            case 32:
                return 115;
                break;
            case 114:
                return 115;
                break;
            case 42:
                return 121;
                break;
            case 120:
                return 121;
                break;
            default:
                return $position;
        }
    }

    public function statistics() {
        return view('dashboard.training.statistics');
    }

    public static function generateTrainingStats($year, $month, $dataType) {
        $position_types_by_rating = TrainingTicket::$position_types_by_rating;
        $retArr = [];
        $retArr['dateSelect'] = ['month' => $month, 'year' => $year];
        // Set date range
        if ($year == null) {
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
        }
        $from = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $to = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $retArr['date'] = ['start_date' => $from, 'end_date' => $to];
        $sessions = TrainingTicket::whereBetween('start_date', [$from, $to])->get();
        if ($dataType == 'stats') {
            $sessionsPrevious = TrainingTicket::whereBetween('start_date', [Carbon::createFromDate($from)->startOfMonth()->subMonths(1)->toDateString(), Carbon::createFromDate($to)->startOfMonth()->subMonths(1)->toDateString()]);
        }
        // Training sessions per month
        $retArr['sessionsPerMonth'] = $sessions->count();
        if ($dataType == 'stats') {
            $retArr['sessionsCompletePerMonth'] = $sessions->where('type', 12)->count();
            $retArr['sessionsPerMonthNoShow'] = $sessions->where('type', 10)->count();
            $retArr['sessionsPreviousMonth'] = $sessionsPrevious->count();
            $retArr['sessionsCompletePreviousMonth'] = $sessionsPrevious->where('type', 12)->count();
        }
        // Training sessions per month by type
        if (($dataType == 'graph')||($dataType == 'stats')) {
            $sessionsS1 = $sessions->whereIn('position', $position_types_by_rating['S1'])->count();
            $sessionsS2 = $sessions->whereIn('position', $position_types_by_rating['S2'])->count();
            $sessionsS3 = $sessions->whereIn('position', $position_types_by_rating['S3'])->count();
            $sessionsC1 = $sessions->whereIn('position', $position_types_by_rating['C1'])->count();
            $sessionsOther = $sessions->whereIn('position', $position_types_by_rating['OTHER'])->count();
            $retArr['sessionsByType'] = ['S1' => $sessionsS1, 'S2' => $sessionsS2, 'S3' => $sessionsS3, 'C1' => $sessionsC1, 'Other' => $sessionsOther];
        }
        // Training sessions, type, and hours by instructor
        $trainers = $trainerSessions = [];
        $activeRoster = User::where('status', '1')->get()->unique('id');
        foreach ($activeRoster as $activeUser) {
            if ($activeUser->getTrainPositionAttribute() > 0) {
                $trainers[] = $activeUser;
            }
        }
        $trainingStaffBelowMins = 0;
        $ins = 0;
        $mtr = 0;
        foreach ($trainers as $trainer) {
            $trainerStats = [];
            $trainerStats['cid'] = $trainer->id;
            $trainerStats['name'] = explode(' ', $trainer->getFullNameAttribute())[1];
            $trainerSesh = $sessions->where('trainer_id', $trainer->id);
            $trainerStats['total'] = $trainerSesh->count();
            $trainerStats['S1'] = $trainerSesh->whereIn('position', $position_types_by_rating['S1'])->count();
            $trainerStats['S2'] = $trainerSesh->whereIn('position', $position_types_by_rating['S2'])->count();
            $trainerStats['S3'] = $trainerSesh->whereIn('position', $position_types_by_rating['S3'])->count();
            $trainerStats['C1'] = $trainerSesh->whereIn('position', $position_types_by_rating['C1'])->count();
            $trainerStats['Other'] = $trainerSesh->whereIn('position', $position_types_by_rating['OTHER'])->count();
            if ($trainerStats['total'] < Config::get('hcf.trainer_min_sessions')) {
                $trainingStaffBelowMins++;
            }
            $trainerSessions[] = $trainerStats;
            if (User::find($trainer->id)->hasRole('ins')) {
                $ins++;
            } elseif (User::find($trainer->id)->hasRole('mtr')) {
                $mtr++;
            }
        }
        $retArr['totalInstructors'] = $ins;
        $retArr['totalMentors'] = $mtr;
        $retArr['trainerSessions'] = $trainerSessions;
        // Find top trainers
        $top_trainers = $trainer_by_total = $trainer_by_cid = [];
        foreach ($retArr['trainerSessions'] as $t) {
            $trainer_by_total[$t['cid']] = $t['total'];
            $trainer_by_cid[$t['cid']] = $t['name'];
        }
        arsort($trainer_by_total);
        foreach ($trainer_by_total as $trainer_cid => $tt) {
            if ($tt == 0) {
                break;
            }
            $top_trainers[] = (object)['name' => $trainer_by_cid[$trainer_cid], 'sessions_given' => $tt];
            if (count($top_trainers) == 3) {
                break;
            }
        }
        // Students requiring training
        if ($dataType == 'graph') {
            $students = User::where('status', '1')->where('visitor', '0')->where('canTrain', '1')->where('rating_id', '<', '5')->get();
            $studentsS1 = $students->where('rating_id', '1')->count();
            $studentsS2 = $students->where('rating_id', '2')->count();
            $studentsS3 = $students->where('rating_id', '3')->count();
            $studentsC1 = $students->where('rating_id', '4')->count();
            $retArr['studentsRequireTng'] = ['S1' => $studentsS1, 'S2' => $studentsS2, 'S3' => $studentsS3, 'C1' => $studentsC1];
        }
        // Number of unique students
        if ($dataType == 'stats') {
            $uniqueStudents = $sessions->unique('controller_id')->count();
            $retArr['uniqueStudents'] = $uniqueStudents;
        }
        // Number of OTS' per month
        if ($dataType == 'stats') {
            $otsMonth = Ots::whereBetween('updated_at', [$from . ' 00:00:00', $to . ' 00:00:00'])->get();
            $otsPerMonthPass = $otsMonth->where('status', '2')->count();
            $otsPerMonthFail = $otsMonth->where('status', '3')->count();
            $retArr['otsPerMonth'] = ['pass' => $otsPerMonthPass, 'fail' => $otsPerMonthFail];
        }
        // Average session duration (over last 6-months)
        if ($dataType == 'graph') {
            $sessionsSixMonths = TrainingTicket::where('start_date', '>', Carbon::now()->subMonths(6))->get();
            $uniqueSessionIDs = $sessionsSixMonths->unique('session_id');
            $sessionDuration = [];
            foreach ($uniqueSessionIDs as $uniqueSessionID) {
                $seshByTypeDuration = $averageSeshDuration = null;
                $seshByType = $sessionsSixMonths->where('session_id', $uniqueSessionID->session_id);
                foreach ($seshByType as $sesh) {
                    $seshByTypeDuration += strtotime("1970-01-01 " . $sesh->duration . ":00");
                }
                $averageSeshDuration = $seshByTypeDuration / $seshByType->count() / 60; // Get duration in minutes
                $sessionDuration[] = [$uniqueSessionID->getSessionNameAttribute(), round($averageSeshDuration, 2)];
            }
            $retArr['sessionDuration'] = $sessionDuration;
        }
        // Generate TA's monthly report
        if ($dataType == 'stats') {
            if ($retArr['sessionsPerMonth'] == 0) {
                $percentSessionsChange = '-100';
            } elseif ($retArr['sessionsPreviousMonth'] == 0) {
                $percentSessionsChange = '+100';
            } else {
                $percentSessionsChange = round($retArr['sessionsPerMonth'] / $retArr['sessionsPreviousMonth'], 1);
                if ($percentSessionsChange > 0) {
                    $percentSessionsChange = '+' . $percentSessionsChange;
                }
            }
            if ($retArr['sessionsCompletePerMonth'] == 0) {
                $percentSessionsCompleteChange = '-100';
            } elseif ($retArr['sessionsCompletePreviousMonth'] == 0) {
                $percentSessionsCompleteChange = '+100';
            } else {
                $percentSessionsCompleteChange = round($retArr['sessionsCompletePerMonth'] / $retArr['sessionsCompletePreviousMonth'], 1);
                if ($percentSessionsCompleteChange > 0) {
                    $percentSessionsCompleteChange = '+' . $percentSessionsCompleteChange;
                }
            }
            $retArr['taMonthlyReport'] = "In the Month of " . Carbon::createFromDate($retArr['date']['start_date'])->format('F') . ", HCF has offered " . $retArr['sessionsPerMonth'] . " training sessions (" . $percentSessionsChange . "% change from " . Carbon::createFromDate($retArr['date']['start_date'])->subMonths(1)->format('F') . "). " . $retArr['sessionsCompletePerMonth'] . " sessions were completed (" . $percentSessionsCompleteChange . "%), with " . $retArr['sessionsPerMonthNoShow'] . " known no-shows. " . $trainingStaffBelowMins . " Training Staff members did not meet monthly minimums.";
            if (count($top_trainers) > 0) {
                $retArr['taMonthlyReport'] .= "Our TOP trainers for the month of " . Carbon::createFromDate($retArr['date']['start_date'])->format('F') . " were:";
                foreach ($top_trainers as $ind => $top_trainer) {
                    $order_no = $ind + 1;
                    if ($ind > 0) {
                        $retArr['taMonthlyReport'] .= "|";
                    }
                    $retArr['taMonthlyReport'] .= " " . $order_no . ". " . $top_trainer->name;
                }
            }
        }
        return $retArr;
    }

    public function newTrainerFeedback() {
        return view('dashboard.training.trainer_feedback')->with('feedbackOptions', TrainerFeedback::getFeedbackOptions());
    }

    public function saveNewTrainerFeedback(Request $request) {
        $validatedData = $request->validate([
            'student_name' => 'nullable|string',
            'student_email' => 'nullable|email',
            'student_cid' => 'nullable|integer',
            'feedback_id' => 'required|integer',
            'feedback_date' => 'required|date',
            'service_level' => 'required|digits_between:1,5',
            'position_trained' => 'required|string',
            'booking_method' => 'required|integer',
            'training_method' => 'required|integer',
            'comments' => 'required'
        ]);

        //Google reCAPTCHA Verification
        $client = new Client;
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => Config::get('google.recaptcha'),
                'response' => $request->input('g-recaptcha-response'),
            ]
        ]);
        $r = json_decode($response->getBody())->success;
        if ($r != true && Config::get('app.env') != 'local' && $request->input('internal') != 1 &&
            app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName() != 'internalTrainerFeedback') {
            return redirect()->back()->with('error', 'You must complete the ReCaptcha to continue.');
        }

        //Continue Request
        $feedback = new TrainerFeedback;
        $feedback->trainer_id = ltrim($request->input('feedback_id'), 'gec');
        $feedback->feedback_date = $request->input('feedback_date');
        $feedback->service_level = $request->input('service_level');
        $feedback->position_trained = $request->input('position_trained');
        $feedback->booking_method = $request->input('booking_method');
        $feedback->training_method = $request->input('training_method');
        $feedback->student_name = $request->input('student_name');
        $feedback->student_email = $request->input('student_email');
        $feedback->student_cid = $request->input('student_cid');
        $feedback->comments = $request->input('comments');
        $feedback->status = 0;
        $feedback->save();

        $redirect = ($request->input('redirect_to') == 'internal') ? '/dashboard' : '/';
        return redirect($redirect)->with('success', 'Thank you for the feedback! It has been received successfully.');
    }

    public function handleSchedule() {
        $user = Auth::user();

        if ($user->rating_id == 1 && !$user->onboarding_complete) {
            return redirect()->back()->with('error', 'Onboarding must be complete before scheduling a training session. Please refer to the HCF onboarding course on the VATUSA Academy. Contact the TA with questions or concerns.');
        }

        return redirect("https://scheddy.vhcf.net");
    }

    private function saveNewTicket(Request $request, $id) {
        $request->validate([
            'controller' => 'required',
            'position' => 'required',
            'session_id' => 'required',
            'type' => 'required',
            'date' => 'required',
            'start' => 'required',
            'end' => 'required',
            'duration' => 'required',
            'movements' => ['nullable',' integer'],
            'score' => ['nullable', 'integer', 'digits_between:1,5']
        ]);

        $ticket = TrainingTicket::find($id);
        if (! $ticket) {
            $ticket = new TrainingTicket;
            $ticket->scheddy_id = $request->scheddy_id;
        }

        $ticket->controller_id = $request->controller;
        $ticket->trainer_id = Auth::id();
        $ticket->position = $request->position;
        $ticket->session_id = $request->session_id;
        $ticket->type = $request->type;
        $ticket->date = $request->date;
        $ticket->start_date = Carbon::createFromFormat('m/d/Y', $request->date)->toDateString();
        $ticket->start_time = $request->start;
        $ticket->end_time = $request->end;
        $ticket->duration = $request->duration;
        $ticket->comments = mb_convert_encoding($request->comments, 'UTF-8'); // character encoding added to protect save method
        $ticket->ins_comments = $request->trainer_comments;
        $ticket->cert = (is_null($request->cert)) ? 0 : $request->cert;
        $ticket->monitor = (is_null($request->monitor)) ? 0 : $request->monitor;
        $ticket->score = $request->score;
        $ticket->movements = $request->movements;
        $ticket->draft = false;
        $ticket->save();
        $extra = null;

        $date = $ticket->date;
        $date = date("Y-m-d");
        $controller = User::find($ticket->controller_id);
        $trainer = User::find($ticket->trainer_id);

        Mail::to($controller->email)->cc('hcf-ta@vatusa.net')->send(new TrainingTicketMail($ticket, $controller, $trainer));

        if ($request->ots == 1) {
            $ots = new Ots;
            $ots->controller_id = $ticket->controller_id;
            $ots->recommender_id = $ticket->trainer_id;
            $ots->position = $request->position;
            $ots->status = 0;
            $ots->save();
            $extra = ' and the OTS recommendation has been added';
        }

        $audit = new Audit;
        $audit->cid = Auth::id();
        $audit->ip = $_SERVER['REMOTE_ADDR'];
        $audit->what = Auth::user()->full_name . ' added a training ticket for ' . User::find($ticket->controller_id)->full_name . '.';
        $audit->save();

        return redirect('/dashboard/training/tickets?id=' . $ticket->controller_id)->with('success', 'The training ticket has been submitted successfully' . $extra . '.');
    }

    private function draftNewTicket(Request $request, $id) {
        $request->validate([
            'controller' => 'required',
            'movements' => ['nullable',' integer'],
            'score' => ['nullable', 'integer', 'digits_between:1,5']
        ]);

        $ticket = TrainingTicket::find($id);

        if (! $ticket) {
            if ($request->automated && !$request->is_new) {
                return response(null);
            }

            $ticket = new TrainingTicket;
            $ticket->scheddy_id = $request->scheddy_id;
        }

        $ticket->controller_id = $request->controller;
        $ticket->trainer_id = Auth::id();
        $ticket->position = $request->position;
        $ticket->session_id = $request->session_id;
        $ticket->type = $request->type;
        $ticket->date = $request->date;
        $ticket->start_date = $request->date ? Carbon::createFromFormat('m/d/Y', $request->date)->toDateString() : null;
        $ticket->start_time = $request->start;
        $ticket->end_time = $request->end;
        $ticket->duration = $request->duration;
        $ticket->comments = mb_convert_encoding($request->comments, 'UTF-8'); // character encoding added to protect save method
        $ticket->ins_comments = $request->trainer_comments;
        $ticket->cert = (is_null($request->cert)) ? 0 : $request->cert;
        $ticket->monitor = (is_null($request->monitor)) ? 0 : $request->monitor;
        $ticket->score = $request->score;
        $ticket->movements = $request->movements;
        $ticket->draft = true;
        $ticket->save();

        if ($request->automated) {
            return response(url('/dashboard/training/tickets/edit/' . $ticket->id));
        }

        return redirect('/dashboard/training/tickets/edit/' . $ticket->id)->with('success', 'The training ticket has been saved successfully, but not finalized. Please finalize all changes once you are ready.');
    }

    private function saveTicket(Request $request, $id) {
        $ticket = TrainingTicket::find($id);
        if (Auth::id() == $ticket->trainer_id || Auth::user()->isAbleTo('snrStaff')) {
            $request->validate([
                'controller' => 'required',
                'position' => 'required',
                'session_id' => 'required',
                'type' => 'required',
                'date' => 'required',
                'start' => 'required',
                'end' => 'required',
                'duration' => 'required',
                'movements' => ['nullable',' integer'],
                'score' => ['nullable', 'integer', 'between:1,5']
            ]);

            $ticket->controller_id = $request->controller;
            $ticket->position = $request->position;
            $ticket->session_id = $request->session_id;
            $ticket->type = $request->type;
            $ticket->date = $request->date;
            $ticket->start_date = Carbon::createFromFormat('m/d/Y', $request->date)->toDateString();
            $ticket->start_time = $request->start;
            $ticket->end_time = $request->end;
            $ticket->duration = $request->duration;
            $ticket->comments = $request->comments;
            $ticket->ins_comments = $request->trainer_comments;
            $ticket->cert = (is_null($request->cert)) ? 0 : $request->cert;
            $ticket->monitor = (is_null($request->monitor)) ? 0 : $request->monitor;
            $ticket->score = $request->score;
            $ticket->movements = $request->movements;
            $ticket->save();

            $audit = new Audit;
            $audit->cid = Auth::id();
            $audit->ip = $_SERVER['REMOTE_ADDR'];
            $audit->what = Auth::user()->full_name . ' edited a training ticket for ' . User::find($request->controller)->full_name . '.';
            $audit->save();

            return redirect('/dashboard/training/tickets/view/' . $ticket->id)->with('success', 'The ticket has been updated successfully.');
        } else {
            return redirect()->back()->with('error', 'You can only edit tickets that you have submitted unless you are the TA.');
        }
    }
}
