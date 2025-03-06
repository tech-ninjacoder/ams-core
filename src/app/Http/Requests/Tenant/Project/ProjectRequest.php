<?php


namespace App\Http\Requests\Tenant\Project;


use App\Http\Requests\BaseRequest;

class ProjectRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:2',
            'description' => 'required|min:2',
            'location' => 'required|min:2',
            'geometry' => 'nullable',
            'pme_id' => 'required|unique:projects'


//            'start_date' => 'required|date',
//            'end_date' => 'nullable|date',
        ];
    }

}
