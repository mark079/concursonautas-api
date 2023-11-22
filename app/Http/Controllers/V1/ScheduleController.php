<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ScheduleResource;
use App\Models\Schedule;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (array_key_exists('goal', $request->query())) {
            return response()->json(ScheduleResource::collection(Schedule::where([['goal_id', '=', $request->query()['goal']]])->orderBy('date', 'asc')->get()));
        }
        return response()->json(ScheduleResource::collection(Schedule::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            '*.user_id' => 'required|exists:users,id',
            '*.goal_id' => 'required|exists:goals,id',
            '*.weekday' => 'required|integer|min:1|max:7',
            '*.start_time' => 'required|date_format:H:i',
            // 'end_time' => 'required|date_format:H:i|after:start_time'
        ]);
        
        if ($validator->fails()) {
            return $this->error('Invalid Data', 422, $validator->errors());
        }
        
        $created = Schedule::insert($request->all());
        if ($created) {
            return $this->success("Registred Data", 200);
        }

        return $this->error('Something went wrong', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return response()->json(new ScheduleResource($schedule));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'goal_id' => 'required|exists:goals,id',
            'weekday' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            // 'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        if ($validator->fails()) {
            return $this->error('Invalid Data', 400, $validator->errors());
        }

        $created = $schedule->update($validator->validated());

        if ($created) {
            return $this->success('Registred Data', 200, $schedule);
        }

        return $this->error('Something went wrong', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::find($id);
        if ($schedule) {
            $deleted = $schedule->delete();
            if ($deleted) {
                return $this->success('Schedule Deleted', 200);
            }
            return $this->error('Something went wrong', 400);
        }
        return $this->error('Schedule Not Found', 404);
    }
}
