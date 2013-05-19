<?php
include 'stupeflix.php';
  
class StupeflixClient extends Stupeflix {

  private $stupeflixAccessKey = 'Kxws1C7wCzJfLFbrsB2m';
  private $stupeflixSecretKey = 'bKm4a18UgLVqzbvemTVOnHT5RIKyxa17nlZFhS1x';
  private $stupeflixHost = 'http://services.stupeflix.com';
  private $user;
  private $resource;
  
  public $status;
  
  protected static $_instance;
  
  public function __construct($accessKey='', $secretKey='', $host='', $service = 'stupeflix-1.0', $debug = false)
  {
  		
  		if($accessKey) $this->stupeflixAccessKey = $accessKey;
  		if($secretKey) $this->stupeflixSecretKey = $secretKey;
  		if($host) $this->stupeflixHost = $host;
  		parent::__construct($this->stupeflixAccessKey, $this->stupeflixSecretKey, $this->stupeflixHost);
 
  }
  
  private function __clone() {}
  
  public static function getInstance()
  {
    if(is_null(self::$_instance))
    {
        self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function createVideo($user, $resource, $xml_file,$metaDict,$profileName='iphone')
  {
  
  		// Each of your users can have different projects.
  		// If you want to overwrite your video each time you render it, you can define a char string
  		// to be used as the ressource.
 		// If you want to create a new video each time you render it (as this is the most common use case)
 		// you need to generate a unique ressource each time.	
  	$this->user = md5($user);
  		
   		// in case you overwrite the video for each render
   		// in case you want to create a new video for each render
   		// $resource = time();
  	$this->resource = $resource;  
  
  	try{

  	if($metaDict) $meta = new StupeflixMeta($metaDict);

  	$upload = new StupeflixDefaultUpload();

    // If you want to use http POST multipart/form-data upload to your own server, use this line instead of the previous (you can use too both upload types)
    //upload = new StupeflixHttpPOSTUpload("http://wwww.mycompany.com/upload");
    $profileIphone = new StupeflixProfile($profileName, array($upload), $meta);
    
    // Notification
    $notify = null;
    // Uncomment this line if you want to receive a ping when the video is available
    //$notify = parent::StupeflixNotify("http://mywebserver.com/path/to/receiver/", "available");
    // Uncomment this line if you want to receive a ping when the video state change (queued / start / info ... available).
    //$notify = parent::StupeflixNotify("http://mywebserver.com/path/to/receiver/");

  	// $profileArray = array($profileIphone, $profileQuicktime);
    $profiles = new StupeflixProfileSet(array($profileIphone), null, $notify);
    $profileNames = $profiles->getProfileNames();
    
    $this->status = "Sending definition...";
    $this->sendDefinition($this->user, $this->resource, $xml_file);
    
    $this->status = "Launching generation of profiles";
    $this->createProfiles($this->user, $this->resource, $profiles);
    
    // This line will give you a url for the preview: you can point a standard flash player at it, you will get a video stream as soon as the video generation starts
    // You will have to ask for a preview in the meta : see before in $metaDict
    $previewURL = $this->getProfilePreviewUrl($user, $resource, $profileName);
    $this->status = "preview URL: " . $previewURL;
        
    // Now wait for the completion of the video
    $lastCompletion = -1;
    
    // Loop : wait for the video to complete
    $this->status = "Waiting for completion...";
    
  	while(true)
    {
        // Retrieve the info for all profiles generated for user/resource
        $infoArray = $this->getProfileStatus($this->user, $this->resource, null);
        $completed = true;
        foreach ($infoArray as $info)
        {
            $profile = $info->profile;

            // Test if the status concern a profile we just generated : we may get status for other profiles previously generated for the same user/resource
            if (! in_array($profile, $profileNames, True))
            {
                // Skipping existing profile not in the list to be generated presently
                continue;
            }
            $stat = $info->status;
            
            if ($stat->status != "available")
            {
                $completed = false;
            }
            $error = null;
            $complete = 0;
            if (isset($stat->complete))
            {
                $complete = $stat->complete;
            }
            if (isset($stat->error))
            {
                if ($stat->error)
                {
                    $this->status = "ERROR while generating: ". $stat->error;
                    exit;
                }
            }

            $this->status = $profile." : generation ".$complete." %";
        }
        if ($completed)
        {
            break;
        }
        sleep(5);
    }
        
    foreach ($profileNames as $profileName)
    {
        $profileUrl = $this->getProfileURL($this->user, $this->resource, $profileName);
        // Additionally, you can download the files using getProfile function
        $movieFilename = "videos/movie$profileName.mp4";
        $this->status = "Downloading from ".$profileUrl." to ".$movieFilename;
        $this->getProfile($this->user, $this->resource, $profileName, $movieFilename);
        $this->status = "success";
    }
    
    } catch (Exception $e) {
  		echo $e->getMessage();
	}
    
  }
  
  public function getStatus()
  {
  	
	return $this->status;
  	
  }
  
  public function getError()
  {
  		return $this->err;
  }
  
}
$stupeflix = StupeflixClient::getInstance();
?>