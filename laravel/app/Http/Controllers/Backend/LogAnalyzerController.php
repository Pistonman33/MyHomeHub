<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class LogAnalyzerController extends Controller
{
    /**
     * Les niveaux de log disponibles
     */
    const LOG_LEVELS = [
        'debug' => ['color' => 'secondary', 'icon' => 'circle-info', 'level' => 100],
        'info' => ['color' => 'info', 'icon' => 'info-circle', 'level' => 200],
        'notice' => ['color' => 'primary', 'icon' => 'bell', 'level' => 250],
        'warning' => ['color' => 'warning', 'icon' => 'triangle-exclamation', 'level' => 300],
        'error' => ['color' => 'danger', 'icon' => 'exclamation-circle', 'level' => 400],
        'critical' => ['color' => 'danger', 'icon' => 'bomb', 'level' => 500],
        'alert' => ['color' => 'danger', 'icon' => 'skull', 'level' => 550],
        'emergency' => ['color' => 'dark', 'icon' => 'radiation', 'level' => 600],
    ];

    /**
     * Afficher la liste des logs
     */
    public function index(Request $request)
    {
        $level = $request->get('level', 'all');
        $search = $request->get('search', '');
        $perPage = $request->get('perPage', 50);
        $source = $request->get('source', 'auto');

        // Parser les logs
        $logs = $this->parseLogs($source);

        // Filtrer par niveau
        if ($level !== 'all' && isset(self::LOG_LEVELS[strtolower($level)])) {
            $logs = collect($logs)->filter(function ($log) use ($level) {
                return strtolower($log['level']) === strtolower($level);
            })->toArray();
        }

        // Filtrer par recherche
        if (!empty($search)) {
            $logs = collect($logs)->filter(function ($log) use ($search) {
                $searchLower = strtolower($search);
                return stripos($log['message'], $searchLower) !== false ||
                       stripos($log['channel'], $searchLower) !== false;
            })->toArray();
        }

        // Inverser l'ordre (logs les plus récents en premier)
        $logs = array_reverse($logs);

        // Paginer manuellement
        $page = Paginator::resolveCurrentPage() ?: 1;
        $totalItems = count($logs);
        $start = ($page - 1) * $perPage;
        $slicedLogs = array_slice($logs, $start, $perPage);

        $paginatedLogs = new LengthAwarePaginator(
            $slicedLogs,
            $totalItems,
            $perPage,
            $page,
            [
                'path' => route('admin.logs.index'),
                'query' => $request->query(),
            ]
        );

        // Calculer les statistiques
        $stats = $this->getStats($logs);
        $logFile = $this->resolveLogFile($source);

        return view('backend.logs.index', [
            'logs' => $paginatedLogs,
            'level' => $level,
            'search' => $search,
            'perPage' => $perPage,
            'levels' => self::LOG_LEVELS,
            'stats' => $stats,
            'logFile' => $logFile,
            'source' => $source,
        ]);
    }

    /**
     * Résoudre le fichier de log à analyser.
     */
    private function resolveLogFile(string $source = 'auto'): string
    {
        $today = Carbon::now()->format('Y-m-d');

        if ($source === 'scheduler') {
            $candidates = [
                storage_path('logs/scheduler-' . $today . '.log'),
                storage_path('logs/scheduler.log'),
            ];
        } elseif ($source === 'laravel') {
            $candidates = [
                storage_path('logs/laravel-' . $today . '.log'),
                storage_path('logs/laravel.log'),
            ];
        } else {
            $candidates = [
                storage_path('logs/scheduler-' . $today . '.log'),
                storage_path('logs/scheduler.log'),
                storage_path('logs/laravel-' . $today . '.log'),
                storage_path('logs/laravel.log'),
            ];
        }

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return $candidates[0];
    }

    /**
     * Parser le fichier de log
     */
    private function parseLogs(string $source = 'auto')
    {
        $logFile = $this->resolveLogFile($source);

        if (!file_exists($logFile)) {
            return [];
        }

        $logs = [];
        $handle = fopen($logFile, 'r');

        if (!$handle) {
            return [];
        }

        $currentLog = null;
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;

            // Format: [2026-06-23 10:30:45] local.ERROR: Message ...
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]\s(\w+)\.(\w+):\s(.+)$/m', trim($line), $matches)) {
                if ($currentLog !== null) {
                    $logs[] = $currentLog;
                }

                $currentLog = [
                    'date' => $matches[1],
                    'channel' => $matches[2],
                    'level' => strtolower($matches[3]),
                    'message' => trim($matches[4]),
                    'lineNumber' => $lineNumber,
                    'extra' => '',
                ];
            } elseif ($currentLog !== null && !empty(trim($line))) {
                // C'est une continuation du message précédent
                $currentLog['extra'] .= trim($line) . "\n";
            }
        }

        // Ajouter le dernier log
        if ($currentLog !== null) {
            $logs[] = $currentLog;
        }

        fclose($handle);

        return $logs;
    }

    /**
     * Calculer les statistiques
     */
    private function getStats($logs)
    {
        $stats = [
            'total' => count($logs),
            'by_level' => [],
            'by_channel' => [],
        ];

        foreach (array_keys(self::LOG_LEVELS) as $level) {
            $stats['by_level'][$level] = 0;
        }

        foreach ($logs as $log) {
            $level = strtolower($log['level']);
            if (isset($stats['by_level'][$level])) {
                $stats['by_level'][$level]++;
            }

            $channel = $log['channel'];
            if (!isset($stats['by_channel'][$channel])) {
                $stats['by_channel'][$channel] = 0;
            }
            $stats['by_channel'][$channel]++;
        }

        return $stats;
    }

    /**
     * Télécharger le fichier de log complet
     */
    public function download(Request $request)
    {
        $source = $request->get('source', 'auto');
        $logFile = $this->resolveLogFile($source);

        if (!file_exists($logFile)) {
            return back()->withErrors(['message' => 'Fichier de log non trouvé']);
        }

        $fileName = basename($logFile);

        return response()->download($logFile, $fileName);
    }

    /**
     * Vider le fichier de log
     */
    public function clear(Request $request)
    {
        $source = $request->get('source', 'auto');
        $logFile = $this->resolveLogFile($source);

        if (!file_exists($logFile)) {
            return back()->withErrors(['message' => 'Fichier de log non trouvé']);
        }

        try {
            file_put_contents($logFile, '');
            return back()->with('success', 'Fichier de log vidé avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erreur lors du vidage du log: ' . $e->getMessage()]);
        }
    }
}
