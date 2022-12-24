<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
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

    public function api_getSubjectByTeacher($teacher)
    {
        function textToUTF($text)
        {
            $str = bin2hex(iconv('UTF-8', 'UTF-16BE', $text));
            $var = str_split($str, 4);
            foreach ($var as &$ch) {
                $ch = '\u' . $ch;
            }
            return implode("", $var);
        }

        try {
            $name_split = explode('-', $teacher);
            $teacher_name = $name_split[0] . ' ' . $name_split[1];
            $data = Subject::all();

            return $data->filter(fn ($item) => array_search($teacher_name, $item['teacher']) !== false);
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

            //UPDATE
            $request['updated_by'] = Auth::user()->name;
            $subject = Subject::updateOrCreate(['id' => $request['id']], $request->all());

            //UPDATE CLASSROOM
            $classroom = Classroom::where('class_id', $subject['subject_id'])->first();
            if ($classroom != null) {
                $classroom->update([
                    'class_id' => $subject['subject_id'],
                    'name' => $subject['name'],
                    'for_class' => $subject['for_class'],
                    'teacher' => $subject['teacher'],
                    'department' => $subject['department'],
                    'updated_by' => Auth::user()->name,
                ]);
            }
            return [$subject, $classroom];
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
