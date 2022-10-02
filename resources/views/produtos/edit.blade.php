@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title font-weight-bold d-inline-block ">{{ $produto->iderp }} - {{ $produto->nome }}</h6>
                        <a class="float-right btn btn-sm btn-primary" href="{{ back()->getTargetUrl() }}">Voltar</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 justify-content-center">
                                @if($produto->imagem)
                                    <p class="font-weight-bold">Imagem utilizada atualmente:</p>
                                    <img src="{{ asset('produtos/' . $produto->imagem) }}" class="mb-3"
                                         style="max-width: 350px; max-height: 350px">
                                @else
                                    <div class="alert alert-danger">Produto sem imagem!</div>
                                @endif
                            </div>
                            <div class="col-12">
                                <form method="post" action="{{ route('produtos.update', ['produto' => $produto]) }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-12 form-group ">
                                            <label>Imagem</label>
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input @error('imagem') is-invalid @enderror"
                                                       name="imagem" id="imagem"
                                                       accept="image/gif, image/jpeg, image/png">
                                                <label class="custom-file-label" for="imagem" data-browse="Procurar">Selecione
                                                    a
                                                    imagem</label>
                                                @error('imagem')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 form-group ">
                                            <button class="btn btn-primary btn-sm" type="submit">@if($produto->imagem)
                                                    Alterar
                                                    imagem @else Cadastrar imagem @endif</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('js')
        <script>
            window.onload = function () {
                $('#imagem').on('change', function () {
                    //get the file name
                    var fileName = $(this).val() ? getFileName($(this).val()) : 'Selecione a imagem';
                    //replace the "Choose a file" label
                    $(this).next('.custom-file-label').html(fileName);
                });
            }

            function getFileName(fullPath)
            {
                var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                var filename = fullPath.substring(startIndex);
                if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                    filename = filename.substring(1);
                }
                return filename;
            }
        </script>
    @endpush
@endsection
