<?php

namespace App\Api\Controllers;

use App\Api\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    private $position;

    public function __construct(Position $position)
    {
        $this->position = $position;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPosition(Request $request)
    {
        $this->validate($request, [
            'name'       => 'required|string',
            'department' => 'nullable|numeric'
        ]);

        $this->position->add($request->get('name'), $request->get('department'));

        return response()->json(['message' => Lang::get('position.added')]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renamePosition($id, Request $request)
    {
        Validator::make(
            ['id' => $id, 'name' => $request->get('name')],
            ['id' => 'required|numeric', 'name' => 'required|string']
        )->validate();

        $this->position->rename($id, $request->get('name'));

        return response()->json(['message' => Lang::get('position.renamed')]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePosition($id)
    {
        Validator::make(['id' => $id], ['id' => 'required|numeric'])->validate();

        $this->position->remove($id);

        return response()->json(['message' => Lang::get('position.deleted')]);
    }
}
