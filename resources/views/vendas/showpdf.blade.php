<style>
    .w-100 {
        width: 100%;;
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
    }
    .table-border {
        border-collapse: collapse;
    }
    .table-border, .th-border, .td-border {
        border: 1px solid black;
    }
    .mt-15 {
        margin-top:15px;
    }
    .font-12 {
        font-size: 12px;
    }
    .-mt-20 {

    }
</style>
<div>
    <h2 class="text-center -mt-20">Pedido de venda</h2>
</div>
<table class="w-100">
    <tr>
        <td class="text-left" ><b>Empresa:</b> {{ $venda->empresa()->first()->razao }} - ({{ $venda->empresa()->first()->fantasia }})</td>
        <td class="text-right font-12"><b>Impresso em:</b> {{ date('d/m/Y H:i:s') }}</td>
    </tr>
</table>
<table class="w-100 mt-15">
    <tr>
        <td><b>Nº pedido:</b> {{ $venda->iderp ? str_pad($venda->iderp, 10, '0', STR_PAD_LEFT) : 'Importação pendente' }}</td>
        <td><b>Cliente:</b> {{ $venda->cliente()->first()->nome }}</td>
    </tr>
</table>
<table class="w-100">
    <tr>
        <td><b>Data do pedido:</b> {{ date('d/m/Y', strtotime($venda->created_at)) }}</td>
        <td><b>Vendedor:</b> {{ $venda->vendedor()->first()->name }}</td>
        <td><b>Forma de pagamento:</b> {{ $venda->tipoVenda()->first()->nome }}</td>
    </tr>
</table>
<table class="w-100">
    <tr>
        <td><b>Quantidade de produtos:</b> {{ $venda->produtos()->count() }}</td>
        <td class="text-right"><b>Vlr. Produtos: </b>R$ {{ number_format($venda->produtos()->sum('total'), 2, ',', '.') }}</td>
        <td class="text-right"><b>Acrés/Desc: </b>R$ {{ number_format($venda->desconto > 0.00 ? $venda->desconto * -1 : $venda->acrescimo, 2, ',', '.') }}</td>
        <td class="text-right"><b>Total:</b> R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
    </tr>
</table>
<table class="table-border mt-15" cellpadding="2px" cellspacing="0">
    <tr>
        <th class="th-border">Produto</th>
        <th class="th-border">Qtd.</th>
        <th class="th-border">Valor</th>
        <th class="th-border">Acrésc/Desc.</th>
        <th class="th-border">Total</th>
    </tr>
    <tbody>
    @foreach($venda->produtos()->get() as $p)
        <tr>
            <td class="td-border">{{ $p->produto()->first()->iderp }} - {{ $p->nome }}</td>
            <td class="text-right td-border">{{ number_format($p->quantidade, 0, ',', '.') }}</td>
            <td class="text-right td-border">{{ number_format($p->preco, 2, ',', '.') }}</td>
            <td class="text-right td-border">{{ number_format($p->desconto > 0.00 ? $p->desconto * -1 : $p->acrescimo, 2, ',', '.') }}</td>
            <td class="text-right td-border">{{ number_format($p->total, 2, ',', '.') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
