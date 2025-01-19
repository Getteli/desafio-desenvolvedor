<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    /**
     * Listar documentos
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->has('name'))
        {
            $upload = Upload::where('name', $request->name)->firstOrFail();
            return response()->json(['upload' => $upload]);
        }
        elseif ($request->has('date'))
        {
            $upload = Upload::whereDate('created_at', $request->date)->get();
            return response()->json(['uploads' => $upload]);
        }
        else
        {
            return response()->json(['uploads' => Upload::paginate(10)]);
        }
    }

    /**
     * Armazenar o documento
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

        $filePath = $file->store('uploads');

        // Verificar se o arquivo já foi enviado
        $duplicate = UploadHistory::where('file_path', $filePath)->first();
        if ($duplicate) 
        {
            return response()->json(['error' => 'Já existe um arquivo com esse nome'], 409);
        }

        // Salvar histórico do upload
        UploadHistory::create([
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'uploaded_at' => now(),
            'uploaded_by' => auth()->user()->id
        ]);

        return response()->json(['message' => 'Upload de arquivo realizado com sucesso', 'file_path' => $filePath], 200);
    }

    /**
     * Retornar um upload
     *
     * @param [type] $id
     * @return void
     */
    public function show($id)
    {
        $upload = Upload::findOrFail($id);
        return response()->file(storage_path('app/' . $upload->path));
    }
    
}
