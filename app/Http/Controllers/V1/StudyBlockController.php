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

function getWeekdaysUntilDate($endDate, $arrayWithWeekdayAndScheduleID, $goal_id, $content_to_study)
{
    // Inicializa a data atual
    $currentDate = new DateTime('now');
    // Converte a data de término para o formato DateTime
    $endDateTime = new DateTime($endDate);
    // Array para armazenar os dias de estudo com seus respectivos cronogramas
    $studyDaysWithSchedules = array();

    // Itera enquanto a data atual não ultrapassa a data de término
    while ($currentDate <= $endDateTime) {
        // Obtém o dia da semana (1 para segunda-feira, 7 para domingo)
        $dayOfWeek = $currentDate->format('N');
        
        // Itera sobre o array com dias da semana e IDs de cronogramas
        foreach ($arrayWithWeekdayAndScheduleID as $weekdayAndScheduleID) {
            // Adiciona um novo elemento ao array de dias de estudo, se o dia da semana da data atual corresponder ao dia da semana no array
            if ($weekdayAndScheduleID['weekday'] == $dayOfWeek) {
                $studyDaysWithSchedules[] = [
                    "user_id" => 1,
                    "goal_id" => $goal_id,
                    "schedule_id" => $weekdayAndScheduleID['schedule_id'],
                    "date" => $currentDate->format('Y-m-d'),
                    "content" => "There are many variations of passages of Lorem Ipsum available",
                    "completed" => 0,
                ];
            }
        }

        $currentDate->modify('+1 day');
    }

    // Obtém o número de elementos no array de dias de estudo
    $count = count($studyDaysWithSchedules);
    
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->timeout(120)->post(
        'http://localhost:3001',
        [
            'mensagem' => "Me retorne $count assuntos para estudar para a prova $content_to_study em formato array, preciso do formato [\"Assunto: Subconteudo\", \"Assunto: Subconteudo\"], lembrando que preciso dos $count resultados"
        ],
    );
    

    $data = $response->json();

    for ($i = 0; $i < count($studyDaysWithSchedules); $i++) {
        $studyDaysWithSchedules[$i]['content'] = $data[$i];
    }

    // Retorna o array final de dias de estudo
    return $studyDaysWithSchedules;
}

class StudyBlockController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtro para retornar apenas os blocos de estudos relacionados com a meta em questão
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
        
        // dados da meta em questão
        $goal = Goal::where('id', '=', $request->all()['goal_id'])->first();
        
        // horários cadastrados para essa meta
        $schedules = Schedule::where('goal_id', '=', $request->all()['goal_id'])->get();
        
        $arrayWithWeekdayAndScheduleID = [];

        // capturando dados da tabela de horários
        foreach ($schedules as $schedule) {
            $arrayWithWeekdayAndScheduleID[] = [
                'weekday' => $schedule['weekday'],
                'schedule_id' => $schedule['id']
            ];
        }

        // Obter os dias da semana até a data da prova e montar os objetos prontos para cadastrar
        $arrayDate = getWeekdaysUntilDate($goal['test_date'], $arrayWithWeekdayAndScheduleID, $request->all()['goal_id'], $goal['content_to_study']);
        
        $created = StudyBlock::insert($arrayDate);
        if ($created) {
            return $this->success('Registred StudyBlock', 200);
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
