<?php

namespace App\Services;

use App\Agendamento;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $calendarId;
    protected $accessToken;

    public function __construct()
    {
        $this->calendarId = env('GOOGLE_CALENDAR_ID');
        $this->accessToken = env('GOOGLE_OAUTH_ACCESS_TOKEN');
    }

    public function sync(Agendamento $agendamento)
    {
        try {
            if (empty($this->calendarId) || empty($this->accessToken)) {
                throw new Exception("Configurações do Google ausentes no arquivo .env");
            }

            $eventData = $this->buildEventPayload($agendamento);
            $url = "https://www.googleapis.com/calendar/v3/calendars/{$this->calendarId}/events";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eventData));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ]);

            $responseBody = curl_exec($ch);
            $responseStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($responseStatusCode === 200 || $responseStatusCode === 201) {
                Log::info("Google Calendar API: Evento sincronizado com sucesso para o Agendamento ID: {$agendamento->id_agendamento}");
                return true;
            }

            throw new Exception("Falha ao criar evento no Google Calendar. Status: " . $responseStatusCode . " - " . $responseBody);

        } catch (Exception $e) {
            Log::error("Erro na sincronização com o Google Calendar: " . $e->getMessage());
            throw $e;
        }
    }

    protected function buildEventPayload(Agendamento $agendamento)
    {
        $inicio = $agendamento->data_horario_inicio instanceof \DateTime 
            ? $agendamento->data_horario_inicio->format('c') 
            : date('c', strtotime($agendamento->data_horario_inicio));

        $fim = $agendamento->data_horario_fim instanceof \DateTime 
            ? $agendamento->data_horario_fim->format('c') 
            : date('c', strtotime($agendamento->data_horario_fim));

        return [
            'summary' => 'Agendamento Cadastrado - Admink',
            'description' => $agendamento->observacao ?? 'Cadastrado via Manutenção Evolutiva.',
            'start' => [
                'dateTime' => $inicio,
                'timeZone' => 'America/Sao_Paulo',
            ],
            'end' => [
                'dateTime' => $fim,
                'timeZone' => 'America/Sao_Paulo',
            ],
        ];
    }
}