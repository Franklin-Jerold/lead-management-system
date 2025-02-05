<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessLeadImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LeadImportController extends Controller
{
    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);


        $file = $request->file('file');


        $path = $file->storeAs('uploads/leads', $file->getClientOriginalName(), 'public');

        Log::info('File uploaded to: ' . $path);


        ProcessLeadImport::dispatch($path);

        return response()->json(['message' => 'File uploaded successfully. Processing in the background.'], 202);
    }
}
