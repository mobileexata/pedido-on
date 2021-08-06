$('select#cliente_id').select2({
ajax: {
url: function () {
return $('meta[name="site-link"]').attr('content') + 'pesquisa/clientes/' + $('select[name="empresa_id"]').val()
},
dataType: 'json',
// width: 'resolve',
delay: 250
},
language: "pt-BR",
placeholder: 'Selecione o cliente',
templateResult: function (item) {
if (!item.id)
return $("<div class='row font-weight-bold'>" +
    "<div class='col-12 col-sm-12'>" + 'Nome' + "</div>" +
{{--    "<div class='col-6 col-sm-2 '>" + 'Situacao' + "</div>" +--}}
{{--    "<div class='col-6 col-sm-4 text-right '>" + 'Saldo pendente' + "</div>" +--}}
    "</div>");
return $("<div class='row'>" +
    "<div class='col-12 col-sm-12'>" + item.text + "</div>" +
{{--    "<div class='col-6 col-sm-2'>" + item.situacao + "</div>" +--}}
{{--    "<div class='col-6 col-sm-4 text-right'>" + item.saldo_pendente + "</div>" +--}}
    "</div>");
},
templateSelection: function (item) {
if(item.id) {
$('#label_cliente_id').text('Cliente' + (item.situacao_saldo_calc ? item.situacao_saldo_calc : '{{ $situacao_saldo_calc ?? '' }}'))
return $("<div class='row font-weight-bold ml-0'>" +
    "<div class='12'>" + item.text + "</div>" +
    "</div>");
} else
$('input#cliente_id').val('');
return $("<div class='row'>" +
    "<div class='col-12'>Selecione o cliente</div>" +
    "</div>");
}
});
@isset($venda)
var data_id = '{{ $venda->cliente_id }}';
var data_text = '{{ auth()->user()->empresas()->findOrFail($venda->empresa_id)->clientes()->findOrFail($venda->cliente_id)->nome }}';
// Set the value, creating a new option if necessary
if ($("select#cliente_id").find("option[value='" + data_id + "']").length) {
    $("select#cliente_id").val(data_id).trigger('change');
} else {
    // Create a DOM Option and pre-select by default
    var newOption = new Option(data_text, data_id, true, true);
    // Append it to the select
    $("select#cliente_id").append(newOption).trigger('change');
}
    //$("select#cliente_id").val('10');
//$("select#cliente_id").trigger('change');
@endisset

