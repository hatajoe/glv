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

        $projects = $this->_getProjects($client);
        $milestones = [];
        foreach ($projects as $project) {
            $ms = $this->_getMilestones($client, $project); 
            foreach ($ms as $m) {
                $milestones[] = $m;
            }
        }
        return \Response::json($milestones);
    }

    private function _getProjects($client) 
    {
        $page = 1;
        $hasNext = true;
        $projects = [];
        while ($hasNext) {
            $ps = $client->api('projects')->all($page, 100);
            foreach ($ps as $p) {
                $projects[] = $p;
            }
            ++$page;
            $hasNext = $this->_hasNext($client->getHttpClient()->getLastResponse());
        }
        return $projects;
    }

    private function _getMilestones($client, $project)
    {
        $page = 1;
        $hasNext = true;
        $milestones = [];
        while ($hasNext) {
            $milestones[$project['id']] = $client->api('milestones')->all($project['id'], $page, 100);
            foreach ($milestones[$project['id']] as &$m) {
                $m['project_id'] = $project['id'];
                $m['web_url'] = $project['web_url'] . '/milestones/' . $m['iid'];
                $m['path_with_namespace'] = $project['path_with_namespace'];
            }
            ++$page;
            $hasNext = $this->_hasNext($client->getHttpClient()->getLastResponse());
        }
        return $milestones;
    }

    private function _hasNext($response) 
    {
        $link = $response->getHeader("Link");
        $pattern = '/rel="next"/';
        if (preg_match($pattern, $link) !== 1) {
            return false;
        }
        return true;
    }
}
