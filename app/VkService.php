<?php

namespace App;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class VkService
{
    private function callApi($id)
    {
        $response = Http::get(config('services.vk.url').'friends.get', [
            'user_id' => $id,
            'access_token' => config('services.vk.token'),
            'v' => config('services.vk.version'),
            'fields' => 'first_name,last_name,id',
        ]);

        return $response->json()['response'] ? $response->json()['response']['items'] : null;
    }

    public function getFriends($id)
    {
        $friends = $this->callApi($id);

        foreach ($friends ?? [] as $friend) {
            if(!User::where('vk_id', $friend['id'])->exists()) {
                $user = User::create([
                    'vk_id' => $friend['id'],
                    'first_name' => $friend['first_name'],
                    'last_name' => $friend['last_name'],
                ]);
            }
        }
    }
}