@extends('layouts.main')

@section('title', 'Clientes')

@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                @session
                @endsession
            </div>
        </div>
        <form method="GET">
            <div class="form-group row">
                <div class="col-md-3 mt-1 mb-1">
                    <select name="per_page" onchange="this.form.submit()" class="selectpicker" data-width="100%" data-style="btn-success">
                        <option value="20" {{ request()->get('per_page') === null ? 'selected="selected"' : '' }} data-subtext="20 por padrão" data-icon="fa fa-sort">Itens por página</option>

                        @for($i = 5; $i <= $clients->total(); $i += 5)
                            <option value="{{ $i }}" {{ $i == request()->get('per_page') && request()->get('per_page') !== null ? 'selected="selected"' : '' }} data-subtext="Itens por página">{{ $i }}</option>
                        @endfor

                    </select>
                </div>
                <div class="col-md-2 mt-1 mb-1">
                    <select name="field_sort" onchange="this.form.submit()" class="selectpicker" data-width="100%" data-style="btn-success">
                        <option value="id" {{ 'id' == request()->get('field_sort') && request()->get('field_sort') !== null ? 'selected="selected"' : '' }} data-icon="fa fa-filter">Id</option>
                        <option value="name" {{ 'name' == request()->get('field_sort') && request()->get('field_sort') !== null ? 'selected="selected"' : '' }} data-icon="fa fa-filter">Nome</option>
                        <option value="email" {{ 'email' == request()->get('field_sort') && request()->get('field_sort') !== null ? 'selected="selected"' : '' }} data-icon="fa fa-filter">Email</option>
                    </select>
                </div>
                <div class="col-md-3 mt-1 mb-1">
                    <select name="sort" onchange="this.form.submit()" class="selectpicker" data-width="100%" data-style="btn-success">
                        <option value="asc" {{ 'asc' == request()->get('sort') && request()->get('sort') !== null ? 'selected="selected"' : '' }} data-icon="fa fa-sort-alpha-asc" >Ascendente</option>
                        <option value="desc" {{ 'desc' == request()->get('sort') && request()->get('sort') !== null ? 'selected="selected"' : '' }} data-icon="fa fa-sort-alpha-desc" >Descendente</option>
                    </select>
                </div>
                <div class="col-md-4 mt-1 mb-1">
                    <input name="search" class="form-control enterSearch" type="search" value="{{ request()->get('search') }}" placeholder="Enter para buscar" aria-label="Search">
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-12 text-center">
                <h1>Clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <a class="text-success" href="{{ route('dashboard.clients.create') }}"><i class="fa fa-plus fa-2x" aria-hidden="true"></i> ADICIONAR</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-success" id="remove">Excluir</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table bg-dotlib table-responsive-sm mt-2">
                    <thead>
                    <tr>
                        <th scope="col">
                            <div>
                                <input type="checkbox" name="id_all" class="id_all" value="">
                            </div>
                        </th>
                        <th scope="col">Id</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <div>
                                    <input type="checkbox" name="id_all" data-id="{{ $client->id }}" class="checked-status" value="">
                                </div>
                            </td>
                            <th scope="row">{{ $client->id }}</th>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email ?? '-' }}</td>
                            <td>{{ maskCpf($client->cpf) }}</td>
                            <td>
                                <form class="form-inline" action="{{ route('dashboard.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Você tem certeza que deseja excluir este registro?')">
                                    @method('DELETE')
                                    @csrf

                                    <div class="form-group mr-3">
                                        <a class="text-decoration-none text-success" href="{{ route('dashboard.clients.show', $client->id) }}">
                                            <i class="fa fa-eye fa-2x" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="form-group">
                                        <a class="text-decoration-none text-light" href="{{ route('dashboard.clients.edit', $client->id) }}">
                                            <i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary-outline"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>-</td>
                            <th scope="row">*</th>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                {{ $clients->appends(request()->input())->links() }}
            </div>
            <div class="col-md-2">
                <p class="font-weight-bold">Total: <span class="text-light">{{ $clients->total() }}</span></p>
            </div>
        </div>
    </div>

    <script defer>
        $(function () {
            var apiToken = <?php echo json_encode(auth()->user()->api_token); ?>;

            $('.id_all').on('click', function () {

                if ($(this).prop('checked')) {

                    $('.checked-status').each(function () {
                        $(this).prop('checked', true);
                    });

                } else {

                    $('.checked-status').each(function () {
                        $(this).prop('checked', false);
                    });
                }

            });

            $('#remove').on('click', function () {
                var ids = [];

                $('.checked-status').each(function () {

                    if ($(this).prop('checked')) {
                        ids.push($(this).data('id'));
                    }

                });

                if (ids.length > 0) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'Authorization': 'Zeus ' + apiToken
                        },
                        url: "{{ route('bulk_action.destroy') }}",
                        data: {
                            ids: ids,
                            model: 'Client'
                        },
                        statusCode: {
                            204: function(data) {
                                location.reload(true);
                            },
                            401: function(xhr) {
                                alert("O seu token é inválido.");
                            }, //Token Invalid
                            500: function(xhr) {
                                alert("Erro no servidor, contate o administrador.");
                            } // Server Error
                        }
                    });
                }
            });
        });
    </script>

@endsection

