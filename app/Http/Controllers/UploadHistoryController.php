<?php

namespace App\Http\Controllers;

use App\Models\UploadHistory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadHistoryController extends Controller
{

    /**
     * Listar os dados
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $query = UploadHistory::query();

        if ($request->has('name'))
        {
            $query->where('file_name', "LIKE", "%".$request->name."%");
        }

        if ($request->has('date'))
        {
            $query->whereDate('uploaded_at', $request->date);
        }

        $uploads = $query->paginate($request->get('per_page') ?? 10);

        return response()->json($uploads);
    }
}
