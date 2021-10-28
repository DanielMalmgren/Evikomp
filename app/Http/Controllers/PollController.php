<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Poll;
use App\PollSession;
use App\Workplace;
use App\Lesson;
use Illuminate\Http\RedirectResponse;

class PollController extends Controller
{
    public function index(Request $request) {
        $data = [
            'polls' => Poll::all(),
        ];
        return view('polls.index')->with($data);
    }

    public function replicate(Poll $poll): RedirectResponse {
        /** @var Poll $newPoll */
        $newPoll = $poll->replicateWithTranslations();
        $newPoll->translateOrDefault(\App::getLocale())->name .= ' - ' . __('kopia');
        $newPoll->push();

        $id_map = collect(); //To keep track on copy id vs orig id, to fix display criterias
        foreach($poll->poll_questions->sortBy('order') as $question) {
            $newQuestion = $question->replicateWithTranslations();
            $newQuestion->poll_id = $newPoll->id;
            $newQuestion->push();
            $id_map->put($question->id, $newQuestion->id);
            if(isset($newQuestion->display_criteria)) {
                $display_criteria_array = explode('==', $question->display_criteria);
                if(isset($display_criteria_array[1]) && $id_map->has($display_criteria_array[0])) { 
                    $newQuestion->display_criteria = $id_map->get($display_criteria_array[0])."==".$display_criteria_array[1];
                    $newQuestion->save();
                }

            }
        }

        return redirect('/poll')->with('success', __('Enkäten har kopierats'));
    }

    public function create() {
        $data = [
            'workplaces' => Workplace::all(),
        ];
        return view('polls.create')->with($data);
    }

    public function edit(Poll $poll) {
        $data = [
            'poll' => $poll,
            'workplaces' => Workplace::all(),
        ];
        return view('polls.edit')->with($data);
    }

    public function store(Request $request): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'infotext' => 'required',
        ],
        [
            'name.required' => __('Du måste ange ett namn på enkäten!'),
            'infotext.required' => __('Du måste ange en text med information om enkäten!'),
        ]);

        $poll = new Poll();
        $poll->save();

        activity()->on($poll)->log('created');

        return $this->update($request, $poll);
    }

    public function update(Request $request, Poll $poll): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'infotext' => 'required',
        ],
        [
            'name.required' => __('Du måste ange ett namn på enkäten!'),
            'infotext.required' => __('Du måste ange en text med information om enkäten!'),
        ]);

        $currentLocale = \App::getLocale();
        $user = Auth::user();
        logger("Poll ".$poll->id." is being edited by ".$user->name);
        activity()->on($poll)->log('updated');

        $poll->translateOrNew($currentLocale)->name = $request->name;
        $poll->translateOrNew($currentLocale)->infotext = $request->infotext;
        $poll->translateOrNew($currentLocale)->infotext2 = $request->infotext2;
        $poll->active_from = $request->active_from;
        $poll->active_to = $request->active_to;
        $poll->scope_full_or_part_time = $request->scope_full_or_part_time;
        $poll->scope_terms_of_employment = $request->scope_terms_of_employment;
        $poll->save();

        $poll->workplaces()->sync($request->workplaces);

        return redirect('/poll')->with('success', __('Ändringar sparade'));
    }

    public function exportresponses(Poll $poll) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Pollresponses.xlsx');
        $worksheet = $spreadsheet->getSheetByName('Enkätsvar');

        $worksheet->setCellValue('A1', __('Namn'));
        $worksheet->getColumnDimension('A')->setAutoSize(true);
        $worksheet->getStyle('A1')->getFont()->setBold(true);
        $worksheet->setCellValue('B1', __('Befattning'));
        $worksheet->getColumnDimension('B')->setAutoSize(true);
        $worksheet->getStyle('B1')->getFont()->setBold(true);
        $worksheet->setCellValue('C1', __('Arbetsplats'));
        $worksheet->getColumnDimension('C')->setAutoSize(true);
        $worksheet->getStyle('C1')->getFont()->setBold(true);
        $worksheet->setCellValue('D1', __('Födelsedatum'));
        $worksheet->getColumnDimension('D')->setAutoSize(true);
        $worksheet->getStyle('D1')->getFont()->setBold(true);
        $worksheet->setCellValue('E1', __('Kön'));
        $worksheet->getColumnDimension('E')->setAutoSize(true);
        $worksheet->getStyle('E1')->getFont()->setBold(true);

        $worksheet->setCellValue('F1', __('Spår'));
        $worksheet->getColumnDimension('F')->setAutoSize(true);
        $worksheet->getStyle('F1')->getFont()->setBold(true);
        $worksheet->setCellValue('G1', __('Modul'));
        $worksheet->getColumnDimension('G')->setAutoSize(true);
        $worksheet->getStyle('G1')->getFont()->setBold(true);

        $worksheet->setCellValue('H1', __('Datum'));
        $worksheet->getColumnDimension('H')->setAutoSize(true);
        $worksheet->getStyle('H1')->getFont()->setBold(true);

        $i = 9;
        $column_order = [];
        foreach($poll->poll_questions->where('type', '!=', 'pagebreak')->sortBy('order') as $question) {
            $cell = $worksheet->getCellByColumnAndRow($i, 1);
            $cell->setValue($question->translateOrDefault(\App::getLocale())->text);
            $worksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            $worksheet->getStyle($cell->getCoordinate())->getFont()->setBold(true);
            $column_order[$question->id] = $i;
            $i++;
        }

        $row = 2;
        foreach($poll->poll_sessions->where('finished', true) as $session) {
            $worksheet->setCellValueByColumnAndRow(1, $row, $session->user->name);
            $worksheet->setCellValueByColumnAndRow(2, $row, $session->user->title->name);
            if($session->user->workplace !== null) {
                $worksheet->setCellValueByColumnAndRow(3, $row, $session->user->workplace->name);
            }
            $worksheet->setCellValueByColumnAndRow(4, $row, $session->user->birthdate);
            $worksheet->setCellValueByColumnAndRow(5, $row, $session->user->gender);

            if($session->lesson !== null) {
                $worksheet->setCellValueByColumnAndRow(6, $row, $session->lesson->track->translateOrDefault(\App::getLocale())->name);
                $worksheet->setCellValueByColumnAndRow(7, $row, $session->lesson->translateOrDefault(\App::getLocale())->name);
            }

            $worksheet->setCellValueByColumnAndRow(8, $row, substr($session->created_at, 0, 10));

            foreach($session->poll_responses as $response) {
                $worksheet->setCellValueByColumnAndRow($column_order[$response->poll_question->id], $row, $response->response);
            }
            $row++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Sammanställning enkätsvar ".$poll->translateOrDefault(\App::getLocale())->name.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }

    public function show(Poll $poll, Lesson $lesson=null): View {

        $poll_session = Auth::user()->poll_sessions->where('poll_id', $poll->id)->first();
        if(isset($poll_session)) {
            if($poll_session->finished) {
                $data = [
                    'poll' => $poll,
                ];
                return view('polls.alreadyfilled')->with($data);
            }
        } else {
            $poll_session = new PollSession();
            $poll_session->poll_id = $poll->id;
            $poll_session->user_id = Auth::user()->id;
            if($lesson !== null) {
                $poll_session->lesson_id = $lesson->id;
            }
            $poll_session->save();
        }

        session(['poll_session_id' => $poll_session->id]);

        $first_question_id = $poll->first_question()->id;

        $data = [
            'poll' => $poll,
            //'poll_session' => $poll_session,
            'first_question_id' => $first_question_id,
        ];
        return view('polls.show')->with($data);
    }
}
