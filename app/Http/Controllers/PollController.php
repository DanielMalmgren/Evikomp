<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Poll;
use App\PollSession;
use App\Municipality;
use Illuminate\Http\RedirectResponse;

class PollController extends Controller
{
    public function index(Request $request) {
        $data = [
            'polls' => Poll::all(),
        ];
        return view('polls.index')->with($data);
    }

    public function edit(Poll $poll) {
        $data = [
            'poll' => $poll,
            'municipalities' => Municipality::orderBy('name')->get(),
        ];
        return view('polls.edit')->with($data);
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

        $poll->translateOrNew($currentLocale)->name = $request->name;
        $poll->translateOrNew($currentLocale)->infotext = $request->infotext;
        $poll->active_from = $request->active_from;
        $poll->active_to = $request->active_to;
        $poll->save();

        $poll->municipalities()->sync($request->municipalities);

        return redirect('/poll')->with('success', __('Ändringar sparade'));
    }

    public function exportresponses(Poll $poll) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Pollresponses.xlsx');
        $worksheet = $spreadsheet->getSheetByName('Enkätsvar');

        $worksheet->setCellValue('A1', 'Namn');
        $worksheet->getColumnDimension('A')->setAutoSize(true);
        $worksheet->getStyle('A1')->getFont()->setBold(true);
        $worksheet->setCellValue('B1', 'Befattning');
        $worksheet->getColumnDimension('B')->setAutoSize(true);
        $worksheet->getStyle('B1')->getFont()->setBold(true);
        $worksheet->setCellValue('C1', 'Arbetsplats');
        $worksheet->getColumnDimension('C')->setAutoSize(true);
        $worksheet->getStyle('C1')->getFont()->setBold(true);

        $i = 4;
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
            $worksheet->setCellValueByColumnAndRow(3, $row, $session->user->workplace->name);
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

    public function show(Poll $poll): View {
        $poll_session = new PollSession();
        $poll_session->poll_id = $poll->id;
        $poll_session->user_id = Auth::user()->id;
        $poll_session->save();

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
