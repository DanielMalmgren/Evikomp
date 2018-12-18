<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Question;
use App\ResponseOption;

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
        $question_id = $this->input('question_id');
        $question = Question::where('id', $question_id)->first();
        $lesson = $question->lesson;
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
        if($validator->passes()) {
            logger("RÄTT! ".$this->input('response'));
        } else {
            logger("FEL! ".$this->input('response'));
        }
        //TODO: Skriv till databasen
    }
}
