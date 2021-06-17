<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\ResponseOption;
use App\TestResponse;
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
        $test_response = TestResponse::find($this->session()->get('test_response_id'));
        if(! isset($test_response)) {
            logger("Something went wrong while validating test response!");
            logger("Test response id: ".$this->session()->get('test_response_id'));
            logger("Test session id: ".$this->session()->get('testsession_id'));
            logger("User id: ".Auth::user()->id);
        }
        $question = $test_response->question;
        $correctoptions = ResponseOption::where([['question_id', '=', $question->id],['isCorrectAnswer', '=', true]])->get();

        return [
            'singleresponse' => Rule::in($correctoptions->pluck('id')),
            'multiresponse.*' => Rule::in($correctoptions->pluck('id')),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $reasoning = TestResponse::find($this->session()->get('test_response_id'))->question->translateOrDefault(\App::getLocale())->reasoning;

        if(isset($reasoning)) {
            return [
                'in' => $reasoning,
            ];
        } else {
            return [
                'in' => __('Fel svar, försök igen!'),
            ];
        }
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
            $test_response->correct = true;
        } else {
            $test_response->wrong_responses++;
        }
        $test_response->save();
    }
}
