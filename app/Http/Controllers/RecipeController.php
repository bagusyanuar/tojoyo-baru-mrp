<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Product;
use App\Models\ProductMaterial;

class RecipeController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $data = Product::with([])
                ->get()->append(['count_recipe']);
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        if ($this->request->method() === 'POST') {
            return $this->create_recipe($id);
        }
        try {
            $data = Product::with(['product_material.product', 'product_material.material'])
                ->first();
            if (!$data) {
                return $this->jsonNotFoundResponse('data not found');
            }
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            ProductMaterial::destroy($id);
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function create_recipe($id)
    {
        try {
            $materialID = $this->postField('material_id');
            $qty = $this->postField('qty');

            $data_request = [
                'product_id' => $id,
                'material_id' => $materialID,
                'qty' => $qty
            ];
            ProductMaterial::create($data_request);
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
