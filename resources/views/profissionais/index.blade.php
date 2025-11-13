@extends('layouts.app')

@section('title', 'Profissionais de Saúde')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

    {{-- CABEÇALHO --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-blue-800">
                <i class="fas fa-user-md mr-2"></i> Profissionais de Saúde
            </h2>
            <p class="text-blue-600 mt-2">
                Gerencie médicos e profissionais da clínica
            </p>
        </div>

        {{-- BOTÃO ADICIONAR NOVO PROFISSIONAL --}}
        <a href="{{ route('profissionais.create') }}"
           class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
            <i class="fas fa-user-plus mr-2"></i> Novo Profissional
        </a>
    </div>

    {{-- MENSAGENS DE FEEDBACK --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <p class="text-green-800 font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3"></i>
                <p class="text-red-800 font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- CARD PRINCIPAL --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-blue-200">

        {{-- BARRA DE FILTROS --}}
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
            <form action="{{ route('profissionais.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                
                {{-- Filtro por Especialidade --}}
                <div class="flex-1">
                    <label for="especialidade" class="block text-xs font-medium text-blue-800 mb-1">
                        Filtrar por Especialidade
                    </label>
                    <select name="especialidade" 
                            id="especialidade"
                            class="block w-full px-3 py-2 border border-blue-200 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 sm:text-sm">
                        <option value="">Todas as especialidades</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp }}" {{ ($especialidade ?? '') === $esp ? 'selected' : '' }}>
                                {{ $esp }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botões de Ação --}}
                <div class="flex gap-2 items-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                        <i class="fas fa-filter mr-2"></i> Filtrar
                    </button>

                    @if($especialidade)
                        <a href="{{ route('profissionais.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 transition duration-150">
                            <i class="fas fa-times mr-2"></i> Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- INFORMAÇÕES DE RESULTADOS --}}
        <div class="px-6 py-3 bg-blue-50 border-b border-blue-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                <p class="text-sm text-blue-800">
                    @if($especialidade)
                        <i class="fas fa-filter mr-1 text-blue-600"></i>
                        Especialidade: <strong class="text-blue-900">{{ $especialidade }}</strong>
                        <span class="text-blue-500 ml-2">|</span>
                    @endif
                    <span class="ml-2">
                        <strong>{{ $profissionais->total() }}</strong> 
                        {{ $profissionais->total() === 1 ? 'profissional encontrado' : 'profissionais encontrados' }}
                    </span>
                </p>

                @if($profissionais->total() > 0)
                    <p class="text-sm text-blue-700">
                        Exibindo 
                        <strong>{{ $profissionais->firstItem() }}</strong> a 
                        <strong>{{ $profissionais->lastItem() }}</strong> de 
                        <strong>{{ $profissionais->total() }}</strong>
                    </p>
                @endif
            </div>
        </div>

        {{-- TABELA DE PROFISSIONAIS --}}
        @if($profissionais->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-200">
                    <thead class="bg-gradient-to-r from-blue-100 to-blue-200">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">
                                <i class="fas fa-user-md mr-1"></i> Profissional
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-900 uppercase tracking-wider hidden md:table-cell">
                                <i class="fas fa-id-badge mr-1"></i> CRM
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-900 uppercase tracking-wider hidden lg:table-cell">
                                <i class="fas fa-stethoscope mr-1"></i> Especialidade
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-900 uppercase tracking-wider hidden md:table-cell">
                                <i class="fas fa-phone mr-1"></i> Telefone
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-blue-900 uppercase tracking-wider hidden lg:table-cell">
                                <i class="fas fa-envelope mr-1"></i> E-mail
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-blue-900 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1"></i> Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-blue-100">
                        @foreach($profissionais as $profissional)
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                
                                {{-- COLUNA: Nome --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-md text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-blue-900">
                                                Dr(a). {{ $profissional->usuario->nome_completo }}
                                            </div>
                                            <div class="text-xs text-blue-600 lg:hidden">
                                                {{ $profissional->especialidade }}
                                            </div>
                                            @if(!$profissional->ativo)
                                                <span class="inline-flex text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 mt-1">
                                                    Inativo
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- COLUNA: CRM --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-800 font-mono hidden md:table-cell">
                                    {{ $profissional->crm_formatado }}
                                </td>

                                {{-- COLUNA: Especialidade --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-800 hidden lg:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-stethoscope mr-1"></i>
                                        {{ $profissional->especialidade }}
                                    </span>
                                </td>

                                {{-- COLUNA: Telefone --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-800 hidden md:table-cell">
                                    {{ formatarTelefone($profissional->telefone_consultorio ?? $profissional->usuario->telefone) ?? '-' }}
                                </td>

                                {{-- COLUNA: E-mail --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-800 hidden lg:table-cell">
                                    {{ $profissional->usuario->email }}
                                </td>

                                {{-- COLUNA: Ações --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        
                                        {{-- Botão Ver --}}
                                        <a href="{{ route('profissionais.show', $profissional->id) }}"
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-150 font-bold"
                                           title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Botão Editar --}}
                                        <a href="{{ route('profissionais.edit', $profissional->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150 font-bold"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Botão Agenda --}}
                                        <a href="{{ route('agendamentos.index', ['profissional_id' => $profissional->id]) }}"
                                           class="text-cyan-600 hover:text-cyan-900 transition-colors duration-150 font-bold"
                                           title="Ver agenda">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>

                                        {{-- Botão Excluir --}}
                                        <button type="button"
                                                onclick="confirmarExclusao({{ $profissional->id }}, '{{ $profissional->usuario->nome_completo }}')"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150 font-bold"
                                                title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        {{-- Formulário de Exclusão (Hidden) --}}
                                        <form id="form-excluir-{{ $profissional->id }}"
                                              action="{{ route('profissionais.destroy', $profissional->id) }}"
                                              method="POST"
                                              class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINAÇÃO --}}
            <div class="bg-blue-50 px-6 py-4 border-t border-blue-200">
                {{ $profissionais->appends(['especialidade' => $especialidade])->links('vendor.pagination.tailwind') }}
            </div>

        @else
            {{-- EMPTY STATE --}}
            <div class="text-center py-16 px-6 bg-white">
                @if($especialidade)
                    <i class="fas fa-search text-blue-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-blue-900 mb-2">
                        Nenhum profissional encontrado
                    </h3>
                    <p class="text-blue-700 mb-6">
                        Não encontramos profissionais na especialidade "<strong>{{ $especialidade }}</strong>".
                    </p>
                    <a href="{{ route('profissionais.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Ver todos os profissionais
                    </a>
                @else
                    <i class="fas fa-user-md text-blue-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-blue-900 mb-2">
                        Nenhum profissional cadastrado
                    </h3>
                    <p class="text-blue-700 mb-6">
                        Comece adicionando o primeiro profissional ao sistema.
                    </p>
                    <a href="{{ route('profissionais.create') }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                        <i class="fas fa-user-plus mr-2"></i> Cadastrar Primeiro Profissional
                    </a>
                @endif
            </div>
        @endif

    </div>

    {{-- CARDS DE ESTATÍSTICAS --}}
    @if($profissionais->total() > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- Card: Total de Profissionais --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-user-md text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-blue-600 truncate">
                                Total de Profissionais
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-blue-900">
                                    {{ $profissionais->total() }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Card: Profissionais Ativos --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-400">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-green-600 truncate">
                                Profissionais Ativos
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-green-900">
                                    {{ $profissionais->where('ativo', true)->count() }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Card: Especialidades Cadastradas --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cyan-400">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-cyan-100 rounded-md p-3">
                        <i class="fas fa-stethoscope text-cyan-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-cyan-600 truncate">
                                Especialidades
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-cyan-900">
                                    {{ $especialidades->count() }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>

{{-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO --}}
<div id="modal-excluir" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border-2 w-96 shadow-lg rounded-md bg-white border-blue-300">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-bold text-gray-900 mt-4">
                Confirmar Exclusão
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-700">
                    Tem certeza que deseja excluir o profissional <strong id="nome-profissional-excluir"></strong>?
                </p>
                <p class="text-xs text-red-600 mt-2 font-semibold">
                    <i class="fas fa-exclamation-circle mr-1"></i> Esta ação não pode ser desfeita!
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="btn-confirmar-excluir"
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 transition duration-150 mb-2">
                    <i class="fas fa-trash-alt mr-2"></i> Sim, excluir profissional
                </button>
                <button onclick="fecharModal()"
                        class="px-4 py-2 bg-gray-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 transition duration-150">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- SCRIPTS --}}
@push('scripts')
<script>
    let profissionalIdParaExcluir = null;

    function confirmarExclusao(profissionalId, nomeProfissional) {
        profissionalIdParaExcluir = profissionalId;
        document.getElementById('nome-profissional-excluir').textContent = nomeProfissional;
        document.getElementById('modal-excluir').classList.remove('hidden');
    }

    function fecharModal() {
        profissionalIdParaExcluir = null;
        document.getElementById('modal-excluir').classList.add('hidden');
    }

    document.getElementById('btn-confirmar-excluir').addEventListener('click', function() {
        if (profissionalIdParaExcluir) {
            document.getElementById('form-excluir-' + profissionalIdParaExcluir).submit();
        }
    });

    document.getElementById('modal-excluir').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModal();
        }
    });

    // Auto-hide mensagens
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.animate-fade-in');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>
@endpush

{{-- ESTILOS --}}
@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
</style>
@endpush