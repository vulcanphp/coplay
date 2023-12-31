<?php

namespace App\Http\Controllers;

use App\Models\Links;
use App\Core\Configurator;
use App\Core\UpdateManager;
use VulcanPhp\Core\Database\Schema\Schema;

class Admin
{
    protected Configurator $config;

    public function __construct()
    {
        // set configurator
        $this->config = Configurator::$instance;

        // check configuration
        if (!$this->config->is('configured')) {
            if (request()->isPostBack() && !empty(input('password')) && !empty(input('tmdb')) && input('password') == input('confirm')) {
                $this->config->setup([
                    'configured' => true,
                    'embeds' => true,
                    'password' => password(input('password')),
                    'tmdb' => input('tmdb'),
                ]);
                response()->back();
            } else {
                echo view('admin.configure');
                exit;
            }
        }

        // check login
        if (!session('logged', false)) {
            if (request()->isPostBack() && password(input('password'), $this->config->get('password'))) {
                session()->set('logged', true);
                response()->back();
            } else {
                echo view('admin.login');
                exit;
            }
        }
    }

    public function index()
    {
        if (!empty(input('action'))) {
            $this->handleAction(input('action'));
            return response()->back();
        }

        return view('admin.index', ['config' => $this->config, 'links' => $this->getLinks()]);
    }

    protected function getLinks()
    {
        if ($this->config->is('links')) {
            if (!$this->config->is('table-created')) {
                db()->exec(
                    Schema::create('links')
                        ->id()
                        ->bigInteger('tmdb')->unsigned()->key('tmdb_id')
                        ->integer('season', 4)
                        ->integer('episode', 6)
                        ->string('link', 255)
                        ->string('server', 50)->nullable()
                        ->build()
                );

                $this->config->set('table-created', true);
            }

            $filter = null;

            if (!empty(input('tmdb'))) {
                $filter = ['tmdb' => input('tmdb')];
            }

            return Links::where($filter)->order('p.id DESC')->paginate();
        }
    }

    protected function handleAction(string $action): void
    {
        $callback = match ($action) {
            'feature' => fn () => $this->config->setup([
                'links' => input('links') == 'on',
                'embeds' => input('embeds') == 'on',
                'api' => input('api') == 'on',
                'tmdb' => input('tmdb'),
                'password' => !empty(trim(input('password', ''))) ? password(input('password')) : $this->config->get('password')
            ]),
            'link' => fn () => (new Links)->input()->save(),
            'delete' => fn () => Links::erase(['id' => input('id')]),
            'update-check' => fn () => UpdateManager::check(),
            'update-download' => fn () => UpdateManager::download(),
            'remove-donate' => fn () => $this->config->set('remove-donate', true),
            'settings' => fn () => $this->config->setup(input()->all(['title', 'tagline', 'intro', 'description', 'disclaimer', 'copyright', 'language'])),
            'scripts' => fn () => $this->config->setup(input()->all(['head', 'body', 'footer'])),
            default => fn () => null
        };

        call_user_func($callback);
    }
}
