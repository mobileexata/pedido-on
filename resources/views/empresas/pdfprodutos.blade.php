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
    <h2 class="title-h2">{{ __('Listagem de produtos') }}</h2>
</div>
<table class="w-100">
    <tr>
        <td class="text-left p-10"><b>Empresa:</b> {{ $nome_empresa }}</td>
        <td class="text-right font-12 p-10"><b>Impresso em:</b> {{ date('d/m/Y H:i:s') }}</td>
    </tr>
</table>
<table class="w-100">
    <tr>
        <td class=" p-10"><b>Quantidade de produtos:</b> {{ $produtos->count() }}</td>
    </tr>
</table>
<hr>
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
    @foreach($produtos as $p)
        <tr class="trProd">
            <td class="text-center">
                <img src="@if($p->imagem){{ public_path('produtos/' . $p->imagem) }}@else{{ public_path('images/no_photo.png') }}@endif" alt="{{ $p->nome }}" class="img-produtos">
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
                R$ {{ number_format($p->preco, 2, ',', '.') }}
            </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
