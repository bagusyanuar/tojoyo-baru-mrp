<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Support\Collection;

class MRPController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMRPResult($id)
    {
        try {
            $product = Product::with(['product_material'])
                ->where('id', '=', $id)
                ->first();
            if (!$product) {
                return $this->jsonNotFoundResponse('product not found');
            }

           $maxProduction = $this->getMaxProduction($product);
            return $this->jsonSuccessResponse('success', $maxProduction);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    private function getMaxProduction($product)
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
        return min($suggestion);
    }
}
