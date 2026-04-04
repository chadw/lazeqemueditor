<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UpdateChecker
{
    public function latestRelease(): ?array
    {
        $endpoint = config('update.endpoint');
        if (!empty($endpoint)) {
            try {
                $res = Http::get($endpoint);
                if ($res->ok()) {
                    $data = $res->json();

                    if (is_array($data) && array_key_exists('tag_name', $data)) {
                        $r = $data;
                    } elseif (is_array($data) && count($data) && array_key_exists('tag_name', $data[0])) {
                        $r = $data[0];
                    } elseif (is_array($data) && count($data)) {
                        $r = $data[0];
                    } else {
                        $r = null;
                    }

                    if ($r) {
                        return [
                            'tag_name' => $r['tag_name'] ?? null,
                            'name' => $r['name'] ?? null,
                            'body' => $r['body'] ?? null,
                            'html_url' => $r['html_url'] ?? null,
                            'published_at' => $r['published_at'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
            }
        }

        $repo = config('update.github_repo');
        if (empty($repo)) {
            return null;
        }

        $token = config('update.github_token');

        $url = "https://api.github.com/repos/{$repo}/releases/latest";

        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'eqemueditor-update-checker',
        ];

        if ($token) {
            $headers['Authorization'] = 'token ' . $token;
        }

        try {
            $res = Http::withHeaders($headers)->get($url);
            if (!$res->ok()) {
                return null;
            }

            $data = $res->json();

            return [
                'tag_name' => $data['tag_name'] ?? null,
                'name' => $data['name'] ?? null,
                'body' => $data['body'] ?? null,
                'html_url' => $data['html_url'] ?? null,
                'published_at' => $data['published_at'] ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function releases(int $perPage = 10): ?array
    {
        $endpoint = config('update.endpoint');
        if (!empty($endpoint)) {
            try {
                $res = Http::get($endpoint);
                if ($res->ok()) {
                    $data = $res->json();
                    if (is_array($data)) {
                        return array_map(function ($r) {
                            return [
                                'tag_name' => $r['tag_name'] ?? null,
                                'name' => $r['name'] ?? null,
                                'body' => $r['body'] ?? null,
                                'html_url' => $r['html_url'] ?? null,
                                'published_at' => $r['published_at'] ?? null,
                            ];
                        }, $data);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        $repo = config('update.github_repo');
        if (empty($repo)) {
            return null;
        }

        $token = config('update.github_token');
        $url = "https://api.github.com/repos/{$repo}/releases";

        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'eqemueditor-update-checker',
        ];

        if ($token) {
            $headers['Authorization'] = 'token ' . $token;
        }

        try {
            $res = Http::withHeaders($headers)->get($url, ['per_page' => $perPage]);
            if (!$res->ok()) {
                return null;
            }

            $data = $res->json();

            $items = array_map(function ($r) {
                return [
                    'tag_name' => $r['tag_name'] ?? null,
                    'name' => $r['name'] ?? null,
                    'body' => $r['body'] ?? null,
                    'html_url' => $r['html_url'] ?? null,
                    'published_at' => $r['published_at'] ?? null,
                ];
            }, $data);

            return $items;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function checkForUpdate(): array
    {
        $latest = $this->latestRelease();
        $current = config('update.current_version');

        if (!$latest || empty($latest['tag_name'])) {
            return ['ok' => false, 'error' => 'No release info'];
        }

        $latestTag = ltrim((string) $latest['tag_name'], 'vV');
        $currentTag = ltrim((string) $current, 'vV');

        try {
            if (version_compare($latestTag, $currentTag, '>')) {
                return ['ok' => true, 'update' => true, 'latest' => $latest];
            }
        } catch (\Throwable $e) {
            if ($latestTag !== $currentTag) {
                return ['ok' => true, 'update' => true, 'latest' => $latest];
            }
        }

        return ['ok' => true, 'update' => false, 'latest' => $latest];
    }
}
