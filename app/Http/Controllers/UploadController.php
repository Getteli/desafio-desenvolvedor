<?php

namespace App\Http\Controllers;

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
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);
    
        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $name);
    
        $upload = new Upload();
        $upload->name = $name;
        $upload->path = $path;
        $upload->save();
    
        return response()->json(['message' => 'Arquivo enviado com sucesso', 'file' => $upload]);
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
