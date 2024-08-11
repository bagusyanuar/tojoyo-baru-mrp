<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MRPController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMRPResult()
    {

        try {
            $id = $this->field('product_id');
            if ($this->request->method() === 'POST') {
                $id = $this->postField('product_id');
            }
            $product = Product::with(['product_material.material'])
                ->where('id', '=', $id)
                ->first();
            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

            if ($this->request->method() === 'POST') {
                return $this->production($product);
            }
            $mrpData = $this->getMRPData($product);
            return $this->jsonSuccessResponse('success', $mrpData);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    private function getMRPData($product)
    {
        /** @var Collection $billsOfMaterial */
        $billsOfMaterial = $product->product_material;

        $billsID = $billsOfMaterial->pluck('material_id');
        $objBOM = [];
        foreach ($billsOfMaterial as $bom) {
            $objBOM['m_' . $bom->material_id] = $bom->qty;
        }

        $materialInventory = Material::with([])
            ->whereIn('id', $billsID)
            ->get();

        $objInventory = [];
        foreach ($materialInventory as $mi) {
            $objInventory['m_' . $mi->id] = $mi->qty;
        }

        $suggestion = [];
        foreach ($objBOM as $key => $ob) {
            $maxDiv = round(($objInventory[$key] / $ob), 0);
            array_push($suggestion, $maxDiv);
        }
        return [
            'max_result' => min($suggestion),
            'material_inventory' => $materialInventory,
            'bills_of_material' => $billsOfMaterial,
            'product' => $product
        ];
    }

    /**
     * @param $product
     * @return \Illuminate\Http\JsonResponse
     */
    private function production($product)
    {
        DB::beginTransaction();
        try {
            $id = $this->postField('product_id');
            $production = $this->postField('production');

            /** @var Collection $billsOfMaterial */
            $billsOfMaterial = $product->product_material;
            $billsID = $billsOfMaterial->pluck('material_id');

            $materialInventory = Material::with([])
                ->whereIn('id', $billsID)
                ->get();

            foreach ($billsOfMaterial as $bom) {
                $qtyOut = $bom->qty;
                $currentMaterial = $materialInventory->where('id','=', $bom->material_id)->first();
                $currentQty = 0;
                if ($currentMaterial) {
                    $currentQty = $currentMaterial->qty;
                }

                if ($currentQty > 0) {
                    $newQty = $currentQty - ($qtyOut * $production);
                    $currentMaterial->update([
                        'qty' => $newQty
                    ]);
                }
            }

            $currentQtyProduct = $product->qty;
            $newQty = $currentQtyProduct + $production;
            $product->update([
                'qty' => $newQty
            ]);
            DB::commit();
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
