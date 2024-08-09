<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Material;
use App\Models\MaterialIn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MaterialInController extends CustomController
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
            $now = Carbon::now()->format('Y-m-d');
            $data = MaterialIn::with(['material'])
                ->where('date', '=', $now)
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $materialIn = MaterialIn::with(['material'])
                ->where('id', '=', $id)
                ->first();
            if(!$materialIn) {
                return $this->jsonNotFoundResponse('material in not found');
            }

            $recordedQty = $materialIn->qty;

            /** @var Model $material */
            $material = $materialIn->material;
            $materialQty = $material->qty;

            $newQty = $materialQty - $recordedQty;
            $material->update([
                'qty' => $newQty
            ]);

            MaterialIn::destroy($id);
            DB::commit();
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }

    private function store()
    {
        try {
            DB::beginTransaction();
            $userID = auth()->id();
            $date = $this->postField('date');
            $materialID = $this->postField('material_id');
            $qty = (int)$this->postField('qty');

            $material = Material::with([])
                ->where('id', '=', $materialID)
                ->first();

            if (!$material) {
                return $this->jsonNotFoundResponse('material not found');
            }

            $currentQtyMaterial = $material->qty;
            $newQtyMaterial = $currentQtyMaterial + $qty;

            $material->update([
                'qty' => $newQtyMaterial
            ]);

            $data_request = [
                'user_id' => $userID,
                'date' => $date,
                'material_id' => $materialID,
                'qty' => $qty
            ];
            MaterialIn::create($data_request);
            DB::commit();
            return $this->jsonSuccessResponse('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
