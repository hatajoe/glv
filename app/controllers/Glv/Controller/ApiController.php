<?php
namespace Glv\Controller;

class ApiController extends \BaseController
{

    public function index()
    {
        return \View::make('index');
    }

    public function milestones()
    {
        $client = new \Gitlab\Client(getenv('GITLAB_API_DOMAIN') . '/api/v3/');
        $client->authenticate(getenv('GITLAB_API_TOKEN'), \Gitlab\Client::AUTH_URL_TOKEN);

        $milestones = [];
        $projects = $client->api('projects')->all(1, 100);
        foreach ($projects as $project) {
            \Log::info('project ' . $project['id']);
            $m = $client->api('milestones')->all($project['id']);
            \Log::info(count($m));
            $milestones[$project['id']] = $client->api('milestones')->all($project['id'], 1, 100);
        }
        return \Response::json($milestones);
    }
}
