$('.select2').select2({
    language: "pt-BR"
});

$('[data-toggle="tooltip"]').tooltip();

$('select.cliente_id').select2({
    ajax: {
        url: function () {
            return $('meta[name="site-link"]').attr('content') + 'pesquisa/clientes/' + $('select[name="empresa_id"]').val()
        },
        dataType: 'json',
        width: 'resolve',
        delay: 250
    },
    language: "pt-BR",
    placeholder: 'Selecione o cliente'
});

$('select.tiposvenda_id').select2({
    ajax: {
        url: function () {
            return $('meta[name="site-link"]').attr('content') + 'pesquisa/tipos-de-vendas/' + $('select[name="empresa_id"]').val()
        },
        dataType: 'json',
        width: 'resolve',
        delay: 250
    },
    language: "pt-BR",
    placeholder: 'Selecione a forma de pagamento'
});

$(document).ready(function() {
    $('.money').mask('000.000.000.000.000,00');
    $('.money2').mask("#.##0,00");
    $('.percent').mask('#.##0,00%');
});

