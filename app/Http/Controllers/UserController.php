<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                    Validator::make($data, [
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
                        'role' => $data['role'],
                    ]);
                }
                //TEACHER
                else if (array_search('teacher', $data['role']) !== false) {
                    //ASSIGN EMAIL AND PASSWORD
                    $data['email'] = join('.', explode(' ', $data['eng_name'])) . '@kjn.ac.th';
                    $data['password'] = 'kjn' . strtolower(explode(' ', $data['eng_name'])[0]);

                    //VALIDATE
                    Validator::make($data, [
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
                    Validator::make($data, [
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
                Validator::make($request->all(), [
                    'id' => ['required', 'unique:users'],
                    'std_id' => ['required'],
                    'name' => ['required', 'string', 'max:255'],
                    'class' => ['required'],
                    'room' => ['required'],
                    'birth_day' => ['required'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
            } else if (array_search('teacher', $request['role']) !== false) {
                Validator::make($request->all(), [
                    'id' => ['required', 'unique:users'],
                    'name' => ['required', 'string', 'max:255'],
                    'eng_name' => ['required', 'string', 'max:255'],
                    'data' => ['required'],
                    'class' => ['required'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
            } else if (array_search('admin', $request['role']) !== false) {
                Validator::make($request->all(), [
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

    // public function api_delete(Request $request)
    // {
    //     try {
    //         $deleteData = [];
    //         dd($request->all());
    //         foreach ($request->all() as $data) {
    //             $deleteUser = User::destroy($data['id']);
    //             array_push($deleteData, $deleteUser);
    //         }
    //         return $deleteData;
    //     } catch (Exception $err) {
    //         return response($err, 403);
    //     }
    // }

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
