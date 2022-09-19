<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Config\RoleMenu;

class EventServiceProvider extends ServiceProvider
{
    const MENU_LEVEL_PADRE = 1;
    const MENU_LEVEL_HIJO = 2;
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            //$event->menu->add(trans('menu.pages'));
            $userId = Auth::id();
            $user = User::find($userId);
            $menu_final = array();
            $menu_padre = [];
            if (is_object($user)) {
                $menus = $this->getMenuByRol($user->role);
                // dd($menus);
                if (count($menus) > 0) {
                    foreach ($menus as $p => $padre) {
                        $menu_hijo = [];
                        foreach ($padre->hijos as $h => $hijo) {
                            array_push($menu_hijo, [
                                'text' => $hijo->chr_title,
                                'url'  => $hijo->chr_ruta_sistema
                            ]);
                        }
                        array_push($menu_padre,[
                            'text'    => $padre->chr_title,
                            'icon'    => $padre->chr_icon,
                            'submenu' => $menu_hijo
                        ]);
                    }
                }
            }
            $items = $menu_padre;
            // $items = Page::all()->map(function (Page $page) {
            //     return [
            //         'text' => $page['title'],
            //         'url' => route('admin.pages.edit', $page)
            //     ];
            // });
            $event->menu->add(...$items);
        });
    }

    public function getMenuByRol($rolid){
        //obtenemos los padres
        $padres = RoleMenu::from('config_rol_menu as rm')
                         ->select('m.*')
                         ->join('config_menu as m','m.id','=','rm.int_menuid')
                         ->where('rm.int_rolid',$rolid)
                         ->where('m.int_level',self::MENU_LEVEL_PADRE)
                         ->where('rm.is_active',1)
                         ->where('m.is_active',1)
                         ->where('rm.is_deleted',0)
                         ->where('m.is_deleted',0)
                         ->get();
        if (count($padres)>0) {
            foreach ($padres as $p => $padre) {
                //obtenemos los hijos por padre
                $hijos = RoleMenu::from('config_rol_menu as rm')
                         ->select('m.*')
                         ->join('config_menu as m','m.id','=','rm.int_menuid')
                         ->where('rm.int_rolid',$rolid)
                         ->where('m.int_padreid',$padre->id)
                         ->where('m.int_level',self::MENU_LEVEL_HIJO)
                         ->where('rm.is_active',1)
                         ->where('m.is_active',1)
                         ->where('rm.is_deleted',0)
                         ->where('m.is_deleted',0)
                         ->get();
                $padre->hijos = $hijos;
                $padre->num_hijos = count($hijos) > 0 ? count($hijos) : 0;   
            }
        }
        return $padres;
    }
}
