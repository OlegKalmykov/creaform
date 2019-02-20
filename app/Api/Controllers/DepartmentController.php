<?php

namespace App\Api\Controllers;

use App\Api\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    private $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStructure()
    {
        $structure = $this->department->getUserCompany();

        if (!$structure) {
            return response()->json(null, 204, []);
        }

        return response()->json(['structure' => $structure]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDepartment(Request $request)
    {
        $this->validate($request, [
            'name'   => 'required|string',
            'parent' => 'nullable|numeric'
        ]);

        $this->department->add($request->get('name'), $request->get('parent'));

        return response()->json(['message' => Lang::get('department.added')]);
    }

    /**
     * @param int|string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renameDepartment($id, Request $request)
    {
        Validator::make(
            ['id' => $id, 'name' => $request->get('name')],
            ['id' => 'required|numeric', 'name' => 'required|string']
        )->validate();

        $this->department->rename($id, $request->get('name'));

        return response()->json(['message' => Lang::get('department.renamed')]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDepartment($id)
    {
        Validator::make(['id' => $id], ['id' => 'required|numeric'])->validate();

        $this->department->remove((int)$id);

        return response()->json(['message' => Lang::get('department.deleted')]);
    }
}
