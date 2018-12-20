<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Question;
use App\ResponseOption;
use App\TestResponse;
use App\TestSession;

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
        return [
            'response' => 'in:'.$correctoptions->implode('id', ',')
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
            'in' => 'Fel svar, försök igen!'
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
