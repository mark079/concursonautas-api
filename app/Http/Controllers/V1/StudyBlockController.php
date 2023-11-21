<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StudyBlockResource;
use App\Models\StudyBlock;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudyBlockController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (array_key_exists('goal', $request->query())) {
            return response()->json(StudyBlockResource::collection(StudyBlock::where([['goal_id', '=', $request->query()['goal']]])->get()));
        }
        return response()->json(StudyBlockResource::collection(StudyBlock::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'goal_id' => 'required|exists:goals,id',
            'schedule_id' => 'required|exists:schedules,id',
            'content' => 'required|min:10',
            'date' => 'required|date_format:Y-m-d',
            'completed' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = StudyBlock::create($validator->validated());
        if ($created) {
            return $this->success('Registred StudyBlock', 200, $created);
        }
        return $this->error('Something went wrong', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyBlock $studyBlock)
    {
        return response()->json(new StudyBlockResource($studyBlock));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudyBlock $studyBlock)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'goal_id' => 'required|exists:goals,id',
            'schedule_id' => 'required|exists:schedules,id',
            'content' => 'required|min:10',
            'date' => 'required|date_format:Y-m-d',
            'completed' => 'required|in:0,1'
        ]);
        
        if($validator->fails()) {
            return $this->error('Invalid Data', 422, $validator->errors());
        }

        $created = $studyBlock->update($validator->validated());

        if($created) {
            return $this->success('StudyBlock Updated', 200, $studyBlock);
        }
        
        return $this->error('Something went wrong', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $studyBlock = StudyBlock::find($id);
        if ($studyBlock) {
            $deleted = $studyBlock->delete();
            if ($deleted) {
                return $this->success('StudyBlock Deleted', 200);
            }
            return $this->error('Something went wrong', 400);
        }

        return $this->error('StudyBlock Not Found', 404);
    }
}
