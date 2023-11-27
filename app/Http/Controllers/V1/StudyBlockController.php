<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StudyBlockResource;
use App\Models\Goal;
use App\Models\Schedule;
use App\Models\StudyBlock;
use App\Traits\HttpResponses;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

function getWeekdaysUntilDate($endDate, $weekArray, $goal_id, $content_to_study)
{


    $currentDate = new DateTime('now');
    $endDateTime = new DateTime($endDate);
    $weekdays = array();

    while ($currentDate <= $endDateTime) {
        $dayOfWeek = $currentDate->format('N'); // 1 (Monday) to 7 (Sunday)
        foreach ($weekArray as $obj) {
            if ($obj['weekday'] == $dayOfWeek) {
                $weekdays[] = [
                    "user_id" => 1,
                    "goal_id" => $goal_id,
                    "schedule_id" => $obj['schedule_id'],
                    "date" => $currentDate->format('Y-m-d'),
                    "content" => "There are many variations of passages of Lorem Ipsum available",
                    "completed" => 0,
                ];
            }
        }

        $currentDate->modify('+1 day');
    }

    $count = count($weekdays);
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->timeout(120)->post(
        'http://localhost:3001',
        [
            'mensagem' => "Me retorne $count assuntos para estudar para a prova $content_to_study em formato array"
        ],
    );
    // Obter a resposta
    $data = $response->json();
    for ($i = 0; $i < count($weekdays); $i++) {
        $weekdays[$i]['content'] = $data[$i];
    }
    return $weekdays;
}

class StudyBlockController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (array_key_exists('goal', $request->query())) {
            return response()->json(StudyBlockResource::collection(StudyBlock::where([['goal_id', '=', $request->query()['goal']]])->orderBy('date', 'asc')->get()));
        }
        return response()->json(StudyBlockResource::collection(StudyBlock::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $weekdays = [];
        $schedules = Schedule::where('goal_id', '=', $request->all()['goal_id'])->get();
        $goal = Goal::where('id', '=', $request->all()['goal_id'])->first();
        foreach ($schedules as $elemento) {
            $weekdays[] = [
                'weekday' => $elemento['weekday'],
                'schedule_id' => $elemento['id']
            ];
        }
        $arrayDate = getWeekdaysUntilDate($goal['test_date'], $weekdays, $request->all()['goal_id'], $goal['content_to_study']);
        $created = StudyBlock::insert($arrayDate);
        if ($created) {
            return $this->success('Registred StudyBlock', 200);
        }
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

        if ($validator->fails()) {
            return $this->error('Invalid Data', 422, $validator->errors());
        }

        $created = $studyBlock->update($validator->validated());

        if ($created) {
            return $this->success('StudyBlock Updated', 200, $studyBlock);
        }

        return $this->error('Something went wrong', 400);
    }

    public function updateCompleted(Request $request, $id)
    {
        $completed = $request->all()['completed'];
        // Encontrar o modelo pelo ID
        $studyBlock = StudyBlock::find($id);

        // Verificar se o modelo foi encontrado
        if ($studyBlock) {
            // Atualizar apenas o campo 'nome'
            $studyBlock->update(['completed' => $completed]);
            return $this->success('Registred', 200);
            // return redirect()->route('sua.rota')->with('success', 'Dado atualizado com sucesso!');
        } else {
            return response()->json('error');
        }
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
