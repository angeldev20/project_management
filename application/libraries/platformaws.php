<?php
/**
 * Created by PhpStorm.
 * User: damon
 * Date: 10/4/17
 * Time: 1:22 PM
 */

class platformaws {

    /** @var  string */
    private $_aws_access_key;

    /** @var  string */
    private $_aws_secret_key;

    /** @var  Aws\S3\S3Client */
    private $_s3;

    public function __construct($init_array = NULL)
    {

        $this->_aws_access_key = $init_array['aws_access_key'];
        $this->_aws_secret_key = $init_array['aws_secret_key'];
    }

    /**
     * @return \Aws\S3\S3Client
     */
    public function getS3Client() {
        $credentials = new Aws\Credentials\Credentials($this->_aws_access_key, $this->_aws_secret_key);
        $this->_s3 = new Aws\S3\S3Client([
            'version'     => 'latest',
            'region'      => 'us-west-2',
            'credentials' => $credentials
        ]);
        return $this->_s3;
    }

    /**
     * @return \Aws\SES\SesClient
     */
    public function getSESClient() {
        $credentials = new Aws\Credentials\Credentials($this->_aws_access_key, $this->_aws_secret_key);
        $ses = new Aws\SES\SesClient([
            'version'     => 'latest',
            'region'      => 'us-west-2',
            'credentials' => $credentials
        ]);
        return $ses;

    }

    /**
     * @param string $bucket - name such as
     * @param $keyname
     * @return mixed
     */
    public function getObject($bucket, $keyname) {
        $result = $this->_s3->getObject([
            'Bucket' => $bucket,
            'Key' => $keyname
        ]);
        return $result;
    }

    /**
     * Creates a file on s3 with the bucket, folder and name, and data specified
     * @param string $bucket
     * @param string $keyname folder and filename
     * @param string $filedata - file data, can be ascii or binary
     * @return \Aws\Result
     */
    //TODO: need to make sure file extension is lower case for mime type detection
    public function putObject($bucket, $keyname, $filedata) {
        $result = $this->_s3->putObject([
            'ACL' => 'public-read',
            'Bucket' => $bucket,
            'Key' => $keyname,
            'Body' => $filedata
        ]);
        return $result;
    }

    /**
     * Creates a file on s3 with the bucket, folder and name, and data specified
     * @param string $bucket
     * @param string $keyname folder and filename
     * @param string $filename - full path and filename to local file
     * @return \Aws\Result
     */
    //TODO: need to make sure file extension is lower case for mime type detection
    public function putObjectFile($bucket, $keyname, $filename) {
        if (!$this->_s3) $client = $this->getS3Client();
        $result = $this->_s3->putObject([
            'ACL' => 'public-read',
            'Bucket' => $bucket,
            'Key' => $keyname,
            'SourceFile' => $filename
        ]);
        return $result;
    }

    /**
     * @param string $bucket
     * @param string $prefix i.e. 'files/'
     * @param string $filemask - i.e. '*.jpg'
     * @return mixed
     */
    private function _listObjects($bucket, $prefix, $filemask = '')
    {
        if (!$this->_s3) $client = $this->getS3Client();
        $objects = $this->_s3->listObjects(
            [
                'Bucket' => $bucket,
                'MaxKeys' => 1000,
                'Prefix' => $prefix . $filemask
            ]
        );
        return $objects;
    }

    /**
     * Gets a list of files from the bucket and prefix location on s3
     *
     * NOTE: this will only list up to the first 1000 files, if more than that are needed, new logic including the
     * 'Marker' param will need to be created
     *
     * @param string $bucket - i.e.  spera-production
     * @param string $prefix - i.e. default/blueline/images/backgrounds/
     * @param string $filemask - TODO: not working, needs some work, leave blank for now
     * @return array
     */
    public function getFileList($bucket, $prefix, $filemask = '') {
        if (!$this->_s3) $client = $this->getS3Client();
        $filenames = [];
        $response = $this->_listObjects($bucket, $prefix, $filemask);
        $fileObjects = $response->getPath('Contents');
        if (is_array($fileObjects)) {
            foreach ($fileObjects as $fileObject) {
                $fileObject = (object) $fileObject;
                $file = explode($prefix, $fileObject->Key)[1];
                if($file != '') $filenames[] = $file;
            }
        }
        return $filenames;
    }

    /**
     * @param string $sourceBucket -  i.e. spera-development
     * @param string $destinationBucket - i.e. spera-development
     * @param string $sourceKeyname - i.e. default/
     * @param string $destinationKeyname i.e. todd/
     * @return bool
     */
    public function copyPath($sourceBucket, $destinationBucket,$sourceKeyname, $destinationKeyname) {
        $success = false;
        if (!$this->_s3) $client = $this->getS3Client();

        $filesAndSubFoldersToCopy = $this->getFileList($sourceBucket, $sourceKeyname);

        $counter = 0;
        try {
            $failed = array();
            foreach($filesAndSubFoldersToCopy as $keyName) {
                $this->_s3->copyObject(array(
                    'Bucket'     => $destinationBucket,
                    'Key'        => $destinationKeyname . $keyName, //TODO: check to see if sourceKeyname should have or does have a slash
                    'CopySource' => "{$sourceBucket}/{$sourceKeyname}{$keyName}",
                    'ACL' => 'public-read'
                ));
                $counter++;
            }
            if ($counter == count($filesAndSubFoldersToCopy)) $success = true;
        } catch (Exception $e) {
            $successful = $e->getMessage();
            $failed = $e->getTrace();
        }
        return $success;
    }
}