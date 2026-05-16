<?php

namespace App\Gateways\Spacepag;

use App\Gateways\Contracts\GatewayDriver;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpacepagDriver implements GatewayDriver
{
    private const BASE_URL = 'https://api.spacepag.com/v1';

    private const SLOW_STEP_MS = 1000;

    public function testConnection(array $credentials): bool
    {
        if ($this->resolveApiKey($credentials) === '') {
            return false;
        }

        foreach (['/organizations/balance', '/webhooks'] as $path) {
            try {
                $response = $this->authenticatedClient($credentials)->get($path);
                if ($response->successful()) {
                    return true;
                }
                if ($response->status() !== 401 && $response->status() !== 403) {
                    Log::debug('Spacepag testConnection non-auth failure', [
                        'path' => $path,
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::debug('Spacepag testConnection exception', [
                    'path' => $path,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return false;
    }

    public function createPixPayment(
        array $credentials,
        float $amount,
        array $consumer,
        string $externalId,
        string $postbackUrl
    ): array {
        $apiKey = $this->resolveApiKey($credentials);
        if ($apiKey === '') {
            throw new \RuntimeException('Spacepag: configure a API Key (sk_… ou pk_…) em Integrações → Gateways → Spacepag.');
        }

        $document = $this->normalizeDocument((string) ($consumer['document'] ?? ''));
        $documentType = strlen($document) === 14 ? 'cnpj' : 'cpf';
        $name = $this->sanitizeName((string) ($consumer['name'] ?? ''));
        $email = $this->sanitizeEmail((string) ($consumer['email'] ?? ''));
        if ($email === '') {
            throw new \RuntimeException('Spacepag: e-mail do cliente é obrigatório.');
        }

        $phone = $this->normalizeCustomerPhone((string) ($consumer['phone'] ?? ''));
        if ($phone === '') {
            throw new \RuntimeException('Spacepag: telefone do cliente é obrigatório para PIX. Inclua telefone no checkout ou marque o campo como obrigatório.');
        }

        $body = [
            'amount' => round($amount, 2),
            'customerName' => $name,
            'customerEmail' => $email,
            'customerPhone' => $phone,
            'customerDocument' => $document,
            'customerDocumentType' => $documentType,
            'description' => 'Pagamento de serviço',
            'metadata' => [
                'order_id' => (string) $externalId,
            ],
        ];

        $start = microtime(true);
        try {
            $response = $this->authenticatedClient($credentials)->post('/payments/transactions', $body);
        } catch (ConnectionException $e) {
            throw new \RuntimeException('Spacepag: falha de conexão com a API. '.$e->getMessage(), 0, $e);
        }
        $ms = (int) round((microtime(true) - $start) * 1000);
        if ($ms >= self::SLOW_STEP_MS) {
            Log::info('Spacepag: slow pix create', [
                'order_id' => $externalId,
                'duration_ms' => $ms,
                'http_status' => $response->status(),
            ]);
        }

        if ($response->status() === 401 || $response->status() === 403) {
            throw new \RuntimeException('Spacepag: '.$this->formatApiErrorMessage($response, 'API Key inválida ou sem permissão.'));
        }

        if (! $response->successful()) {
            throw new \RuntimeException('Spacepag: '.$this->formatApiErrorMessage($response, 'Erro ao gerar transação PIX.'));
        }

        $json = $response->json();
        $data = is_array($json['data'] ?? null) ? $json['data'] : [];
        $transactionId = $this->extractTransactionIdFromPayload($data);
        if ($transactionId === '') {
            throw new \RuntimeException('Spacepag: resposta sem identificador de transação.');
        }

        $pix = is_array($data['pix'] ?? null) ? $data['pix'] : [];
        $qrCode = is_array($pix['qrCode'] ?? null) ? $pix['qrCode'] : [];
        $emv = is_string($qrCode['emv'] ?? null) ? $qrCode['emv'] : null;
        $image = is_string($qrCode['image'] ?? null) ? $qrCode['image'] : null;

        return [
            'transaction_id' => $transactionId,
            'qrcode' => $image,
            'copy_paste' => $emv,
            'raw' => is_array($json) ? $json : [],
        ];
    }

    public function createCardPayment(
        array $credentials,
        float $amount,
        array $consumer,
        string $externalId,
        array $card
    ): array {
        throw new \RuntimeException('Spacepag não suporta pagamento com cartão neste checkout. Use outro gateway.');
    }

    public function createBoletoPayment(
        array $credentials,
        float $amount,
        array $consumer,
        string $externalId,
        string $notificationUrl
    ): array {
        throw new \RuntimeException('Spacepag não suporta boleto neste checkout. Use outro gateway.');
    }

    public function getTransactionStatus(string $transactionId, array $credentials): ?string
    {
        if ($this->resolveApiKey($credentials) === '') {
            return null;
        }

        $transactionId = trim($transactionId);
        if ($transactionId === '') {
            return null;
        }

        try {
            $response = $this->authenticatedClient($credentials)
                ->get('/payments/transactions/'.rawurlencode($transactionId));
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $json = $response->json();
        $data = is_array($json['data'] ?? null) ? $json['data'] : [];
        $status = $data['status'] ?? null;

        return $this->mapApiStatusToInternal(is_string($status) ? $status : null);
    }

    private function authenticatedClient(array $credentials): PendingRequest
    {
        $apiKey = $this->resolveApiKey($credentials);
        if ($apiKey === '') {
            throw new \RuntimeException('Spacepag: API Key não configurada.');
        }

        $options = [
            'connect_timeout' => $this->connectTimeoutSeconds($credentials),
        ];

        if ($this->shouldDisableProxy($credentials)) {
            $options['proxy'] = '';
        }

        if ($this->shouldForceIpv4ByDefault($credentials) && defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            $options['curl'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }

        return Http::baseUrl($this->baseUrl($credentials))
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeoutSeconds($credentials))
            ->withOptions($options)
            ->withHeaders([
                'X-API-Key' => $apiKey,
                'User-Agent' => config('app.name', 'Getfy'),
            ]);
    }

    /**
     * Chave para header X-API-Key (doc: pública ou privada; server-side prefere sk_).
     */
    private function resolveApiKey(array $credentials): string
    {
        $candidates = [];
        foreach (['api_key', 'secret_key', 'public_key'] as $field) {
            $raw = (string) ($credentials[$field] ?? '');
            if ($raw === '') {
                continue;
            }
            $normalized = $this->normalizeApiKey($raw);
            if ($normalized !== '') {
                $candidates[] = $normalized;
            }
        }

        foreach ($candidates as $key) {
            if (str_starts_with($key, 'sk_')) {
                return $key;
            }
        }

        return $candidates[0] ?? '';
    }

    private function normalizeApiKey(string $raw): string
    {
        $key = trim($raw);
        $key = preg_replace('/\s+/', '', $key) ?? '';
        if (preg_match('/^x-api-key:\s*(.+)$/i', $key, $m)) {
            $key = trim($m[1]);
        }
        if (preg_match('/^bearer\s+(.+)$/i', $key, $m)) {
            $key = trim($m[1]);
        }

        return trim($key, " \t\n\r\0\x0B\"'");
    }

    private function formatApiErrorMessage(Response $response, string $fallback): string
    {
        $json = $response->json();
        if (is_array($json)) {
            $message = $json['message'] ?? null;
            if (is_string($message) && trim($message) !== '') {
                return trim($message);
            }
        }

        return $fallback.' (HTTP '.$response->status().')';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function extractTransactionIdFromPayload(array $data): string
    {
        $tid = $data['transactionId'] ?? null;
        if (is_string($tid) && trim($tid) !== '') {
            return trim($tid);
        }
        $id = $data['id'] ?? null;
        if (is_string($id) && trim($id) !== '') {
            return trim($id);
        }

        return '';
    }

    private function mapApiStatusToInternal(?string $status): ?string
    {
        if ($status === null || $status === '') {
            return null;
        }
        $s = strtoupper(trim($status));
        if ($s === 'APPROVED' || $s === 'PAID') {
            return 'paid';
        }
        if ($s === 'PENDING' || $s === 'PROCESSING') {
            return 'pending';
        }
        if ($s === 'EXPIRED' || $s === 'FAILED') {
            return 'cancelled';
        }
        if ($s === 'REFUNDED' || $s === 'REVERSED') {
            return 'refunded';
        }

        return strtolower($status);
    }

    /**
     * Formato do exemplo oficial PIX: DDD + número (ex.: 119853646233), sem forçar +55.
     */
    private function normalizeCustomerPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $digits = is_string($digits) ? $digits : '';
        if ($digits === '') {
            return '';
        }
        if (str_starts_with($digits, '55') && strlen($digits) > 11) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) >= 10 && strlen($digits) <= 11) {
            return $digits;
        }
        if (strlen($digits) >= 12) {
            return $digits;
        }

        return '';
    }

    private function normalizeDocument(string $document): string
    {
        $digits = preg_replace('/\D/', '', $document);
        $digits = is_string($digits) ? $digits : '';

        if (strlen($digits) === 11 || strlen($digits) === 14) {
            return $digits;
        }

        if (strlen($digits) > 14) {
            $digits = substr($digits, -14);
            if (strlen($digits) === 11 || strlen($digits) === 14) {
                return $digits;
            }
        }

        return '00000000000';
    }

    private function sanitizeName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?: '';
        $name = trim($name);
        if ($name === '') {
            return 'Cliente';
        }

        if (strlen($name) > 80) {
            return substr($name, 0, 80);
        }

        return $name;
    }

    private function sanitizeEmail(string $email): string
    {
        $email = trim($email);
        $email = preg_replace('/[\x00-\x1F\x7F]/u', '', $email) ?: '';
        $email = trim($email);
        if ($email === '') {
            return '';
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function baseUrl(array $credentials): string
    {
        $override = $credentials['base_url'] ?? null;
        if (is_string($override)) {
            $override = trim(str_replace(["\r", "\n", "\t"], '', $override), " \t\n\r\0\x0B/");
            if ($override !== '') {
                if (str_contains(strtolower($override), 'api.spacepag.com.br')) {
                    return self::BASE_URL;
                }

                return $override;
            }
        }

        return self::BASE_URL;
    }

    private function timeoutSeconds(array $credentials): int
    {
        $v = $credentials['timeout'] ?? null;
        $n = is_numeric($v) ? (int) $v : 25;

        return min(120, max(10, $n));
    }

    private function connectTimeoutSeconds(array $credentials): int
    {
        $v = $credentials['connect_timeout'] ?? null;
        $n = is_numeric($v) ? (int) $v : 10;

        return min(60, max(5, $n));
    }

    private function shouldForceIpv4ByDefault(array $credentials): bool
    {
        $v = $credentials['force_ipv4'] ?? null;
        if ($v === null) {
            return filter_var(getenv('GETFY_DOCKER') ?: false, FILTER_VALIDATE_BOOLEAN);
        }

        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldDisableProxy(array $credentials): bool
    {
        $v = $credentials['disable_proxy'] ?? null;
        if ($v === null) {
            return filter_var(getenv('GETFY_DOCKER') ?: false, FILTER_VALIDATE_BOOLEAN);
        }

        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }
}
