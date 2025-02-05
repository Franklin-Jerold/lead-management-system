<?php

namespace App\Jobs;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProcessLeadImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }


    public function handle()
    {
        try {
            Log::info('Processing file in background: ' . $this->filePath);


            $rows = Excel::toArray([], storage_path('app/public/' . $this->filePath))[0];


            $chunkSize = 1000;
            $chunks = array_chunk($rows, $chunkSize);

            foreach ($chunks as $chunk) {
                foreach ($chunk as $row) {
                    if (isset($row['property_type'], $row['location'], $row['budget'], $row['bedrooms'], $row['bathrooms'], $row['status'], $row['source'])) {
                        Lead::firstOrCreate(
                            [
                                'property_type' => $row['property_type'],
                                'location' => $row['location'],
                                'budget' => $row['budget'],
                                'bedrooms' => $row['bedrooms'],
                                'bathrooms' => $row['bathrooms'],
                                'status' => $row['status'],
                                'source' => $row['source'],
                            ],
                            [
                                'created_by' => Auth::id(),
                            ]
                        );
                    }
                }
            }

            Log::info('Import completed for file: ' . $this->filePath);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
        }
    }
}
