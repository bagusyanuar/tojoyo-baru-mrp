<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Material;
use App\Models\MaterialIn;
use App\Models\Product;
use App\Models\ProductOut;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductOutController extends CustomController
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
            $date = $this->field('date');
            $now = Carbon::now()->format('Y-m-d');
            $data = ProductOut::with(['product'])
                ->where('date', '=', $date)
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function store()
    {
        try {
            DB::beginTransaction();
            $userID = auth()->id();
            $date = $this->postField('date');
            $productID = $this->postField('product_id');
            $qty = (int)$this->postField('qty');

            $product = Product::with([])
                ->where('id', '=', $productID)
                ->first();

            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

            $currentQtyProduct = $product->qty;
            $newQtyMaterial = $currentQtyProduct - $qty;

            if ($newQtyMaterial < 0) {
                return $this->jsonErrorResponse('out of stock');
            }

            $product->update([
                'qty' => $newQtyMaterial
            ]);

            $data_request = [
                'user_id' => $userID,
                'date' => $date,
                'product_id' => $productID,
                'qty' => $qty
            ];
            ProductOut::create($data_request);
            DB::commit();
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
