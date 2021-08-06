@extends('layouts.app')

@section('content')
    <div class="container">
        <form>
            <div class="row">
                <div class="col-12 col-sm-2 form-group">
                    <h4 class="mt-0 mt-sm-4">Filtragem:</h4>
                </div>
                <div class="col-12 col-sm-2 form-group">
                    <label>Data inicial</label>
                    <input type="date" class="form-control" name="data_inicial" value="{{ $data_inicial }}">
                </div>
                <div class="col-12 col-sm-2 form-group">
                    <label>Data final</label>
                    <input type="date" class="form-control" name="data_final" value="{{ $data_final }}">
                </div>
                @if(!auth()->user()->user_id)
                    <div class="col-12 col-sm-4 form-group">
                        <label>Vendedor</label>
                        <select name="vendedor" class="form-control select2">
                            <option value="">Selecione um vendedor para filtrar</option>
                            @foreach($vendedores as $v)
                                <option value="{{ $v->id }}"
                                        @if($vendedor and $v->id == $vendedor) selected @endif>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-12 col-sm-2 form-group">
                    <label class="hidden-on-sm" @if(Agent::isMobile()) style="display:none;" @endif>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div
                                    class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Pedidos') }}</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $qtdVendas }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-cart-plus fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div
                                    class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Descontos') }}</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $totalDescontos }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-minus-square fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div
                                    class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Acréscimos') }}</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $totalAcrescimos }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-plus-square fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div
                                    class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total') }}</div>
                                <div class="h5 mb-0 font-weight-bold">{{ $totalVendas }}</div>
                            </div>
                            <div class="col-auto"><i class="fas  fa-dollar-sign fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <canvas id="myChart"></canvas>
            </div>
{{--            <div class="col-12">--}}
{{--                <canvas id="myChart2"></canvas>--}}
{{--            </div>--}}
        </div>
    </div>
    @push('js')
        <script>
            const x = '{{ $chart }}';
            window.onload = function () {
                var e = JSON.parse(x.replace(/&quot;/g, '"'));
                e.options.scales = {
                    y: {
                        ticks: {
                            callback: function (value, index, values) {
                                return value.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'});
                            }
                        }
                    }
                };

                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, e);

                // var f = {
                //     type: "bar",
                //     data: {
                //         labels: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio"],
                //         datasets: [{
                //             label: "Meta mensal",
                //             data: [2200, 2000, 2500, 2200, 2000, 2500, 2200, 2000, 2500, 2200, 2000, 2500, 2200, 2000, 2500],
                //             borderColor: "rgba(0, 0, 0)",
                //             type: "line",
                //             order: 1,
                //
                //         }, {
                //             type: "bar",
                //             label: "Marcos",
                //             data: [1000, 1200, 1100, 900, 2300],
                //             order: 2,
                //             backgroundColor: "rgba(24, 173, 217, .3)",
                //             borderColor: "rgba(24, 173, 217, 1)",
                //             borderWidth: 2,
                //         }, {
                //             type: "bar",
                //             label: "Flavio",
                //             data: [1050, 1060, 940, 800, 1800],
                //             order: 2,
                //             backgroundColor: "rgba(167, 111, 171, .3)",
                //             borderColor: "rgba(167, 111, 171, 1)",
                //             borderWidth: 2,
                //         }, {
                //             type: "bar",
                //             label: "Sandye",
                //             data: [2050, 2060, 1390, 400, 1200],
                //             order: 2,
                //             backgroundColor: "rgba(167, 024, 171, .3)",
                //             borderColor: "rgba(167, 024, 171, 1)",
                //             borderWidth: 2,
                //         }]
                //     },
                //     options: {
                //         plugins: {
                //             title: {
                //                 display: true,
                //                 text: "Meta x Resultado (Por funcionário)"
                //             }
                //         },
                //         responsive: true,
                //         scales: {
                //             y: {
                //                 ticks: {
                //                     callback: function (value, index, values) {
                //                         return value.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'});
                //                     }
                //                 }
                //             }
                //         }
                //     }
                // };
                // var ctx2 = document.getElementById('myChart2').getContext('2d');
                // var myChart2 = new Chart(ctx2, f);
            }
        </script>
    @endpush
@endsection
