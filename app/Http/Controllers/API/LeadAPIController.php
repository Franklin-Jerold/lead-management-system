<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Auth;


class LeadAPIController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'budget' => 'required|numeric',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'status' => 'required|in:new,contacted,scheduled,closed',
            'source' => 'required|in:website,referral,social media',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $lead = Lead::firstOrCreate(
            [
                'property_type' => $request->property_type,
                'location' => $request->location,
                'budget' => $request->budget,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'status' => $request->status,
                'source' => $request->source,
            ],
            [
                'created_by' => Auth::id(),
            ]
        );

        return response()->json([
            'message' => $lead->wasRecentlyCreated
                ? 'Lead created successfully'
                : 'Lead already exists'
        ], $lead->wasRecentlyCreated ? 201 : 200);
    }



    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'property_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'budget' => 'required|numeric',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'status' => 'required|in:new,contacted,scheduled,closed',
            'source' => 'required|in:website,referral,social media',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        // Check if another lead with the same data already exists (excluding current record)
        $existingLead = Lead::where([
            ['property_type', $request->property_type],
            ['location', $request->location],
            ['budget', $request->budget],
            ['bedrooms', $request->bedrooms],
            ['bathrooms', $request->bathrooms],
            ['status', $request->status],
            ['source', $request->source],
        ])->where('id', '!=', $id)->exists();

        if ($existingLead) {
            return response()->json(['error' => 'A lead with the same details already exists'], 409);
        }

        $lead->update([
            'property_type' => $request->property_type,
            'location' => $request->location,
            'budget' => $request->budget,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'status' => $request->status,
            'source' => $request->source,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Lead updated successfully'], 200);
    }



    public function index(Request $request)
    {
        $pageSize = $request->get('per_page', 10);
        $pageNumber = $request->get('page', 1);
        $offset = ($pageNumber - 1) * $pageSize;

        $query = Lead::query();
        $totalLeads = $query->count(); // Count before pagination

        $leads = $query->skip($offset)->take($pageSize)->get()->map(function ($lead) {
            return [
                'property_type' => $lead->property_type,
                'location' => $lead->location,
                'budget' => $lead->budget,
                'bedrooms' => $lead->bedrooms,
                'bathrooms' => $lead->bathrooms,
                'status' => $lead->status,
                'source' => $lead->source,
                'created_by' => $lead->created_by,
            ];
        });

        return response()->json([
            'data' => $leads,
            'total' => $totalLeads,
            'per_page' => $pageSize,
            'current_page' => $pageNumber,
        ], 200);
    }



    public function show($id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            //return response()->json(['error' => 'Lead not found'], 404);
        }

        return response()->json(['data' => $lead], 200);
    }


    public function destroy($id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            //return response()->json(['error' => 'Lead not found'], 404);
        }

        $lead->deleted_by = Auth::id();
        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully'], 200);
    }
}
