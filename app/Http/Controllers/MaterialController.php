<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;

class MaterialController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->request->method() === 'POST') {
            return $this->store();
        }
        try {
            $materials = Material::with([])
                ->get();
            return $this->jsonSuccessResponse('success', $materials);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        try {
            $material = Material::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$material) {
                return $this->jsonNotFoundResponse('material not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->patch($material);
            }
            return $this->jsonSuccessResponse('success', $material);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Material::destroy($id);
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
    private function store()
    {
        try {
            $name = $this->postField('name');
            $unit = $this->postField('unit');
            $qty = 0;
            $data_request = [
                'name' => $name,
                'qty' => $qty,
                'unit' => $unit,
            ];
            Material::create($data_request);
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    /**
     * @param Model $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function patch($data)
    {
        try {
            $name = $this->postField('name');
            $unit = $this->postField('unit');
            $data_request = [
                'name' => $name,
                'unit' => $unit,
            ];
            $data->update($data_request);
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
