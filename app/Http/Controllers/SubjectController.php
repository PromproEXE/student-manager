<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function api_getSubject()
    {
        try {
            return Subject::all();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_create(Request $request)
    {
        try {
            $subjects = [];
            foreach ($request->all() as $data) {
                $validated = Validator::make($data, [
                    'subject_id' => ['required', 'string'],
                    'name' => ['required', 'string'],
                    'for_class' => ['required', 'array'],
                    'teacher' => ['required', 'array']
                ]);

                //CREATE
                $create = Subject::create([
                    'subject_id' => $data['subject_id'],
                    'name' => $data['name'],
                    'for_class' => $data['for_class'],
                    'department' => $data['department'],
                    'teacher' => $data['teacher'],
                    'created_by' => Auth::user()->name,
                    'updated_by' => Auth::user()->name
                ]);
                array_push($subjects, $create);
            }
            return $subjects;
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'subject_id' => ['required', 'string'],
                'id' => ['required'],
                'name' => ['required', 'string'],
                'for_class' => ['required', 'array'],
                'teacher' => ['required', 'array']
            ]);

            //CREATE
            $subject = Subject::find($id);
            $request['updated_by'] = Auth::user()->name;
            return $subject->update($request->all());
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_delete($id)
    {
        try {
            $delete = Subject::destroy($id);
            return $delete;
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
