<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function api_getRoomAmount()
    {
        try {
            return [14, 14, 13, 11, 11, 11];
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getClassroom()
    {
        try {
            return Classroom::all();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getClassroomById($id)
    {
        try {
            return Classroom::where('id', $id)->first();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getClassroomByUserId($id)
    {
        try {
            $user = User::find($id);
            $classrooms = [];
            foreach ($user['classroom'] as $class_id) {
                $classData = Classroom::where('class_id', $class_id)->first();
                if ($classData != null || $classData != []) {
                    array_push($classrooms, $classData);
                }
            }

            return $classrooms;
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getClassroomByTeacher($teacher)
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
            $data = Classroom::all();

            return $data->filter(fn ($item) => array_search($teacher_name, $item['teacher']) !== false);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_autoCreate(Request $request)
    {
        try {
            $subjects = Subject::all();
            $classroom = [];

            foreach ($subjects as $subject) {
                $data = Classroom::firstOrCreate(
                    ['class_id' => $subject['subject_id']],
                    [
                        'name' => $subject['name'],
                        'department' => $subject['department'],
                        'for_class' => $subject['for_class'],
                        'teacher' => $subject['teacher'],
                        'data' => json_decode('{"post": []}'),
                        'created_by' => Auth::user()->name,
                        'updated_by' => Auth::user()->name
                    ]
                );
                array_push($classroom, $data);

                foreach ($subject['for_class'] as $index => $class) {
                    foreach ($class as $room) {
                        $student_data = User::where('role', 'like', '%student%')->get();
                        $filterData = $student_data->filter(fn ($std) => $std['class'] == $index + 1 && $std['room'] == $room);

                        foreach ($filterData as $std) {
                            $arrSearch = array_search($subject['subject_id'], $std['classroom']);

                            if ($arrSearch === false) {
                                $classroom = $std['classroom'];
                                array_push($classroom, $subject['subject_id']);

                                $std['classroom'] = $classroom;
                                $std->save();
                            }
                        }
                    }
                }
            }
            return [];
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_createPost(Request $request, $subject_id)
    {
        try {
            //VALIDATE
            $validated = $request->validate([
                'type' => ['required', 'string'],
                'title' => ['required', 'string'],
            ]);

            //GENERATE POST ID
            $request['id'] = Str::random(9);

            //GET CLASSROOM DATA
            $classData = Classroom::where('class_id', $subject_id)->first();

            //PUSH NEW POST DATA TO CLASSROOM
            $postData = $classData['data'];
            array_push($postData['post'], $request->all());

            $classData->data = $postData;

            //SAVE
            return $classData->save();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getPost($id, $post_id)
    {
        try {
            $data = Classroom::where('id', $id)->first(['data']);
            $filtered = array_search($post_id, array_column($data['data']['post'], 'id'));
            if ($filtered !== false) {
                return $data['data']['post'][$filtered];
            } else {
                return [];
            }
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_updatePost(Request $request, $id, $post_id)
    {
        try {
            //GET CLASS DATA
            $classData = Classroom::where('id', $id)->first();

            //GET POST INDEX
            $post_index = array_search($post_id, array_column($classData['data']['post'], 'id'));

            //UPDATE DATA
            if ($post_index !== false) {
                //FIX INDIRECTED OVERLOADED
                $postData = $classData['data'];
                $postData['post'][$post_index] = $request->all();

                //ASSIGN DATA
                $classData['data'] = $postData;
                return $classData->save();
            }
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_deletePost($id, $post_id)
    {
        try {
            //GET CLASS DATA
            $classData = Classroom::where('id', $id)->first();

            //GET POST INDEX
            $post_index = array_search($post_id, array_column($classData['data']['post'], 'id'));

            //DELETE DATA
            if ($post_index !== false) {
                //FIX INDIRECTED OVERLOADED
                $postData = $classData['data'];
                array_splice($postData['post'], $post_index, 1);

                //ASSIGN DATA
                $classData['data'] = $postData;
                return $classData->save();
            }
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getTeacherName($id)
    {
        try {
            return Classroom::where('id', $id)->first(['teacher']);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_update(Request $request, $id)
    {
    }

    public function api_delete($id)
    {
        try {
            return Classroom::destroy($id);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
