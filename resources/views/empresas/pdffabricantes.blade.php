<style>
    .w-100 {
        width: 100%;
    }
    .text-right {
        text-align: right;
    }
    .text-left {
        text-align: left;
    }
    .text-center {
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid black;
    }
    .title-h2 {
        text-align: center;
        margin-top: -20px;
        text-transform: uppercase;
    }
    .font-12 {
        font-size: 12px;
    }
    .p-10 {
        padding: 10px;
    }
    .img-produtos {
        max-width: 100px;
        max-height: 100px;
    }
</style>
<div>
    <h2 class="title-h2">{{ __('Listagem de produtos por fabricante') }}</h2>
</div>
<table class="w-100">
    <tr>
        <td class="text-left p-10"><b>Empresa:</b> {{ $nome_empresa }}</td>
        <td class="text-right font-12 p-10"><b>Impresso em:</b> {{ date('d/m/Y H:i:s') }}</td>
    </tr>
</table>
<table class="w-100">
    <tr>
        <td class=" p-10"><b>Quantidade de fabricantes:</b> {{ $fabricantes->count() }}</td>
    </tr>
</table>
<hr>
@foreach($fabricantes as $f)
    <h2>{{ $f['iderp'] }} - {{ $f['nome'] }}</h2>
    @if($f['produtos']->count())
        <table class="w-100" cellpadding="2px" cellspacing="0">
            <tr>
                <th>Foto</th>
                <th>Ref.</th>
                <th>Produto</th>
                @if($estoque == 1)
                <th>Estoque</th>
                @endif
                @if($preco == 1)
                <th>Preço</th>
                @endif
            </tr>
            <tbody>
            @foreach($f['produtos'] as $p)
                <tr class="trProd">
                    <td class="text-center">
                        <img src="{{ $p->image }}" alt="{{ $p->nome }}" class="img-produtos">
                    </td>
                    <td>
                        {{ $p->referencia }}
                    </td>
                    <td>
                        {{ $p->iderp }} - {{ $p->nome }}
                    </td>
                    @if($estoque == 1)
                    <td class="text-right">
                        <span @if((float)$p->estoque < 0.00) class="text-danger" @endif>
                            {{ number_format($p->estoque, 0, ',', '.') }}
                        </span>
                    </td>
                    @endif
                    @if($preco == 1)
                    <td class="text-right">
                        @foreach($tipos_precos as $id => $t)
                            {{ $t }}: <b class="float-right">R$ {{ number_format((isset($p->precos[$id]) ? $p->precos[$id] :   0), 2, ',', '.') }}</b><br>
                        @endforeach
                    </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right">Qtd. Total: {{ $f['produtos']->count() }}</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    @if($estoque == 1)
                        <th class="text-right">{{ number_format($f['produtos']->sum('estoque'), 0, ',', '.') }}</th>
                    @endif
                    @if($preco == 1)
                        <th class="text-right">{{ number_format($f['produtos']->sum('preco'), 2, ',', '.') }}</th>
                    @endif
                </tr>
            </tfoot>
        </table>
    @else
        <b>Nenhum produto encontrado para este fabricante</b>
    @endif
@endforeach