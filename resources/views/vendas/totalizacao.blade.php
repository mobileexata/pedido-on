<div class="col-12 col-sm-2 form-group">
    <label for="acrescimo">Total</label>
    <input type="text" class="form-control money form-control-sm" name="total_venda" value="{{ number_format($venda['total'] ?? '0.00', 2, ',', '.') }}" readonly>
</div>
<div class="col-12 col-sm-2 form-group">
    <label class="d-none d-sm-block">&nbsp;</label>
    <button class="btn btn-primary btn-block btn-sm" type="submit">Salvar</button>
</div>
