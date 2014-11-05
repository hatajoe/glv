<?php
namespace Glv\Controller;

class ApiController extends \BaseController
{
    private $client;

    public function __construct()
    {
        $this->client = new \Gitlab\Client(getenv('GITLAB_API_DOMAIN') . '/api/v3/');
        $this->client->authenticate(getenv('GITLAB_API_TOKEN'), \Gitlab\Client::AUTH_URL_TOKEN);
    }

    public function index()
    {
        $users = $this->getUsers($this->client);
        $data = [
            'users' => array_sort($users, function ($v) {
                return $v['username'];
            })
        ];
        return \View::make('index', $data);
    }

    public function milestones()
    {
        $projects = $this->getProjects($this->client);
        $milestones = [];
        foreach ($projects as $project) {
            $ms = $this->getMilestones($this->client, $project);
            foreach ($ms as $m) {
                $milestones[] = $m;
            }
        }
        $this->response = \Response::json($milestones, 200, ['Access-Controll-Allow-Origin' => '*']);
        $this->response->headers->add(array('Access-Control-Allow-Origin', '*'));
        return $this->response;
    }

    public function issues($userId)
    {
        $users = $this->getUsers($this->client);
        $user = null;
        foreach ($users as $u) {
            if ($userId != $u['id']) {
                continue;
            }
            $user = $u;
            break;
        }
        $projects = $this->getProjects($this->client);
        $is = [];
        foreach ($projects as $p) {
            $issues = $this->getIssues($this->client, $p);
            foreach ($issues as $i) {
                if (empty($i['assignee']['id']) || $i['assignee']['id'] != $userId) {
                    continue;
                }
                $i['issue_url'] = $p['web_url'] . '/issues/' . $i['iid'];
                $is[] = $i;
            }
        }
        $data = [
            'user'  => $user,
            'users' => array_sort($users, function ($v) {
                return $v['username'];
            }),
            'issues' => $is
        ];
        return \View::make('issues', $data);
    }

    private function getUsers($client)
    {
        $page = 1;
        $hasNext = true;
        $users = [];
        while ($hasNext) {
            $us = $client->api('users')->all(null, $page, 100);
            foreach ($us as $u) {
                $users[] = $u;
            }
            ++$page;
            $hasNext = $this->hasNext($client->getHttpClient()->getLastResponse());
        }
        return $users;
    }

    private function getProjects($client)
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
            $hasNext = $this->hasNext($client->getHttpClient()->getLastResponse());
        }
        return $projects;
    }

    private function getIssues($client, $project)
    {
        $page = 1;
        $hasNext = true;
        $issues = [];
        while ($hasNext) {
            $is = $client->api('issues')->all($project['id'], $page, 100);
            foreach ($is as $i) {
                $issues[] = $i;
            }
            ++$page;
            $hasNext = $this->hasNext($client->getHttpClient()->getLastResponse());
        }
        return $issues;
    }

    private function getMilestones($client, $project)
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
            $hasNext = $this->hasNext($client->getHttpClient()->getLastResponse());
        }
        return $milestones;
    }

    private function hasNext($response)
    {
        $link = $response->getHeader("Link");
        $pattern = '/rel="next"/';
        if (preg_match($pattern, $link) !== 1) {
            return false;
        }
        return true;
    }
}
