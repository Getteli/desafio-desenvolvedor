<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\UploadHistory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadController extends Controller
{

    /**
     * Listar os dados
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $query = Upload::query();

        if ($request->has('TckrSymb'))
        {
            $query->where('TckrSymb', $request->TckrSymb);
        }

        if ($request->has('RptDt'))
        {
            $query->where('RptDt', $request->RptDt);
        }

        $uploads = $query->paginate($request->get('per_page') ?? 10);

        return response()->json(['uploads' => $uploads]);
    }

    /**
     * Armazenar o dados
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $allowedFileTypes = ['csv', 'xlsx', 'xls'];
        $file = $request->file('file');

        if (!$file || !in_array($file->getClientOriginalExtension(), $allowedFileTypes))
        {
            return response()->json(['error' => 'Este formato é invalido, tente outro por favor.'], 422);
        }

        $filePath = $file->store('uploads', 'public');
        $path = "storage/" . $filePath;
        $name = $file->getClientOriginalName();

        // Verificar se o arquivo já foi enviado
        $duplicate = UploadHistory::where('file_name', $name)->first();
        if ($duplicate) 
        {
            return response()->json(['error' => 'Já existe um arquivo com esse nome'], 409);
        }

        // Salvar histórico do upload
        UploadHistory::create([
            'file_path' => $path,
            'file_name' => $name,
            'uploaded_at' => now(),
            'uploaded_by' => auth()->user()->name
        ]);

        // Abrir o arquivo e processar as linhas
        $file = IOFactory::load($path);
        $worksheet = $file->getActiveSheet();
        $rows = $worksheet->toArray();

        foreach ($rows as $key => $row)
        {
            // pula as 2 primeiras linhas
            if ($key < 2) continue;

            Upload::create([
                'RptDt' => $row[0],
                'TckrSymb' => $row[1],
                'MktNm' => $row[5],
                'SctyCtgyNm' => $row[6],
                'ISIN' => $row[15],
                'CrpnNm' => $row[47]
            ]);
        }

        return response()->json(['message' => 'Upload de arquivo realizado com sucesso', 'file_path' => $path], 200);
    }

    /**
     * Retornar uma linha dos dados
     *
     * @param [type] $id
     * @return void
     */
    public function show($id)
    {
        $upload = Upload::findOrFail($id);
        return response()->json($upload);
    }
    
}
