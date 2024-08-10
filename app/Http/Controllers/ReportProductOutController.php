<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\ProductOut;
use Carbon\Carbon;

class ReportProductOutController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        try {
            $start = $this->field('start');
            $end = $this->field('end');
            $data = ProductOut::with(['product'])
                ->whereBetween('date', [$start, $end])
                ->orderBy('created_at', 'DESC')
                ->get();
            return $this->jsonSuccessResponse('success', $data);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
