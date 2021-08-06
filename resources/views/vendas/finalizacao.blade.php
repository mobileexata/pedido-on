<form class="formCadPedido" action="{{ route('vendas.update', ['venda' => $venda->id]) }}"
      method="post">
    @csrf
    @method('PUT')
    <div class="row">
        <input type="hidden" name="empresa_id" value="{{ $venda->empresa_id }}">
        <input type="hidden" name="concluida" value="S">
        <input type="hidden" name="total_calc" value="{{ number_format(($venda->total - $venda->acrescimo + $venda->desconto) ?? '0.00', 2, ',', '.') }}">
        <div class="col-12 form-group">
            <label for="cliente_id">
                Cliente
            </label>
            <select name="cliente_id"
                    class="select2 @error('tiposvenda_id') is-invalid @enderror"
                    style="width: 100%;">
                @foreach(auth()->user()->empresas()->findOrFail($venda->empresa_id)->clientes()->get() as $c)
                    <option value="{{ $c->id }}"@if($venda->cliente_id == $c->id) selected @endif>{{ $c->nome }}</option>
                @endforeach
            </select>
            @error('cliente_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 form-group">
            <label for="tiposvenda_id">
                Forma de pagamento
            </label>
            <select name="tiposvenda_id"
                    class="select2 @error('tiposvenda_id') is-invalid @enderror"
                    style="width: 100%;">
                @foreach(auth()->user()->empresas()->findOrFail($venda->empresa_id)->tiposVendas()->get() as $t)
                    <option value="{{ $t->id }}"
                            @if($venda->tiposvenda_id == $t->id) selected @endif> {{ $t->nome }}</option>
                @endforeach
            </select>
            @error('tiposvenda_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-6 form-group">
            <label for="acrescimo">Acréscimo %</label>
            <input type="text" class="form-control percent form-control-sm" name="acrescimo_percent" value="0,00" onblur="calculaAcrescimoVenda()">
        </div>
        <div class="col-6 form-group">
            <label for="acrescimo">Acréscimo</label>
            <input type="text" class="form-control money form-control-sm" name="acrescimo" value="{{ number_format($venda->acrescimo ?? '0,00', 2, ',', '.') }}"  onkeyup="calculaTotalVenda($(this))">
        </div>
        <div class="col-6 form-group">
            <label for="acrescimo">Desconto %</label>
            <input type="text" class="form-control percent form-control-sm" name="desconto_percent" value="0,00" onblur="calculaDescontoVenda()">
        </div>
        <div class="col-6 form-group">
            <label for="acrescimo">Desconto</label>
            <input type="text" class="form-control money form-control-sm" name="desconto" value="{{ number_format($venda->desconto ?? '0,00', 2, ',', '.') }}"  onkeyup="calculaTotalVenda($(this))">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-12 form-group">
            <label for="acrescimo">Total</label>
            <input type="text" class="form-control money form-control-sm" name="total" value="{{ number_format($venda->total ?? '0.00', 2, ',', '.') }}"  readonly style="border-color: #000000;border-width: 2px;">
        </div>
        <div class="col-12 form-group">
            <button class="btn btn-primary btn-block btn-sm" type="submit">Finalizar pedido</button>
        </div>
    </div>
</form>
