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
        $projects = $this->getProjects($this->client);
        $projectsHasIssues = [];
        foreach ($projects as $p) {
            $issues = $this->getIssues($this->client, $p);
            $hasIssue = false;
            foreach ($issues as $i) {
                if ($i['state'] != 'opened') {
                    continue;
                }
                $hasIssue = true;
                break;
            }
            if ($hasIssue) {
                $projectsHasIssues[] = $p;
            }
        }
        $data = [
            'projects' => array_sort($projectsHasIssues, function ($v) {
                return $v['path_with_namespace'];
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

    public function issues($projectId)
    {
        $projects = $this->getProjects($this->client);
        $projectMapByProjectId = [];
        foreach ($projects as $p) {
            $projectMapByProjectId[$p['id']] = $p;
        }

        $users = $this->getUsers($this->client);
        $userMapByUserId = [];
        foreach ($users as $u) {
            $userMapByUserId[$u['id']] = $u;
        }

        $selectedProject = null;
        $projectsHasIssues = [];
        $issueMapByUserId = [];
        foreach ($projects as $p) {
            $ms = $this->getMilestones($this->client, $p);
            $issues = $this->getIssues($this->client, $p);
            $hasIssue = false;
            foreach ($issues as $i) {
                if (empty($i['assignee']['id']) || $i['state'] != 'opened') {
                    continue;
                }
                if ($p['id'] == $projectId) {
                    if (empty($issueMapByUserId[$i['assignee']['id']])) {
                        $issueMapByUserId[$i['assignee']['id']] = new \stdClass();
                        $issueMapByUserId[$i['assignee']['id']]->user = $userMapByUserId[$i['assignee']['id']];
                        $issueMapByUserId[$i['assignee']['id']]->issues = [];
                    }
                    $i['issue_url'] = $projectMapByProjectId[$p['id']]['web_url'] . '/issues/' . $i['iid'];
                    if (isset($i['milestone'])) {
                        foreach ($ms[$p['id']] as $m) {
                            if ($m['id'] != $i['milestone']['id']) {
                                continue;
                            }
                            $i['milestone_url'] = $projectMapByProjectId[$p['id']]['web_url'] . '/milestones/' . $m['iid'];
                        }
                    }
                    $issueMapByUserId[$i['assignee']['id']]->issues[] = $i;
                    $selectedProject = $p;
                }
                $hasIssue = true;
            }
            if ($hasIssue) {
                $projectsHasIssues[] = $p;
            }
        }
        $data = [
            'project'  => $selectedProject,
            'projects' => array_sort($projectsHasIssues, function ($v) {
                return $v['path_with_namespace'];
            }),
            'members'  => $issueMapByUserId
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
