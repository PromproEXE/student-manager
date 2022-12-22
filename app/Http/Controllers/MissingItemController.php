<?php

namespace App\Http\Controllers;

use App\Models\MissingItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MissingItemController extends Controller
{
    public function api_getMissingItem()
    {
        try {
            return MissingItem::all();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_getMissingItemFromId($id)
    {
        try {
            return MissingItem::where('id', $id)->first();
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_create(Request $request)
    {
        try {
            $validate = $request->validate([
                'title' => ['required', 'string'],
                'details' => ['required', 'string'],
                'img' => ['array'],
            ]);

            return MissingItem::create([
                'title' => $request['title'],
                'details' => $request['details'],
                'img' => $request['img'],
                'created_by' => Auth::user()->name,
                'updated_by' => Auth::user()->name
            ]);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_update(Request $request, $id)
    {
        try {
            $validate = $request->validate([
                'title' => ['required', 'string'],
                'details' => ['required', 'string'],
                'img' => ['required', 'array'],
                'found' => ['required', 'boolean'],
                'created_by' => ['required', 'string']
            ]);

            $request['updated_by'] = Auth::user()->name;

            $data = MissingItem::find($id);
            return $data->update($request->all());
        } catch (Exception $err) {
            return response($err, 403);
        }
    }

    public function api_delete($id)
    {
        try {
            $data = MissingItem::where('id', $id)->first();
            if (count($data['img']) > 0) {
                foreach ($data['img'] as $path) {
                    Storage::disk('public_upload')->delete($path);
                }
            }
            return MissingItem::destroy($id);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
