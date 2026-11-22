<?php

class JobSearchService
{
    private const API_URL = 'https://www.arbeitnow.com/api/job-board-api';

    public function search(array $params)
    {
        $keywords = $this->getStringParam($params, 'what');
        $location = $this->getStringParam($params, 'where');
        $page = filter_var($params['page'] ?? 1, FILTER_VALIDATE_INT);
        $resultsPerPage = filter_var($params['results_per_page'] ?? 20, FILTER_VALIDATE_INT);

        if (strlen($keywords) > 100) {
            throw new InvalidArgumentException('what must be at most 100 characters.');
        }
        if (strlen($location) > 100) {
            throw new InvalidArgumentException('where must be at most 100 characters.');
        }
        if ($page === false || $page < 1 || $page > 1000) {
            throw new InvalidArgumentException('page must be an integer from 1 to 1000.');
        }
        if ($resultsPerPage === false || $resultsPerPage < 1 || $resultsPerPage > 50) {
            throw new InvalidArgumentException('results_per_page must be an integer from 1 to 50.');
        }

        $remoteOnly = null;
        if (array_key_exists('remote', $params)) {
            $remoteOnly = filter_var($params['remote'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($remoteOnly === null) {
                throw new InvalidArgumentException('remote must be true or false.');
            }
        }

        if (!function_exists('curl_init')) {
            throw new RuntimeException('The PHP cURL extension is required for job searches.');
        }

        $url = self::API_URL . '?' . http_build_query(
            ['page' => $page],
            '',
            '&',
            PHP_QUERY_RFC3986
        );
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'CVer job search'
        ]);
        $responseBody = curl_exec($curl);
        $curlErrno = curl_errno($curl);
        $httpStatus = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($responseBody === false) {
            $message = $curlErrno === CURLE_OPERATION_TIMEDOUT
                ? 'Job search provider request timed out.'
                : 'Job search provider request failed (cURL error ' . $curlErrno . ').';
            throw new RuntimeException($message);
        }
        if ($httpStatus !== 200) {
            throw new RuntimeException('Job search provider returned HTTP ' . $httpStatus . '.');
        }

        $response = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE
            || !is_array($response)
            || !isset($response['data'])
            || !is_array($response['data'])
        ) {
            throw new RuntimeException('Job search provider returned an invalid response.');
        }

        $jobs = [];
        foreach ($response['data'] as $job) {
            if (!is_array($job) || !$this->isValidJobUrl($job['url'] ?? null)) {
                continue;
            }

            $description = html_entity_decode(
                strip_tags((string) ($job['description'] ?? '')),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );
            $tags = is_array($job['tags'] ?? null) ? $job['tags'] : [];
            $searchableText = implode(' ', [
                (string) ($job['title'] ?? ''),
                (string) ($job['company_name'] ?? ''),
                (string) ($job['location'] ?? ''),
                $description,
                implode(' ', array_map('strval', $tags))
            ]);

            if ($keywords !== '' && stripos($searchableText, $keywords) === false) {
                continue;
            }
            if ($location !== '' && stripos((string) ($job['location'] ?? ''), $location) === false) {
                continue;
            }
            if ($remoteOnly !== null && (bool) ($job['remote'] ?? false) !== $remoteOnly) {
                continue;
            }

            $jobs[] = [
                'slug' => (string) ($job['slug'] ?? ''),
                'title' => (string) ($job['title'] ?? ''),
                'company_name' => (string) ($job['company_name'] ?? ''),
                'description' => $description,
                'location' => (string) ($job['location'] ?? ''),
                'remote' => (bool) ($job['remote'] ?? false),
                'job_types' => is_array($job['job_types'] ?? null) ? $job['job_types'] : [],
                'tags' => $tags,
                'url' => $job['url'],
                'created_at' => isset($job['created_at']) ? (int) $job['created_at'] : null
            ];
            if (count($jobs) >= $resultsPerPage) {
                break;
            }
        }

        return [
            'source' => 'Arbeitnow',
            'attribution_url' => 'https://www.arbeitnow.com/',
            'jobs' => $jobs,
            'pagination' => [
                'page' => (int) ($response['meta']['current_page'] ?? $page),
                'next' => $response['links']['next'] ?? null,
                'previous' => $response['links']['prev'] ?? null
            ]
        ];
    }

    private function getStringParam(array $params, $name)
    {
        if (!array_key_exists($name, $params)) {
            return '';
        }
        if (!is_string($params[$name]) && !is_numeric($params[$name])) {
            throw new InvalidArgumentException($name . ' must be a string.');
        }

        return trim((string) $params[$name]);
    }

    private function isValidJobUrl($url)
    {
        if (!is_string($url)) {
            return false;
        }

        $parsedUrl = parse_url($url);
        return is_array($parsedUrl)
            && ($parsedUrl['scheme'] ?? '') === 'https'
            && in_array($parsedUrl['host'] ?? '', ['arbeitnow.com', 'www.arbeitnow.com'], true);
    }
}
