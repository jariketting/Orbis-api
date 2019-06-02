<?php
namespace Orbis;


class Session extends Model
{
    /**
     * Validates session id
     */
    static function validate() : void {
        $data = ['valid' => false];

        $sessionId = Post::get('session_id');

        if(!$sessionId)
            JsonResponse::error('Session id not given.', '"session_id" is missing in post data.', 400);

        $data['session_id'] = $sessionId;

        JsonResponse::setData($data);
    }
}