<?php namespace Core;

class InstagramPlugin
{
    protected static $tag = 'domowekwietnik';
    protected static $access_token = 'EAA0RHoXQa44BAGtbPlXrwDgpHz5HNYQDl3bXlY5MF9InDj62i1EA3NPs3iZCuh1GF5LvWJjqPFky5d5uR8a1Uori9uFJvscoGCpV4ZBnjfmGayBXZBda6XSXJRrF2h3ObITMpSalJHujdbqaJxMZAfMZByadhVCCIO2eqx9UIZAzSQdrfZBW1On5PZCr8rKLYB9iw7URc3MZBqsWxxjWiSk0l';

    protected static function connect( $api_url )
    {
    	$connection_c = curl_init(); // initializing
    	curl_setopt( $connection_c, CURLOPT_URL, $api_url ); // API URL to connect
    	curl_setopt( $connection_c, CURLOPT_RETURNTRANSFER, 1 ); // return the result, do not print
    	curl_setopt( $connection_c, CURLOPT_TIMEOUT, 20 );
    	$json_return = curl_exec( $connection_c ); // connect and get json data
    	curl_close( $connection_c ); // close connection
    	return json_decode( $json_return ); // decode and return
    }

    public static function getMedias()
    {
        $results = self::connect('https://api.instagram.com/v1/users/self/media/recent?access_token=' . self::$access_token);
        print_r($results);
        exit;
        if( empty( $results )) {
            return;
        }

        print_r($results);
        exit;

        $data = [];

        foreach( $results->data as $post )
        {
            $data[] = [
                'link' => $post->link,
                'photo' => [
                    'url' => $post->images->standard_resolution->url,
                    'thumb' => $post->images->thumbnail->url
                ]
            ];
            /*
        	$post->images->standard_resolution->url - URL of 612x612 image
        	$post->images->low_resolution->url - URL of 150x150 image
        	$post->images->thumbnail->url - URL of 306x306 image

        	$post->type - "image" or "video"
        	$post->videos->low_resolution->url - URL of 480x480 video
        	$post->videos->standard_resolution->url - URL of 640x640 video

        	$post->link - URL of an Instagram post
        	$post->tags - array of assigned tags
        	$post->id - Instagram post ID
        	$post->filter - photo filter
        	$post->likes->count - the number of likes to this photo
        	$post->comments->count - the number of comments
        	$post->caption->text
        	$post->created_time

        	$post->user->username
        	$post->user->profile_picture
        	$post->user->id

        	$post->location->latitude
        	$post->location->longitude
        	$post->location->street_address
        	$post->location->name
        	*/
        }

        return $data;
    }

    public function oauth()
    {
        $results = self::connect('https://api.instagram.com/oauth/access_token');
        
    }
}
