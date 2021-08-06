<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyRequest;
use App\Http\Requests\UserConfigRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdatePasswordRequest;
use App\Rota;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function configuracoes()
    {
        return view('user.config');
    }

    private function rconfig()
    {
        return redirect()->route('user.config');
    }

    public function updateConfig(UserConfigRequest $r)
    {
        $data = $r->all();
        $user = Auth::user();
        $user->name = $data['name'];
        $user->iderp = $data['iderp'];
        $data['meta'] = str_replace(['.', ','], ['', '.'], $data['meta']);
        $user->meta = $data['meta'];
        $user->update();
        flash('Dados alterados com sucesso')->success()->important();
        return $this->rconfig();
    }

    public function updateToken()
    {
        User::geraToken();
        return $this->rconfig();
    }

    public function updatePassword(UserUpdatePasswordRequest $r)
    {
        $data = $r->all();
        $user = Auth::user();
        $senha = Hash::make($data['password']);
        $user->password = $senha;
        $user->update();
        flash('Senha alterada com sucesso')->success()->important();
        return $this->rconfig();
    }

    public function index()
    {
        $data = request()->all();
        $users = Auth::user()->users();
        if (isset($data['q']))
            $users->where('name', 'like', "%{$data['q']}%")->orWhere('email', 'like', "%{$data['q']}%");
        return view('users.index', ['users' => $users->paginate(), 'q' => $data['q'] ?? null]);
    }

    private function rindex()
    {
        return redirect()->route('user.vendedores.index');
    }

    public function create()
    {
        return view('users.create', ['rotas' => Rota::all()]);
    }

    public function edit($user)
    {
        return view('users.edit', [
            'user' => User::findOrFail($user),
            'empresas' => auth()->user()->empresas()->get()
        ]);
    }

    public function store(UserRequest $r)
    {
        $data = $r->all();
        $user = $this->createUser($data);
        flash('Selecione as empresas que este funcionário irá ter acesso as vendas.')->important();
        return redirect()->route('user.vendedores.edit', ['user' => $user->id]);
    }

    public function update(UserRequest $r, $user)
    {
        $data = $r->all();
        $u = auth()->user()->users()->findOrFail($user);
        if ($data['password'])
            $data['password'] = Hash::make($data['password']);
        else
            unset($data['password']);

        $data['meta'] = str_replace(['.', ','], ['', '.'], $data['meta']);
        $u->update($data);
        $u->empresa_user()->delete();
        if (isset($data['empresas']))
            foreach ($data['empresas'] as $e)
                $u->empresa_user()->create(['empresa_id' => $e]);
        flash('Vendedor alterado com sucesso')->important();
        return $this->rindex();
    }

    public function destroy(DestroyRequest $r, $user)
    {
        $u = auth()->user()->users()->findOrFail($user);
        $u->empresa_user()->delete();
        $u->delete();
        return $this->rindex();
    }

    private function createUser(array $data)
    {
        $data['meta'] = str_replace(['.', ','], ['', '.'], $data['meta']);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_id' => Auth::id()
        ]);
    }

}
