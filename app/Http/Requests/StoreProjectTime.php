<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectTime extends FormRequest
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
        return [
            'starttime' => 'required',
            'endtime' => 'required',
            'date' => 'required',
            'workplace_id' => 'required'
        ];
        //TODO: Finns det någon rule för att endtime ska vara större än starttime?
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'starttime.required' => __('Du måste ange en starttid!'),
            'endtime.required' => __('Du måste ange en sluttid!'),
            'date.required' => __('Du måste ange ett datum!')
        ];
    }
}
