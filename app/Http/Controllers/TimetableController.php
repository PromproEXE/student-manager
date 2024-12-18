<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    public function api_getAll()
    {
        try {
            return Timetable::all();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getTimetableFromClass($class, $room)
    {
        try {
            return Timetable::where('class', $class)->where('room', $room)->get();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_todayClassCount()
    {
        try {
            $timetable = Timetable::where('class', Auth::user()->class)->where('room', Auth::user()->room);
            $dayOfWeek = date('w');

            if ($dayOfWeek > 0 && $dayOfWeek < 5) {
                return count($timetable->filter(fn ($item) => $item['data']['2022']['2'][$dayOfWeek]['type'] == 'study')) + 1;
            } else {
                return 0;
            }
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_create(Request $request)
    {
        try {
            $validated = $request->validate([
                'class' => ['required'],
                'room' => ['required'],
            ]);

            if (Timetable::where('class', $request['class'])->where('room', $request['room'])->count() === 0) {
                return Timetable::create([
                    'class' => $request['class'],
                    'room' => $request['room'],
                    'created_by' => Auth::user()->name,
                    'updated_by' => Auth::user()->name,
                    'data' => json_decode('{}'),
                ]);
            } else {
                throw new Exception('DUPLICATE ENTRY');
            }
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'class' => ['required'],
                'room' => ['required']
            ]);

            return Timetable::find($id)->update($request->all());
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_delete($id)
    {
        try {
            return Timetable::destroy($id);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
