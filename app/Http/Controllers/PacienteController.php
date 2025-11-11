<?php

namespace App\Http\Controllers;

use App\Http\Requests\PacienteStoreRequest;
use App\Http\Requests\PacienteUpdateRequest;
use App\Models\Paciente;
use App\Services\PacienteService;
use Illuminate\Http\Request;

/**
 * CONTROLLER: PacienteController
 * 
 * CRUD completo de Pacientes.
 * Padrão: Controller enxuto (máximo 15-20 linhas por método).
 * Lógica de negócio delegada ao PacienteService.
 */
class PacienteController extends Controller
{
    public function __construct(
        private PacienteService $pacienteService
    ) {}

    /**
     * Lista todos os pacientes separados em novos e com histórico.
     */
    public function index(Request $request)
    {
        $busca = $request->get('busca');

        // Query base
        $query = Paciente::query();

        // Aplicar busca se existir
        if ($busca) {
            $query->where('nome_completo', 'like', "%$busca%")
                  ->orWhere('cpf', 'like', "%$busca%");
        }

        // Separar pacientes novos (sem prontuários)
        $pacientesNovos = (clone $query)
            ->doesntHave('prontuarios')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_novos');

        // Separar pacientes com histórico (com prontuários)
        $pacientesComHistorico = (clone $query)
            ->has('prontuarios')
            ->withCount('prontuarios')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_clientes');

        return view('pacientes.index', compact('pacientesNovos', 'pacientesComHistorico', 'busca'));
    }

    /**
     * Exibe formulário de criação.
     */
    public function create()
    {
        return view('pacientes.create');
    }

    /**
     * Armazena um novo paciente.
     */
    public function store(PacienteStoreRequest $request)
    {
        $paciente = $this->pacienteService->criarPaciente($request->validated());

        return redirect()
            ->route('pacientes.show', $paciente->id)
            ->with('success', 'Paciente cadastrado com sucesso!');
    }

    /**
     * Exibe detalhes de um paciente.
     */
    public function show(int $id)
    {
        $paciente = Paciente::with([
            'prontuarios.profissional.usuario',
            'prontuarios.agendamento',
            'prontuarios.examesSolicitados.tipoExame',
            'prontuarios.examesSolicitados.resultado'
        ])->findOrFail($id);

        // Buscar todos os exames do paciente (através dos prontuários)
        $exames = \App\Models\ExameSolicitado::whereHas('prontuario', function($query) use ($id) {
            $query->where('paciente_id', $id);
        })
        ->with(['tipoExame', 'resultado', 'profissionalSolicitante.usuario', 'prontuario'])
        ->orderBy('data_solicitacao', 'desc')
        ->get();

        // Estatísticas de exames
        $estatisticasExames = [
            'total' => $exames->count(),
            'pendentes' => $exames->whereIn('status', ['solicitado', 'em_analise'])->count(),
            'concluidos' => $exames->where('status', 'concluido')->count(),
            'com_resultado' => $exames->filter(fn($e) => $e->resultado !== null)->count(),
        ];

        return view('pacientes.show', compact('paciente', 'exames', 'estatisticasExames'));
    }

    /**
     * Exibe formulário de edição.
     */
    public function edit(int $id)
    {
        $paciente = $this->pacienteService->buscarPacientePorId($id);

        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Atualiza um paciente existente.
     */
    public function update(PacienteUpdateRequest $request, int $id)
    {
        $paciente = Paciente::findOrFail($id);
        $this->pacienteService->atualizarPaciente($paciente, $request->validated());

        return redirect()
            ->route('pacientes.show', $paciente->id)
            ->with('success', 'Paciente atualizado com sucesso!');
    }

    /**
     * Exclui (soft delete) um paciente.
     */
    public function destroy(int $id)
    {
        try {
            $paciente = Paciente::findOrFail($id);
            $this->pacienteService->excluirPaciente($paciente);

            return redirect()
                ->route('pacientes.index')
                ->with('success', 'Paciente excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}