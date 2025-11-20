<?php
namespace Integrations;
class DoorLoop {
  public static function pushTenant(array $tenant): array {
    $key = $_ENV['DOORLOOP_API_KEY'] ?? getenv('DOORLOOP_API_KEY') ?: '';
    if (!$key) return ['ok'=>false,'error'=>'No DOORLOOP_API_KEY configured'];
    $payload = [
      'firstName' => $tenant['business_name'] ?? 'Tenant',
      'email' => $tenant['email'] ?? null,
      'phone' => $tenant['phone'] ?? null,
      'externalId' => $tenant['id'] ?? null,
      'notes' => 'Synced from Aban Suites site'
    ];
    $ch = curl_init('https://api.doorloop.com/v1/tenants');
    curl_setopt_array($ch,[
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => ['Content-Type: application/json','Authorization: Bearer ' . $key],
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 20
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($err) return ['ok'=>false,'error'=>$err];
    return ['ok'=>($code>=200 && $code<300),'status'=>$code,'body'=>$res];
  }
}
