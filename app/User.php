<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_token', 'user_id', 'iderp', 'meta', 'show_custo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'show_custo' => 'boolean'
    ];

    public static function geraToken()
    {
        $user = User::findOrFail(Auth::id());
        if ($user->user_token)
            TokensRegistrado::create([
                'user_id' => $user->id,
                'token' => $user->user_token
            ]);
        $user->user_token = self::geraTokenUnico();
        $user->update();
        flash('Token atualizado com sucesso')->success()->important();
        return true;
    }

    private static function geraTokenUnico()
    {
        $token = Str::random();
            if (User::where('user_token', $token)->count())
                return self::geraTokenUnico();
        return $token;
    }

    public function empresas()
    {
        //se for usuário "admin" retorna as empresas que possuem user_id na tabela empresas
        //se não, retorna as empresas do usuário "não admin" da table empresa_user
        return !$this->user_id ? $this->hasMany(Empresa::class) : $this->belongsToMany(Empresa::class);
    }

    public function empresa_user()
    {
        return $this->hasMany(EmpresaUser::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function vendas()
    {
        return $this->hasManyThrough(Venda::class, User::class, $this->user_id ? 'id' : 'vendas.owner_user_id');
    }

    public function rotas()
    {
        return $this->belongsToMany(Rota::class, 'users_rotas');
    }

    public function showCusto()
    {
        if (!$this->user_id) {
            return true;
        }

        return $this->show_custo;
    }
}
