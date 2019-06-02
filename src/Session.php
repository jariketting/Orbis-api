<?php
namespace Orbis;


class Session extends Model
{
    /**
     * Validates session id
     */
    static function validate() : void {
        $data = ['valid' => false]; //store return data

        $sessionId = Post::get('session_id'); //get session id from post

        //give error when no session id is given
        if(!$sessionId)
            JsonResponse::error('Session id not given.', '"session_id" is missing in post data.', 400);

        //return session id (for debugging)
        $data['session_id'] = $sessionId;

        JsonResponse::setData($data); //set data to created return data
    }
}