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
            $m = $client->api('milestones')->all($project['id']);
            $milestones[$project['id']] = $client->api('milestones')->all($project['id'], 1, 100);
            foreach ($milestones[$project['id']] as &$m) {
                $m['project_id'] = $project['id'];
                $m['web_url'] = $project['web_url'] . '/milestones/' . $m['iid'];
                $m['path_with_namespace'] = $project['path_with_namespace'];
            }
        }
        return \Response::json($milestones);
    }
}
