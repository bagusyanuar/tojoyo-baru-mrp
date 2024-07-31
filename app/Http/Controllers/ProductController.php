<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Product;
use App\Models\ProductMaterial;
use Illuminate\Database\Eloquent\Model;

class ProductController extends CustomController
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
            $products = Product::with([])
                ->get();
            return $this->jsonSuccessResponse('success', $products);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function findByID($id)
    {
        try {
            $product = Product::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->patch($product);
            }
            return $this->jsonSuccessResponse('success', $product);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Product::destroy($id);
            return $this->jsonSuccessResponse('success');
        }catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function product_material($id)
    {
        try {
            $product = Product::with(['product_material'])
                ->where('id', '=', $id)
                ->first();
            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->add_material($id);
            }
            return $this->jsonSuccessResponse('success', $product);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function store()
    {
        try {
            $name = $this->postField('name');
            $qty = 0;
            $data_request = [
                'name' => $name,
                'qty' => $qty
            ];
            Product::create($data_request);
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
            $data_request = [
                'name' => $name,
            ];
            $data->update($data_request);
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function add_material($id)
    {
        try {
            $productID = $id;
            $materialID = $this->postField('material_id');
            $qty = $this->postField('qty');

            $data_request = [
                'product_id' => $productID,
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
