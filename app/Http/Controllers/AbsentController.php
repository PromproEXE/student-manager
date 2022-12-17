<?php

namespace App\Http\Controllers;

use App\Models\Absent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AbsentController extends Controller
{
    public function api_getall()
    {
        try {
            return Absent::all();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getFromId($id)
    {
        try {
            return Absent::where('id', $id)->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getFromUserId($user_id)
    {
        try {
            return Absent::where('user_id', $user_id)->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getFromClass($class, $room)
    {
        try {
            return Absent::where('user_class', $class)
                ->where('user_room', $room)
                ->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_update(Request $request, $id)
    {
        try {
            //GET DATA
            $absent = Absent::where('id', $id)->first();

            //CHECK IS NOT APPROVE BEFORE THIS QUERY
            if (!is_null($request['approve']) && $absent->approve == null) {
                $request['approve_at'] = Carbon::now()->toDateTimeString();
                $request['approve_by'] = Auth::user()->name;
            }

            //UPDATE DATA
            return $absent->update($request->all());
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_create(Request $request)
    {
        try {
            //VALIDATE ABSENT DATA
            Validator::make($request->all(), [
                'type' => ['required'],
                'from' => ['required'],
                'to' => ['required'],
                'details' => ['required']
            ]);

            //INSERT ABSENT DATA
            $absentData = Absent::create([
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'user_class' => Auth::user()->class,
                'user_room' => Auth::user()->room,
                'type' => $request['type'],
                'from' => $request['from'],
                'to' => $request['to'],
                'details' => $request['details'],
                'created_by' => Auth::user()->name
            ]);

            //INSERT TO USER
            $user = User::where('id', Auth::user()->id)->first();

            //PROCESS DATA
            $userAbsent = $user->absent;
            array_push($userAbsent, $absentData->id);

            //SAVE DATA
            $user->absent = $userAbsent;
            return $user->save();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
