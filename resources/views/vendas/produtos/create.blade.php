<form onsubmit="return addProdutoVenda($(this))" action="{{ route('produtos-vendas.store') }}" method="post">
    @csrf
    <input type="hidden" name="venda_id" value="{{ $venda->id }}">
    <input type="hidden" name="nome" value="">
    <div class="row">
        <div class="col-12 col-sm-2 form-group">
            <label for="filter_type">
                Tipo de pesquisa
            </label>
            <select name="filter_type" class="filter_type" style="width: 100%;">
                <option value="aproximada">Aproximada</option>
                <option value="ref">Referência</option>
                <option value="nome">Nome</option>
                <option value="cod">Código</option>
                <option value="ean">EAN</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 form-group">
            <label for="produto_id">Produto</label>
            <select name="produto_id" class="produto_id @error('produto_id') is-invalid @enderror" style="width: 100%;"></select>
            @error('produto_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-6 col-sm-2 form-group">
            <label for="estoque">
                Estoque
            </label>
            <input name="estoque" type="text" class="money2 form-control form-control-sm" value="0,00" readonly>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-6 col-sm-2 form-group">
            <label for="preco">
                Preço de venda
            </label>
            <input name="preco" type="text" onkeyup="calculaTotal()" class="money2 form-control form-control-sm @error('preco') is-invalid @enderror" value="{{ old('preco') ?? '0.00' }}">
            @error('preco')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-6 col-sm-2 form-group">
            <label for="quantidade">
                Quantidade
            </label>
            <input name="quantidade" type="text" onkeyup="calculaTotal()" class="money form-control form-control-sm @error('quantidade') is-invalid @enderror" value="{{ old('quantidade') ?? '1.00' }}">
            @error('quantidade')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-6 col-sm-2 col-lg-2 form-group">
            <label for="desconto">
                Desconto
            </label>
            <input name="desconto" type="text" onkeyup="calculaTotal()" class="money2 form-control form-control-sm @error('desconto') is-invalid @enderror" value="{{ old('desconto') ?? '0.00' }}">
            @error('desconto')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-6 col-sm-2 form-group d-none">
            <label for="acrescimo">
                Acréscimo
            </label>
            <input name="acrescimo" type="text" onkeyup="calculaTotal()" class="money2 form-control form-control-sm @error('acrescimo') is-invalid @enderror" value="{{ old('acrescimo') ?? '0.00' }}">
            @error('acrescimo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-6 col-sm-2 form-group">
            <label for="acrescimo">
                Total
            </label>
            <input type="text" name="total" class="money2 form-control form-control-sm" value="0.00" readonly style="border-color: #000000;border-width: 2px;">
        </div>
        <div class="col-12 col-sm-3 form-group">
            <label class="d-none d-sm-block">&nbsp;</label>
            <button type="submit" class="btn btn-primary btn-block btn-sm">Adicionar no pedido</button>
        </div>
    </div>
</form>

