<?php 


/**
 * Requires ffmpeg 
 * Simple batch transcoder that uses ffmpeg. 
 *
 * to run from the command line and convert all videos in a folder to mp4..., 
 * cd into folder with videos. and run:
 * php /path/to/Transcoder.php mp4
 */


class Transcoder{

    /**
     * bash ffmpeg command. sometimes in different locations ie /usr/bin/ffmpeg
     * @var string
     */
	protected static $ffmpeg='ffmpeg';

    /**
     * list of supported extentions. many more exts are likely possible
     * @var array
     */
	protected static $supportedExts=array(

		'flv',
        'f4v',
        'mov',
        'avi',
        'mp4',
        'ogv',
        'mpeg',
        'm4v',
        'wmv',
        'mts',
        'mkv'
    );


	/**
     *
     * @param string $file
     * @return array<string>
     */
    private function _ffmpegTranscodeVideoCommands($file, $formats)
    {
        $target = $file; //$this->resolvePrimaryFile($file);

        $base = substr($file, 0, strrpos($file, '.'));

        $cmds = array();
        //self::_checkffmpeg();
        $ffmpeg = self::$ffmpeg;
        $input = escapeshellarg($file);
        foreach ($formats as $format) {
            $output = escapeshellarg($base . '.' . $format);

            if ($input !== $output) {
                $cmds[] = $ffmpeg . ' -i ' . $input . ' ' . $output;
            }
        }
        return $cmds;
    }
    public function isSupportedExt($ext){
    	return in_array($ext, self::$supportedExts);
    }
    public function getExt($file){
    	return substr($file, strrpos($file, '.')+1);
    }
    public function getTranscodeCmd($file, $toExt){
    	return $this->_ffmpegTranscodeVideoCommands($file, array($toExt))[0];
    }
    public function listVideoFiles($dir){
    	$paths=array_filter(scandir($dir), function($p){
			return $this->isSupportedExt($this->getExt($p));
		});
		return $paths;
    }

}


if (realpath($argv[0]) === __FILE__) {

	$transcoder=new Transcoder();
   
	$dir=getcwd();

	print_r($paths=$transcoder->listVideoFiles($dir));


	if(count($argv)>1){

		if($transcoder->isSupportedExt($argv[1])){
			echo 'Transcoding to: '.$argv[1]."\n";
			foreach($paths as $path){
				if($transcoder->getExt($path)===$argv[1]){
					continue;
				}
				$cmd=$transcoder->getTranscodeCmd($dir.'/'.$path, $argv[1]);
				echo $cmd."\n\n";
				system($cmd, $retval);
				if($retval!==false){
					echo 'Success'."\n\n";
				}else{
					echo 'Fail'."\n\n";
				}
			}
		}
	}else{
		echo 'Provide output format arg to transcode all files ('.count($paths).') in: '.$dir."\n";
	}

	

}