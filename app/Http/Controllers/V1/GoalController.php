<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GoalResource;
use App\Models\Goal;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoalController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(GoalResource::collection(Goal::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:C,V',
            'test_date' => ['required', 'date', 'date_format:Y-m-d', function ($attribute, $value, $fail) {
                $dataRequisitada = Carbon::parse($value);

                if ($dataRequisitada->lte(Carbon::now()->addWeek())) {
                    $fail("Data deve ser no mínimo uma semana futura");
                }
            }],
            'content_to_study' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error('Invalid Data', 422, $validator->errors());
        }

        $created = Goal::create($validator->validated());

        if ($created) {
            return $this->success('Registered Data', 200, new GoalResource($created));
        }
        return $this->error('Something went wrong', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Goal $goal)
    {
        return response()->json(new GoalResource($goal));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:C,V',
            'test_date' => 'required|date|date_format:Y-m-d',
            'content_to_study' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error('Invalid Data', 422, $validator->errors());
        }

        $updated = $goal->update($validator->validated());
        if ($updated) {
            return $this->success('Goal Updated', 200, new GoalResource($goal));
        }

        return $this->error('Something went wrong', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $goal = Goal::find($id);
        if ($goal) {
            $deleted = $goal->delete();
            if ($deleted) {
                return $this->success('Goal Deleted', 200);
            }
            return $this->error('Something went wrong', 400);
        }
        return $this->error('Goal Not Found', 404);
    }
}
