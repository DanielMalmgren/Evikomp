<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Question;
use App\ResponseOption;
use App\TestResponse;
use App\TestSession;
use Illuminate\Validation\Rule;

class StoreTestResponse extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //$test_response = TestResponse::find($this->input('test_response_id'));
        $test_response = TestResponse::find($this->session()->get('test_response_id'));
        //$testsession = TestSession::find($this->input('testsession_id'));
        //$question_id = $this->input('question_id');
        //$question = Question::where('id', $question_id)->first();
        //$question = Question::find($this->input('question_id'));
        $question = $test_response->question;
        //$lesson = $testsession->lesson;
        $correctoptions = ResponseOption::where([['question_id', '=', $question->id],['isCorrectAnswer', '=', true]])->get();
        logger("Rätt svar för fråga ".$question->id." är ".$correctoptions->implode('id', ','));
        return [
            'singleresponse' => Rule::in($correctoptions->pluck('id')),
            'multiresponse.*' => Rule::in($correctoptions->pluck('id'))
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'in' => __('Fel svar, försök igen!')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        //$test_response = TestResponse::find($this->input('test_response_id'));
        $test_response = TestResponse::find($this->session()->get('test_response_id'));
        if($validator->passes()) {
            //logger("RÄTT! ".$this->input('response'));
            $test_response->correct = true;
        } else {
            //logger("FEL! ".$this->input('response'));
            $test_response->wrong_responses++;
        }
        $test_response->save();
    }
}
