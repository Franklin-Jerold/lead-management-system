<?php

namespace App\Jobs;

use App\Models\Lead;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProcessLeadImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;


    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }


    public function handle(): void
    {
        try {

            $rows = Excel::toArray([], storage_path('app/public/' . $this->filePath))[0];

            Log::info('Processing file: ' . $this->filePath);

            foreach ($rows as $row) {

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

            Log::info('Lead import completed successfully.');
        } catch (\Exception $e) {
            Log::error('Error processing import: ' . $e->getMessage());
        }
    }
}
