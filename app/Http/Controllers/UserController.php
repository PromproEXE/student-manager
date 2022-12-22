<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\Timetable;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    use PasswordValidationRules;
    public function api_getall()
    {
        try {
            return User::all();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getStudentFromClass($class, $room)
    {
        try {
            return User::where('class', $class)
                ->where('room', $room)
                ->where('role', 'like', '%student%')
                ->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getStudentNameFromClass($class, $room)
    {
        try {
            return User::where('class', $class)
                ->where('room', $room)
                ->where('role', 'like', '%student%')
                ->get(['name', 'class', 'room']);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getStudentName()
    {
        try {
            return User::where('role', 'like', '%student%')->get(['name', 'class', 'room']);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getStudent()
    {
        try {
            return User::where('role', 'like', '%student%')->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getTeacher()
    {
        try {
            return User::where('role', 'like', '%teacher%')->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getAdmin()
    {
        try {
            return User::where('role', 'like', '%admin%')->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_countStudent()
    {
        try {
            return User::where('role', 'like', '%student%')->count();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_countTeacher()
    {
        try {
            return User::where('role', 'like', '%teacher%')->count();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_countAdmin()
    {
        try {
            return User::where('role', 'like', '%admin%')->count();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_create(Request $request)
    {
        try {
            $userData = [];
            foreach ($request->all() as $data) {
                $user = null;
                //STUDENT
                if (array_search('student', $data['role']) !== false) {
                    //ASSIGN EMAIL AND PASSWORD
                    $data['email'] = 'st' . $data['std_id'] . '@kjn.ac.th';
                    $data['password'] = 'kjn' . $data['std_id'];

                    //VALIDATE
                    $validated = Validator::make($data, [
                        'std_id' => ['required'],
                        'name' => ['required', 'string', 'max:255'],
                        'class' => ['required'],
                        'room' => ['required'],
                        'birth_day' => ['required'],
                        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                        'password' => $this->passwordRules(),
                    ]);

                    $user = User::create([
                        'std_id' => $data['std_id'],
                        'name' => $data['name'],
                        'class' => $data['class'],
                        'room' => $data['room'],
                        'birth_day' => $data['birth_day'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'absent' => [],
                        'role' => $data['role'],
                    ]);

                    //CREATE TIMETABLE
                    $timetable = Timetable::where('class', $data['class'])->where('room', $data['room'])->get();
                    if (count($timetable) === 0) {
                        $timetableData = Timetable::create([
                            'class' => $data['class'],
                            'room' => $data['room'],
                            'created_by' => Auth::user()->name,
                            'updated_by' => Auth::user()->name,
                            'data' => json_decode('{}'),

                        ]);
                    }
                }
                //TEACHER
                else if (array_search('teacher', $data['role']) !== false) {
                    //ASSIGN EMAIL AND PASSWORD
                    $data['email'] = join('.', explode(' ', $data['eng_name'])) . '@kjn.ac.th';
                    $data['password'] = 'kjn' . strtolower(explode(' ', $data['eng_name'])[0]);

                    //VALIDATE
                    $validated = Validator::make($data, [
                        'name' => ['required', 'string', 'max:255'],
                        'eng_name' => ['required', 'string', 'max:255'],
                        'class' => ['required'],
                        'data' => ['required'],
                        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                        'password' => $this->passwordRules(),
                    ]);

                    $user = User::create([
                        'name' => $data['name'],
                        'eng_name' => $data['eng_name'],
                        'class' => $data['class'],
                        'data' => $data['data'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'role' => $data['role'],
                    ]);
                }
                //ADMIN
                else if (array_search('admin', $data['role']) !== false) {
                    //ASSIGN EMAIL AND PASSWORD
                    $data['email'] = join('.', explode(' ', $data['eng_name'])) . '@kjn.ac.th';
                    $data['password'] = 'kjn' . strtolower(explode(' ', $data['eng_name'])[0]);

                    //VALIDATE
                    $validated = Validator::make($data, [
                        'name' => ['required', 'string', 'max:255'],
                        'eng_name' => ['required', 'string', 'max:255'],
                        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                        'password' => $this->passwordRules(),
                    ]);

                    $user = User::create([
                        'name' => $data['name'],
                        'eng_name' => $data['eng_name'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'role' => $data['role'],
                    ]);
                }

                array_push($userData, $user);
            }
            return $userData;
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_update(Request $request, $id)
    {
        try {
            if (array_search('student', $request['role']) !== false) {
                $validated = $request->validate([
                    'id' => ['required', 'unique:users'],
                    'std_id' => ['required'],
                    'name' => ['required', 'string', 'max:255'],
                    'class' => ['required'],
                    'room' => ['required'],
                    'birth_day' => ['required'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
            } else if (array_search('teacher', $request['role']) !== false) {
                $validated = $request->validate([
                    'id' => ['required', 'unique:users'],
                    'name' => ['required', 'string', 'max:255'],
                    'eng_name' => ['required', 'string', 'max:255'],
                    'data' => ['required'],
                    'class' => ['required'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
            } else if (array_search('admin', $request['role']) !== false) {
                $validated = $request->validate([
                    'id' => ['required', 'unique:users'],
                    'name' => ['required', 'string', 'max:255'],
                    'eng_name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
            }
            $user = User::where('id', $id)->first()->update($request->all());
            return $user;
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_delete($id)
    {
        try {
            $user = User::destroy($id);
            return $user;
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
